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
        // Return 303 redirects so PUT/DELETE requests redirect as GET
        $this->app->singleton(PasswordUpdateResponse::class, function () {
            return new class implements PasswordUpdateResponse {
                public function toResponse($request)
                {
                    return back()->setStatusCode(303);
                }
            };
        });

        $this->app->singleton(ProfileInformationUpdatedResponse::class, function () {
            return new class implements ProfileInformationUpdatedResponse {
                public function toResponse($request)
                {
                    return back()->setStatusCode(303);
                }
            };
        });

        $this->app->singleton(TwoFactorDisabledResponse::class, function () {
            return new class implements TwoFactorDisabledResponse {
                public function toResponse($request)
                {
                    return back()->setStatusCode(303);
                }
            };
        });

        $this->app->singleton(TwoFactorEnabledResponse::class, function () {
            return new class implements TwoFactorEnabledResponse {
                public function toResponse($request)
                {
                    return back()->setStatusCode(303);
                }
            };
        });

        $this->app->singleton(TwoFactorConfirmedResponse::class, function () {
            return new class implements TwoFactorConfirmedResponse {
                public function toResponse($request)
                {
                    return back()->setStatusCode(303);
                }
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
