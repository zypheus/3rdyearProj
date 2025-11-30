<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">
                    {{ __('Record Payment') }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">For Loan #{{ $loan->id }} - {{ $loan->user->name }}</p>
            </div>
            <a href="{{ route('loans.payments.index', $loan) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-300 rounded-xl font-medium text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancel
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            {{-- Loan Summary --}}
            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl border border-indigo-100 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-indigo-100 rounded-lg">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-indigo-900">Loan Summary</h3>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <span class="text-xs text-indigo-600">Applicant</span>
                        <p class="font-semibold text-indigo-900">{{ $loan->user->name }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-indigo-600">Loan Type</span>
                        <p class="font-semibold text-indigo-900">{{ ucfirst($loan->loan_type) }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-indigo-600">Loan Amount</span>
                        <p class="font-semibold text-indigo-900">₱{{ number_format($loan->approved_amount ?? $loan->amount, 2) }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-indigo-600">Status</span>
                        <p><x-status-badge :status="$loan->status" size="sm" /></p>
                    </div>
                </div>
            </div>

            <x-card>
                <form method="POST" action="{{ route('loans.payments.store', $loan) }}" class="space-y-6">
                    @csrf

                    {{-- Payment Details Section --}}
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 flex items-center gap-2 mb-4">
                            <div class="p-2 bg-emerald-100 rounded-lg">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            Payment Details
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Payment Amount --}}
                            <div>
                                <label for="amount" class="flex items-center gap-2 text-sm font-medium text-slate-700 mb-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Payment Amount (₱)
                                </label>
                                <input type="number" id="amount" name="amount" value="{{ old('amount') }}" required min="1" step="0.01"
                                    class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all duration-200"
                                    placeholder="0.00">
                                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                            </div>

                            {{-- Payment Date --}}
                            <div>
                                <label for="payment_date" class="flex items-center gap-2 text-sm font-medium text-slate-700 mb-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Payment Date
                                </label>
                                <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required max="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all duration-200">
                                <x-input-error :messages="$errors->get('payment_date')" class="mt-2" />
                            </div>

                            {{-- Principal Amount --}}
                            <div>
                                <label for="principal_amount" class="flex items-center gap-2 text-sm font-medium text-slate-700 mb-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    Principal Amount (₱)
                                    <span class="text-xs text-slate-400">(Optional)</span>
                                </label>
                                <input type="number" id="principal_amount" name="principal_amount" value="{{ old('principal_amount') }}" min="0" step="0.01"
                                    class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all duration-200"
                                    placeholder="0.00">
                                <x-input-error :messages="$errors->get('principal_amount')" class="mt-2" />
                            </div>

                            {{-- Interest Amount --}}
                            <div>
                                <label for="interest_amount" class="flex items-center gap-2 text-sm font-medium text-slate-700 mb-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                                    </svg>
                                    Interest Amount (₱)
                                    <span class="text-xs text-slate-400">(Optional)</span>
                                </label>
                                <input type="number" id="interest_amount" name="interest_amount" value="{{ old('interest_amount') }}" min="0" step="0.01"
                                    class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all duration-200"
                                    placeholder="0.00">
                                <x-input-error :messages="$errors->get('interest_amount')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-3 flex items-start gap-2 p-3 rounded-lg bg-slate-50 border border-slate-200">
                            <svg class="w-5 h-5 text-slate-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-slate-600">If principal and interest amounts are left blank, the entire amount will be applied to principal.</p>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-slate-200"></div>

                    {{-- Payment Method Section --}}
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 flex items-center gap-2 mb-4">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            Payment Method
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Payment Method --}}
                            <div>
                                <label for="payment_method" class="flex items-center gap-2 text-sm font-medium text-slate-700 mb-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    Method
                                </label>
                                <select id="payment_method" name="payment_method" required
                                    class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white">
                                    <option value="">Select method...</option>
                                    @foreach ($paymentMethods as $method)
                                        <option value="{{ $method }}" {{ old('payment_method') === $method ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $method)) }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                            </div>

                            {{-- Reference Number --}}
                            <div>
                                <label for="reference_number" class="flex items-center gap-2 text-sm font-medium text-slate-700 mb-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                    </svg>
                                    Reference Number
                                    <span class="text-xs text-slate-400">(Optional)</span>
                                </label>
                                <input type="text" id="reference_number" name="reference_number" value="{{ old('reference_number') }}"
                                    class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200"
                                    placeholder="Transaction ID, check #, etc.">
                                <x-input-error :messages="$errors->get('reference_number')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="mt-4">
                            <label for="notes" class="flex items-center gap-2 text-sm font-medium text-slate-700 mb-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Notes
                                <span class="text-xs text-slate-400">(Optional)</span>
                            </label>
                            <textarea id="notes" name="notes" rows="3"
                                class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 resize-none"
                                placeholder="Any additional notes about this payment...">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                        <a href="{{ route('loans.payments.index', $loan) }}" class="px-5 py-2.5 text-sm font-medium text-slate-700 hover:text-slate-900 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 border border-transparent rounded-xl font-semibold text-sm text-white shadow-sm hover:from-emerald-700 hover:to-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Record Payment
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
