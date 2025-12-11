<div class="flex items-center gap-3 mr-4">
    <a href="{{ request()->fullUrlWithQuery(['lang' => 'id']) }}"
       class="text-sm font-bold {{ app()->getLocale() === 'id' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
        ID
    </a>
    <span class="text-gray-300 dark:text-gray-600">|</span>
    <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}"
       class="text-sm font-bold {{ app()->getLocale() === 'en' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
        EN
    </a>
</div>
