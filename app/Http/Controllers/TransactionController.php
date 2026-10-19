<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientFundsException;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $transactions = Transaction::query()
            ->with('account')
            ->whereHas('account', fn ($q) => $q->where('user_id', $userId))
            ->latest()
            ->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function create(Request $request): View
    {
        $accounts = Account::query()
            ->where('user_id', $request->user()->id)
            ->get();

        return view('transactions.create', compact('accounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = $request->user()->id;

        $validator = Validator::make($request->all(), [
            'account_id' => [
                'required',
                Rule::exists('accounts', 'id')->where('user_id', $userId),
            ],
            'type'   => ['required', Rule::in(['deposit', 'withdrawal'])],
            'amount' => ['required', 'numeric', 'min:1'],
            'notes'  => ['nullable', 'string', 'max:500'],
        ]);

        // Custom validation: sufficient funds for withdrawals
        $validator->after(function ($validator) use ($request) {
            if ($validator->errors()->isNotEmpty() || $request->input('type') !== 'withdrawal') {
                return;
            }

            $account = Account::query()->find($request->input('account_id'));

            $remaining = round((float) $account->balance - (float) $request->input('amount'), 2);

            if ($remaining < (float) $account->minimum_balance) {
                $validator->errors()->add(
                    'amount',
                    'Insufficient funds. A withdrawal cannot bring the balance below the minimum balance of '
                    . number_format((float) $account->minimum_balance, 2) . '.'
                );
            }
        });

        $data = $validator->validate();

        try {
            DB::transaction(function () use ($data) {
                Transaction::create($data + [
                    'reference_number' => strtoupper(Str::random(10)),
                ]);
            });
        } catch (InsufficientFundsException $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }

        return redirect()->route('transactions.index')->with('success', 'Transaction completed.');
    }
}
