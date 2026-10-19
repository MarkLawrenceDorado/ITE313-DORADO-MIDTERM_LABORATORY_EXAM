<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Transactions</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-8">
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <table class="w-full bg-white shadow rounded">
            <thead>
                <tr class="text-left border-b">
                    <th class="p-2">Ref</th>
                    <th>Account</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($transactions as $t)
                <tr class="border-b">
                    <td class="p-2">{{ $t->reference_number }}</td>
                    <td>{{ $t->account->account_number }}</td>
                    <td>{{ ucfirst($t->type) }}</td>
                    <td>{{ number_format($t->amount, 2) }}</td>
                    <td>{{ $t->created_at->format('M d, Y h:i A') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-4 text-center">No transactions yet.</td></tr>
            @endforelse
            </tbody>
        </table>

        <div class="mt-4">{{ $transactions->links() }}</div>
    </div>
</x-app-layout>
