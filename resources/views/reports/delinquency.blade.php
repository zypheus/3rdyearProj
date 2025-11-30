<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">
                    Delinquency Report
                </h2>
                <p class="mt-1 text-sm text-slate-500">Track overdue loans and payments</p>
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
            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <x-stat-card 
                    title="Delinquent Loans" 
                    :value="$summary['total_delinquent_loans']" 
                    color="rose">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card 
                    title="Overdue Payments" 
                    :value="$summary['total_overdue_payments']" 
                    color="amber">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card 
                    title="Total Overdue Amount" 
                    :value="'₱' . number_format($summary['total_overdue_amount'], 2)" 
                    color="rose">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </x-slot>
                </x-stat-card>
            </div>

            {{-- Delinquent Loans Summary --}}
            <x-card title="Delinquent Loans" class="mb-6">
                <div class="overflow-x-auto -mx-6 px-6">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Loan</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Member</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Outstanding</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Overdue</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Overdue Amount</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Days</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($delinquentLoans as $loan)
                                @php
                                    $daysOverdue = now()->diffInDays(\Carbon\Carbon::parse($loan->oldest_overdue));
                                @endphp
                                <tr class="bg-red-50/50 hover:bg-red-50 transition-colors">
                                    <td class="py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-red-600">#{{ $loan->id }}</span>
                                    </td>
                                    <td class="py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-200 text-red-700 text-xs font-semibold">
                                                {{ strtoupper(substr($loan->user->name, 0, 1)) }}
                                            </span>
                                            <div>
                                                <div class="text-sm font-medium text-slate-900">{{ $loan->user->name }}</div>
                                                <div class="text-xs text-slate-500">{{ $loan->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                        ₱{{ number_format($loan->outstanding_balance, 2) }}
                                    </td>
                                    <td class="py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $loan->overdue_count }} payment(s)
                                        </span>
                                    </td>
                                    <td class="py-4 whitespace-nowrap text-sm font-bold text-red-600">
                                        ₱{{ number_format($loan->overdue_amount, 2) }}
                                    </td>
                                    <td class="py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $daysOverdue > 30 ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $daysOverdue }} days
                                        </span>
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
                                    <td colspan="7" class="py-12 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 mb-4">
                                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-emerald-700">🎉 No delinquent loans!</h3>
                                        <p class="text-sm text-slate-500 mt-1">All payments are up to date.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            {{-- Overdue Payments Detail --}}
            <x-card title="Overdue Payments Detail">
                <div class="overflow-x-auto -mx-6 px-6">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Due Date</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Loan</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Member</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Amount Due</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Days Overdue</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($overduePayments as $payment)
                                @php
                                    $daysOverdue = now()->diffInDays($payment->due_date);
                                @endphp
                                <tr class="{{ $daysOverdue > 30 ? 'bg-red-50/50' : 'bg-amber-50/50' }} hover:bg-slate-50 transition-colors">
                                    <td class="py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-red-600">{{ $payment->due_date->format('M d, Y') }}</span>
                                    </td>
                                    <td class="py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-indigo-600">#{{ $payment->loan_id }}</span>
                                    </td>
                                    <td class="py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-200 text-slate-600 text-xs font-semibold">
                                                {{ strtoupper(substr($payment->loan->user->name, 0, 1)) }}
                                            </span>
                                            <div>
                                                <div class="text-sm font-medium text-slate-900">{{ $payment->loan->user->name }}</div>
                                                <div class="text-xs text-slate-500">{{ $payment->loan->user->phone ?? 'No phone' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 whitespace-nowrap text-sm font-bold text-slate-900">
                                        ₱{{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $daysOverdue > 30 ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $daysOverdue }} days
                                        </span>
                                    </td>
                                    <td class="py-4 whitespace-nowrap">
                                        <a href="{{ route('loans.payments.create', $payment->loan) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Record Payment
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 mb-4">
                                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-emerald-700">🎉 No overdue payments!</h3>
                                        <p class="text-sm text-slate-500 mt-1">All members are paying on time.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($overduePayments->hasPages())
                    <div class="mt-6 pt-4 border-t border-slate-200">
                        {{ $overduePayments->links() }}
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
