<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-6 rounded shadow">
                    Total accounts<br>
                    <span id="total-accounts" class="text-3xl font-bold">-</span>
                </div>
                <div class="bg-white p-6 rounded shadow">
                    Money in bank<br>
                    <span id="total-money" class="text-3xl font-bold">-</span>
                </div>
                <div class="bg-white p-6 rounded shadow">
                    Transactions today<br>
                    <span id="transactions-today" class="text-3xl font-bold">-</span>
                </div>
            </div>

            <p class="text-sm text-gray-500">
                Last updated: <span id="last-updated">-</span> (refreshes every 30 seconds)
            </p>

            <div class="space-x-4">
                <a href="{{ route('transactions.create') }}" class="text-indigo-600 underline">New transaction</a>
                <a href="{{ route('transactions.index') }}" class="text-indigo-600 underline">View transactions</a>
            </div>
        </div>
    </div>

    <script>
    async function loadStats() {
        try {
            const res = await fetch('/api/dashboard/stats', { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error(res.status);
            const d = await res.json();
            document.getElementById('total-accounts').textContent = d.total_accounts;
            document.getElementById('total-money').textContent =
                Number(d.total_money).toLocaleString(undefined, { style: 'currency', currency: 'PHP' });
            document.getElementById('transactions-today').textContent = d.transactions_today;
            document.getElementById('last-updated').textContent = new Date().toLocaleTimeString();
        } catch (e) {
            console.error('Failed to load stats', e);
        }
    }
    loadStats();
    setInterval(loadStats, 30000); // AJAX polling every 30 seconds
    </script>
</x-app-layout>
