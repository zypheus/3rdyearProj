<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">
                    Audit Log
                </h2>
                <p class="mt-1 text-sm text-slate-500">Track system activities and user actions</p>
            </div>
            <a href="{{ route('reports.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Reports
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Filters --}}
            <x-card class="mb-6">
                <form method="GET" action="{{ route('reports.audit') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Action
                        </label>
                        <select name="action" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                            <option value="">All Actions</option>
                            @foreach ($actions as $action)
                                <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $action)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            User
                        </label>
                        <select name="user_id" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                            <option value="">All Users</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            From Date
                        </label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" 
                            class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            To Date
                        </label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" 
                            class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl font-medium hover:from-indigo-700 hover:to-indigo-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('reports.audit') }}" class="px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-700 font-medium hover:bg-slate-50 transition-colors">
                            Clear
                        </a>
                    </div>
                </form>
            </x-card>

            {{-- Audit Log Table --}}
            <x-card title="Activity Log">
                <div class="overflow-x-auto -mx-6 px-6">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Timestamp</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">User</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Action</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Target</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Description</th>
                                <th class="py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($logs as $log)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-slate-900">{{ $log->created_at->format('M d, Y') }}</div>
                                        <div class="text-xs text-slate-500">{{ $log->created_at->format('H:i:s') }}</div>
                                    </td>
                                    <td class="py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-200 text-slate-600 text-xs font-semibold">
                                                {{ strtoupper(substr($log->user?->name ?? 'S', 0, 1)) }}
                                            </span>
                                            <div>
                                                <div class="text-sm font-medium text-slate-900">{{ $log->user?->name ?? 'System' }}</div>
                                                @if($log->user?->email)
                                                    <div class="text-xs text-slate-500">{{ $log->user->email }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 whitespace-nowrap">
                                        @php
                                            $actionClass = match(true) {
                                                str_contains($log->action, 'create') || str_contains($log->action, 'register') => 'bg-emerald-100 text-emerald-800',
                                                str_contains($log->action, 'update') || str_contains($log->action, 'approve') => 'bg-blue-100 text-blue-800',
                                                str_contains($log->action, 'delete') || str_contains($log->action, 'reject') => 'bg-red-100 text-red-800',
                                                str_contains($log->action, 'login') || str_contains($log->action, 'logout') => 'bg-purple-100 text-purple-800',
                                                default => 'bg-slate-100 text-slate-800'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $actionClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                        </span>
                                    </td>
                                    <td class="py-4 whitespace-nowrap">
                                        @if ($log->auditable_type)
                                            <span class="inline-flex items-center px-2 py-1 bg-slate-100 rounded-lg text-xs text-slate-700">
                                                {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-4">
                                        <p class="text-sm text-slate-600 max-w-md truncate" title="{{ $log->description }}">
                                            {{ Str::limit($log->description, 80) }}
                                        </p>
                                    </td>
                                    <td class="py-4 whitespace-nowrap">
                                        <span class="text-sm text-slate-500 font-mono">{{ $log->ip_address ?? '-' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center">
                                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 mb-3">
                                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-slate-500">No audit logs found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-6 pt-4 border-t border-slate-200">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            </x-card>

            {{-- Info Box --}}
            <div class="mt-6 p-5 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl">
                <div class="flex gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-blue-900">About Audit Logs</h3>
                        <p class="mt-1 text-sm text-blue-700">
                            Audit logs track all significant actions in the system including user authentication, loan processing, document verification, and payment recording. This data is retained for compliance and security purposes.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
