<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Lacak {{ $shipment->tracking_number }} - Nexus Logistics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .timeline-line { position: absolute; left: 15px; top: 10px; bottom: 0; width: 2px; background: #e2e8f0; z-index: 0; }
        .dark .timeline-line { background: #334155; }
    </style>
    <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-950 min-h-screen text-slate-800 dark:text-slate-200 transition-colors duration-300">

    <nav class="bg-slate-900 text-white p-4 shadow-lg sticky top-0 z-50">
        <div class="max-w-3xl mx-auto flex flex-wrap gap-3 justify-between items-center">
            <div class="font-bold text-xl tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">
                Nexus Logistics
            </div>
            <div class="flex items-center gap-4">
                <button id="theme-toggle" class="text-slate-400 hover:text-white transition focus:outline-none">
                    <!-- Sun icon -->
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                    <!-- Moon icon -->
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                </button>

                <div class="flex items-center gap-2 text-xs uppercase tracking-[0.3em] text-slate-500 bg-white/5 border border-white/10 rounded-full px-4 py-2 ml-4">
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
            <a href="/" class="text-sm text-slate-300 hover:text-white transition flex items-center gap-1 w-full md:w-auto mt-2 md:mt-0 justify-center md:justify-start">
                &larr; {{ __('tracking.nav_back') }}
            </a>
        </div>
    </nav>

    <div class="max-w-3xl mx-auto p-4 mt-6 pb-20">
        
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden mb-6 transition-colors duration-300">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('home.hero.form_label') }}</span>
                    <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">{{ $shipment->tracking_number }}</h1>
                </div>
                @php
                    $statusColors = [
                        'pending' => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300',
                        'picked_up' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
                        'in_transit' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
                        'delivered' => 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300',
                        'returned' => 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300',
                    ];
                    $colorClass = $statusColors[$shipment->status] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300';
                @endphp
                <div class="flex flex-col items-end gap-2">
                    <span class="px-4 py-2 rounded-full font-bold text-sm uppercase {{ $colorClass }}">
                        {{ $shipment->status_label }}
                    </span>
                    @if($shipment->estimated_delivery)
                        <div class="text-right">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Est. Delivery</p>
                            <p class="font-bold text-slate-700 dark:text-slate-200">{{ $shipment->estimated_delivery->format('d M Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="p-6 bg-slate-50/50 dark:bg-slate-800/50 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="relative pl-4 border-l-4 border-slate-300 dark:border-slate-600">
                    <p class="text-xs font-bold text-slate-400 uppercase mb-1">{{ __('tracking.sender') }}</p>
                    <p class="font-bold text-slate-700 dark:text-slate-200 text-lg">{{ $shipment->sender_name }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $shipment->sender_phone }}</p>
                </div>
                
                <div class="relative pl-4 border-l-4 border-blue-500">
                    <p class="text-xs font-bold text-slate-400 uppercase mb-1">{{ __('tracking.receiver') }}</p>
                    <p class="font-bold text-slate-700 dark:text-slate-200 text-lg">{{ $shipment->receiver_name }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $shipment->receiver_phone }}</p>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">{{ $shipment->receiver_address }}</p>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-slate-100 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 flex gap-6 text-sm text-slate-600 dark:text-slate-400 flex-wrap">
                <div>
                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ __('tracking.weight') }}:</span>
                    {{ filled($shipment->weight_kg) ? rtrim(rtrim(number_format($shipment->weight_kg, 2, ',', '.'), '0'), ',') . ' kg' : '-' }}
                </div>
                <div>
                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ __('tracking.price') }}:</span>
                    {{ filled($shipment->price) ? 'Rp ' . number_format($shipment->price, 0, ',', '.') : '-' }}
                </div>
            </div>
        </div>

        <h3 class="font-bold text-xl text-slate-800 dark:text-white mb-4 px-2">{{ __('tracking.history_title') }}</h3>
        
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 sm:p-8 transition-colors duration-300">
            <div class="relative">
                <div class="timeline-line"></div>

                <div class="space-y-8 relative z-10">
                    @forelse($shipment->updates as $index => $update)
                        <div class="relative pl-10">
                            <div class="absolute left-[6px] top-1 w-5 h-5 rounded-full border-4 border-white dark:border-slate-900 shadow-sm
                                {{ $index === 0 ? 'bg-blue-600 ring-4 ring-blue-100 dark:ring-blue-900' : 'bg-slate-300 dark:bg-slate-600' }}">
                            </div>

                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1">
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-slate-200 text-lg leading-none">
                                        {{ $update->status_label }}
                                    </h4>
                                    <p class="text-slate-600 dark:text-slate-400 font-medium mt-1">{{ $update->location }}</p>
                                    @if($update->description)
                                        <p class="text-sm text-slate-500 dark:text-slate-500 italic mt-1">"{{ $update->description }}"</p>
                                    @endif
                                    
                                    @if($update->proof_of_delivery)
                                        <div class="mt-3">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Proof of Delivery</p>
                                            <img src="{{ $update->proof_of_delivery_url }}" 
                                                 alt="Proof of Delivery" 
                                                 class="w-32 h-32 object-cover rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm hover:scale-105 transition-transform cursor-pointer"
                                                 onclick="window.open(this.src, '_blank')">
                                        </div>
                                    @endif
                                </div>
                                <div class="text-xs font-mono font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded self-start mt-2 sm:mt-0">
                                    {{ $update->happened_at->format('d M Y, H:i') }} WIB
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 italic">
                            {{ __('tracking.history_empty') }}
                        </div>
                    @endforelse

                    <div class="relative pl-10 opacity-70">
                        <div class="absolute left-[6px] top-1 w-5 h-5 rounded-full border-4 border-white dark:border-slate-900 bg-slate-300 dark:bg-slate-600"></div>
                        <div>
                            <h4 class="font-bold text-slate-700 dark:text-slate-300">{{ __('tracking.order_created') }}</h4>
                            <p class="text-sm text-slate-500 dark:text-slate-500">{{ __('tracking.order_created_desc') }}</p>
                            <div class="text-xs font-mono text-slate-400 mt-1">
                                {{ $shipment->created_at->format('d M Y, H:i') }} WIB
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="text-center mt-10 text-slate-400 text-sm">
            &copy; {{ date('Y') }} Nexus Logistics System. Real-time Tracking.
        </div>

    </div>
    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Change the icons inside the button based on previous settings
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        themeToggleBtn.addEventListener('click', function() {
            // toggle icons inside button
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // if set via local storage previously
            if (localStorage.theme === 'dark') {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        });
    </script>
</body>
</html>