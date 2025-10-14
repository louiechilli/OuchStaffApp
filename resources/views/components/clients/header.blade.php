{{-- Header --}}
<div class="flex items-end justify-between gap-4">
    <div>
        <h1 class="text-2xl md:text-3xl font-semibold text-slate-800">
            Clients
        </h1>
        <p class="mt-1 text-slate-500">Explore all clients ever booked into the system.</p>
    </div>
    
    <a href="{{ route('dashboard') }}" 
        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Dashboard
    </a>
</div>
