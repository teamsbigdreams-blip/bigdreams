<?php

namespace App\Providers;

use App\Listeners\RecordUserLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, RecordUserLogin::class);

        VerifyEmail::toMailUsing(function (object $notifiable, string $url): MailMessage {
            return (new MailMessage)
                ->subject('Konfirmasi Email Anda')
                ->markdown('mail.verify-email', [
                    'user' => $notifiable,
                    'url' => $url,
                ]);
        });
        if (! $this->app->environment('local')) {
            URL::forceScheme('https');
        }
    }
}
