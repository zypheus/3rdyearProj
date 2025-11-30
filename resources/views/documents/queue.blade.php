<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">
                    Document Verification Queue
                </h2>
                <p class="mt-1 text-sm text-slate-500">Review and verify uploaded documents</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    {{ $documents->total() }} pending
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($documents->isEmpty())
                <x-card>
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 mb-4">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900">All caught up!</h3>
                        <p class="mt-1 text-sm text-slate-500">No documents pending verification.</p>
                    </div>
                </x-card>
            @else
                <div class="space-y-4">
                    @foreach ($documents as $document)
                        <x-card hover>
                            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                                {{-- Document Info --}}
                                <div class="flex items-start gap-4 flex-1">
                                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="font-semibold text-slate-900 truncate">{{ $document->filename }}</h3>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700">
                                                {{ ucfirst(str_replace('_', ' ', $document->document_type)) }}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-sm text-slate-500">Uploaded {{ $document->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>

                                {{-- Loan Info --}}
                                <div class="flex items-center gap-4 pl-16 lg:pl-0 lg:border-l lg:border-slate-200 lg:pl-6">
                                    <div class="text-center lg:text-left">
                                        <a href="{{ route('loans.show', $document->loan) }}" class="font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                            Loan #{{ $document->loan_id }}
                                        </a>
                                        <p class="text-sm text-slate-500">
                                            {{ ucfirst($document->loan->loan_type) }} • ₱{{ number_format($document->loan->amount, 2) }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Applicant --}}
                                <div class="flex items-center gap-3 pl-16 lg:pl-0 lg:border-l lg:border-slate-200 lg:pl-6">
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-sm font-bold">
                                        {{ strtoupper(substr($document->loan->user->name, 0, 1)) }}
                                    </span>
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $document->loan->user->name }}</p>
                                        <p class="text-sm text-slate-500">{{ $document->loan->user->email }}</p>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center gap-2 pl-16 lg:pl-0 lg:border-l lg:border-slate-200 lg:pl-6">
                                    <a href="{{ route('documents.show', $document) }}" class="inline-flex items-center gap-1.5 px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </a>
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
                                </div>
                            </div>

                            {{-- Reject Form (expandable) --}}
                            <div class="mt-4 pt-4 border-t border-slate-100" x-data="{ showReject: false }">
                                <button type="button" @click="showReject = !showReject" class="text-sm text-slate-500 hover:text-slate-700 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                    <span x-text="showReject ? 'Hide rejection form' : 'Reject this document'"></span>
                                </button>
                                <form method="POST" action="{{ route('documents.reject', $document) }}" x-show="showReject" x-transition class="mt-3 flex flex-col sm:flex-row gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="text" name="notes" placeholder="Reason for rejection..." 
                                        class="flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:border-red-500 focus:ring-red-500" required>
                                    <button type="submit" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-red-600 rounded-lg text-sm font-medium text-white hover:bg-red-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Reject Document
                                    </button>
                                </form>
                            </div>
                        </x-card>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
