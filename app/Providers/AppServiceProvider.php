<?php

namespace App\Providers;

use App\Models\Passport\Client;
use App\Models\Passport\Token;
use App\Repositories\CustomTokenRepository;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Laravel\Passport\TokenRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            TokenRepository::class,
            CustomTokenRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::useTokenModel(Token::class);
        Passport::useClientModel(Client::class);
        Passport::tokensExpireIn(now()->addDay(1));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
