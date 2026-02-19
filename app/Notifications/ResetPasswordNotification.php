<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * Construir el mensaje de correo para la notificación.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Restablecer Contraseña - AgroCafé')
            ->view('emails.reset-password', [
                'url' => $url,
                'user' => $notifiable,
                'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
            ]);
    }
}
