<?php

namespace Butler\Service\Http\Controllers;

use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirect()
    {
        return $this->driver()->redirect();
    }

    public function callback()
    {
        $oauthUser = $this->driver()->user();

        $user = [
            'id' => $oauthUser->id,
            'username' => $oauthUser->nickname,
            'name' => $oauthUser->name,
            'email' => $oauthUser->email,
            'oauth_token' => $oauthUser->token,
            'oauth_refresh_token' => $oauthUser->refreshToken,
            'remember_token' => null,
        ];

        Auth::login(new GenericUser($user));

        session(['user' => $user]);

        return redirect()->route('front');
    }

    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();

        return redirect()->route('front');
    }

    protected function driver()
    {
        return Socialite::driver('passport');
    }
}
