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
                        Documents for Loan #{{ $loan->id }}
                    </h2>
                    <p class="mt-0.5 text-sm text-slate-500">{{ ucfirst($loan->loan_type) }} loan • ₱{{ number_format($loan->amount, 2) }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if ($canUpload)
                    <a href="{{ route('loans.documents.create', $loan) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl text-sm font-medium hover:from-indigo-700 hover:to-indigo-800 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Upload Document
                    </a>
                @endif
                <a href="{{ route('loans.show', $loan) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    Back to Loan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($documents->isEmpty())
                <x-card>
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900">No documents uploaded</h3>
                        <p class="mt-1 text-sm text-slate-500">Get started by uploading your first document.</p>
                        @if ($canUpload)
                            <div class="mt-6">
                                <a href="{{ route('loans.documents.create', $loan) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl font-medium hover:from-indigo-700 hover:to-indigo-800 transition-colors shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Upload Document
                                </a>
                            </div>
                        @endif
                    </div>
                </x-card>
            @else
                <div class="grid grid-cols-1 gap-4">
                    @foreach ($documents as $document)
                        @php
                            $isRejected = str_starts_with($document->notes ?? '', 'REJECTED:');
                            $bgClass = $document->is_verified ? 'bg-emerald-50 border-emerald-200' : ($isRejected ? 'bg-red-50 border-red-200' : 'bg-white border-slate-200');
                        @endphp
                        <div class="border-2 rounded-2xl p-6 {{ $bgClass }} transition-all hover:shadow-md">
                            <div class="flex flex-col lg:flex-row lg:items-start gap-4">
                                {{-- Document Icon --}}
                                <div class="flex-shrink-0">
                                    <div class="w-14 h-14 rounded-xl {{ $document->is_verified ? 'bg-emerald-100' : ($isRejected ? 'bg-red-100' : 'bg-indigo-100') }} flex items-center justify-center">
                                        @if ($document->is_verified)
                                            <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @elseif ($isRejected)
                                            <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @else
                                            <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>

                                {{-- Document Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                        <h3 class="font-semibold text-slate-900 truncate">{{ $document->filename }}</h3>
                                        <x-status-badge :status="$document->is_verified ? 'verified' : ($isRejected ? 'rejected' : 'pending')" size="sm" />
                                    </div>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-slate-500">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                            {{ ucfirst(str_replace('_', ' ', $document->document_type)) }}
                                        </span>
                                        <span>•</span>
                                        <span>{{ $document->formatted_file_size ?? 'Unknown size' }}</span>
                                        <span>•</span>
                                        <span>{{ $document->created_at->diffForHumans() }}</span>
                                    </div>

                                    @if ($document->notes)
                                        <div class="mt-3 p-3 rounded-lg {{ $isRejected ? 'bg-red-100' : 'bg-slate-100' }}">
                                            <p class="text-sm {{ $isRejected ? 'text-red-700' : 'text-slate-600' }}">
                                                <strong>{{ $isRejected ? 'Reason:' : 'Notes:' }}</strong> 
                                                {{ $isRejected ? str_replace('REJECTED: ', '', $document->notes) : $document->notes }}
                                            </p>
                                        </div>
                                    @endif

                                    @if ($document->verifier)
                                        <div class="mt-3 flex items-center gap-2 text-xs text-slate-500">
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-200 text-slate-600 text-xs font-medium">
                                                {{ strtoupper(substr($document->verifier->name, 0, 1)) }}
                                            </span>
                                            {{ $document->is_verified ? 'Verified' : 'Reviewed' }} by {{ $document->verifier->name }}
                                            <span class="text-slate-400">•</span>
                                            {{ $document->verified_at->format('M d, Y') }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('documents.show', $document) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Download
                                    </a>

                                    @if ($canVerify && !$document->is_verified)
                                        <form method="POST" action="{{ route('documents.verify', $document) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 rounded-lg text-sm font-medium text-white hover:bg-emerald-700 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Verify
                                            </button>
                                        </form>
                                    @endif

                                    @if (!$document->is_verified && ($document->user_id === auth()->id() || auth()->user()->isAdmin()))
                                        <form method="POST" action="{{ route('documents.destroy', $document) }}" onsubmit="return confirm('Delete this document?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-600 rounded-lg text-sm font-medium text-white hover:bg-red-700 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            {{-- Rejection form for officers --}}
                            @if ($canVerify && !$document->is_verified && !$isRejected)
                                <div class="mt-4 pt-4 border-t border-slate-200" x-data="{ showReject: false }">
                                    <button type="button" @click="showReject = !showReject" class="text-sm text-slate-500 hover:text-slate-700 flex items-center gap-1">
                                        <svg class="w-4 h-4 transition-transform" :class="showReject && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                        <span x-text="showReject ? 'Hide rejection form' : 'Reject this document'"></span>
                                    </button>
                                    <form method="POST" action="{{ route('documents.reject', $document) }}" x-show="showReject" x-transition class="mt-3 flex flex-col sm:flex-row gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="text" name="notes" placeholder="Reason for rejection..." 
                                            class="flex-1 px-4 py-2 border border-slate-300 rounded-lg text-sm focus:border-red-500 focus:ring-red-500" required>
                                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-red-600 rounded-lg text-sm font-medium text-white hover:bg-red-700 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Reject Document
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
