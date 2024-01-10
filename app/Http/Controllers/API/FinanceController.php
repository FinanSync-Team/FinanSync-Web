<?php

namespace App\Http\Controllers\API;

use App\Repositories\Contract\FinanceRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class FinanceController extends BaseApiController {
    public function __construct(private FinanceRepositoryInterface $financeRepository)
    {
        //sleep(4);
    }

    public function getAll()
    {
        $finances = $this->financeRepository->getAll();
        return $this->sendResponse($finances, 'Finances retrieved successfully.');
    }

    public function getById(int $id)
    {
        $finance = $this->financeRepository->getById($id);
        if (is_null($finance)) {
            return $this->sendError('Finance not found.');
        }
        return $this->sendResponse($finance, 'Finance retrieved successfully.');
    }

    public function create(Request $request)
    {
        $data = Validator::make($request->all(), [
            'name' => 'required|string',
            'type' => 'required|string|in:'. implode(',', \App\Enums\FinanceType::values()),
            'category' => 'required|string|in:'. implode(',', \App\Enums\FinanceCategory::values()),
            'source' => 'required|string|in:'. implode(',', \App\Enums\FinanceSource::values()),
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);
        if ($data->fails()) {
            return $this->sendError('Validation Error.', 400, $data->errors());
        }
        $finance = $this->financeRepository->create($data->validated());
        return $this->sendResponse($finance, 'Finance created successfully.', 201);
    }

    public function update(Request $request, int $id)
    {
        $data = Validator::make($request->all(), [
            'name' => 'required|string',
            'type' => 'required|string|in:'. implode(',', \App\Enums\FinanceType::values()),
            'category' => 'required|string|in:'. implode(',', \App\Enums\FinanceCategory::values()),
            'source' => 'required|string|in:'. implode(',', \App\Enums\FinanceSource::values()),
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);
        if ($data->fails()) {
            return $this->sendError('Validation Error.', 400, $data->errors());
        }
        $finance = $this->financeRepository->update($id, $data->validated());
        return $this->sendResponse($finance, 'Finance updated successfully.');
    }

    public function delete(int $id)
    {
        $this->financeRepository->delete($id);
        return $this->sendResponse(null, 'Finance deleted successfully.');
    }

    public function setupMonthlyBudget(Request $request)
    {
        $data = Validator::make($request->all(), [
            'amount' => 'required|numeric',
        ]);
        if ($data->fails()) {
            $errors = $data->errors()->get('amount');
            return $this->sendError('Validation Error.', 400, $errors);
        }
        $this->financeRepository->setupMonthlyBudget($data->validated()['amount']);
        return $this->sendResponse(null, 'Monthly budget setup successfully.');
    }
}