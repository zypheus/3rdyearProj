<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4">
            {{-- Top row: Back button and title --}}
            <div class="flex items-start sm:items-center gap-3">
                <a href="{{ route('loans.show', $loan) }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div class="min-w-0">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 truncate">
                        Payments for Loan #{{ $loan->id }}
                    </h2>
                    <p class="mt-0.5 text-xs sm:text-sm text-slate-500">{{ ucfirst($loan->loan_type) }} loan • {{ $loan->term_months }} months term</p>
                </div>
            </div>
            
            {{-- Action buttons - stacked on mobile, row on larger screens --}}
            <div class="flex flex-col xs:flex-row flex-wrap items-stretch xs:items-center gap-2 sm:gap-3">
                @if ($canRecord)
                    <a href="{{ route('loans.payments.create', $loan) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 sm:px-5 sm:py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl text-sm sm:text-base font-bold hover:from-indigo-700 hover:to-indigo-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Record Payment</span>
                    </a>
                @endif
                @if (auth()->user()->isMember() && $loan->user_id === auth()->id() && $loan->isActive())
                    <a href="{{ route('loans.payments.create', $loan) }}?advance=1" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 sm:px-5 sm:py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-xl text-sm sm:text-base font-bold hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        <span>Make Advance Payment</span>
                    </a>
                @endif
                <div class="flex gap-2 sm:gap-3">
                    <a href="{{ route('loans.schedule', $loan) }}" class="flex-1 xs:flex-none inline-flex items-center justify-center gap-2 px-3 py-2 sm:px-4 bg-slate-700 text-white rounded-xl text-xs sm:text-sm font-medium hover:bg-slate-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="hidden xs:inline">View Schedule</span>
                        <span class="xs:hidden">Schedule</span>
                    </a>
                    <a href="{{ route('loans.show', $loan) }}" class="flex-1 xs:flex-none inline-flex items-center justify-center gap-2 px-3 py-2 sm:px-4 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                        <span class="hidden xs:inline">Back to Loan</span>
                        <span class="xs:hidden">Back</span>
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
                <x-stat-card 
                    title="Loan Amount" 
                    :value="'₱' . number_format($loan->approved_amount ?? $loan->amount, 2)" 
                    color="indigo">
                    <x-slot name="icon">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card 
                    title="Total Paid" 
                    :value="'₱' . number_format($totalPaid, 2)" 
                    color="emerald">
                    <x-slot name="icon">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card 
                    title="Remaining Balance" 
                    :value="'₱' . number_format($remainingBalance, 2)" 
                    :color="$remainingBalance > 0 ? 'amber' : 'emerald'">
                    <x-slot name="icon">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card 
                    title="Interest Paid" 
                    :value="'₱' . number_format($interestPaid, 2)" 
                    color="blue">
                    <x-slot name="icon">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="mb-6 sm:mb-8 p-3 sm:p-4 bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs sm:text-sm font-medium text-slate-700">Payment Progress</span>
                        <span class="text-xs sm:text-sm font-semibold text-slate-900">{{ number_format($progressPercent, 1) }}%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2 sm:h-3">
                        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-2 sm:h-3 rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                    </div>
                </div>
            @endif

            {{-- Payment History --}}
            <x-card title="Payment History">
                @if ($payments->isEmpty())
                    <div class="text-center py-8 sm:py-12">
                        <div class="inline-flex items-center justify-center w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-slate-100 mb-3 sm:mb-4">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-semibold text-slate-900">No payments yet</h3>
                        <p class="mt-1 text-xs sm:text-sm text-slate-500">Payments will appear here once recorded.</p>
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
                    {{-- Mobile Card View --}}
                    <div class="block md:hidden space-y-3">
                        @foreach ($payments as $payment)
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg {{ $payment->status === 'confirmed' ? 'bg-emerald-100' : ($payment->status === 'rejected' ? 'bg-red-100' : 'bg-amber-100') }} flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 {{ $payment->status === 'confirmed' ? 'text-emerald-600' : ($payment->status === 'rejected' ? 'text-red-600' : 'text-amber-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-slate-900">{{ $payment->payment_date->format('M d, Y') }}</span>
                                    </div>
                                    <x-status-badge :status="$payment->status" size="sm" />
                                </div>
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <span class="text-slate-500 text-xs">Amount</span>
                                        <p class="font-bold text-slate-900">₱{{ number_format($payment->amount, 2) }}</p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 text-xs">Method</span>
                                        <p class="text-slate-700">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 text-xs">Principal</span>
                                        <p class="text-slate-600">₱{{ number_format($payment->principal_amount, 2) }}</p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 text-xs">Interest</span>
                                        <p class="text-slate-600">₱{{ number_format($payment->interest_amount, 2) }}</p>
                                    </div>
                                </div>
                                @if ($payment->reference_number)
                                    <div class="mt-2 pt-2 border-t border-slate-200">
                                        <span class="text-xs text-slate-400">Ref: {{ $payment->reference_number }}</span>
                                    </div>
                                @endif
                                <div class="mt-2 pt-2 border-t border-slate-200 flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-200 text-slate-600 text-xs font-medium">
                                        {{ strtoupper(substr($payment->recorder?->name ?? 'S', 0, 1)) }}
                                    </span>
                                    <span class="text-xs text-slate-500">{{ $payment->recorder?->name ?? 'System' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Desktop Table View --}}
                    <div class="hidden md:block overflow-x-auto -mx-6 px-6">
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
