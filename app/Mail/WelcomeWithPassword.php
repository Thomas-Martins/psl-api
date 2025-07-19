<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeWithPassword extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User   $user,
        public string $password,
        public ?string $userLocale = null
    ) {}

    public function build()
    {
        $locale = $this->userLocale ?: config('app.locale');
        $this->locale($locale);

        return $this
            ->subject(__('emails.welcome.subject', ['app_name' => config('app.name')]))
            ->view('emails.welcome-with-password', [
                'user'     => $this->user,
                'password' => $this->password,
            ]);
    }
}
