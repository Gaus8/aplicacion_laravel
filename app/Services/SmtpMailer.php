<?php

namespace App\Services;

use App\Models\SmtpSetting;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class SmtpMailer
{
    public function configured(): bool
    {
        return SmtpSetting::query()->where('is_active', true)->exists();
    }

    public function mailer(): Mailer
    {
        $setting = SmtpSetting::query()->where('is_active', true)->first();
        if (!$setting) {
            return Mail::mailer(config('mail.default'));
        }

        $mailer = Mail::build([
            'name' => 'admin-smtp-settings',
            'transport' => 'smtp',
            'scheme' => match ($setting->encryption) {
                'tls' => 'smtp',
                'ssl' => 'smtps',
                default => 'smtp',
            },
            'host' => $setting->host,
            'port' => $setting->port,
            'username' => $setting->authentication_required ? $setting->username : null,
            'password' => $setting->authentication_required && $setting->password_encrypted
                ? Crypt::decryptString($setting->password_encrypted)
                : null,
            'timeout' => 10,
        ]);
        $mailer->alwaysFrom($setting->from_address, $setting->from_name);

        return $mailer;
    }

    public function test(SmtpSetting $setting, string $recipient): void
    {
        if (!$setting->is_active) {
            throw ValidationException::withMessages(['smtp' => 'Activa y guarda la configuración antes de probarla.']);
        }

        $this->mailer()->raw(
            'Esta es una prueba de conexión SMTP solicitada desde el panel administrativo.',
            fn ($message) => $message->to($recipient)->subject('Prueba de configuración SMTP')
        );
    }
}
