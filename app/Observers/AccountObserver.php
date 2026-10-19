<?php

namespace App\Observers;

use App\Exceptions\InsufficientFundsException;
use App\Models\Account;
use App\Models\Transaction;
use App\Notifications\LowBalanceNotification;
use Illuminate\Support\Facades\DB;

class AccountObserver
{
    /**
     * Runs BEFORE the transaction row is saved.
     * Throwing here stops the insert, so no record is created.
     */
    public function creating(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $account = Account::query()
                ->lockForUpdate()
                ->findOrFail($transaction->account_id);

            $balance = (float) $account->balance;
            $amount = (float) $transaction->amount;
            $minimum = (float) $account->minimum_balance;

            $newBalance = round(
                $transaction->type === 'deposit' ? $balance + $amount : $balance - $amount,
                2
            );

            if ($transaction->type !== 'deposit' && $newBalance < $minimum) {
                throw new InsufficientFundsException(
                    'Insufficient funds: the balance cannot fall below the minimum balance.'
                );
            }

            $account->balance = $newBalance;

            if ($newBalance > $minimum) {
                $account->low_balance_notified_at = null;
            }

            $account->save();
        });
    }

    /**
     * Runs AFTER the transaction row is saved: low-balance alert.
     */
    public function created(Transaction $transaction): void
    {
        if ($transaction->type === 'deposit') {
            return;
        }

        $account = Account::query()->with('user')->findOrFail($transaction->account_id);

        $isLow = (float) $account->balance <= (float) $account->minimum_balance;

        if ($isLow && $account->low_balance_notified_at === null) {
            $account->user->notify(new LowBalanceNotification($account));
            $account->forceFill(['low_balance_notified_at' => now()])->save();
        }
    }
}
