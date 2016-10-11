<?php

namespace App\Providers;

use App\Board;
use App\Policies\BoardPolicy;
use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        Board::class => BoardPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::tokensExpireIn(Carbon::now()->addDays(15));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));

        Passport::routes(null, ['middleware' => 'log.requests']);
    }
}
