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
                <form method="POST" action="{{ route('loans.store') }}" class="space-y-6" id="loanForm">
                    @csrf

                    {{-- Loan Type --}}
                    <div>
                        <x-input-label for="loan_type" :value="__('Loan Type')" class="text-sm font-medium text-slate-700 mb-1.5" />
                        <select id="loan_type" name="loan_type" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200" required>
                            <option value="">Select loan type...</option>
                            @foreach ($loanTypes as $type)
                                <option value="{{ $type }}" {{ old('loan_type') === $type ? 'selected' : '' }}
                                    data-description="{{ $loanTypeDescriptions[$type] ?? '' }}">
                                    {{ $loanTypeLabels[$type] ?? ucfirst($type) . ' Loan' }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('loan_type')" class="mt-2" />
                        <div class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-2">
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
                            <div class="p-3 bg-red-50 rounded-lg col-span-2 sm:col-span-1">
                                <span class="text-xs font-semibold text-red-700">Calamity</span>
                                <p class="text-xs text-red-600 mt-0.5">Disaster assistance</p>
                            </div>
                        </div>
                    </div>

                    {{-- Calamity Loan Info Box --}}
                    <div id="calamity-info" class="hidden p-4 bg-gradient-to-r from-red-50 to-orange-50 border border-red-200 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-red-800">Calamity Loan Features</h4>
                                <ul class="mt-2 text-xs text-red-700 space-y-1">
                                    <li>• <strong>Fixed Interest Rate:</strong> {{ $calamityConfig['interest_rate'] ?? 10.5 }}% per annum</li>
                                    <li>• <strong>Loan Terms:</strong> 2 years (24 months) or 3 years (36 months) only</li>
                                    <li>• <strong>Grace Period:</strong> {{ $calamityConfig['grace_period_months'] ?? 2 }} months before first payment</li>
                                    <li>• <strong>Loanable Amount:</strong> {{ $calamityConfig['loanable_percentage'] ?? 80 }}% of your eligible amount</li>
                                    <li>• <strong>Late Payment Penalty:</strong> 1/20 of 1% per day of delay</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Regular Loan Amount (hidden for calamity) --}}
                    <div id="regular-amount-section">
                        <x-input-label for="amount" :value="__('Loan Amount')" class="text-sm font-medium text-slate-700 mb-1.5" />
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-medium">₱</span>
                            <x-text-input id="amount" class="pl-8" type="number" name="amount" :value="old('amount')" min="1000" max="1000000" step="100" placeholder="0.00" />
                        </div>
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        <p class="mt-1.5 text-xs text-slate-500">Minimum: ₱1,000 • Maximum: ₱1,000,000</p>
                    </div>

                    {{-- Calamity Eligible Amount (shown only for calamity) --}}
                    <div id="calamity-amount-section" class="hidden space-y-4">
                        <div>
                            <x-input-label for="eligible_amount" :value="__('Eligible Amount')" class="text-sm font-medium text-slate-700 mb-1.5" />
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-medium">₱</span>
                                <x-text-input id="eligible_amount" class="pl-8" type="number" name="eligible_amount" :value="old('eligible_amount')" min="{{ config('loans.calamity.min_eligible_amount', 5000) }}" max="{{ config('loans.calamity.max_eligible_amount', 500000) }}" step="100" placeholder="0.00" />
                            </div>
                            <x-input-error :messages="$errors->get('eligible_amount')" class="mt-2" />
                            <p class="mt-1.5 text-xs text-slate-500">
                                Min: ₱{{ number_format(config('loans.calamity.min_eligible_amount', 5000)) }} • 
                                Max: ₱{{ number_format(config('loans.calamity.max_eligible_amount', 500000)) }}
                            </p>
                        </div>

                        {{-- Calculated Loanable Amount Display --}}
                        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-sm font-medium text-emerald-800">Loanable Amount ({{ $calamityConfig['loanable_percentage'] ?? 80 }}%)</span>
                                    <p class="text-xs text-emerald-600 mt-0.5">This is the amount you can borrow</p>
                                </div>
                                <span id="loanable-amount" class="text-2xl font-bold text-emerald-700">₱0.00</span>
                            </div>
                        </div>
                    </div>

                    {{-- Term --}}
                    <div>
                        <x-input-label for="term_months" :value="__('Loan Term')" class="text-sm font-medium text-slate-700 mb-1.5" />
                        <select id="term_months" name="term_months" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200" required>
                            <option value="">Select term...</option>
                            @foreach ([3, 6, 12, 18, 24, 36, 48, 60] as $months)
                                <option value="{{ $months }}" {{ old('term_months') == $months ? 'selected' : '' }}
                                    class="regular-term-option {{ in_array($months, $calamityConfig['term_options'] ?? [24, 36]) ? 'calamity-term-option' : '' }}">
                                    {{ $months }} months {{ $months >= 12 ? '(' . round($months / 12, 1) . ' year' . ($months > 12 ? 's' : '') . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('term_months')" class="mt-2" />
                        <p id="calamity-term-note" class="hidden mt-1.5 text-xs text-red-600">
                            <svg class="inline w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Calamity loans are available for 2 or 3 year terms only
                        </p>
                    </div>

                    {{-- Interest Rate (hidden for calamity) --}}
                    <div id="interest-rate-section">
                        <x-input-label :value="__('Annual Interest Rate')" class="text-sm font-medium text-slate-700 mb-1.5" />
                        <div class="p-3 bg-slate-100 border border-slate-200 rounded-xl flex items-center justify-between">
                            <span class="text-slate-600">Fixed rate for Regular Loan</span>
                            <span class="text-lg font-bold text-slate-900">10.5%</span>
                        </div>
                        <p class="mt-1.5 text-xs text-slate-500">Interest rate is fixed at 10.5% per annum for all regular loans</p>
                    </div>

                    {{-- Calamity Interest Rate Display --}}
                    <div id="calamity-interest-section" class="hidden">
                        <x-input-label :value="__('Annual Interest Rate')" class="text-sm font-medium text-slate-700 mb-1.5" />
                        <div class="p-3 bg-slate-100 border border-slate-200 rounded-xl flex items-center justify-between">
                            <span class="text-slate-600">Fixed rate for Calamity Loan</span>
                            <span class="text-lg font-bold text-slate-900">{{ $calamityConfig['interest_rate'] ?? 5.95 }}%</span>
                        </div>
                        <p class="mt-1.5 text-xs text-slate-500">Interest rate is fixed for all calamity loans</p>
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
                        <p id="grace-period-note" class="hidden mt-2 text-xs text-amber-600">
                            <svg class="inline w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            First payment due after {{ $calamityConfig['grace_period_months'] ?? 2 }}-month grace period
                        </p>
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
        const calamityConfig = @json($calamityConfig ?? []);
        const loanTypeSelect = document.getElementById('loan_type');
        const regularAmountSection = document.getElementById('regular-amount-section');
        const calamityAmountSection = document.getElementById('calamity-amount-section');
        const interestRateSection = document.getElementById('interest-rate-section');
        const calamityInterestSection = document.getElementById('calamity-interest-section');
        const calamityInfo = document.getElementById('calamity-info');
        const calamityTermNote = document.getElementById('calamity-term-note');
        const gracePeriodNote = document.getElementById('grace-period-note');
        const termSelect = document.getElementById('term_months');
        const amountInput = document.getElementById('amount');
        const eligibleAmountInput = document.getElementById('eligible_amount');
        const interestRateInput = document.getElementById('interest_rate');
        const loanableAmountDisplay = document.getElementById('loanable-amount');

        function isCalamityLoan() {
            return loanTypeSelect.value === 'calamity';
        }

        function toggleCalamityFields() {
            const isCalamity = isCalamityLoan();

            // Toggle visibility
            calamityInfo.classList.toggle('hidden', !isCalamity);
            regularAmountSection.classList.toggle('hidden', isCalamity);
            calamityAmountSection.classList.toggle('hidden', !isCalamity);
            interestRateSection.classList.toggle('hidden', isCalamity);
            calamityInterestSection.classList.toggle('hidden', !isCalamity);
            calamityTermNote.classList.toggle('hidden', !isCalamity);
            gracePeriodNote.classList.toggle('hidden', !isCalamity);

            // Toggle required attributes
            amountInput.required = !isCalamity;
            eligibleAmountInput.required = isCalamity;
            interestRateInput.required = !isCalamity;

            // Filter term options for calamity loans
            filterTermOptions(isCalamity);

            // Recalculate payments
            calculateMonthlyPayment();
        }

        function filterTermOptions(isCalamity) {
            const options = termSelect.querySelectorAll('option');
            const calamityTerms = calamityConfig.term_options || [24, 36];

            options.forEach(option => {
                if (option.value === '') return; // Keep placeholder

                const termValue = parseInt(option.value);
                if (isCalamity) {
                    option.hidden = !calamityTerms.includes(termValue);
                    // Clear selection if current term is not valid for calamity
                    if (option.selected && !calamityTerms.includes(termValue)) {
                        termSelect.value = '';
                    }
                } else {
                    option.hidden = false;
                }
            });
        }

        function calculateLoanableAmount() {
            const eligibleAmount = parseFloat(eligibleAmountInput.value) || 0;
            const percentage = calamityConfig.loanable_percentage || 80;
            const loanableAmount = eligibleAmount * (percentage / 100);

            loanableAmountDisplay.textContent = '₱' + loanableAmount.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            return loanableAmount;
        }

        function calculateMonthlyPayment() {
            let amount, annualRate;
            const termMonths = parseInt(termSelect.value) || 0;

            if (isCalamityLoan()) {
                amount = calculateLoanableAmount();
                annualRate = calamityConfig.interest_rate || 5.95;
            } else {
                amount = parseFloat(amountInput.value) || 0;
                annualRate = 10.5; // Fixed interest rate for regular loans
            }

            if (amount > 0 && termMonths > 0) {
                // Use FLAT RATE calculation method
                const termYears = termMonths / 12;
                const totalInterest = amount * (annualRate / 100) * termYears;
                const totalAmount = amount + totalInterest;
                const payment = totalAmount / termMonths;

                document.getElementById('monthly-payment').textContent =
                    '₱' + payment.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            } else {
                document.getElementById('monthly-payment').textContent = '₱0.00';
            }
        }

        // Event listeners
        loanTypeSelect.addEventListener('change', toggleCalamityFields);
        amountInput.addEventListener('input', calculateMonthlyPayment);
        eligibleAmountInput.addEventListener('input', () => {
            calculateLoanableAmount();
            calculateMonthlyPayment();
        });
        termSelect.addEventListener('change', calculateMonthlyPayment);

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            toggleCalamityFields();
            calculateMonthlyPayment();
        });
    </script>
</x-app-layout>
