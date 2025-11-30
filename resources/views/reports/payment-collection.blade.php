<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">
                    Payment Collection Report
                </h2>
                <p class="mt-1 text-sm text-slate-500">Track payment collections and revenue</p>
            </div>
            <a href="{{ route('reports.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Reports
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Filters --}}
            <x-card class="mb-6">
                <form method="GET" action="{{ route('reports.payments') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Status
                        </label>
                        <select name="status" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                            <option value="">All Confirmed</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
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
                        <a href="{{ route('reports.payments') }}" class="px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-700 font-medium hover:bg-slate-50 transition-colors">
                            Clear
                        </a>
                    </div>
                </form>
            </x-card>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <x-stat-card 
                    title="Total Collected" 
                    :value="'₱' . number_format($summary['total_collected'], 2)" 
                    color="emerald">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card 
                    title="Principal" 
                    :value="'₱' . number_format($summary['principal_collected'], 2)" 
                    color="indigo">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card 
                    title="Interest" 
                    :value="'₱' . number_format($summary['interest_collected'], 2)" 
                    color="blue">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card 
                    title="Pending" 
                    :value="'₱' . number_format($summary['total_pending'], 2)" 
                    color="amber">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </x-slot>
                </x-stat-card>
            </div>

            {{-- Payments Table --}}
            <x-card title="Payment Records">
                <div class="overflow-x-auto -mx-6 px-6">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Loan</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Member</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Amount</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Principal</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Interest</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Method</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Recorded By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($payments as $payment)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                        {{ $payment->payment_date->format('M d, Y') }}
                                    </td>
                                    <td class="py-4 whitespace-nowrap">
                                        <a href="{{ route('loans.show', $payment->loan) }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">
                                            #{{ $payment->loan_id }}
                                        </a>
                                    </td>
                                    <td class="py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-200 text-slate-600 text-xs font-semibold">
                                                {{ strtoupper(substr($payment->loan->user->name, 0, 1)) }}
                                            </span>
                                            <span class="text-sm text-slate-900">{{ $payment->loan->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 whitespace-nowrap text-sm font-bold text-slate-900">
                                        ₱{{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="py-4 whitespace-nowrap text-sm text-slate-600">
                                        ₱{{ number_format($payment->principal_amount, 2) }}
                                    </td>
                                    <td class="py-4 whitespace-nowrap text-sm text-slate-600">
                                        ₱{{ number_format($payment->interest_amount, 2) }}
                                    </td>
                                    <td class="py-4 whitespace-nowrap text-sm text-slate-600">
                                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                    </td>
                                    <td class="py-4 whitespace-nowrap">
                                        <x-status-badge :status="$payment->status" size="sm" />
                                    </td>
                                    <td class="py-4 whitespace-nowrap text-sm text-slate-500">
                                        {{ $payment->recorder?->name ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 text-center">
                                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 mb-3">
                                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-slate-500">No payments found matching the criteria.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-6 pt-4 border-t border-slate-200">
                    {{ $payments->appends(request()->query())->links() }}
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
