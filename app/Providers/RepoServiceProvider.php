<?php
namespace App\Providers;

use App\Repositories\AuthRepository;
use App\Repositories\Contract\AuthRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contract\FinanceRepositoryInterface;
use App\Repositories\FinanceRepository;

class RepoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(FinanceRepositoryInterface::class, FinanceRepository::class);
    }
}