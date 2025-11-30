<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">
                    {{ __('Dashboard') }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">Overview of loan operations and key metrics</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('reports.loans') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    View Reports
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Key Metrics --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
                <x-stat-card 
                    title="Total Disbursed" 
                    :value="'₱' . number_format($totalDisbursed, 2)" 
                    color="indigo"
                >
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </x-slot>
                </x-stat-card>
                
                <x-stat-card 
                    title="Total Collected" 
                    :value="'₱' . number_format($totalCollected, 2)" 
                    color="emerald"
                >
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </x-slot>
                </x-stat-card>
                
                <x-stat-card 
                    title="Outstanding Balance" 
                    :value="'₱' . number_format($totalOutstanding, 2)" 
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
                    href="{{ route('reports.delinquency') }}"
                >
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </x-slot>
                </x-stat-card>
            </div>

            {{-- Loan Status & User Overview --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <x-card title="Loan Status Overview">
                    <div class="space-y-3">
                        @php
                            $statusItems = [
                                ['key' => 'pending', 'label' => 'Pending', 'color' => 'amber'],
                                ['key' => 'under_review', 'label' => 'Under Review', 'color' => 'blue'],
                                ['key' => 'approved', 'label' => 'Approved', 'color' => 'emerald'],
                                ['key' => 'active', 'label' => 'Active', 'color' => 'indigo'],
                                ['key' => 'completed', 'label' => 'Completed', 'color' => 'slate'],
                                ['key' => 'defaulted', 'label' => 'Defaulted', 'color' => 'red'],
                            ];
                        @endphp
                        
                        @foreach($statusItems as $item)
                            <div class="flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 rounded-full bg-{{ $item['color'] }}-500"></div>
                                    <span class="text-sm text-slate-700">{{ $item['label'] }}</span>
                                </div>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-{{ $item['color'] }}-100 text-{{ $item['color'] }}-800">
                                    {{ $loanStats[$item['key']] ?? 0 }}
                                </span>
                            </div>
                        @endforeach
                        
                        <div class="pt-4 mt-2 border-t border-slate-200 flex items-center justify-between">
                            <span class="font-semibold text-slate-900">Total Loans</span>
                            <span class="text-lg font-bold text-slate-900">{{ $loanStats['total'] }}</span>
                        </div>
                    </div>
                </x-card>

                <x-card title="System Overview">
                    {{-- User Stats --}}
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center justify-between p-3 rounded-lg bg-purple-50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-slate-700">Administrators</span>
                            </div>
                            <span class="text-lg font-bold text-purple-600">{{ $userStats['admins'] }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 rounded-lg bg-blue-50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-slate-700">Loan Officers</span>
                            </div>
                            <span class="text-lg font-bold text-blue-600">{{ $userStats['officers'] }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-slate-700">Members</span>
                            </div>
                            <span class="text-lg font-bold text-slate-600">{{ $userStats['members'] }}</span>
                        </div>
                    </div>
                    
                    {{-- Pending Actions --}}
                    <h4 class="text-sm font-semibold text-slate-900 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        Pending Actions
                    </h4>
                    <div class="space-y-2">
                        <a href="{{ route('documents.queue') }}" class="flex items-center justify-between p-3 rounded-lg border border-slate-200 hover:border-amber-300 hover:bg-amber-50 transition-colors group">
                            <span class="text-sm text-slate-600 group-hover:text-slate-900">Documents Awaiting Verification</span>
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $pendingDocuments > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">{{ $pendingDocuments }}</span>
                        </a>
                        <a href="{{ route('reports.delinquency') }}" class="flex items-center justify-between p-3 rounded-lg border border-slate-200 hover:border-red-300 hover:bg-red-50 transition-colors group">
                            <span class="text-sm text-slate-600 group-hover:text-slate-900">Overdue Payments</span>
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $overduePayments > 0 ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">{{ $overduePayments }}</span>
                        </a>
                    </div>
                </x-card>
            </div>

            {{-- Recent Activity --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Recent Loans --}}
                <x-card title="Recent Loan Applications">
                    <x-slot name="actions">
                        <a href="{{ route('loans.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                            View All
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </x-slot>
                    
                    <div class="space-y-3">
                        @forelse ($recentLoans as $loan)
                            <a href="{{ route('loans.show', $loan) }}" class="flex items-center justify-between p-4 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/50 transition-all duration-200 group">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-sm font-semibold">
                                        {{ strtoupper(substr($loan->user->name, 0, 1)) }}
                                    </span>
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $loan->user->name }}</p>
                                        <p class="text-sm text-slate-500">₱{{ number_format($loan->amount, 2) }} • {{ $loan->term_months }} months</p>
                                    </div>
                                </div>
                                <x-status-badge :status="$loan->status" size="sm" />
                            </a>
                        @empty
                            <div class="text-center py-8">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 mb-3">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-slate-500">No recent loan applications</p>
                            </div>
                        @endforelse
                    </div>
                </x-card>

                {{-- Recent Payments --}}
                <x-card title="Recent Payments">
                    <x-slot name="actions">
                        <a href="{{ route('reports.payments') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                            View Report
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </x-slot>
                    
                    <div class="space-y-3">
                        @forelse ($recentPayments as $payment)
                            <div class="flex items-center justify-between p-4 rounded-xl border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $payment->loan->user->name }}</p>
                                        <p class="text-sm text-slate-500">Loan #{{ $payment->loan_id }} • {{ $payment->payment_date->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <span class="font-bold text-emerald-600">₱{{ number_format($payment->amount, 2) }}</span>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 mb-3">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-slate-500">No recent payments</p>
                            </div>
                        @endforelse
                    </div>
                </x-card>
            </div>

            {{-- Quick Links --}}
            <x-card title="Reports & Analytics" subtitle="Generate detailed reports and insights">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <a href="{{ route('reports.loans') }}" class="group p-5 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all duration-200 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 mb-3 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h4 class="font-medium text-slate-900">Loan Summary</h4>
                        <p class="text-xs text-slate-500 mt-1">Portfolio overview</p>
                    </a>
                    
                    <a href="{{ route('reports.payments') }}" class="group p-5 rounded-xl border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 transition-all duration-200 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 mb-3 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h4 class="font-medium text-slate-900">Collections</h4>
                        <p class="text-xs text-slate-500 mt-1">Payment tracking</p>
                    </a>
                    
                    <a href="{{ route('reports.delinquency') }}" class="group p-5 rounded-xl border border-slate-200 hover:border-amber-300 hover:bg-amber-50 transition-all duration-200 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-amber-100 text-amber-600 mb-3 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h4 class="font-medium text-slate-900">Delinquency</h4>
                        <p class="text-xs text-slate-500 mt-1">Overdue analysis</p>
                    </a>
                    
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('reports.audit') }}" class="group p-5 rounded-xl border border-slate-200 hover:border-purple-300 hover:bg-purple-50 transition-all duration-200 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-purple-100 text-purple-600 mb-3 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            </div>
                            <h4 class="font-medium text-slate-900">Audit Log</h4>
                            <p class="text-xs text-slate-500 mt-1">Activity history</p>
                        </a>
                    @else
                        <div class="p-5 rounded-xl border border-slate-100 bg-slate-50 text-center opacity-50 cursor-not-allowed">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-slate-200 text-slate-400 mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <h4 class="font-medium text-slate-400">Audit Log</h4>
                            <p class="text-xs text-slate-300 mt-1">Admin only</p>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
