<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Beranda') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col">

    <div class="site-bg flex-1 flex flex-col"
         x-data="{ slide: 0 }"
         x-init="setInterval(() => slide = (slide + 1) % 3, 5000)">

        <img src="{{ asset('images/pelapor-bg-1.jpg') }}" alt="" class="site-bg-slide" :style="{ opacity: slide === 0 ? 1 : 0 }">
        <img src="{{ asset('images/pelapor-bg-2.jpg') }}" alt="" class="site-bg-slide" :style="{ opacity: slide === 1 ? 1 : 0 }">
        <img src="{{ asset('images/pelapor-bg-3.jpg') }}" alt="" class="site-bg-slide" :style="{ opacity: slide === 2 ? 1 : 0 }">
        <div class="site-bg-overlay"></div>

        <div class="relative flex-1 flex flex-col" style="z-index: 10;">
            <x-header />

            <main class="flex-1">
                @if (session('success'))
                    <div class="max-w-4xl mx-auto px-4 pt-6">
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl px-4 py-3">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <x-footer />

</body>
</html>
