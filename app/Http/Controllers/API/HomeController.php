<?php

namespace App\Http\Controllers\API;

use App\Repositories\Contract\FinanceRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Repositories\Contract\AuthRepositoryInterface;

class HomeController extends BaseApiController {

    public function __construct(private AuthRepositoryInterface $authRepository, private FinanceRepositoryInterface $financeRepository)
    {}
    public function __invoke()
    {
        return $this->sendResponse([
            'user' => $this->authRepository->user(),
            'finances' => $this->financeRepository->getAll()->take(3),
            'balance' => $this->financeRepository->calculateBalance(),
            'budget' => $this->financeRepository->calculateBudget(),
        ], 'Welcome to the API.');
    }
}