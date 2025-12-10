<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4">
            {{-- Top row: Back button and title --}}
            <div class="flex items-start sm:items-center gap-3">
                <a href="{{ route('loans.index') }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div class="min-w-0">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 truncate">
                        Loan #{{ $loan->id }}
                    </h2>
                    <p class="mt-0.5 text-xs sm:text-sm text-slate-500">{{ ucfirst($loan->loan_type) }} loan application</p>
                </div>
            </div>
            
            {{-- Action buttons --}}
            <div class="flex flex-wrap items-center gap-2">
                @if ($loan->isPending() && $loan->user_id === auth()->id())
                    <a href="{{ route('loans.edit', $loan) }}" class="inline-flex items-center gap-2 px-3 py-2 sm:px-4 bg-amber-500 text-white rounded-xl text-xs sm:text-sm font-medium hover:bg-amber-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                @endif
                <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-2 px-3 py-2 sm:px-4 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    <span class="hidden xs:inline">Back to Loans</span>
                    <span class="xs:hidden">Back</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                {{-- Main Loan Info --}}
                <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                    <x-card>
                        {{-- Status Header --}}
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 sm:gap-4 mb-4 sm:mb-6">
                            <div>
                                <h3 class="text-lg sm:text-xl font-bold text-slate-900">
                                    {{ ucfirst($loan->loan_type) }} Loan
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-500 mt-1">Applied on {{ $loan->created_at->format('F d, Y') }}</p>
                            </div>
                            <x-status-badge :status="$loan->status" size="lg" />
                        </div>

                        {{-- Loan Details Grid --}}
                        <div class="grid grid-cols-2 gap-3 sm:gap-6">
                            <div class="p-3 sm:p-4 bg-slate-50 rounded-xl">
                                <dt class="text-xs sm:text-sm font-medium text-slate-500">Requested Amount</dt>
                                <dd class="mt-1 text-lg sm:text-2xl font-bold text-slate-900">₱{{ number_format($loan->amount, 2) }}</dd>
                            </div>
                            @if ($loan->approved_amount)
                                <div class="p-3 sm:p-4 bg-emerald-50 rounded-xl">
                                    <dt class="text-xs sm:text-sm font-medium text-emerald-600">Approved Amount</dt>
                                    <dd class="mt-1 text-lg sm:text-2xl font-bold text-emerald-700">₱{{ number_format($loan->approved_amount, 2) }}</dd>
                                </div>
                            @endif
                            <div class="p-3 sm:p-4 bg-slate-50 rounded-xl">
                                <dt class="text-xs sm:text-sm font-medium text-slate-500">Term</dt>
                                <dd class="mt-1 text-base sm:text-lg font-semibold text-slate-900">{{ $loan->term_months }} months</dd>
                            </div>
                            <div class="p-3 sm:p-4 bg-slate-50 rounded-xl">
                                <dt class="text-xs sm:text-sm font-medium text-slate-500">Interest Rate</dt>
                                <dd class="mt-1 text-base sm:text-lg font-semibold text-slate-900">{{ $loan->interest_rate }}% <span class="text-xs sm:text-sm font-normal text-slate-500">per annum</span></dd>
                            </div>
                            @if ($loan->disbursement_date)
                                <div class="p-3 sm:p-4 bg-indigo-50 rounded-xl col-span-2">
                                    <dt class="text-xs sm:text-sm font-medium text-indigo-600">Disbursement Date</dt>
                                    <dd class="mt-1 text-base sm:text-lg font-semibold text-indigo-900">{{ $loan->disbursement_date->format('F d, Y') }}</dd>
                                </div>
                            @endif
                        </div>

                        {{-- Calamity Loan Info Box --}}
                        @if ($loan->isCalamityLoan() && $calamitySummary)
                            <div class="mt-4 sm:mt-6 p-4 bg-gradient-to-r from-red-50 to-orange-50 border border-red-200 rounded-xl">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-bold text-red-800">Calamity Loan Details</h4>
                                        <div class="mt-3 grid grid-cols-2 gap-3 text-xs">
                                            @if ($loan->eligible_amount)
                                                <div>
                                                    <span class="text-red-600 font-medium">Eligible Amount:</span>
                                                    <span class="text-red-800 font-bold">₱{{ number_format($loan->eligible_amount, 2) }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-red-600 font-medium">Loanable ({{ $calamitySummary['loanable_percentage'] }}%):</span>
                                                    <span class="text-red-800 font-bold">₱{{ number_format($calamitySummary['loanable_amount'], 2) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <span class="text-red-600 font-medium">Grace Period:</span>
                                                <span class="text-red-800 font-bold">{{ $calamitySummary['grace_period_months'] }} months</span>
                                            </div>
                                            @if ($calamitySummary['grace_period_end'])
                                                <div>
                                                    <span class="text-red-600 font-medium">Grace Ends:</span>
                                                    <span class="text-red-800 font-bold">{{ $calamitySummary['grace_period_end'] }}</span>
                                                </div>
                                            @endif
                                            @if ($calamitySummary['first_payment_due'])
                                                <div class="col-span-2">
                                                    <span class="text-red-600 font-medium">First Payment Due:</span>
                                                    <span class="text-red-800 font-bold">{{ $calamitySummary['first_payment_due'] }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <span class="text-red-600 font-medium">Monthly Payment:</span>
                                                <span class="text-red-800 font-bold">₱{{ number_format($calamitySummary['monthly_payment'], 2) }}</span>
                                            </div>
                                            <div>
                                                <span class="text-red-600 font-medium">Penalty Rate:</span>
                                                <span class="text-red-800 font-bold">{{ $loan->penalty_rate }}%/day</span>
                                            </div>
                                        </div>
                                        @if ($calamitySummary['is_in_grace_period'])
                                            <div class="mt-3 px-3 py-2 bg-amber-100 border border-amber-300 rounded-lg">
                                                <p class="text-xs font-medium text-amber-800">
                                                    <svg class="inline w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                    </svg>
                                                    This loan is currently in grace period. No payments required until {{ $calamitySummary['first_payment_due'] }}.
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Purpose --}}
                        @if ($loan->purpose)
                            <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-slate-200">
                                <dt class="text-xs sm:text-sm font-medium text-slate-500 mb-2">Purpose</dt>
                                <dd class="text-sm sm:text-base text-slate-700 bg-slate-50 p-3 sm:p-4 rounded-xl">{{ $loan->purpose }}</dd>
                            </div>
                        @endif

                        {{-- Rejection Reason --}}
                        @if ($loan->isRejected() && $loan->rejection_reason)
                            <div class="mt-4 sm:mt-6 p-3 sm:p-4 bg-red-50 border border-red-200 rounded-xl">
                                <div class="flex items-start gap-2 sm:gap-3">
                                    <div class="flex-shrink-0">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <dt class="text-xs sm:text-sm font-semibold text-red-800">Rejection Reason</dt>
                                        <dd class="mt-1 text-sm text-red-700">{{ $loan->rejection_reason }}</dd>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Reviewer Info --}}
                        @if ($loan->reviewer)
                            <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-slate-200 flex items-center gap-2 sm:gap-3">
                                <span class="inline-flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-slate-200 text-slate-600 text-xs font-semibold">
                                    {{ strtoupper(substr($loan->reviewer->name, 0, 1)) }}
                                </span>
                                <div>
                                    <dt class="text-xs font-medium text-slate-500">Reviewed by</dt>
                                    <dd class="text-xs sm:text-sm text-slate-900">
                                        {{ $loan->reviewer->name }}
                                        <span class="text-slate-400">•</span>
                                        <span class="text-slate-500">{{ $loan->reviewed_at->format('M d, Y') }}</span>
                                    </dd>
                                </div>
                            </div>
                        @endif
                    </x-card>

                    {{-- Documents Section --}}
                    <x-card title="Documents">
                        @if ($loan->documents->isEmpty())
                            <div class="text-center py-8">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 mb-3">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-slate-500">No documents uploaded yet</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach ($loan->documents as $document)
                                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-slate-900">{{ $document->filename }}</p>
                                                <p class="text-xs text-slate-500">{{ ucfirst(str_replace('_', ' ', $document->document_type)) }}</p>
                                            </div>
                                        </div>
                                        <x-status-badge :status="$document->is_verified ? 'verified' : 'unverified'" size="sm" />
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </x-card>

                    {{-- Payments Section --}}
                    @if ($loan->isActive() || $loan->isCompleted())
                        <x-card title="Payment History">
                            @if ($loan->payments->isEmpty())
                                <div class="text-center py-6 sm:py-8">
                                    <div class="inline-flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-slate-100 mb-2 sm:mb-3">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs sm:text-sm text-slate-500">No payments recorded yet</p>
                                </div>
                            @else
                                <div class="space-y-2 sm:space-y-3">
                                    @foreach ($loan->payments as $payment)
                                        <div class="flex items-center justify-between p-3 sm:p-4 bg-slate-50 rounded-xl">
                                            <div class="flex items-center gap-2 sm:gap-3">
                                                <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 rounded-lg {{ $payment->status === 'confirmed' ? 'bg-emerald-100' : ($payment->status === 'rejected' ? 'bg-red-100' : 'bg-amber-100') }} flex items-center justify-center">
                                                    @if($payment->status === 'confirmed')
                                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-sm sm:text-base font-semibold text-slate-900">₱{{ number_format($payment->amount, 2) }}</p>
                                                    <p class="text-xs text-slate-500">{{ $payment->payment_date->format('M d, Y') }} • {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                                                </div>
                                            </div>
                                            <x-status-badge :status="$payment->status" size="sm" />
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- View All Payments & Action Buttons --}}
                            <div class="mt-4 pt-4 border-t border-slate-200 space-y-2">
                                @if (auth()->user()->isMember() && $loan->user_id === auth()->id() && $loan->isActive())
                                    <a href="{{ route('loans.payments.create', $loan) }}?advance=1" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 sm:px-5 sm:py-3.5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-xl text-sm sm:text-base font-bold hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                        </svg>
                                        Make Advance Payment
                                    </a>
                                @endif
                                <a href="{{ route('loans.payments.index', $loan) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 sm:py-2.5 bg-white border-2 border-slate-300 text-slate-700 rounded-xl text-xs sm:text-sm font-semibold hover:bg-slate-50 hover:border-slate-400 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    View All Payments
                                </a>
                            </div>
                        </x-card>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="space-y-4 sm:space-y-6">
                    {{-- Applicant Info --}}
                    <x-card title="Applicant">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <span class="inline-flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-base sm:text-lg font-bold shadow-lg flex-shrink-0">
                                {{ strtoupper(substr($loan->user->name, 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-900 truncate">{{ $loan->user->name }}</p>
                                <p class="text-xs sm:text-sm text-slate-500 truncate">{{ $loan->user->email }}</p>
                            </div>
                        </div>
                        @if ($loan->user->phone)
                            <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-slate-200 flex items-center gap-2 text-xs sm:text-sm text-slate-600">
                                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <span class="truncate">{{ $loan->user->phone }}</span>
                            </div>
                        @endif
                    </x-card>

                    {{-- Actions Card (for Officers/Admins) --}}
                    @if ($canReview)
                        <x-card title="Review Actions">
                            @if ($loan->isPending())
                                <form method="POST" action="{{ route('loans.review', $loan) }}" class="mb-4">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Start Review
                                    </button>
                                </form>
                            @endif

                            @if ($loan->canBeReviewed())
                                {{-- Approve Form --}}
                                <form method="POST" action="{{ route('loans.approve', $loan) }}" class="mb-4 p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                                    @csrf
                                    @method('PATCH')
                                    <h4 class="text-sm font-semibold text-emerald-800 mb-3">Approve Loan</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="text-xs font-medium text-emerald-700">Approved Amount</label>
                                            <input type="number" name="approved_amount" value="{{ $loan->amount }}" min="1000" step="100" class="mt-1 w-full px-3 py-2 text-sm border border-emerald-300 rounded-lg focus:border-emerald-500 focus:ring-emerald-500">
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium text-emerald-700">Disbursement Date</label>
                                            <input type="date" name="disbursement_date" min="{{ date('Y-m-d') }}" class="mt-1 w-full px-3 py-2 text-sm border border-emerald-300 rounded-lg focus:border-emerald-500 focus:ring-emerald-500">
                                        </div>
                                    </div>
                                    <button type="submit" class="mt-3 w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-lg font-medium text-sm hover:bg-emerald-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Approve Loan
                                    </button>
                                </form>

                                {{-- Reject Form --}}
                                <form method="POST" action="{{ route('loans.reject', $loan) }}" class="p-3 sm:p-4 bg-red-50 rounded-xl border border-red-200" onsubmit="confirmReject(event)">
                                    @csrf
                                    @method('PATCH')
                                    <h4 class="text-xs sm:text-sm font-semibold text-red-800 mb-2 sm:mb-3">Reject Loan</h4>
                                    <div>
                                        <label class="text-xs font-medium text-red-700">Rejection Reason</label>
                                        <textarea name="rejection_reason" rows="2" class="mt-1 w-full px-3 py-2 text-sm border border-red-300 rounded-lg focus:border-red-500 focus:ring-red-500" placeholder="Enter reason..." required></textarea>
                                    </div>
                                    <button type="submit" class="mt-2 sm:mt-3 w-full inline-flex justify-center items-center gap-2 px-3 sm:px-4 py-2 sm:py-2.5 bg-red-600 text-white rounded-lg font-medium text-xs sm:text-sm hover:bg-red-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Reject Loan
                                    </button>
                                </form>
                            @endif
                        </x-card>
                    @endif

                    {{-- Schedule Proposal & Disbursement Actions --}}
                    @if (auth()->user()->isAdminOrOfficer() && $loan->isApproved())
                        {{-- Propose/Confirm Schedule --}}
                        <x-card title="Payment Schedule">
                            @if($loan->paymentSchedules()->exists())
                                <p class="text-sm text-emerald-700 mb-3">✓ Schedule confirmed with {{ $loan->paymentSchedules()->count() }} entries</p>
                                <a href="{{ route('loans.schedule', $loan) }}" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-sm font-medium hover:bg-slate-200 transition-colors">
                                    View Schedule
                                </a>
                            @else
                                <p class="text-sm text-slate-600 mb-4">Generate and confirm the payment schedule before disbursement.</p>
                                <a href="{{ route('loans.schedule.propose', $loan) }}" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Propose Schedule
                                </a>
                            @endif
                        </x-card>

                        {{-- Disbursement Form --}}
                        <x-card title="Disburse Funds">
                            <form method="POST" action="{{ route('loans.disburse', $loan) }}">
                                @csrf
                                @method('PATCH')
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Amount (₱)</label>
                                        <input type="number" name="amount" value="{{ $loan->approved_amount ?? $loan->amount }}" min="1" step="0.01" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Method</label>
                                        <select name="method" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                            <option value="bank_transfer">Bank Transfer</option>
                                            <option value="cash">Cash</option>
                                            <option value="check">Check</option>
                                            <option value="online">Online</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Reference Number</label>
                                        <input type="text" name="reference_number" maxlength="100" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Disbursed At</label>
                                        <input type="datetime-local" name="disbursed_at" value="{{ now()->format('Y-m-d\TH:i') }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                                        <textarea name="notes" rows="2" maxlength="500" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                                    </div>
                                </div>
                                <button type="submit" class="mt-4 w-full inline-flex justify-center items-center gap-2 px-4 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-xl font-medium hover:from-emerald-700 hover:to-emerald-800 transition-colors" onclick="confirmDisburse(event)">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Disburse & Activate Loan
                                </button>
                            </form>
                        </x-card>
                    @endif

                    {{-- Delete Action --}}
                    @if ($loan->isPending() && ($loan->user_id === auth()->id() || auth()->user()->isAdmin()))
                        <x-card>
                            <div class="p-3 sm:p-4 bg-red-50 rounded-xl border border-red-200">
                                <h4 class="text-xs sm:text-sm font-semibold text-red-800 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Danger Zone
                                </h4>
                                <p class="text-xs text-red-600 mb-2 sm:mb-3">This action cannot be undone.</p>
                                <form method="POST" action="{{ route('loans.destroy', $loan) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="confirmDelete(event, 'Delete Loan Application?', 'This will permanently delete this loan application and all related data.')" class="w-full inline-flex justify-center items-center gap-2 px-3 sm:px-4 py-2 sm:py-2.5 bg-red-600 text-white rounded-lg font-medium text-xs sm:text-sm hover:bg-red-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete Application
                                    </button>
                                </form>
                            </div>
                        </x-card>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
