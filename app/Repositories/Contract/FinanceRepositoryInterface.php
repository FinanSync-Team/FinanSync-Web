<?php


namespace App\Repositories\Contract;

interface FinanceRepositoryInterface {
    public function getAll();
    public function getById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);

    public function setupMonthlyBudget(int $amount);

    public function calculateBalance();
    public function calculateBudget();

    public function budgeting();
}