<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\PasswordUpdateResponse;
use Laravel\Fortify\Contracts\ProfileInformationUpdatedResponse;
use Laravel\Fortify\Contracts\TwoFactorDisabledResponse;
use Laravel\Fortify\Contracts\TwoFactorEnabledResponse;
use Laravel\Fortify\Contracts\TwoFactorConfirmedResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Inertia v0.x sends real PUT/DELETE via XHR. A 302/303 redirect
        // causes the browser to follow with the same method, hitting a
        // GET-only route with PUT → 405. Return 409 with X-Inertia-Location
        // to force a client-side page reload (Inertia's external redirect).
        $inertiaResponse = function ($request) {
            if ($request->header('X-Inertia')) {
                return response('', 409, [
                    'X-Inertia-Location' => url()->previous(),
                ]);
            }
            return back();
        };

        $this->app->singleton(PasswordUpdateResponse::class, function () use ($inertiaResponse) {
            return new class($inertiaResponse) implements PasswordUpdateResponse {
                private $respond;
                public function __construct($respond) { $this->respond = $respond; }
                public function toResponse($request) { return ($this->respond)($request); }
            };
        });

        $this->app->singleton(ProfileInformationUpdatedResponse::class, function () use ($inertiaResponse) {
            return new class($inertiaResponse) implements ProfileInformationUpdatedResponse {
                private $respond;
                public function __construct($respond) { $this->respond = $respond; }
                public function toResponse($request) { return ($this->respond)($request); }
            };
        });

        $this->app->singleton(TwoFactorDisabledResponse::class, function () use ($inertiaResponse) {
            return new class($inertiaResponse) implements TwoFactorDisabledResponse {
                private $respond;
                public function __construct($respond) { $this->respond = $respond; }
                public function toResponse($request) { return ($this->respond)($request); }
            };
        });

        $this->app->singleton(TwoFactorEnabledResponse::class, function () use ($inertiaResponse) {
            return new class($inertiaResponse) implements TwoFactorEnabledResponse {
                private $respond;
                public function __construct($respond) { $this->respond = $respond; }
                public function toResponse($request) { return ($this->respond)($request); }
            };
        });

        $this->app->singleton(TwoFactorConfirmedResponse::class, function () use ($inertiaResponse) {
            return new class($inertiaResponse) implements TwoFactorConfirmedResponse {
                private $respond;
                public function __construct($respond) { $this->respond = $respond; }
                public function toResponse($request) { return ($this->respond)($request); }
            };
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::requestPasswordResetLinkView(function () {
            return view('auth.forgot-password');
        });

        Fortify::resetPasswordView(function ($request) {
            return view('auth.reset-password', ['request' => $request]);
        });

        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });

        Fortify::twoFactorChallengeView(function () {
            return view('auth.two-factor-challenge');
        });

        Fortify::confirmPasswordView(function () {
            return view('auth.confirm-password');
        });
    }
}
