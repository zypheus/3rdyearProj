<svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    <!-- Outer circle with gradient -->
    <defs>
        <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#6366f1;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#8b5cf6;stop-opacity:1" />
        </linearGradient>
    </defs>
    
    <!-- Main circle background -->
    <circle cx="20" cy="20" r="18" fill="url(#logoGradient)" />
    
    <!-- Dollar/Peso sign stylized -->
    <path d="M20 8 L20 10 M20 30 L20 32" stroke="white" stroke-width="2" stroke-linecap="round"/>
    <path d="M16 14 C16 11 18 10 20 10 C22 10 24 11 24 13 C24 15 22 16 20 16 C18 16 16 17 16 19 C16 21 18 22 20 22 C22 22 24 21 24 18" 
          stroke="white" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
    
    <!-- Underline accent -->
    <path d="M14 26 L26 26" stroke="white" stroke-width="2" stroke-linecap="round" opacity="0.8"/>
    
    <!-- Small decorative dots -->
    <circle cx="10" cy="20" r="1.5" fill="white" opacity="0.6"/>
    <circle cx="30" cy="20" r="1.5" fill="white" opacity="0.6"/>
</svg>
