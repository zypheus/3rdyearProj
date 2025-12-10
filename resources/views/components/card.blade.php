@props([
    'title' => null,
    'subtitle' => null,
    'padding' => true,
    'hover' => false,
    'actions' => null
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-200 overflow-hidden' . ($hover ? ' card-hover' : '')]) }}>
    @if($title || $actions)
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-100 flex items-center justify-between gap-2">
            <div class="min-w-0">
                @if($title)
                    <h3 class="text-base sm:text-lg font-semibold text-slate-900 truncate">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate">{{ $subtitle }}</p>
                @endif
            </div>
            @if($actions)
                <div class="flex items-center gap-2 flex-shrink-0">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif
    
    <div class="{{ $padding ? 'p-4 sm:p-6' : '' }}">
        {{ $slot }}
    </div>
</div>
