<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('loans.show', $loan) }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">
                        Payments for Loan #{{ $loan->id }}
                    </h2>
                    <p class="mt-0.5 text-sm text-slate-500">{{ ucfirst($loan->loan_type) }} loan • {{ $loan->term_months }} months term</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if ($canRecord)
                    <a href="{{ route('loans.payments.create', $loan) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl text-sm font-medium hover:from-indigo-700 hover:to-indigo-800 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Record Payment
                    </a>
                @endif
                <a href="{{ route('loans.schedule', $loan) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-700 text-white rounded-xl text-sm font-medium hover:bg-slate-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    View Schedule
                </a>
                <a href="{{ route('loans.show', $loan) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    Back to Loan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <x-stat-card 
                    title="Loan Amount" 
                    :value="'₱' . number_format($loan->approved_amount ?? $loan->amount, 2)" 
                    color="indigo">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card 
                    title="Total Paid" 
                    :value="'₱' . number_format($totalPaid, 2)" 
                    color="emerald">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card 
                    title="Remaining Balance" 
                    :value="'₱' . number_format($remainingBalance, 2)" 
                    :color="$remainingBalance > 0 ? 'amber' : 'emerald'">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card 
                    title="Interest Paid" 
                    :value="'₱' . number_format($interestPaid, 2)" 
                    color="blue">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </x-slot>
                </x-stat-card>
            </div>

            {{-- Progress Bar --}}
            @if($loan->approved_amount ?? $loan->amount > 0)
                @php
                    $progressPercent = min(100, ($totalPaid / ($loan->approved_amount ?? $loan->amount)) * 100);
                @endphp
                <div class="mb-8 p-4 bg-white rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-slate-700">Payment Progress</span>
                        <span class="text-sm font-semibold text-slate-900">{{ number_format($progressPercent, 1) }}%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-3 rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                    </div>
                </div>
            @endif

            {{-- Payment History --}}
            <x-card title="Payment History">
                @if ($payments->isEmpty())
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900">No payments yet</h3>
                        <p class="mt-1 text-sm text-slate-500">Payments will appear here once recorded.</p>
                        @if ($canRecord)
                            <a href="{{ route('loans.payments.create', $loan) }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Record First Payment
                            </a>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto -mx-6 px-6">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-slate-200">
                                    <th scope="col" class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th scope="col" class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th scope="col" class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Principal
                                    </th>
                                    <th scope="col" class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Interest
                                    </th>
                                    <th scope="col" class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Method
                                    </th>
                                    <th scope="col" class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Recorded By
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($payments as $payment)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-lg {{ $payment->status === 'confirmed' ? 'bg-emerald-100' : ($payment->status === 'rejected' ? 'bg-red-100' : 'bg-amber-100') }} flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-4 h-4 {{ $payment->status === 'confirmed' ? 'text-emerald-600' : ($payment->status === 'rejected' ? 'text-red-600' : 'text-amber-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                                <span class="text-sm font-medium text-slate-900">{{ $payment->payment_date->format('M d, Y') }}</span>
                                            </div>
                                        </td>
                                        <td class="py-4 whitespace-nowrap">
                                            <span class="text-sm font-bold text-slate-900">₱{{ number_format($payment->amount, 2) }}</span>
                                        </td>
                                        <td class="py-4 whitespace-nowrap text-sm text-slate-600">
                                            ₱{{ number_format($payment->principal_amount, 2) }}
                                        </td>
                                        <td class="py-4 whitespace-nowrap text-sm text-slate-600">
                                            ₱{{ number_format($payment->interest_amount, 2) }}
                                        </td>
                                        <td class="py-4 whitespace-nowrap">
                                            <div class="text-sm text-slate-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</div>
                                            @if ($payment->reference_number)
                                                <div class="text-xs text-slate-400">Ref: {{ $payment->reference_number }}</div>
                                            @endif
                                        </td>
                                        <td class="py-4 whitespace-nowrap">
                                            <x-status-badge :status="$payment->status" size="sm" />
                                        </td>
                                        <td class="py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-200 text-slate-600 text-xs font-medium">
                                                    {{ strtoupper(substr($payment->recorder?->name ?? 'S', 0, 1)) }}
                                                </span>
                                                <span class="text-sm text-slate-600">{{ $payment->recorder?->name ?? 'System' }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
