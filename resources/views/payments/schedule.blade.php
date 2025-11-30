<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">
                    {{ __('Payment Schedule') }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">Amortization for Loan #{{ $loan->id }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('loans.payments.index', $loan) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 border border-transparent rounded-xl font-semibold text-sm text-white shadow-sm hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Payment History
                </a>
                <a href="{{ route('loans.show', $loan) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-300 rounded-xl font-medium text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Loan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            {{-- Loan Summary --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <x-stat-card title="Principal Amount" :value="'₱' . number_format($loan->approved_amount ?? $loan->amount, 2)" color="indigo">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card title="Loan Term" :value="$loan->term_months . ' months'" color="blue">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card title="Interest Rate" :value="$loan->interest_rate . '% p.a.'" color="amber">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <x-stat-card title="Monthly Payment" :value="'₱' . number_format($schedule[0]['payment'] ?? 0, 2)" color="emerald">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </x-slot>
                </x-stat-card>
            </div>

            {{-- Payment Schedule Table --}}
            <x-card title="Amortization Schedule" subtitle="Monthly payment breakdown">
                <x-slot name="actions">
                    <span class="text-sm text-slate-500">{{ count($schedule) }} payments</span>
                </x-slot>

                <div class="overflow-x-auto -mx-6 -mb-6">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Month
                                </th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Payment
                                </th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Principal
                                </th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Interest
                                </th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Balance
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @php
                                $totalPayment = 0;
                                $totalPrincipal = 0;
                                $totalInterest = 0;
                            @endphp
                            @foreach ($schedule as $row)
                                @php
                                    $totalPayment += $row['payment'];
                                    $totalPrincipal += $row['principal'];
                                    $totalInterest += $row['interest'];
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 text-sm font-semibold">
                                                {{ $row['month'] }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900 text-right">
                                        ₱{{ number_format($row['payment'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 text-right">
                                        ₱{{ number_format($row['principal'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 text-right">
                                        ₱{{ number_format($row['interest'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-right {{ $row['balance'] <= 0 ? 'text-emerald-600' : 'text-slate-900' }}">
                                        ₱{{ number_format($row['balance'], 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gradient-to-r from-slate-100 to-slate-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">
                                    Total
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600 text-right">
                                    ₱{{ number_format($totalPayment, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900 text-right">
                                    ₱{{ number_format($totalPrincipal, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-amber-600 text-right">
                                    ₱{{ number_format($totalInterest, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900 text-right">
                                    —
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-card>

            {{-- Note --}}
            <div class="flex items-start gap-3 p-4 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100">
                <div class="p-2 bg-blue-100 rounded-lg flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-blue-900">About This Schedule</h4>
                    <p class="text-sm text-blue-800 mt-1">
                        This schedule is calculated based on the approved loan amount using standard amortization. 
                        Actual payment amounts may vary slightly due to rounding or any adjustments made during the loan term.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
