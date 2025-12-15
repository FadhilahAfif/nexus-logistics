<style>
    .nexus-lang-switch {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem; /* text-xs */
        text-transform: uppercase;
        letter-spacing: 0.3em;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        margin-right: 1rem;
        
        /* Light mode defaults */
        background-color: #f3f4f6; /* bg-gray-100 */
        border: 1px solid #e5e7eb; /* border-gray-200 */
        color: #6b7280; /* text-gray-500 */
    }

    /* Dark mode overrides - using .dark selector which Filament uses */
    .dark .nexus-lang-switch {
        background-color: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.1);
        color: #94a3b8; /* text-slate-400 */
    }
    
    .nexus-lang-inner {
        display: flex;
        gap: 0.5rem;
        font-weight: 600;
        letter-spacing: normal;
    }

    .nexus-lang-link {
        text-decoration: none;
        transition: color 0.2s;
    }
    
    .nexus-lang-link.active {
        color: #4f46e5; /* indigo-600 */
    }
    .dark .nexus-lang-link.active {
        color: #ffffff;
    }
    
    .nexus-lang-link.inactive {
        color: #9ca3af; /* gray-400 */
    }
    .nexus-lang-link.inactive:hover {
        color: #4b5563; /* gray-600 */
    }
    .dark .nexus-lang-link.inactive {
        color: #94a3b8; /* slate-400 */
    }
    .dark .nexus-lang-link.inactive:hover {
        color: #ffffff;
    }
</style>

<div class="nexus-lang-switch">
    <span>{{ __('home.lang_switch.label') }}</span>
    <div class="nexus-lang-inner">
        <a href="{{ request()->fullUrlWithQuery(['lang' => 'id']) }}"
           class="nexus-lang-link {{ app()->getLocale() === 'id' ? 'active' : 'inactive' }}">
            {{ __('home.lang_switch.id') }}
        </a>
        <span style="opacity: 0.4;">/</span>
        <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}"
           class="nexus-lang-link {{ app()->getLocale() === 'en' ? 'active' : 'inactive' }}">
            {{ __('home.lang_switch.en') }}
        </a>
    </div>
</div>
