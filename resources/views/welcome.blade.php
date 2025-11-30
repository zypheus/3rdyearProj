<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LoanEase - Modern Loan Management System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #a855f7 100%);
        }
        .feature-card:hover {
            transform: translateY(-4px);
        }
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .blob {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            animation: morph 8s ease-in-out infinite;
        }
        @keyframes morph {
            0%, 100% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
            50% { border-radius: 70% 30% 30% 70% / 70% 70% 30% 30%; }
        }
    </style>
</head>
<body class="antialiased bg-slate-50">
    {{-- Navigation --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">LoanEase</span>
                </div>

                {{-- Navigation Links --}}
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">Features</a>
                    <a href="#how-it-works" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">How it Works</a>
                    <a href="#about" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">About</a>
                </div>

                {{-- Auth Buttons --}}
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:shadow-indigo-500/40 transition-all duration-200">
                            Go to Dashboard
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-indigo-600 transition-colors">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:shadow-indigo-500/40 transition-all duration-200">
                            Get Started
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden pt-16">
        {{-- Background Elements --}}
        <div class="absolute inset-0 hero-gradient opacity-5"></div>
        <div class="absolute top-20 left-10 w-72 h-72 bg-indigo-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 floating"></div>
        <div class="absolute top-40 right-10 w-72 h-72 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 floating" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-20 left-1/2 w-72 h-72 bg-pink-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 floating" style="animation-delay: 4s;"></div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 border border-indigo-100 rounded-full mb-8">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-sm font-medium text-indigo-700">Trusted by thousands of borrowers</span>
            </div>
            
            {{-- Main Heading --}}
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-slate-900 leading-tight mb-6">
                Simplify Your
                <span class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">Loan Journey</span>
            </h1>
            
            <p class="max-w-2xl mx-auto text-xl text-slate-600 mb-10">
                Apply for loans, track payments, and manage your finances with ease. LoanEase provides a seamless experience from application to completion.
            </p>
            
            {{-- CTA Buttons --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-lg font-semibold rounded-2xl shadow-xl shadow-indigo-500/30 hover:shadow-2xl hover:shadow-indigo-500/40 hover:scale-105 transition-all duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-lg font-semibold rounded-2xl shadow-xl shadow-indigo-500/30 hover:shadow-2xl hover:shadow-indigo-500/40 hover:scale-105 transition-all duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Start Your Application
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-slate-700 text-lg font-semibold rounded-2xl border-2 border-slate-200 hover:border-indigo-300 hover:text-indigo-600 transition-all duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Sign In to Account
                    </a>
                @endauth
            </div>

            {{-- Stats --}}
            <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl font-bold text-slate-900">₱50M+</div>
                    <div class="text-sm text-slate-500 mt-1">Loans Processed</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-slate-900">5,000+</div>
                    <div class="text-sm text-slate-500 mt-1">Happy Borrowers</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-slate-900">24hrs</div>
                    <div class="text-sm text-slate-500 mt-1">Avg. Approval Time</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-slate-900">99.9%</div>
                    <div class="text-sm text-slate-500 mt-1">Uptime</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 bg-indigo-100 text-indigo-700 text-sm font-semibold rounded-full mb-4">Features</span>
                <h2 class="text-4xl font-bold text-slate-900 mb-4">Everything You Need</h2>
                <p class="max-w-2xl mx-auto text-lg text-slate-600">Powerful features to help you manage your loans efficiently</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Feature 1 --}}
                <div class="feature-card group p-8 bg-gradient-to-br from-slate-50 to-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Easy Application</h3>
                    <p class="text-slate-600">Submit loan applications online with a simple, intuitive form. Upload required documents instantly.</p>
                </div>

                {{-- Feature 2 --}}
                <div class="feature-card group p-8 bg-gradient-to-br from-slate-50 to-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Quick Approval</h3>
                    <p class="text-slate-600">Get fast decisions on your loan application. Our officers review applications promptly.</p>
                </div>

                {{-- Feature 3 --}}
                <div class="feature-card group p-8 bg-gradient-to-br from-slate-50 to-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Payment Tracking</h3>
                    <p class="text-slate-600">Track your payments easily. View payment schedule, history, and remaining balance at a glance.</p>
                </div>

                {{-- Feature 4 --}}
                <div class="feature-card group p-8 bg-gradient-to-br from-slate-50 to-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 bg-gradient-to-br from-rose-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Real-time Reports</h3>
                    <p class="text-slate-600">Access comprehensive reports and analytics to understand your loan status and payment history.</p>
                </div>

                {{-- Feature 5 --}}
                <div class="feature-card group p-8 bg-gradient-to-br from-slate-50 to-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Document Upload</h3>
                    <p class="text-slate-600">Upload and manage required documents securely. Track verification status in real-time.</p>
                </div>

                {{-- Feature 6 --}}
                <div class="feature-card group p-8 bg-gradient-to-br from-slate-50 to-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Secure & Private</h3>
                    <p class="text-slate-600">Your data is protected with industry-standard security. Complete audit trail for transparency.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- How It Works Section --}}
    <section id="how-it-works" class="py-24 bg-gradient-to-br from-slate-50 to-indigo-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 bg-indigo-100 text-indigo-700 text-sm font-semibold rounded-full mb-4">Process</span>
                <h2 class="text-4xl font-bold text-slate-900 mb-4">How It Works</h2>
                <p class="max-w-2xl mx-auto text-lg text-slate-600">Get your loan in just 4 simple steps</p>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                {{-- Step 1 --}}
                <div class="relative text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-indigo-500/30">
                        <span class="text-2xl font-bold text-white">1</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Register</h3>
                    <p class="text-sm text-slate-600">Create your account in minutes with basic information</p>
                    {{-- Connector Line --}}
                    <div class="hidden md:block absolute top-8 left-[60%] w-[80%] h-0.5 bg-gradient-to-r from-indigo-300 to-purple-300"></div>
                </div>

                {{-- Step 2 --}}
                <div class="relative text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-purple-500/30">
                        <span class="text-2xl font-bold text-white">2</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Apply</h3>
                    <p class="text-sm text-slate-600">Fill out the loan application form and upload documents</p>
                    <div class="hidden md:block absolute top-8 left-[60%] w-[80%] h-0.5 bg-gradient-to-r from-purple-300 to-pink-300"></div>
                </div>

                {{-- Step 3 --}}
                <div class="relative text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-pink-500/30">
                        <span class="text-2xl font-bold text-white">3</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Get Approved</h3>
                    <p class="text-sm text-slate-600">Our team reviews and approves your application quickly</p>
                    <div class="hidden md:block absolute top-8 left-[60%] w-[80%] h-0.5 bg-gradient-to-r from-pink-300 to-amber-300"></div>
                </div>

                {{-- Step 4 --}}
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-amber-500/30">
                        <span class="text-2xl font-bold text-white">4</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Receive Funds</h3>
                    <p class="text-sm text-slate-600">Get your loan amount and start making payments</p>
                </div>
            </div>
        </div>
    </section>

    {{-- About Section --}}
    <section id="about" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="inline-block px-4 py-1.5 bg-indigo-100 text-indigo-700 text-sm font-semibold rounded-full mb-4">About LoanEase</span>
                    <h2 class="text-4xl font-bold text-slate-900 mb-6">We Make Borrowing Simple and Transparent</h2>
                    <p class="text-lg text-slate-600 mb-6">
                        LoanEase is a modern loan management system designed to streamline the entire lending process. From application to final payment, we provide a seamless experience for both borrowers and administrators.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-slate-600">Transparent terms with no hidden fees</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-slate-600">Fast approval process within 24 hours</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-slate-600">Flexible payment options to suit your needs</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-slate-600">Dedicated support throughout your loan journey</span>
                        </li>
                    </ul>
                </div>
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 rounded-3xl transform rotate-3"></div>
                    <div class="relative bg-white p-8 rounded-3xl shadow-2xl">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="p-6 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl text-center">
                                <div class="text-3xl font-bold text-indigo-600 mb-1">100%</div>
                                <div class="text-sm text-slate-600">Online Process</div>
                            </div>
                            <div class="p-6 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl text-center">
                                <div class="text-3xl font-bold text-emerald-600 mb-1">24/7</div>
                                <div class="text-sm text-slate-600">System Access</div>
                            </div>
                            <div class="p-6 bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl text-center">
                                <div class="text-3xl font-bold text-amber-600 mb-1">3 Years</div>
                                <div class="text-sm text-slate-600">Max Loan Term</div>
                            </div>
                            <div class="p-6 bg-gradient-to-br from-rose-50 to-pink-50 rounded-2xl text-center">
                                <div class="text-3xl font-bold text-rose-600 mb-1">₱500K</div>
                                <div class="text-sm text-slate-600">Max Loan Amount</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-24 hero-gradient">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">Ready to Get Started?</h2>
            <p class="text-xl text-white/80 mb-10">Join thousands of satisfied borrowers who trust LoanEase for their financial needs.</p>
            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-white text-indigo-600 text-lg font-semibold rounded-2xl shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Go to Your Dashboard
                </a>
            @else
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-white text-indigo-600 text-lg font-semibold rounded-2xl shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Create Free Account
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-white/10 text-white text-lg font-semibold rounded-2xl border-2 border-white/30 hover:bg-white/20 transition-all duration-200">
                        Sign In Instead
                    </a>
                </div>
            @endauth
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-slate-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12">
                {{-- Brand --}}
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold">LoanEase</span>
                    </div>
                    <p class="text-slate-400 mb-6 max-w-md">A modern loan management system designed to simplify the borrowing experience for everyone.</p>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-slate-400 hover:text-white transition-colors">Features</a></li>
                        <li><a href="#how-it-works" class="text-slate-400 hover:text-white transition-colors">How It Works</a></li>
                        <li><a href="#about" class="text-slate-400 hover:text-white transition-colors">About</a></li>
                    </ul>
                </div>

                {{-- Account --}}
                <div>
                    <h4 class="font-semibold mb-4">Account</h4>
                    <ul class="space-y-2">
                        @auth
                            <li><a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-white transition-colors">Dashboard</a></li>
                            <li><a href="{{ route('profile.edit') }}" class="text-slate-400 hover:text-white transition-colors">Profile</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="text-slate-400 hover:text-white transition-colors">Sign In</a></li>
                            <li><a href="{{ route('register') }}" class="text-slate-400 hover:text-white transition-colors">Register</a></li>
                        @endauth
                    </ul>
                </div>
            </div>

            <div class="border-t border-slate-800 mt-12 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} LoanEase. All rights reserved.</p>
                <p class="text-slate-500 text-sm">Built with ❤️ using Laravel & Tailwind CSS</p>
            </div>
        </div>
    </footer>
</body>
</html>
