<?php

namespace App\Http\Controllers\API;

use App\Repositories\Contract\AuthRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class AuthController extends BaseApiController
{
    private $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(Request $request)
    {
        $data = Validator::make($request->all(), [
            'username' => 'required|string|unique:users|min:3|max:255',
            'email' => 'required|string|email|unique:users|min:3|max:255',
            'phone_number' => 'required|string|unique:users|min:5|max:15',
            'name' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);
        if ($data->fails()) {
            return $this->sendError('Validation Error.', 400, $data->errors());
        }

        $user = $this->authRepository->register($data->validated());

        return $this->sendResponse($user, 'User register successfully.', 201);
    }

    public function login(Request $request)
    {
        $data = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required',
        ]);
        if ($data->fails()) {
            return $this->sendError('Validation Error.', 400, $data->errors());
        }

        $loginData = $this->authRepository->login($data->validated());

        if (!$loginData) {
            return $this->sendError('Invalid email/username or password.', 401);
        }

        return $this->sendResponse($loginData, 'User login successfully.');
    }

    public function logout()
    {
        $this->authRepository->logout();
        return $this->sendResponse(null, 'User logout successfully.');
    }

    public function user()
    {
        return $this->authRepository->user();
    }
}
