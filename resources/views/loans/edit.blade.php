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
                        Edit Loan #{{ $loan->id }}
                    </h2>
                    <p class="mt-0.5 text-sm text-slate-500">Update your loan application details</p>
                </div>
            </div>
            <a href="{{ route('loans.show', $loan) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancel
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card>
                <form method="POST" action="{{ route('loans.update', $loan) }}" x-data="{ 
                    amount: {{ old('amount', $loan->amount) }}, 
                    term: {{ old('term_months', $loan->term_months) }}, 
                    rate: {{ old('interest_rate', $loan->interest_rate) }},
                    get monthlyPayment() {
                        if (this.amount <= 0 || this.term <= 0) return 0;
                        const monthlyRate = this.rate / 100 / 12;
                        if (monthlyRate === 0) return this.amount / this.term;
                        return (this.amount * monthlyRate * Math.pow(1 + monthlyRate, this.term)) / (Math.pow(1 + monthlyRate, this.term) - 1);
                    }
                }">
                    @csrf
                    @method('PUT')

                    {{-- Loan Type --}}
                    <div class="mb-6">
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-3">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Loan Type
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @php
                                $typeIcons = [
                                    'personal' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
                                    'business' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                                    'emergency' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
                                    'educational' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>'
                                ];
                            @endphp
                            @foreach ($loanTypes as $type)
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="loan_type" value="{{ $type }}" class="peer sr-only" {{ old('loan_type', $loan->loan_type) === $type ? 'checked' : '' }} required>
                                    <div class="p-4 border-2 border-slate-200 rounded-xl text-center transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-slate-300 hover:bg-slate-50">
                                        <svg class="w-6 h-6 mx-auto mb-2 text-slate-400 peer-checked:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            {!! $typeIcons[$type] ?? '' !!}
                                        </svg>
                                        <span class="text-sm font-medium text-slate-700">{{ ucfirst($type) }}</span>
                                    </div>
                                    <div class="absolute top-2 right-2 w-4 h-4 rounded-full bg-indigo-500 text-white hidden peer-checked:flex items-center justify-center">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('loan_type')" class="mt-2" />
                    </div>

                    {{-- Amount --}}
                    <div class="mb-6">
                        <label for="amount" class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Loan Amount
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-medium">₱</span>
                            <input type="number" id="amount" name="amount" x-model="amount" 
                                class="w-full pl-10 pr-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 text-lg font-semibold transition-colors"
                                required min="1000" max="1000000" step="100">
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Min: ₱1,000 • Max: ₱1,000,000</p>
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        {{-- Term --}}
                        <div>
                            <label for="term_months" class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Loan Term
                            </label>
                            <select id="term_months" name="term_months" x-model="term"
                                class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                                @foreach ([3, 6, 12, 18, 24, 36, 48, 60] as $months)
                                    <option value="{{ $months }}" {{ old('term_months', $loan->term_months) == $months ? 'selected' : '' }}>
                                        {{ $months }} months ({{ $months/12 >= 1 ? floor($months/12) . ' year' . (floor($months/12) > 1 ? 's' : '') : '' }}{{ $months % 12 > 0 && floor($months/12) >= 1 ? ' ' . ($months % 12) . 'mo' : '' }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('term_months')" class="mt-2" />
                        </div>

                        {{-- Interest Rate --}}
                        <div>
                            <label for="interest_rate" class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Interest Rate (% per year)
                            </label>
                            <div class="relative">
                                <input type="number" id="interest_rate" name="interest_rate" x-model="rate"
                                    class="w-full pl-4 pr-10 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 transition-colors"
                                    required min="0" max="50" step="0.1">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 font-medium">%</span>
                            </div>
                            <x-input-error :messages="$errors->get('interest_rate')" class="mt-2" />
                        </div>
                    </div>

                    {{-- Monthly Payment Calculator --}}
                    <div class="mb-6 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border border-indigo-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-indigo-700">Estimated Monthly Payment</p>
                                <p class="text-xs text-indigo-500">Based on your current inputs</p>
                            </div>
                            <p class="text-2xl font-bold text-indigo-700" x-text="'₱' + monthlyPayment.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></p>
                        </div>
                    </div>

                    {{-- Purpose --}}
                    <div class="mb-6">
                        <label for="purpose" class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Purpose of Loan
                            <span class="text-xs font-normal text-slate-400">(Optional)</span>
                        </label>
                        <textarea id="purpose" name="purpose" rows="4" 
                            class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 transition-colors resize-none"
                            placeholder="Describe how you plan to use the loan funds...">{{ old('purpose', $loan->purpose) }}</textarea>
                        <x-input-error :messages="$errors->get('purpose')" class="mt-2" />
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex items-center justify-between pt-6 border-t border-slate-200">
                        <a href="{{ route('loans.show', $loan) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Cancel
                        </a>
                        <x-primary-button class="gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update Application
                        </x-primary-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
