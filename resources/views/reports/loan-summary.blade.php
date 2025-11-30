<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">
                    Loan Summary Report
                </h2>
                <p class="mt-1 text-sm text-slate-500">Overview of all loan applications and their status</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Reports
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Filters --}}
            <x-card class="mb-6">
                <form method="GET" action="{{ route('reports.loans') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Status
                        </label>
                        <select name="status" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                            <option value="">All Statuses</option>
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            From Date
                        </label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" 
                            class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            To Date
                        </label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" 
                            class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl font-medium hover:from-indigo-700 hover:to-indigo-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('reports.loans') }}" class="px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-700 font-medium hover:bg-slate-50 transition-colors">
                            Clear
                        </a>
                    </div>
                </form>
            </x-card>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <x-stat-card 
                    title="Total Loans" 
                    :value="number_format($summary['total_count'])" 
                    color="indigo">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card 
                    title="Total Amount" 
                    :value="'₱' . number_format($summary['total_amount'], 2)" 
                    color="blue">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card 
                    title="Outstanding Balance" 
                    :value="'₱' . number_format($summary['total_outstanding'], 2)" 
                    color="amber">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                        </svg>
                    </x-slot>
                </x-stat-card>
            </div>

            {{-- Loans Table --}}
            <x-card title="Loan Records">
                <div class="overflow-x-auto -mx-6 px-6">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ID</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Member</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Amount</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Term</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Interest</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Outstanding</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Applied</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($loans as $loan)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-indigo-600">#{{ $loan->id }}</span>
                                    </td>
                                    <td class="py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-200 text-slate-600 text-xs font-semibold">
                                                {{ strtoupper(substr($loan->user->name, 0, 1)) }}
                                            </span>
                                            <div>
                                                <div class="text-sm font-medium text-slate-900">{{ $loan->user->name }}</div>
                                                <div class="text-xs text-slate-500">{{ $loan->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 whitespace-nowrap text-sm font-semibold text-slate-900">
                                        ₱{{ number_format($loan->amount, 2) }}
                                    </td>
                                    <td class="py-4 whitespace-nowrap text-sm text-slate-600">
                                        {{ $loan->term_months }} months
                                    </td>
                                    <td class="py-4 whitespace-nowrap text-sm text-slate-600">
                                        {{ $loan->interest_rate }}%
                                    </td>
                                    <td class="py-4 whitespace-nowrap text-sm font-medium {{ $loan->outstanding_balance > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                                        ₱{{ number_format($loan->outstanding_balance, 2) }}
                                    </td>
                                    <td class="py-4 whitespace-nowrap">
                                        <x-status-badge :status="$loan->status" size="sm" />
                                    </td>
                                    <td class="py-4 whitespace-nowrap text-sm text-slate-500">
                                        {{ $loan->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="py-4 whitespace-nowrap">
                                        <a href="{{ route('loans.show', $loan) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg text-sm font-medium transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 text-center">
                                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 mb-3">
                                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-slate-500">No loans found matching the criteria.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-6 pt-4 border-t border-slate-200">
                    {{ $loans->appends(request()->query())->links() }}
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
