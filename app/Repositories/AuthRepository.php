<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contract\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AuthRepository implements AuthRepositoryInterface
{
    public function register(array $data)
    {
        $user = User::create($data);
        return $user;
    }

    public function login(array $data)
    {
        $loginType = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $login = [
            $loginType => $data['login'],
            'password' => $data['password']
        ];

        if (!Auth::attempt($login)) {
            return false;
        }
        $user = $this->user();
        $accessToken = auth()->user()->createToken($user['email'])->plainTextToken;

        return [
            'token' => $accessToken,
            'user' => $user
        ];
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return true;
    }

    public function user()
    {
        return auth()->user()->only(['id', 'name', 'username', 'email', 'phone_number', 'monthly_budget']);
    }
}