<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        return response()->json([
            'total_accounts'     => Account::query()->count(),
            'total_money'        => (float) Account::query()->sum('balance'),
            'transactions_today' => Transaction::query()->whereDate('created_at', today())->count(),
        ]);
    }
}
