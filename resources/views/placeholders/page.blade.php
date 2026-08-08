@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl rounded-3xl border border-slate-200 bg-white p-10 shadow-sm">
        <h1 class="text-3xl font-semibold text-slate-900">{{ $title }}</h1>
        <p class="mt-4 text-slate-600">{{ $message }}</p>
    </div>
@endsection
