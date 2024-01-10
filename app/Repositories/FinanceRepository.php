<?php
namespace App\Repositories;

use App\Repositories\Contract\FinanceRepositoryInterface;
use App\Models\Finance;
use App\Helper\Currency;

class FinanceRepository implements FinanceRepositoryInterface
{
    public function getAll()
    {
        return auth()
            ->user()
            ->finances()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($finance) {
                return $this->transform($finance);
            });
    }

    public function getById(int $id)
    {
        $finance = Finance::findOrFail($id);
        return $this->transform($finance);
    }

    public function create(array $data)
    {
        return $this->transform(
            auth()
                ->user()
                ->finances()
                ->create($data),
        );
    }

    public function update(int $id, array $data)
    {
        $finance = auth()
            ->user()
            ->finances()
            ->findOrFail($id);
        $finance->update($data);
        return $finance;
    }

    public function delete(int $id)
    {
        //make sure the finance belongs to authenticated user
        $finance = auth()
            ->user()
            ->finances()
            ->findOrFail($id);
        $finance->delete();
        return $finance;
    }

    public function setupMonthlyBudget(int $amount)
    {
        //update the monthly budget of authenticated user
        $user = auth()->user();
        $user->monthly_budget = $amount;
        $user->save();
        return $user;
    }

    private function transform($finance)
    {
        return $finance
            ? [
                'id' => $finance->id,
                'name' => $finance->name,
                'type' => $finance->type,
                'category' => $finance->category,
                'source' => $finance->source,
                'amount' => $finance->amount,
                'formatted_amount' => Currency::rupiah($finance->amount),
                'created_at' => $finance->created_at->format('d M Y H:i:s'),
                'updated_at' => $finance->updated_at->format('d M Y H:i:s'),
                'human_diff' => $finance->created_at->diffForHumans(),
            ]
            : null;
    }

    public function calculateBalance()
    {
        $finances = auth()->user()->finances;
        $balance = 0;
        foreach ($finances as $finance) {
            if ($finance->type == \App\Enums\FinanceType::EXPENSE->value) {
                $balance -= $finance->amount;
            } else {
                $balance += $finance->amount;
            }
        }
        return [
            'value' => $balance,
            'formatted_value' => Currency::rupiah($balance),
        ];
    }

    public function calculateBudget()
    {
        $finances = auth()
            ->user()
            ->finances()
            ->whereDate('created_at', '>=', now()->startOfMonth())
            ->get();
        $expense = 0;
        $income = 0;
        foreach ($finances as $finance) {
            if ($finance->type == \App\Enums\FinanceType::INCOME->value) {
                $income += $finance->amount;
            } else {
                $expense += $finance->amount;
            }
        }
        //$expense = $expense * -1;
        return [
            'expense' => $expense,
            'formatted_expense' => Currency::rupiah($expense),
            'income' => $income,
            'formatted_income' => Currency::rupiah($income),
        ];
    }

    // public function calculateChart()
    // {
    //     $finances = auth()
    //         ->user()
    //         ->finances()
    //         ->whereDate('created_at', '>=', now()->startOfMonth())
    //         ->where('type', \App\Enums\FinanceType::EXPENSE->value)
    //         ->get();
    //     $totalAmount = $finances->sum('amount');

    //     // Aggregate the data by categories and calculate the percentage
    //     $categoryData = Finance::groupBy('category')
    //         ->selectRaw('category, SUM(amount) as total')
    //         ->get()
    //         ->mapWithKeys(function ($item) use ($totalAmount) {
    //             // Calculate the percentage
    //             $percentage = $totalAmount > 0 ? ($item->total / $totalAmount) * 100 : 0;
    //             return [$item->category => round($percentage, 2)]; // rounding off to 2 decimal places
    //         });

    //     return $categoryData;
    // }

    private function calculateChart()
    {
        $categories = array_fill_keys(\App\Enums\FinanceCategory::values(), ['percentage' => 0, 'amount' => 0]);

        $finances = auth()
            ->user()
            ->finances()
            ->whereDate('created_at', '>=', now()->startOfMonth())
            ->where('type', \App\Enums\FinanceType::EXPENSE->value);
        $totalAmount = $finances->sum('amount');

        // Get the sum of amounts for each category
        $data = $finances
            ->groupBy('category')
            ->selectRaw('category, SUM(amount) as total')
            ->pluck('total', 'category');

        // Calculate the percentage and amount for each category and update the categories array
        foreach ($categories as $category => $value) {
            $amount = $data[$category] ?? 0;
            $percentage = $totalAmount > 0 ? ($amount / $totalAmount) * 100 : 0;
            $categories[$category] = [
                'percentage' => round($percentage, 2),
                'amount' => $amount,
            ];
        }

        return $categories;
    }

    private function calculateMonthlyBudgeting()
    {
        $finances = auth()
            ->user()
            ->finances()
            ->whereDate('created_at', '>=', now()->startOfMonth())
            ->get();
        $expense = 0;
        $income = 0;
        foreach ($finances as $finance) {
            if ($finance->type == \App\Enums\FinanceType::INCOME->value) {
                $income += $finance->amount;
            } else {
                $expense += $finance->amount;
            }
        }
        $budgeting = auth()->user()->monthly_budget - $expense;
        return [
            'budget' => auth()->user()->monthly_budget,
            'used_budget' => $expense,
            'formatted_used_budget' => Currency::rupiah($expense),
            'left_budget' => $budgeting,
            'formatted_left_budget' => Currency::rupiah($budgeting),
        ];
    }

    private function calculateBudgeting()
    {
        $user = auth()->user();
        $finances = $user
            ->finances()
            ->whereDate('created_at', '>=', now()->startOfMonth())
            ->where('type', \App\Enums\FinanceType::EXPENSE->value);
        $categories = $finances
            ->groupBy('category')
            ->selectRaw('category, SUM(amount) as total')
            ->get()
            ->map(function ($categoryItem) use ($finances) {
                $fin = clone $finances;

                $details = $fin
                    ->where('category', $categoryItem->category)
                    ->selectRaw('SUM(amount) as total, name as title')
                    ->groupBy('name')
                    ->get()
                    ->sortByDesc('total')
                    ->values();

                if ($details->count() > 2) {
                    $otherDetails = $details->slice(2)->reduce(function ($carry, $item) {
                        return $carry + $item->total;
                    }, 0);

                    $details = $details->slice(0, 2);
                    $details->push(['category'=> $categoryItem->category,'title' => 'Other', 'total' => $otherDetails]);
                }

                return [
                    'category' => $categoryItem->category,
                    'total' => $categoryItem->total,
                    'detail' => $details,
                ];
            });

        return $categories->values()->all();
    }

    public function budgeting()
    {
        return [
            'chart' => $this->calculateChart(),
            'monthly_budgeting' => $this->calculateMonthlyBudgeting(),
            'progress' => $this->calculateBudgeting(),
        ];
    }
}
