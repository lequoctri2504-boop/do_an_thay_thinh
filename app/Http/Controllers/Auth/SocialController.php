<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                Auth::login($user, true);
                return redirect('/');
            }

            $newUser = User::create([
                'ho_ten'     => $socialUser->getName() ?? 'Khách hàng',
                'email'      => $socialUser->getEmail(),
                'mat_khau'   => Hash::make(Str::random(24)),
                'provider'   => $provider,
                'provider_id'=> $socialUser->getId(),
                'avatar'     => $socialUser->getAvatar(),
                'vai_tro'    => 'KHACH_HANG',
            ]);

            Auth::login($newUser, true);
            return redirect('/');

        } catch (\Exception $e) {
            return redirect('/dangnhap')->with('error', 'Đăng nhập thất bại!');
        }
    }
}