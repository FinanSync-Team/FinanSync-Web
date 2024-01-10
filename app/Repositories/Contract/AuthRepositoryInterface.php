<?php

namespace App\Repositories\Contract;

interface AuthRepositoryInterface {
    public function register(array $data);
    public function login(array $data);
    public function logout();
    public function user();
}