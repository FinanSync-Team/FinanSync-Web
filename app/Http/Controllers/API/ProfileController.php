<?php

namespace App\Http\Controllers\API;

use App\Repositories\Contract\AuthRepositoryInterface;

class ProfileController extends BaseApiController {

    public function __construct(private AuthRepositoryInterface $authRepository)
    {}
    public function __invoke()
    {
        return $this->sendResponse($this->authRepository->user(), 'User API');
    }
}