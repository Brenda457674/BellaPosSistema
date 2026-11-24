<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Correo de recuperar contraseña
        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            $minutos = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

            return (new MailMessage)
                ->subject('Cambia tu contraseña - Bella Flor')
                ->greeting('Hola, ' . $notifiable->nombres)
                ->line('Recibimos una solicitud para cambiar la contraseña de tu cuenta.')
                ->action('Crear contraseña nueva', $url)
                ->line("Este enlace vence en {$minutos} minutos.")
                ->line('Si no fuiste tú, ignora este mensaje: tu contraseña sigue igual.')
                ->salutation('Bella Flor POS');
        });

        // Correo de verificación
        VerifyEmail::toMailUsing(function ($notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verifica tu correo - Bella Flor')
                ->greeting('Hola, ' . $notifiable->nombres)
                ->line('Confirma que este correo es tuyo para activar tu cuenta.')
                ->action('Verificar correo', $url)
                ->line('Si no creaste esta cuenta, ignora este mensaje.')
                ->salutation('Bella Flor POS');
        });
    }
}
