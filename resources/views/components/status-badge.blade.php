@props([
    'status',
    'size' => 'md'
])

@php
    $statusConfig = [
        // Loan statuses
        'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'dot' => 'bg-amber-500', 'label' => 'Pending'],
        'under_review' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'dot' => 'bg-blue-500', 'label' => 'Under Review'],
        'approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'dot' => 'bg-emerald-500', 'label' => 'Approved'],
        'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'dot' => 'bg-red-500', 'label' => 'Rejected'],
        'active' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800', 'dot' => 'bg-indigo-500', 'label' => 'Active'],
        'completed' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-800', 'dot' => 'bg-slate-500', 'label' => 'Completed'],
        'defaulted' => ['bg' => 'bg-red-200', 'text' => 'text-red-900', 'dot' => 'bg-red-600', 'label' => 'Defaulted'],
        
        // Payment statuses
        'confirmed' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'dot' => 'bg-emerald-500', 'label' => 'Confirmed'],
        
        // Document statuses
        'verified' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'dot' => 'bg-emerald-500', 'label' => 'Verified'],
        'unverified' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'dot' => 'bg-amber-500', 'label' => 'Unverified'],
        
        // Default
        'default' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-800', 'dot' => 'bg-slate-500', 'label' => 'Unknown'],
    ];

    $config = $statusConfig[$status] ?? $statusConfig['default'];
    $label = $config['label'] ?? ucfirst(str_replace('_', ' ', $status));
    
    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-1 text-xs',
        'lg' => 'px-3 py-1.5 text-sm',
    ];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    
    $dotSizeClasses = [
        'sm' => 'w-1.5 h-1.5',
        'md' => 'w-2 h-2',
        'lg' => 'w-2.5 h-2.5',
    ];
    $dotSizeClass = $dotSizeClasses[$size] ?? $dotSizeClasses['md'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 font-semibold rounded-full {$config['bg']} {$config['text']} {$sizeClass}"]) }}>
    <span class="{{ $dotSizeClass }} rounded-full {{ $config['dot'] }}"></span>
    {{ $label }}
</span>
