<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SocialController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();

            // البحث عن المستخدم أو إنشاؤه إذا مش موجود
            $user = User::firstOrCreate(
                ['email' => $socialUser->getEmail()],
                [
                    'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                    'password' => bcrypt(uniqid()), // إضافة كلمة سر عشوائية
                    'social_id' => $socialUser->getId(),  // إضافة الـ social id
                    'social_type' => $provider,  // إضافة نوع الحساب (مثل google أو github)
                    'email_verified_at' => now(), 
                    ]
                );
        
                // لو كان المستخدم موجود لكن لسه الإيميل مش متحقق
                if (is_null($user->email_verified_at)) {
                    $user->email_verified_at = now();
                    $user->save();
                }
      

            Auth::login($user);

            return redirect('/');  

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'حدث خطأ أثناء تسجيل الدخول: ' . $e->getMessage());
        }
    }
}
