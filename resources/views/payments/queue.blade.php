<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">
                    Pending Payment Verification
                </h2>
                <p class="mt-1 text-sm text-slate-500">Review and verify advance payments submitted by members</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    {{ $payments->total() }} pending
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($payments->isEmpty())
                <x-card>
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 mb-4">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900">All caught up!</h3>
                        <p class="mt-1 text-sm text-slate-500">No pending payments awaiting verification.</p>
                    </div>
                </x-card>
            @else
                <div class="space-y-4">
                    @foreach ($payments as $payment)
                        <x-card hover>
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                                {{-- Payment Info --}}
                                <div class="flex-1 space-y-4">
                                    {{-- Header --}}
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <div class="flex items-center gap-3 mb-2">
                                                <h3 class="text-lg font-semibold text-slate-900">
                                                    ₱{{ number_format($payment->amount, 2) }}
                                                </h3>
                                                @if($payment->is_advance)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                                        </svg>
                                                        Advance Payment
                                                    </span>
                                                @endif
                                                <x-status-badge :status="$payment->status" size="sm" />
                                            </div>
                                            <div class="flex items-center gap-2 text-sm text-slate-500">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                Submitted {{ $payment->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Member & Loan Info --}}
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                                                <span class="text-white text-sm font-semibold">
                                                    {{ strtoupper(substr($payment->user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500">Member</p>
                                                <p class="font-medium text-slate-900">{{ $payment->user->name }}</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500">Loan</p>
                                                <a href="{{ route('loans.show', $payment->loan) }}" class="font-medium text-indigo-600 hover:text-indigo-800">
                                                    #{{ $payment->loan_id }}
                                                </a>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500">Payment Date</p>
                                                <p class="font-medium text-slate-900">{{ $payment->payment_date->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Payment Details --}}
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-slate-200">
                                        <div>
                                            <p class="text-xs text-slate-500">Amount</p>
                                            <p class="font-semibold text-slate-900">₱{{ number_format($payment->amount, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500">Principal</p>
                                            <p class="font-semibold text-slate-900">₱{{ number_format($payment->principal_amount, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500">Interest</p>
                                            <p class="font-semibold text-slate-900">₱{{ number_format($payment->interest_amount, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500">Method</p>
                                            <p class="font-semibold text-slate-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                                        </div>
                                    </div>

                                    {{-- Notes --}}
                                    @if ($payment->notes)
                                        <div class="pt-4 border-t border-slate-200">
                                            <p class="text-xs text-slate-500 mb-1">Member Notes:</p>
                                            <p class="text-sm text-slate-700 bg-slate-50 rounded-lg p-3">{{ $payment->notes }}</p>
                                        </div>
                                    @endif

                                    {{-- Loan Context --}}
                                    <div class="pt-4 border-t border-slate-200">
                                        <div class="flex items-center justify-between text-sm">
                                            <div>
                                                <span class="text-slate-500">Loan Balance:</span>
                                                <span class="font-semibold text-slate-900 ml-2">₱{{ number_format($payment->loan->outstanding_balance, 2) }}</span>
                                            </div>
                                            <a href="{{ route('loans.payments.index', $payment->loan) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                                View All Payments →
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="lg:w-64 flex-shrink-0">
                                    <div class="flex flex-col gap-3">
                                        {{-- View Details --}}
                                        <a href="{{ route('payments.show', $payment) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-sm font-medium hover:bg-slate-200 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View Details
                                        </a>

                                        {{-- Confirm --}}
                                        <form method="POST" action="{{ route('payments.status', $payment) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-xl text-sm font-semibold hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Confirm Payment
                                            </button>
                                        </form>

                                        {{-- Reject (expandable) --}}
                                        <div x-data="{ showReject: false }">
                                            <button type="button" @click="showReject = !showReject" class="w-full text-sm text-slate-500 hover:text-slate-700 flex items-center justify-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="showReject ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'"/>
                                                </svg>
                                                <span x-text="showReject ? 'Hide rejection form' : 'Reject payment'"></span>
                                            </button>

                                            <form method="POST" action="{{ route('payments.status', $payment) }}" x-show="showReject" x-transition class="mt-3 space-y-3" style="display: none;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <div>
                                                    <label for="notes_{{ $payment->id }}" class="block text-xs font-medium text-slate-700 mb-1">Rejection Reason</label>
                                                    <textarea id="notes_{{ $payment->id }}" name="notes" rows="3" required
                                                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:border-red-500 focus:ring-red-500"
                                                        placeholder="Explain why this payment is being rejected..."></textarea>
                                                </div>
                                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg text-sm font-medium hover:from-red-700 hover:to-red-800 transition-colors" onclick="return confirm('Are you sure you want to reject this payment?')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Reject Payment
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-card>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($payments->hasPages())
                    <div class="mt-6">
                        {{ $payments->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
