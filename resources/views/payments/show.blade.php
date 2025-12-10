<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">
                    {{ __('Payment Details') }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">Transaction for Loan #{{ $payment->loan_id }}</p>
            </div>
            <a href="{{ route('loans.payments.index', $payment->loan) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-300 rounded-xl font-medium text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Payments
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-card>
                {{-- Payment Header --}}
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6 pb-6 border-b border-slate-200">
                    <div class="flex items-center gap-4">
                        <div class="p-4 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-2xl">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-3xl font-bold text-slate-900">₱{{ number_format($payment->amount, 2) }}</h3>
                            <p class="text-slate-500">Payment for Loan #{{ $payment->loan_id }}</p>
                        </div>
                    </div>
                    <x-status-badge :status="$payment->status" size="lg" />
                </div>

                @if(!empty($simulationMode) && $payment->isPending() && auth()->id() === $payment->user_id)
                    <div class="mb-6 p-4 bg-indigo-50 border border-indigo-200 rounded-xl text-sm text-indigo-700">
                        Simulation Mode: This payment is pending. You may confirm or reject it below. No real transaction occurred.
                    </div>
                @endif

                {{-- Payment Details Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Left Column --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500">Payment Date</span>
                                <p class="text-sm font-semibold text-slate-900">{{ $payment->payment_date->format('F d, Y') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500">Payment Method</span>
                                <p class="text-sm font-semibold text-slate-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                            </div>
                        </div>

                        @if ($payment->reference_number)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500">Reference Number</span>
                                <p class="text-sm font-semibold text-slate-900 font-mono">{{ $payment->reference_number }}</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Right Column --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500">Principal Paid</span>
                                <p class="text-sm font-semibold text-slate-900">₱{{ number_format($payment->principal_amount, 2) }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500">Interest Paid</span>
                                <p class="text-sm font-semibold text-slate-900">₱{{ number_format($payment->interest_amount, 2) }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500">Recorded By</span>
                                <p class="text-sm font-semibold text-slate-900">{{ $payment->recorder?->name ?? 'System' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($payment->notes)
                    <div class="mt-6 pt-6 border-t border-slate-200">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500">Notes</span>
                                <p class="text-sm text-slate-900 mt-1">{{ $payment->notes }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Timestamps --}}
                <div class="mt-6 pt-6 border-t border-slate-200 flex flex-wrap gap-4 text-xs text-slate-500">
                    <span>Created: {{ $payment->created_at->format('M d, Y H:i') }}</span>
                    <span>•</span>
                    <span>Updated: {{ $payment->updated_at->format('M d, Y H:i') }}</span>
                </div>
            </x-card>

            {{-- Status / Simulation Actions --}}
            @if ($payment->isPending())
                @if(!empty($simulationMode) && auth()->id() === $payment->user_id)
                    <x-card x-data="{processing:false, delay: {{ (int)($simulationDelayMs ?? 0) }}, start(e){processing=true; const form=e.target.closest('form'); setTimeout(()=>form.submit(), delay);} }">
                        <h4 class="text-lg font-semibold text-slate-900 flex items-center gap-2 mb-4">
                            <div class="p-2 bg-indigo-100 rounded-lg">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            Simulation Actions
                        </h4>
                        <p class="text-sm text-indigo-700 mb-4">Confirm or reject this simulated payment. A short delay ({{ (int)($simulationDelayMs ?? 0) }} ms) is applied to mimic processing.</p>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <form method="POST" action="{{ route('payments.status', $payment) }}" class="flex-1" @submit.prevent="start($event)">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="confirmed">
                                <button :disabled="processing" type="submit" class="w-full inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 border border-transparent rounded-xl font-semibold text-sm text-white shadow-sm hover:from-emerald-700 hover:to-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Confirm Simulation
                                </button>
                            </form>
                            <form method="POST" action="{{ route('payments.status', $payment) }}" class="flex-1" @submit.prevent="start($event)">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button :disabled="processing" type="submit" class="w-full inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-rose-600 to-rose-700 border border-transparent rounded-xl font-semibold text-sm text-white shadow-sm hover:from-rose-700 hover:to-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50" onclick="return confirm('Reject this simulated payment?')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Reject Simulation
                                </button>
                            </form>
                        </div>
                    </x-card>
                @elseif(auth()->user()->isAdminOrOfficer())
                    <x-card>
                        <h4 class="text-lg font-semibold text-slate-900 flex items-center gap-2 mb-4">
                            <div class="p-2 bg-amber-100 rounded-lg">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            Update Payment Status
                        </h4>
                        <p class="text-sm text-slate-500 mb-4">Review and update the status of this payment.</p>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <form method="POST" action="{{ route('payments.status', $payment) }}" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 border border-transparent rounded-xl font-semibold text-sm text-white shadow-sm hover:from-emerald-700 hover:to-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Confirm Payment
                                </button>
                            </form>
                            <form method="POST" action="{{ route('payments.status', $payment) }}" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-rose-600 to-rose-700 border border-transparent rounded-xl font-semibold text-sm text-white shadow-sm hover:from-rose-700 hover:to-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition-all duration-200" onclick="return confirm('Reject this payment?')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Reject Payment
                                </button>
                            </form>
                        </div>
                    </x-card>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
