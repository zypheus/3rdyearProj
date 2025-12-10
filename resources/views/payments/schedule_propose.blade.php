<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Proposed Payment Schedule</h2>
                <p class="mt-1 text-sm text-slate-500">Loan #{{ $loan->id }} • {{ $loan->user->name }}</p>
            </div>
            <a href="{{ route('loans.show', $loan) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-700 hover:bg-slate-50">
                Back to Loan
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-card>
                <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700">
                    Review the generated schedule. Confirm to persist entries and enable advance-payment linking.
                </div>
                <div class="overflow-hidden rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-slate-600">#</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-slate-600">Due Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-slate-600">Amount</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-slate-600">Principal</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-slate-600">Interest</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @foreach($entries as $e)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-slate-800">{{ $e['sequence'] }}</td>
                                    <td class="px-4 py-2 text-sm text-slate-800">{{ \Carbon\Carbon::parse($e['due_date'])->format('M d, Y') }}</td>
                                    <td class="px-4 py-2 text-sm font-semibold text-slate-900">₱{{ number_format($e['amount'], 2) }}</td>
                                    <td class="px-4 py-2 text-sm text-slate-800">₱{{ number_format($e['principal_component'], 2) }}</td>
                                    <td class="px-4 py-2 text-sm text-slate-800">₱{{ number_format($e['interest_component'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <form method="POST" action="{{ route('loans.schedule.confirm', $loan) }}" class="mt-6">
                    @csrf
                    <input type="hidden" name="entries" value='@json($entries)'>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow">
                        Confirm Schedule
                    </button>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
