<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">
                    {{ __('Upload Document') }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">For Loan #{{ $loan->id }}</p>
            </div>
            <a href="{{ route('loans.documents.index', $loan) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-300 rounded-xl font-medium text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancel
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card>
                <form method="POST" action="{{ route('loans.documents.store', $loan) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    {{-- Document Type Section --}}
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 flex items-center gap-2 mb-4">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            Document Details
                        </h3>

                        {{-- Document Type --}}
                        <div>
                            <label for="document_type" class="flex items-center gap-2 text-sm font-medium text-slate-700 mb-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Document Type
                            </label>
                            <select id="document_type" name="document_type" required
                                class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white">
                                <option value="">Select document type...</option>
                                @foreach ($documentTypes as $type)
                                    <option value="{{ $type }}" {{ old('document_type') === $type ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('document_type')" class="mt-2" />
                        </div>

                        {{-- Document Type Descriptions --}}
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="flex items-start gap-2 p-3 rounded-lg bg-slate-50 border border-slate-200">
                                <svg class="w-5 h-5 text-slate-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                </svg>
                                <div>
                                    <span class="text-sm font-semibold text-slate-900">ID</span>
                                    <p class="text-xs text-slate-600">Government-issued ID</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2 p-3 rounded-lg bg-slate-50 border border-slate-200">
                                <svg class="w-5 h-5 text-slate-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <span class="text-sm font-semibold text-slate-900">Income Proof</span>
                                    <p class="text-xs text-slate-600">Payslips, ITR</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2 p-3 rounded-lg bg-slate-50 border border-slate-200">
                                <svg class="w-5 h-5 text-slate-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                <div>
                                    <span class="text-sm font-semibold text-slate-900">Bank Statement</span>
                                    <p class="text-xs text-slate-600">Last 3 months</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2 p-3 rounded-lg bg-slate-50 border border-slate-200">
                                <svg class="w-5 h-5 text-slate-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <span class="text-sm font-semibold text-slate-900">Employment</span>
                                    <p class="text-xs text-slate-600">Certificate of Employment</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-slate-200"></div>

                    {{-- File Upload Section --}}
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 flex items-center gap-2 mb-4">
                            <div class="p-2 bg-indigo-100 rounded-lg">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            Upload File
                        </h3>

                        {{-- Dropzone --}}
                        <div class="relative" x-data="{ isDragging: false, fileName: '' }">
                            <div 
                                x-on:dragenter.prevent="isDragging = true"
                                x-on:dragleave.prevent="isDragging = false"
                                x-on:drop.prevent="isDragging = false; fileName = $event.dataTransfer.files[0]?.name || ''"
                                x-on:dragover.prevent
                                :class="isDragging ? 'border-indigo-500 bg-indigo-50' : 'border-slate-300 hover:border-indigo-400'"
                                class="relative border-2 border-dashed rounded-2xl p-8 text-center transition-all duration-200 cursor-pointer group">
                                
                                <input 
                                    id="file" 
                                    name="file" 
                                    type="file" 
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    required 
                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                    x-on:change="fileName = $event.target.files[0]?.name || ''">
                                
                                <div class="flex flex-col items-center">
                                    <div class="p-4 bg-indigo-100 rounded-2xl mb-4 group-hover:bg-indigo-200 transition-colors">
                                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                    </div>
                                    
                                    <template x-if="!fileName">
                                        <div>
                                            <p class="text-slate-700 font-medium">
                                                <span class="text-indigo-600">Click to upload</span> or drag and drop
                                            </p>
                                            <p class="text-sm text-slate-500 mt-1">PDF, JPG, PNG, DOC up to 10MB</p>
                                        </div>
                                    </template>
                                    
                                    <template x-if="fileName">
                                        <div class="flex items-center gap-2 px-4 py-2 bg-emerald-100 rounded-xl">
                                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-sm font-medium text-emerald-800" x-text="fileName"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label for="notes" class="flex items-center gap-2 text-sm font-medium text-slate-700 mb-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Notes
                            <span class="text-xs text-slate-400">(Optional)</span>
                        </label>
                        <textarea id="notes" name="notes" rows="3"
                            class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200 resize-none"
                            placeholder="Any additional information about this document...">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                        <a href="{{ route('loans.documents.index', $loan) }}" class="px-5 py-2.5 text-sm font-medium text-slate-700 hover:text-slate-900 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 border border-transparent rounded-xl font-semibold text-sm text-white shadow-sm hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Upload Document
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
