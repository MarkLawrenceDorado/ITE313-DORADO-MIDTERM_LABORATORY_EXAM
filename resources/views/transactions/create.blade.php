<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Transaction</h2>
    </x-slot>

    <div class="max-w-md mx-auto py-8">
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('transactions.store') }}" class="space-y-4">
            @csrf
            <div>
                <label>Account</label>
                <select name="account_id" class="w-full border rounded">
                    @foreach ($accounts as $a)
                        <option value="{{ $a->id }}">{{ $a->account_number }} (Balance: {{ number_format($a->balance, 2) }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Type</label>
                <select name="type" class="w-full border rounded">
                    <option value="deposit">Deposit</option>
                    <option value="withdrawal">Withdrawal</option>
                </select>
            </div>
            <div>
                <label>Amount</label>
                <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="w-full border rounded">
            </div>
            <div>
                <label>Notes</label>
                <textarea name="notes" class="w-full border rounded">{{ old('notes') }}</textarea>
            </div>
            <button class="bg-indigo-600 text-white px-4 py-2 rounded">Submit</button>
        </form>
    </div>
</x-app-layout>
