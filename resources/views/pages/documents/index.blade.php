@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('bookings.show', $booking) }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← Back to Booking
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Required Documents</h1>
            <p class="text-gray-600 mt-2">Booking #{{ $booking->id }}</p>
        </div>

        <!-- Progress Bar -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Signing Progress</h2>
                <span class="text-sm font-medium text-gray-700">
                    {{ $progress['signed'] }} of {{ $progress['total'] }} signed
                </span>
            </div>
            
            <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                <div class="bg-green-600 h-3 rounded-full transition-all duration-300" 
                     style="width: {{ $progress['percentage'] }}%"></div>
            </div>
            
            <div class="flex justify-between text-xs text-gray-600 mt-2">
                <span>{{ $progress['pending'] }} pending</span>
                @if($progress['declined'] > 0)
                    <span class="text-red-600">{{ $progress['declined'] }} declined</span>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
                {{ session('warning') }}
            </div>
        @endif

        <!-- Documents List -->
        <div class="space-y-4">
            @forelse($documents as $document)
                <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 
                    {{ $document->isSigned() ? 'border-green-500' : ($document->isDeclined() ? 'border-red-500' : 'border-yellow-500') }}">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-xl font-semibold text-gray-900">
                                        {{ $document->template->name }}
                                    </h3>
                                    
                                    @if($document->isSigned())
                                        <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                                            ✓ Signed
                                        </span>
                                    @elseif($document->isDeclined())
                                        <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">
                                            ✗ Declined
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
                                            ⏳ Pending
                                        </span>
                                    @endif
                                </div>
                                
                                @if($document->template->description)
                                    <p class="text-gray-600 mt-2">{{ $document->template->description }}</p>
                                @endif
                                
                                <div class="mt-4 text-sm text-gray-500">
                                    @if($document->viewed_at)
                                        <p>Viewed: {{ $document->viewed_at->format('M j, Y g:i A') }}</p>
                                    @endif
                                    @if($document->signed_at)
                                        <p>Signed: {{ $document->signed_at->format('M j, Y g:i A') }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex gap-2 ml-4">
                                @if($document->isPending())
                                    <a href="{{ route('bookings.documents.show', [$booking, $document]) }}" 
                                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        Review & Sign
                                    </a>
                                @elseif($document->isSigned())
                                    <a href="{{ route('bookings.documents.show', [$booking, $document]) }}" 
                                       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                                        View Document
                                    </a>
                                    <a href="{{ route('bookings.documents.download', [$booking, $document]) }}" 
                                       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                        Download PDF
                                    </a>
                                @else
                                    <a href="{{ route('bookings.documents.show', [$booking, $document]) }}" 
                                       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                                        View Document
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <p class="text-gray-600">No documents required for this booking.</p>
                </div>
            @endforelse
        </div>

        @if($progress['all_signed'])
            <div class="mt-8 bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-green-900 mb-2">All Documents Signed! ✓</h3>
                <p class="text-green-700">You're all set for your appointment.</p>
            </div>
        @endif
    </div>
</div>
@endsection