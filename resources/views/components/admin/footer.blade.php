<footer class="bg-white border-t border-gray-200 px-6 py-4">
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-600">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600">
                Version: {{ config('app.version', '1.0.0') }}
            </span>
            <a href="#" class="text-sm text-blue-600 hover:text-blue-800">
                Help & Support
            </a>
        </div>
    </div>
</footer>