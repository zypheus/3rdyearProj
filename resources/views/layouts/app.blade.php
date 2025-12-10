<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LoanEase') }} - Loan Management System</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            
            /* Custom scrollbar */
            ::-webkit-scrollbar { width: 8px; height: 8px; }
            ::-webkit-scrollbar-track { background: #f1f5f9; }
            ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
            ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
            
            /* Smooth transitions */
            .transition-smooth { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
            
            /* Gradient backgrounds */
            .bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
            .bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
            .bg-gradient-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
            .bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
            
            /* Card hover effects */
            .card-hover { transition: all 0.3s ease; }
            .card-hover:hover { transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
            
            /* Shimmer effect for loading states */
            @keyframes shimmer {
                0% { background-position: -200px 0; }
                100% { background-position: calc(200px + 100%) 0; }
            }
            .shimmer {
                animation: shimmer 1.5s ease-in-out infinite;
                background: linear-gradient(90deg, #f0f0f0 0px, #e0e0e0 40px, #f0f0f0 80px);
                background-size: 200px;
            }

            /* Pulse animation for notifications */
            @keyframes pulse-ring {
                0% { transform: scale(0.8); opacity: 1; }
                100% { transform: scale(2); opacity: 0; }
            }
            .pulse-ring::before {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: 50%;
                background: currentColor;
                animation: pulse-ring 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-50">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white border-b border-slate-200 sticky top-16 z-30 backdrop-blur-sm bg-white/95">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash Messages with SweetAlert2 -->
            @if (session('success') || session('error') || session('warning') || session('info'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        @if (session('success'))
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: "{{ session('success') }}",
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 4000,
                                timerProgressBar: true,
                                customClass: {
                                    popup: 'rounded-xl shadow-lg'
                                }
                            });
                        @endif
                        @if (session('error'))
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: "{{ session('error') }}",
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 5000,
                                timerProgressBar: true,
                                customClass: {
                                    popup: 'rounded-xl shadow-lg'
                                }
                            });
                        @endif
                        @if (session('warning'))
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning!',
                                text: "{{ session('warning') }}",
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 5000,
                                timerProgressBar: true,
                                customClass: {
                                    popup: 'rounded-xl shadow-lg'
                                }
                            });
                        @endif
                        @if (session('info'))
                            Swal.fire({
                                icon: 'info',
                                title: 'Info',
                                text: "{{ session('info') }}",
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 4000,
                                timerProgressBar: true,
                                customClass: {
                                    popup: 'rounded-xl shadow-lg'
                                }
                            });
                        @endif
                    });
                </script>
            @endif

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200 mt-auto">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="flex items-center gap-2">
                            <x-application-logo class="h-6 w-6 text-indigo-600" />
                            <span class="text-sm text-slate-600">LoanEase &copy; {{ date('Y') }}. All rights reserved.</span>
                        </div>
                        <div class="flex items-center gap-6 text-sm text-slate-500">
                            <span>Loan Management System</span>
                            <span class="hidden sm:inline">•</span>
                            <span class="hidden sm:inline text-slate-400">v1.0.0</span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <!-- SweetAlert2 Helper Functions -->
        <script>
            // Confirm delete action with SweetAlert
            function confirmDelete(event, title = 'Are you sure?', text = 'This action cannot be undone.') {
                event.preventDefault();
                const form = event.target.closest('form');
                
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-xl px-6 py-2.5 font-semibold',
                        cancelButton: 'rounded-xl px-6 py-2.5 font-semibold'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }

            // Confirm action with SweetAlert (generic)
            function confirmAction(event, title = 'Confirm Action', text = 'Are you sure you want to proceed?', confirmText = 'Yes, proceed', icon = 'question') {
                event.preventDefault();
                const form = event.target.closest('form');
                
                Swal.fire({
                    title: title,
                    text: text,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-xl px-6 py-2.5 font-semibold',
                        cancelButton: 'rounded-xl px-6 py-2.5 font-semibold'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }

            // Confirm rejection with SweetAlert
            function confirmReject(event) {
                event.preventDefault();
                const form = event.target.closest('form');
                
                Swal.fire({
                    title: 'Reject Loan Application?',
                    text: 'This will reject the loan application. Please ensure you have provided a reason.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, reject it',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-xl px-6 py-2.5 font-semibold',
                        cancelButton: 'rounded-xl px-6 py-2.5 font-semibold'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }

            // Confirm disbursement with SweetAlert
            function confirmDisburse(event) {
                event.preventDefault();
                const form = event.target.closest('form');
                
                Swal.fire({
                    title: 'Disburse Funds?',
                    text: 'This will disburse funds and activate the loan. This action cannot be undone.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, disburse funds',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-xl px-6 py-2.5 font-semibold',
                        cancelButton: 'rounded-xl px-6 py-2.5 font-semibold'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        </script>
    </body>
</html>
