@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="min-h-[80vh] flex flex-col items-center justify-center px-4 py-10">
    <div class="w-full max-w-sm space-y-5">

        {{-- Logo --}}
        <div class="text-center space-y-0">
            <div class="w-28 h-28 flex items-center justify-center mx-auto">
                <img src="{{ asset('images/logo_rshd.png') }}" alt="SI-ADUAN" class="h-28 w-auto object-contain drop-shadow-md">
            </div>
            <div class="-mt-3">
                <div class="text-xl font-extrabold text-navy drop-shadow-sm">SI-ADUAN</div>
                <div class="text-sm text-white/90 drop-shadow-sm">Sistem Pengaduan Masyarakat 
                    <br> RSUD H. Damanhuri Barabai</div>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('login.store') }}" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-7 space-y-5">
            @csrf
            <div class="text-base font-bold text-navy text-center">Masuk ke Panel</div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-navy mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="email@rsud-damanhuri.id"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#1565C0] transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-navy mb-1.5">Password</label>
                    <input type="password" name="password" required
                           placeholder="••••••••" 
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#1565C0] transition">
                </div>
            </div>

            @if ($errors->any())
                <div class="text-xs text-red-600 bg-red-50 border border-red-100 rounded-xl px-4 py-2.5">
                    {{ $errors->first() }}
                </div>
            @endif

            <button type="submit"
                    class="w-full grad-btn text-white font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2">
                Masuk
            </button>
        </form>
    </div>
</div>
@endsection
