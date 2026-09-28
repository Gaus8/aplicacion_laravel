<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestSmtpSettingRequest;
use App\Http\Requests\Admin\UpdateSmtpSettingRequest;
use App\Models\SmtpSetting;
use App\Services\SmtpMailer;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Gate;
use Throwable;

class SmtpSettingController extends Controller
{
    public function edit()
    {
        Gate::authorize('viewAny', SmtpSetting::class);
        $setting = SmtpSetting::query()->first();

        return view('admin.settings.smtp', compact('setting'));
    }

    public function update(UpdateSmtpSettingRequest $request)
    {
        $data = $request->validated();
        $setting = SmtpSetting::query()->first() ?? new SmtpSetting();
        $authenticationRequired = (bool) ($data['authentication_required'] ?? false);

        if ($authenticationRequired && blank($data['password'] ?? null) && blank($setting->password_encrypted)) {
            return back()->withErrors(['password' => 'Ingresa la contraseña SMTP para guardar esta configuración.'])->withInput($request->except('password'));
        }

        $setting->host = $data['host'];
        $setting->port = $data['port'];
        $setting->encryption = $data['encryption'] ?? null;
        $setting->authentication_required = $authenticationRequired;
        $setting->username = $authenticationRequired ? ($data['username'] ?? null) : null;
        if (!$authenticationRequired) {
            $setting->password_encrypted = null;
        } elseif (filled($data['password'] ?? null)) {
            $setting->password_encrypted = Crypt::encryptString($data['password']);
        }
        $setting->from_address = $data['from_address'];
        $setting->from_name = $data['from_name'];
        $setting->is_active = (bool) ($data['is_active'] ?? false);
        $setting->save();

        return redirect()->route('admin.smtp.edit')->with('success', 'La configuración SMTP se guardó correctamente.');
    }

    public function test(TestSmtpSettingRequest $request, SmtpMailer $mailer)
    {
        $setting = SmtpSetting::query()->first();
        if (!$setting) {
            return back()->withErrors(['smtp' => 'Guarda una configuración SMTP antes de probarla.']);
        }

        try {
            $mailer->test($setting, (string) $request->user()->email);
            $setting->last_test_status = 'success';
            $message = 'La prueba SMTP se envió al correo de tu cuenta.';
        } catch (Throwable) {
            $setting->last_test_status = 'failed';
            $message = 'No se pudo completar la prueba SMTP. Revisa el servidor y las credenciales.';
        }

        $setting->last_tested_at = now();
        $setting->save();

        return redirect()->route('admin.smtp.edit')->with($setting->last_test_status === 'success' ? 'success' : 'error', $message);
    }
}
