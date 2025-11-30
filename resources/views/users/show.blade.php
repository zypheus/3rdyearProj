<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">
                    {{ __('User Profile') }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">Viewing details for {{ $user->name }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 border border-transparent rounded-xl font-semibold text-sm text-white shadow-sm hover:from-amber-600 hover:to-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-300 rounded-xl font-medium text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- User Info Card --}}
                <div class="lg:col-span-1">
                    <x-card :padding="false">
                        <div class="p-6">
                            {{-- Profile Header --}}
                            <div class="flex flex-col items-center text-center">
                                @php
                                    $gradients = [
                                        'admin' => 'from-purple-500 to-purple-600',
                                        'officer' => 'from-blue-500 to-blue-600',
                                        'member' => 'from-slate-500 to-slate-600',
                                    ];
                                    $gradient = $gradients[$user->role] ?? $gradients['member'];
                                @endphp
                                <span class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br {{ $gradient }} text-white text-2xl font-bold shadow-lg mb-4">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </span>
                                <h3 class="text-xl font-bold text-slate-900">{{ $user->name }}</h3>
                                <p class="text-slate-500 mt-1">{{ $user->email }}</p>
                                
                                {{-- Role Badge --}}
                                <div class="mt-3">
                                    @if ($user->role === 'admin')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Administrator
                                        </span>
                                    @elseif ($user->role === 'officer')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Loan Officer
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                            </svg>
                                            Member
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Role Change Form --}}
                            @if ($user->id !== auth()->id())
                                <div class="mt-6 pt-6 border-t border-slate-200">
                                    <h4 class="text-sm font-semibold text-slate-900 mb-3 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Change Role
                                    </h4>
                                    <form method="POST" action="{{ route('users.role', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <div class="flex gap-2">
                                            <select name="role" class="flex-1 px-3 py-2 border-2 border-slate-200 rounded-xl text-sm text-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 bg-white transition-all duration-200">
                                                @foreach (\App\Models\User::ROLES as $role)
                                                    <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>
                                                        {{ ucfirst($role) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 border border-transparent rounded-xl font-semibold text-sm text-white shadow-sm hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200">
                                                Update
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif

                            {{-- User Details --}}
                            <div class="mt-6 pt-6 border-t border-slate-200 space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-500">Phone</span>
                                        <p class="text-sm font-medium text-slate-900">{{ $user->phone ?? 'Not provided' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-500">Member Since</span>
                                        <p class="text-sm font-medium text-slate-900">{{ $user->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-500">Last Updated</span>
                                        <p class="text-sm font-medium text-slate-900">{{ $user->updated_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </div>

                            @if ($user->address)
                                <div class="mt-4 pt-4 border-t border-slate-200">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="text-xs text-slate-500">Address</span>
                                            <p class="text-sm font-medium text-slate-900">{{ $user->address }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </x-card>
                </div>

                {{-- Activity Tabs --}}
                <div class="lg:col-span-2" x-data="{ activeTab: 'loans' }">
                    <x-card :padding="false">
                        {{-- Tab Navigation --}}
                        <div class="border-b border-slate-200">
                            <nav class="flex">
                                <button @click="activeTab = 'loans'" :class="activeTab === 'loans' ? 'border-indigo-500 text-indigo-600 bg-indigo-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'" class="flex-1 py-4 px-4 text-center border-b-2 font-medium text-sm transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Loans 
                                    <span class="px-2 py-0.5 text-xs rounded-full" :class="activeTab === 'loans' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600'">{{ $user->loans->count() }}</span>
                                </button>
                                <button @click="activeTab = 'documents'" :class="activeTab === 'documents' ? 'border-indigo-500 text-indigo-600 bg-indigo-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'" class="flex-1 py-4 px-4 text-center border-b-2 font-medium text-sm transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Documents 
                                    <span class="px-2 py-0.5 text-xs rounded-full" :class="activeTab === 'documents' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600'">{{ $user->documents->count() }}</span>
                                </button>
                                <button @click="activeTab = 'payments'" :class="activeTab === 'payments' ? 'border-indigo-500 text-indigo-600 bg-indigo-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50'" class="flex-1 py-4 px-4 text-center border-b-2 font-medium text-sm transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Payments 
                                    <span class="px-2 py-0.5 text-xs rounded-full" :class="activeTab === 'payments' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600'">{{ $user->payments->count() }}</span>
                                </button>
                            </nav>
                        </div>

                        <div class="p-6">
                            {{-- Loans Tab --}}
                            <div x-show="activeTab === 'loans'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                @if ($user->loans->isEmpty())
                                    <div class="text-center py-12">
                                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-slate-900">No loans yet</h3>
                                        <p class="text-slate-500 mt-1">This user hasn't applied for any loans.</p>
                                    </div>
                                @else
                                    <div class="space-y-3">
                                        @foreach ($user->loans as $loan)
                                            <div class="p-4 rounded-xl border border-slate-200 hover:border-indigo-200 hover:bg-slate-50/50 transition-all">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex items-start gap-3">
                                                        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <p class="font-semibold text-slate-900">{{ ucfirst($loan->loan_type) }} Loan</p>
                                                            <p class="text-sm text-slate-500">Amount: ₱{{ number_format($loan->amount, 2) }}</p>
                                                            <p class="text-xs text-slate-400 mt-1">{{ $loan->created_at->format('M d, Y') }}</p>
                                                        </div>
                                                    </div>
                                                    <x-status-badge :status="$loan->status" size="sm" />
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Documents Tab --}}
                            <div x-show="activeTab === 'documents'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                @if ($user->documents->isEmpty())
                                    <div class="text-center py-12">
                                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-slate-900">No documents</h3>
                                        <p class="text-slate-500 mt-1">This user hasn't uploaded any documents.</p>
                                    </div>
                                @else
                                    <div class="space-y-3">
                                        @foreach ($user->documents as $document)
                                            <div class="p-4 rounded-xl border border-slate-200 hover:border-indigo-200 hover:bg-slate-50/50 transition-all">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex items-start gap-3">
                                                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <p class="font-semibold text-slate-900">{{ ucfirst(str_replace('_', ' ', $document->document_type)) }}</p>
                                                            <p class="text-sm text-slate-500">{{ $document->filename }}</p>
                                                        </div>
                                                    </div>
                                                    @if ($document->is_verified)
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                            </svg>
                                                            Verified
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">
                                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                            </svg>
                                                            Pending
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Payments Tab --}}
                            <div x-show="activeTab === 'payments'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                @if ($user->payments->isEmpty())
                                    <div class="text-center py-12">
                                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-slate-900">No payments</h3>
                                        <p class="text-slate-500 mt-1">This user hasn't made any payments yet.</p>
                                    </div>
                                @else
                                    <div class="space-y-3">
                                        @foreach ($user->payments as $payment)
                                            <div class="p-4 rounded-xl border border-slate-200 hover:border-indigo-200 hover:bg-slate-50/50 transition-all">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex items-start gap-3">
                                                        <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <p class="font-semibold text-slate-900">₱{{ number_format($payment->amount, 2) }}</p>
                                                            <p class="text-sm text-slate-500">{{ $payment->payment_date->format('M d, Y') }} • {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                                                        </div>
                                                    </div>
                                                    <x-status-badge :status="$payment->status" size="sm" />
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
