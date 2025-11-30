<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">
                    {{ __('My Dashboard') }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">Welcome back, {{ auth()->user()->name }}! Here's your loan overview.</p>
            </div>
            <a href="{{ route('loans.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 border border-transparent rounded-xl font-semibold text-sm text-white shadow-sm hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Apply for Loan
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @php
                $user = auth()->user();
                $activeLoans = $user->loans()->where('status', 'active')->get();
                $pendingLoans = $user->loans()->whereIn('status', ['pending', 'under_review'])->count();
                $upcomingPayments = \App\Models\Payment::whereIn('loan_id', $activeLoans->pluck('id'))
                    ->where('status', 'pending')
                    ->where('due_date', '>=', now())
                    ->where('due_date', '<=', now()->addDays(30))
                    ->orderBy('due_date')
                    ->take(5)
                    ->get();
                $overduePayments = \App\Models\Payment::whereIn('loan_id', $activeLoans->pluck('id'))
                    ->where('status', 'pending')
                    ->where('due_date', '<', now())
                    ->count();
            @endphp

            {{-- Quick Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
                <x-stat-card 
                    title="Active Loans" 
                    :value="$activeLoans->count()" 
                    color="indigo"
                >
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </x-slot>
                </x-stat-card>
                
                <x-stat-card 
                    title="Pending Applications" 
                    :value="$pendingLoans" 
                    color="amber"
                >
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </x-slot>
                </x-stat-card>
                
                <x-stat-card 
                    title="Total Outstanding" 
                    :value="'₱' . number_format($activeLoans->sum('outstanding_balance'), 2)" 
                    color="blue"
                >
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </x-slot>
                </x-stat-card>
                
                <x-stat-card 
                    title="Overdue Payments" 
                    :value="$overduePayments" 
                    :color="$overduePayments > 0 ? 'red' : 'emerald'"
                >
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </x-slot>
                </x-stat-card>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Active Loans --}}
                <x-card title="My Active Loans">
                    <x-slot name="actions">
                        <a href="{{ route('loans.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                            View All
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </x-slot>
                    
                    <div class="space-y-4">
                        @forelse ($activeLoans as $loan)
                            <a href="{{ route('loans.show', $loan) }}" class="block p-4 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/50 transition-all duration-200 group">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-slate-900">Loan #{{ $loan->id }}</span>
                                            <x-status-badge :status="$loan->status" size="sm" />
                                        </div>
                                        <p class="text-sm text-slate-500 mt-0.5">{{ ucfirst(str_replace('_', ' ', $loan->loan_type)) }} Loan</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-lg text-slate-900">₱{{ number_format($loan->outstanding_balance, 2) }}</p>
                                        <p class="text-xs text-slate-500">remaining</p>
                                    </div>
                                </div>
                                <div class="relative">
                                    @php
                                        $paidPercentage = $loan->amount > 0 ? (($loan->amount - $loan->outstanding_balance) / $loan->amount) * 100 : 0;
                                    @endphp
                                    <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $paidPercentage }}%"></div>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1.5 flex justify-between">
                                        <span>{{ number_format($paidPercentage, 1) }}% paid</span>
                                        <span class="text-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity">View details →</span>
                                    </p>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-10">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h4 class="font-medium text-slate-900 mb-1">No active loans</h4>
                                <p class="text-sm text-slate-500 mb-4">Start by applying for your first loan</p>
                                <a href="{{ route('loans.create') }}" class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Apply for a loan
                                </a>
                            </div>
                        @endforelse
                    </div>
                </x-card>

                {{-- Upcoming Payments --}}
                <x-card title="Upcoming Payments">
                    <div class="space-y-3">
                        @forelse ($upcomingPayments as $payment)
                            <div class="flex items-center justify-between p-4 rounded-xl border {{ $payment->due_date->isPast() ? 'border-red-200 bg-red-50' : 'border-slate-200 hover:border-slate-300' }} transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $payment->due_date->isPast() ? 'bg-red-100 text-red-600' : ($payment->due_date->isToday() ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-600') }} flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900">Loan #{{ $payment->loan_id }}</p>
                                        <p class="text-sm {{ $payment->due_date->isPast() ? 'text-red-600 font-semibold' : 'text-slate-500' }}">
                                            @if ($payment->due_date->isPast())
                                                Overdue by {{ $payment->due_date->diffInDays(now()) }} days
                                            @elseif ($payment->due_date->isToday())
                                                Due today
                                            @elseif ($payment->due_date->diffInDays(now()) <= 7)
                                                Due in {{ $payment->due_date->diffInDays(now()) }} days
                                            @else
                                                {{ $payment->due_date->format('M d, Y') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <span class="font-bold text-lg text-slate-900">₱{{ number_format($payment->amount, 2) }}</span>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50 mb-4">
                                    <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h4 class="font-medium text-slate-900 mb-1">All caught up!</h4>
                                <p class="text-sm text-slate-500">No upcoming payments in the next 30 days</p>
                            </div>
                        @endforelse
                    </div>
                </x-card>
            </div>

            {{-- Quick Actions --}}
            <x-card title="Quick Actions" subtitle="Common tasks you can perform">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <a href="{{ route('loans.create') }}" class="group p-5 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all duration-200 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 mb-3 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <h4 class="font-medium text-slate-900">Apply for Loan</h4>
                        <p class="text-xs text-slate-500 mt-1">Submit new application</p>
                    </a>
                    
                    <a href="{{ route('loans.index') }}" class="group p-5 rounded-xl border border-slate-200 hover:border-blue-300 hover:bg-blue-50 transition-all duration-200 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-blue-100 text-blue-600 mb-3 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <h4 class="font-medium text-slate-900">My Loans</h4>
                        <p class="text-xs text-slate-500 mt-1">View all applications</p>
                    </a>
                    
                    @if ($activeLoans->isNotEmpty())
                        <a href="{{ route('loans.schedule', $activeLoans->first()) }}" class="group p-5 rounded-xl border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 transition-all duration-200 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 mb-3 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h4 class="font-medium text-slate-900">Payment Schedule</h4>
                            <p class="text-xs text-slate-500 mt-1">View payment dates</p>
                        </a>
                    @else
                        <div class="p-5 rounded-xl border border-slate-100 bg-slate-50 text-center opacity-60 cursor-not-allowed">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-slate-200 text-slate-400 mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h4 class="font-medium text-slate-500">Payment Schedule</h4>
                            <p class="text-xs text-slate-400 mt-1">No active loans</p>
                        </div>
                    @endif
                    
                    <a href="{{ route('profile.edit') }}" class="group p-5 rounded-xl border border-slate-200 hover:border-purple-300 hover:bg-purple-50 transition-all duration-200 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-purple-100 text-purple-600 mb-3 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h4 class="font-medium text-slate-900">My Profile</h4>
                        <p class="text-xs text-slate-500 mt-1">Update your info</p>
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
