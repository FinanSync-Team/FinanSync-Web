<?php

namespace App\Http\Controllers\API;

use App\Repositories\Contract\FinanceRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Repositories\Contract\AuthRepositoryInterface;

class BudgetingController extends BaseApiController {

    public function __construct(private FinanceRepositoryInterface $financeRepository)
    {}
    public function __invoke()
    {
        return $this->sendResponse($this->financeRepository->budgeting(), 'Welcome to the API.');
    }
}