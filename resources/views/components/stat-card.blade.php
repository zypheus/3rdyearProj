@props([
    'title',
    'value',
    'icon' => null,
    'trend' => null,
    'trendUp' => true,
    'color' => 'indigo',
    'href' => null
])

@php
    $colorClasses = [
        'indigo' => 'from-indigo-500 to-indigo-600',
        'emerald' => 'from-emerald-500 to-emerald-600',
        'amber' => 'from-amber-500 to-amber-600',
        'red' => 'from-red-500 to-red-600',
        'blue' => 'from-blue-500 to-blue-600',
        'purple' => 'from-purple-500 to-purple-600',
        'slate' => 'from-slate-500 to-slate-600',
    ];
    $gradientClass = $colorClasses[$color] ?? $colorClasses['indigo'];
    $hasIconSlot = isset($__laravel_slots['icon']) && !$__laravel_slots['icon']->isEmpty();
@endphp

@if($href)
<a href="{{ $href }}" class="block">
@endif
<div {{ $attributes->merge(['class' => 'relative overflow-hidden bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-200 p-3 sm:p-4 md:p-6 card-hover']) }}>
    <div class="flex items-start justify-between gap-2">
        <div class="flex-1 min-w-0">
            <p class="text-xs sm:text-sm font-medium text-slate-500 uppercase tracking-wider truncate">{{ $title }}</p>
            <p class="mt-1 sm:mt-2 text-lg sm:text-xl md:text-3xl font-bold text-slate-900 truncate">{{ $value }}</p>
            @if($trend)
                <div class="mt-1 sm:mt-2 flex items-center gap-1 text-xs sm:text-sm">
                    @if($trendUp)
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-emerald-600 font-medium">{{ $trend }}</span>
                    @else
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-red-600 font-medium">{{ $trend }}</span>
                    @endif
                </div>
            @endif
        </div>
        @if($hasIconSlot || $icon)
            <div class="flex-shrink-0 p-2 sm:p-3 rounded-lg sm:rounded-xl bg-gradient-to-br {{ $gradientClass }} text-white shadow-lg">
                @if($hasIconSlot)
                    {{ $__laravel_slots['icon'] }}
                @else
                    {!! $icon !!}
                @endif
            </div>
        @endif
    </div>
    
    <!-- Decorative gradient -->
    <div class="absolute -bottom-2 -right-2 w-16 sm:w-24 h-16 sm:h-24 bg-gradient-to-br {{ $gradientClass }} opacity-5 rounded-full blur-2xl"></div>
</div>
@if($href)
</a>
@endif
