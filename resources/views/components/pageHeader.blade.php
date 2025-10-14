{{-- Page Section --}}
<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">{{ $title ?? '' }}</h1>
            <p class="mt-1 text-slate-600">{{ $subtitle ?? '' }}</p>
        </div>
        
        @if(isset($action, $actionUrl))
            <a href="{{ $actionUrl }}" 
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            {{ $action }}
        </a>
        @endif
    </div>
</div>