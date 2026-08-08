@extends('layouts.app')

@section('content')

    {{-- HERO SECTION --}}
    <div class="bg-gradient-to-b from-slate-50 to-white">
        <div class="mx-auto max-w-7xl px-6 py-20 sm:py-28 lg:px-8">
            <div class="grid gap-14 lg:grid-cols-2 lg:items-center">

                {{-- Left: Headline --}}
                <div>
                    <p class="mb-5 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-1.5 text-sm font-semibold text-emerald-700">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Client Collaboration Portal
                    </p>

                    <h1 class="text-4xl font-bold leading-tight tracking-tight text-slate-900 sm:text-5xl lg:text-[3.25rem]">
                        Deliver work, get approvals, and get paid — all in one place.
                    </h1>

                    <p class="mt-6 max-w-xl text-lg leading-8 text-slate-600">
                        The all-in-one portal for agencies and freelancers to share deliverables, collect client approvals, chat, and invoice — without switching between five different tools.
                    </p>

                    <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-7 py-3.5 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700">
                            Get Started Free
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-7 py-3.5 text-sm font-semibold text-slate-800 transition hover:border-slate-400 hover:bg-slate-50">
                            Sign In
                        </a>
                    </div>

                    <div class="mt-10 flex items-center gap-6 text-sm text-slate-500">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            No credit card required
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            Set up in minutes
                        </div>
                    </div>
                </div>

                {{-- Right: Visual Card --}}
                <div class="relative">
                    <div class="overflow-hidden rounded-3xl bg-slate-950 text-white shadow-2xl shadow-slate-900/20">
                        <div class="bg-gradient-to-br from-slate-900 via-slate-950 to-slate-800 px-8 py-10">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-400">Project Overview</p>
                            <h2 class="mt-3 text-2xl font-semibold">Homepage Redesign</h2>
                            <p class="mt-2 text-sm text-slate-400">Client: Bright Retail Co.</p>

                            <div class="mt-6 flex items-center gap-3">
                                <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-400">Waiting for Approval</span>
                            </div>
                        </div>

                        <div class="space-y-4 p-6">
                            <div class="flex items-center justify-between rounded-2xl bg-slate-900 px-5 py-4">
                                <div>
                                    <p class="text-sm font-medium text-white">Homepage_v3.fig</p>
                                    <p class="text-xs text-slate-400">Uploaded 2 hours ago</p>
                                </div>
                                <span class="text-xs font-semibold text-emerald-400">✓ Approved</span>
                            </div>

                            <div class="flex items-center justify-between rounded-2xl bg-slate-900 px-5 py-4">
                                <div>
                                    <p class="text-sm font-medium text-white">Invoice #0142</p>
                                    <p class="text-xs text-slate-400">$1,200.00 due Aug 12</p>
                                </div>
                                <span class="text-xs font-semibold text-amber-400">Pending</span>
                            </div>

                            <div class="flex items-center justify-between rounded-2xl bg-slate-900 px-5 py-4">
                                <div>
                                    <p class="text-sm font-medium text-white">3 new messages</p>
                                    <p class="text-xs text-slate-400">From client</p>
                                </div>
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Floating badge --}}
                    <div class="absolute -bottom-6 -left-6 hidden rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-xl sm:block">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Payment Received</p>
                        <p class="mt-1 text-lg font-bold text-emerald-600">$1,200.00</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- FEATURES SECTION --}}
    <div class="border-t border-slate-100 bg-white">
        <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-emerald-600">Everything you need</p>
                <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">One portal, built for agencies and their clients</h2>
                <p class="mt-4 text-base leading-7 text-slate-600">Role-based access keeps agency owners in control while giving clients a clean, simple experience.</p>
            </div>

            <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">

                <div class="rounded-2xl border border-slate-200 p-6 transition hover:border-emerald-200 hover:shadow-md">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5h18M3 12h18M3 16.5h18" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-slate-900">Projects</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Create, assign, and track active work across every client engagement.</p>
                </div>

                <div class="rounded-2xl border border-slate-200 p-6 transition hover:border-emerald-200 hover:shadow-md">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-slate-900">Deliverables & Approvals</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Upload files, collect one-click approvals, and keep full version history.</p>
                </div>

                <div class="rounded-2xl border border-slate-200 p-6 transition hover:border-emerald-200 hover:shadow-md">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-slate-900">Invoicing</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Send invoices and get paid online without leaving the dashboard.</p>
                </div>

                <div class="rounded-2xl border border-slate-200 p-6 transition hover:border-emerald-200 hover:shadow-md">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.556-4.03 8.25-9 8.25a9.76 9.76 0 01-3.618-.687L3 21l1.395-3.72C3.512 16.06 3 14.578 3 12.75 3 8.194 7.03 4.5 12 4.5s9 3.694 9 7.5z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-slate-900">Messaging</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Keep every project conversation recorded and organized in one thread.</p>
                </div>

            </div>
        </div>
    </div>

    {{-- CTA BANNER --}}
    <div class="bg-slate-950">
        <div class="mx-auto max-w-7xl px-6 py-16 text-center lg:px-8">
            <h2 class="text-3xl font-bold text-white sm:text-4xl">Ready to bring your client work into one place?</h2>
            <p class="mx-auto mt-4 max-w-xl text-base text-slate-400">Join agencies and freelancers who've ditched scattered emails and spreadsheets for a single, branded client portal.</p>
            <div class="mt-8">
                <a href="{{ route('register') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-8 py-3.5 text-sm font-semibold text-white shadow-lg shadow-emerald-500/20 transition hover:bg-emerald-400">
                    Create Your Free Account
                </a>
            </div>
        </div>
    </div>

@endsection