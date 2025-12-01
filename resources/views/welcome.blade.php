<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Logistics - Global Tracking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-950 text-white min-h-screen">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-32 -left-20 w-96 h-96 bg-blue-500/40 rounded-full blur-3xl"></div>
        <div class="absolute top-10 right-0 w-[28rem] h-[28rem] bg-purple-700/40 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10">
        <div class="max-w-6xl mx-auto px-6 pt-6 flex justify-end">
            <div class="flex items-center gap-2 text-xs uppercase tracking-[0.3em] text-slate-500 bg-white/5 border border-white/10 rounded-full px-4 py-2">
                <span>{{ __('home.lang_switch.label') }}</span>
                <div class="flex gap-2 font-semibold tracking-normal">
                    <a href="{{ request()->fullUrlWithQuery(['lang' => 'id']) }}"
                       class="{{ app()->getLocale() === 'id' ? 'text-white' : 'text-slate-400 hover:text-white' }}">
                        {{ __('home.lang_switch.id') }}
                    </a>
                    <span class="opacity-40">/</span>
                    <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}"
                       class="{{ app()->getLocale() === 'en' ? 'text-white' : 'text-slate-400 hover:text-white' }}">
                        {{ __('home.lang_switch.en') }}
                    </a>
                </div>
            </div>
        </div>

        <header class="max-w-6xl mx-auto px-6 pt-10 pb-10 grid gap-10 lg:grid-cols-2 items-center">
            <div>
                <p class="uppercase text-sm tracking-[0.3em] text-blue-300 mb-4">{{ __('home.hero.tagline') }}</p>
                <h1 class="text-4xl lg:text-5xl font-black leading-tight text-white">
                    {{ __('home.hero.title') }}
                </h1>
                <p class="text-slate-300 text-lg mt-6">
                    {{ __('home.hero.subtitle') }}
                </p>
                <div class="mt-8 flex flex-wrap gap-4 text-sm text-slate-400">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        {{ __('home.hero.bullets.infra') }}
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                        {{ __('home.hero.bullets.sla') }}
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                        {{ __('home.hero.bullets.api') }}
                    </div>
                </div>
            </div>

            <div class="bg-slate-900/70 border border-white/10 rounded-3xl p-8 shadow-2xl backdrop-blur">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <p class="text-sm text-slate-400">{{ __('home.hero.form_title') }}</p>
                        <h2 class="text-2xl font-bold text-white">{{ __('home.hero.form_subtitle') }}</h2>
                    </div>
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-500/10 text-green-300 border border-green-500/30">
                        {{ __('home.hero.live_badge') }}
                    </span>
                </div>

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-500/15 border border-red-500/40 rounded-2xl text-red-200 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('track') }}" method="GET" class="space-y-4">
                    <label class="text-xs uppercase tracking-[0.3em] text-slate-400">{{ __('home.hero.form_label') }}</label>
                    <div class="flex gap-2 bg-white/5 border border-white/10 rounded-2xl p-2 focus-within:border-blue-400/70 transition">
                        <input type="text" name="tracking_number" placeholder="{{ __('home.hero.form_placeholder') }}" 
                               class="bg-transparent text-white w-full px-4 py-3 focus:outline-none placeholder-slate-500 font-semibold text-lg" required>
                        <button type="submit" class="bg-gradient-to-r from-blue-500 to-indigo-500 hover:opacity-90 text-white px-5 py-3 rounded-2xl font-bold transition">
                            {{ __('home.hero.form_button') }}
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-xs text-slate-500">
                    {{ __('home.hero.form_consent') }}
                </div>

                <div class="mt-8">
                    <p class="text-slate-400 text-sm mb-3">{{ __('home.hero.internal_access') }}</p>
                    <a href="/admin" class="inline-flex items-center gap-2 text-blue-300 hover:text-white transition font-semibold">
                        {{ __('home.hero.admin_login') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </header>

        <section class="max-w-6xl mx-auto px-6 grid gap-6 md:grid-cols-4">
            @php
                $total = $metrics['total'] ?? 0;
                $delivered = $metrics['delivered'] ?? 0;
                $inTransit = $metrics['in_transit'] ?? 0;
                $pending = $metrics['pending'] ?? 0;
            @endphp
            <div class="bg-white/5 border border-white/5 rounded-2xl p-6">
                <p class="text-sm text-slate-400">{{ __('home.cards.total.title') }}</p>
                <p class="text-3xl font-black mt-2">{{ number_format($total) }}</p>
                <p class="text-xs text-slate-500 mt-2">{{ __('home.cards.total.desc') }}</p>
            </div>
            <div class="bg-white/5 border border-white/5 rounded-2xl p-6">
                <p class="text-sm text-slate-400">{{ __('home.cards.delivered.title') }}</p>
                <p class="text-3xl font-black text-green-300 mt-2">{{ number_format($delivered) }}</p>
                <p class="text-xs text-slate-500 mt-2">{{ $total ? round(($delivered / max($total,1)) * 100) : 0 }}% {{ __('home.cards.delivered.desc') }}</p>
            </div>
            <div class="bg-white/5 border border-white/5 rounded-2xl p-6">
                <p class="text-sm text-slate-400">{{ __('home.cards.in_transit.title') }}</p>
                <p class="text-3xl font-black text-amber-300 mt-2">{{ number_format($inTransit) }}</p>
                <p class="text-xs text-slate-500 mt-2">{{ __('home.cards.in_transit.desc') }}</p>
            </div>
            <div class="bg-white/5 border border-white/5 rounded-2xl p-6">
                <p class="text-sm text-slate-400">{{ __('home.cards.pending.title') }}</p>
                <p class="text-3xl font-black text-slate-200 mt-2">{{ number_format($pending) }}</p>
                <p class="text-xs text-slate-500 mt-2">{{ __('home.cards.pending.desc') }}</p>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-6 mt-16 grid gap-8 md:grid-cols-3">
            @foreach(trans('home.pillars') as $index => $pillar)
                @php
                    $colors = ['purple', 'blue', 'emerald'];
                    $color = $colors[$index] ?? 'indigo';
                @endphp
                <div class="bg-gradient-to-br from-{{ $color }}-600/30 to-slate-900 rounded-3xl border border-{{ $color }}-500/20 p-6">
                    <div class="text-sm uppercase tracking-[0.3em] text-{{ $color }}-200 mb-4">{{ $pillar['tag'] }}</div>
                    <h3 class="text-2xl font-bold mb-3">{{ $pillar['title'] }}</h3>
                    <p class="text-slate-300 text-sm leading-relaxed">
                        {{ $pillar['body'] }}
                    </p>
                </div>
            @endforeach
        </section>



        <footer class="max-w-6xl mx-auto px-6 mt-24 pb-12 flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-t border-white/5 pt-8 text-sm text-slate-500">
            <div>
                {{ __('home.footer.copy', ['year' => date('Y')]) }}
            </div>
            <div class="flex flex-wrap gap-4 text-xs uppercase tracking-[0.2em] text-slate-500">
                @foreach(trans('home.footer.tags') as $tag)
                    <span>{{ $tag }}</span>
                @endforeach
            </div>
        </footer>
    </div>
</body>
</html>