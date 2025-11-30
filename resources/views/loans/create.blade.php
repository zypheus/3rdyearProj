<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">
                    {{ __('Apply for Loan') }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">Submit a new loan application</p>
            </div>
            <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Loans
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card>
                <form method="POST" action="{{ route('loans.store') }}" class="space-y-6">
                    @csrf

                    {{-- Loan Type --}}
                    <div>
                        <x-input-label for="loan_type" :value="__('Loan Type')" class="text-sm font-medium text-slate-700 mb-1.5" />
                        <select id="loan_type" name="loan_type" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200" required>
                            <option value="">Select loan type...</option>
                            @foreach ($loanTypes as $type)
                                <option value="{{ $type }}" {{ old('loan_type') === $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }} Loan
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('loan_type')" class="mt-2" />
                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <div class="p-3 bg-indigo-50 rounded-lg">
                                <span class="text-xs font-semibold text-indigo-700">Personal</span>
                                <p class="text-xs text-indigo-600 mt-0.5">General purpose loans</p>
                            </div>
                            <div class="p-3 bg-blue-50 rounded-lg">
                                <span class="text-xs font-semibold text-blue-700">Business</span>
                                <p class="text-xs text-blue-600 mt-0.5">Business funding</p>
                            </div>
                            <div class="p-3 bg-amber-50 rounded-lg">
                                <span class="text-xs font-semibold text-amber-700">Emergency</span>
                                <p class="text-xs text-amber-600 mt-0.5">Urgent needs</p>
                            </div>
                            <div class="p-3 bg-emerald-50 rounded-lg">
                                <span class="text-xs font-semibold text-emerald-700">Education</span>
                                <p class="text-xs text-emerald-600 mt-0.5">School expenses</p>
                            </div>
                        </div>
                    </div>

                    {{-- Amount --}}
                    <div>
                        <x-input-label for="amount" :value="__('Loan Amount')" class="text-sm font-medium text-slate-700 mb-1.5" />
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-medium">₱</span>
                            <x-text-input id="amount" class="pl-8" type="number" name="amount" :value="old('amount')" required min="1000" max="1000000" step="100" placeholder="0.00" />
                        </div>
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        <p class="mt-1.5 text-xs text-slate-500">Minimum: ₱1,000 • Maximum: ₱1,000,000</p>
                    </div>

                    {{-- Term --}}
                    <div>
                        <x-input-label for="term_months" :value="__('Loan Term')" class="text-sm font-medium text-slate-700 mb-1.5" />
                        <select id="term_months" name="term_months" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200" required>
                            <option value="">Select term...</option>
                            @foreach ([3, 6, 12, 18, 24, 36, 48, 60] as $months)
                                <option value="{{ $months }}" {{ old('term_months') == $months ? 'selected' : '' }}>
                                    {{ $months }} months {{ $months >= 12 ? '(' . round($months / 12, 1) . ' year' . ($months > 12 ? 's' : '') . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('term_months')" class="mt-2" />
                    </div>

                    {{-- Interest Rate --}}
                    <div>
                        <x-input-label for="interest_rate" :value="__('Annual Interest Rate (%)')" class="text-sm font-medium text-slate-700 mb-1.5" />
                        <div class="relative">
                            <x-text-input id="interest_rate" class="pr-8" type="number" name="interest_rate" :value="old('interest_rate', '12')" required min="0" max="50" step="0.1" />
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500">%</span>
                        </div>
                        <x-input-error :messages="$errors->get('interest_rate')" class="mt-2" />
                        <p class="mt-1.5 text-xs text-slate-500">Standard rate: 12% per annum</p>
                    </div>

                    {{-- Purpose --}}
                    <div>
                        <x-input-label for="purpose" :value="__('Purpose of Loan (Optional)')" class="text-sm font-medium text-slate-700 mb-1.5" />
                        <textarea id="purpose" name="purpose" rows="4" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200 resize-none" placeholder="Describe how you plan to use this loan...">{{ old('purpose') }}</textarea>
                        <x-input-error :messages="$errors->get('purpose')" class="mt-2" />
                    </div>

                    {{-- Estimated Monthly Payment --}}
                    <div class="p-5 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border border-indigo-100">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-slate-900">Estimated Monthly Payment</h3>
                                <p class="text-xs text-slate-500">Based on your inputs above</p>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-indigo-600" id="monthly-payment">₱0.00</p>
                        <p class="mt-2 text-xs text-slate-500">This is an estimate. Actual amount may vary based on approval.</p>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-200">
                        <a href="{{ route('loans.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
                            Cancel
                        </a>
                        <x-primary-button>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Submit Application
                        </x-primary-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    <script>
        function calculateMonthlyPayment() {
            const amount = parseFloat(document.getElementById('amount').value) || 0;
            const termMonths = parseInt(document.getElementById('term_months').value) || 0;
            const annualRate = parseFloat(document.getElementById('interest_rate').value) || 0;
            
            if (amount > 0 && termMonths > 0) {
                const monthlyRate = annualRate / 100 / 12;
                let payment;
                
                if (monthlyRate === 0) {
                    payment = amount / termMonths;
                } else {
                    payment = amount * (monthlyRate * Math.pow(1 + monthlyRate, termMonths)) / 
                              (Math.pow(1 + monthlyRate, termMonths) - 1);
                }
                
                document.getElementById('monthly-payment').textContent = 
                    '₱' + payment.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            } else {
                document.getElementById('monthly-payment').textContent = '₱0.00';
            }
        }

        document.getElementById('amount').addEventListener('input', calculateMonthlyPayment);
        document.getElementById('term_months').addEventListener('change', calculateMonthlyPayment);
        document.getElementById('interest_rate').addEventListener('input', calculateMonthlyPayment);
        
        // Calculate on page load if values exist
        calculateMonthlyPayment();
    </script>
</x-app-layout>
