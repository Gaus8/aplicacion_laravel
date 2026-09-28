<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RequestPasswordOtpRequest;
use App\Http\Requests\Auth\ResendPasswordOtpRequest;
use App\Http\Requests\Auth\ResetPasswordWithOtpRequest;
use App\Http\Requests\Auth\VerifyPasswordOtpRequest;
use App\Models\PasswordRecoveryOtp;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\SmtpMailer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

class PasswordRecoveryController extends Controller
{
    private string $dummyCodeHash;

    public function __construct()
    {
        $this->dummyCodeHash = Hash::make(Str::random(48));
    }

    public function requestForm()
    {
        return view('auth.password-recovery.request');
    }

    public function sendCode(RequestPasswordOtpRequest $request, SmtpMailer $smtpMailer, AuditLogger $auditLogger)
    {
        $email = $request->validated('email');
        $request->session()->put('password_recovery.email', $email);
        $request->session()->forget('password_recovery.verified');
        $auditLogger->record('auth.password_recovery.requested', 'warning', null, 'Recuperación solicitada');

        $userExists = User::query()->where('email', $email)->exists();
        if ($userExists && $smtpMailer->configured()) {
            $this->issueCode($email, $smtpMailer);
        }

        return redirect()->route('password.otp.form')->with('status', 'Si existe una cuenta para ese correo, recibirás un código para continuar.');
    }

    public function otpForm()
    {
        $email = session('password_recovery.email');
        if (!filled($email)) {
            return redirect()->route('password.request');
        }

        return view('auth.password-recovery.otp', ['maskedEmail' => $this->maskEmail($email)]);
    }

    public function verifyCode(VerifyPasswordOtpRequest $request, AuditLogger $auditLogger)
    {
        $email = (string) $request->session()->get('password_recovery.email');
        if ($email === '') {
            $auditLogger->record('auth.password_recovery.code_rejected', 'failure', null, 'Código rechazado');
            return redirect()->route('password.request')->withErrors(['code' => 'El código no es válido o ya venció. Solicita otro.']);
        }

        $challenge = PasswordRecoveryOtp::query()->where('email', $email)->first();
        $matches = Hash::check($request->validated('code'), $challenge?->code_hash ?? $this->dummyCodeHash);
        $expired = !$challenge || $challenge->expires_at->isPast();
        $locked = $challenge && $challenge->attempts >= 5;

        if (!$challenge || $expired || $locked || !$matches) {
            if ($challenge && !$expired && !$locked) {
                $challenge->attempts++;
                $challenge->save();
            }

            $auditLogger->record('auth.password_recovery.code_rejected', 'failure', null, 'Código rechazado');
            return back()->withErrors(['code' => 'El código no es válido o ya venció. Solicita otro.']);
        }

        $challenge->verified_at = now();
        $challenge->save();
        $auditLogger->record(
            'auth.password_recovery.code_verified',
            'success',
            User::query()->where('email', $email)->first(),
            'Código de recuperación verificado'
        );
        $request->session()->put('password_recovery.verified', true);
        $request->session()->regenerate();

        return redirect()->route('password.reset.form');
    }

    public function resendCode(ResendPasswordOtpRequest $request, SmtpMailer $smtpMailer, AuditLogger $auditLogger)
    {
        $email = (string) $request->session()->get('password_recovery.email');
        $auditLogger->record('auth.password_recovery.resent', 'warning', null, 'Reenvío solicitado');
        if ($email !== '' && User::query()->where('email', $email)->exists() && $smtpMailer->configured()) {
            $this->issueCode($email, $smtpMailer);
        }

        return redirect()->route('password.otp.form')->with('status', 'Si existe una cuenta para ese correo, recibirás un código para continuar.');
    }

    public function resetForm()
    {
        if (!$this->hasVerifiedChallenge()) {
            return redirect()->route('password.request')->withErrors(['recovery' => 'La solicitud venció. Inicia de nuevo la recuperación.']);
        }

        return view('auth.password-recovery.reset');
    }

    public function resetPassword(ResetPasswordWithOtpRequest $request, AuditLogger $auditLogger)
    {
        $email = (string) $request->session()->get('password_recovery.email');
        $user = User::query()->where('email', $email)->first();
        if (!$user) {
            $request->session()->forget('password_recovery');
            return redirect()->route('login')->with('status', 'No fue posible completar la recuperación. Inicia sesión o vuelve a intentarlo.');
        }

        $user->password = Hash::make($request->validated('password'));
        $user->save();
        $user->tokens()->delete();
        $auditLogger->record('auth.password_reset.completed', 'success', $user, 'Contraseña restablecida');
        PasswordRecoveryOtp::query()->where('email', $email)->delete();
        $request->session()->forget('password_recovery');

        return redirect()->route('login')->with('success', 'Tu contraseña se actualizó. Inicia sesión con la nueva contraseña.');
    }

    private function issueCode(string $email, SmtpMailer $smtpMailer): void
    {
        $challenge = PasswordRecoveryOtp::query()->where('email', $email)->first();
        if ($challenge && $challenge->sent_at->gt(now()->subSeconds(60))) {
            return;
        }

        $code = (string) random_int(100000, 999999);
        $challenge ??= new PasswordRecoveryOtp();
        $challenge->email = $email;
        $challenge->code_hash = Hash::make($code);
        $challenge->attempts = 0;
        $challenge->sent_at = now();
        $challenge->expires_at = now()->addMinutes(5);
        $challenge->verified_at = null;
        $challenge->save();

        try {
            $smtpMailer->mailer()->raw(
                'Tu código para restablecer la contraseña de '.config('app.name')." es: {$code}\n\nEl código vence en 5 minutos. Si no solicitaste este cambio, puedes ignorar este mensaje.",
                fn ($message) => $message->to($email)->subject('Código para recuperar tu contraseña')
            );
        } catch (Throwable) {
            // Mantiene una respuesta pública genérica y evita exponer detalles SMTP.
        }
    }

    private function hasVerifiedChallenge(): bool
    {
        $email = session('password_recovery.email');

        return session('password_recovery.verified') === true
            && filled($email)
            && PasswordRecoveryOtp::query()
                ->where('email', $email)
                ->whereNotNull('verified_at')
                ->where('expires_at', '>', now())
                ->exists();
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');

        return mb_substr($local, 0, 1).'***@'.$domain;
    }
}
