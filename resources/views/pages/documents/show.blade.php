@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('bookings.documents.index', $booking) }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← Back to Documents
            </a>
            <h1 class="text-3xl font-bold text-gray-900">{{ $document->template->name }}</h1>
            <p class="text-gray-600 mt-2">Booking #{{ $booking->id }}</p>
        </div>

        <!-- Document Status Banner -->
        @if($document->isSigned())
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-2">
                    <span class="text-green-600 text-2xl">✓</span>
                    <div>
                        <h3 class="font-semibold text-green-900">Document Signed</h3>
                        <p class="text-sm text-green-700">Signed on {{ $document->signed_at->format('M j, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>
        @elseif($document->isDeclined())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-2">
                    <span class="text-red-600 text-2xl">✗</span>
                    <div>
                        <h3 class="font-semibold text-red-900">Document Declined</h3>
                        <p class="text-sm text-red-700">You declined this document</p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-2">
                    <span class="text-yellow-600 text-2xl">⏳</span>
                    <div>
                        <h3 class="font-semibold text-yellow-900">Signature Required</h3>
                        <p class="text-sm text-yellow-700">Please review and sign this document to proceed</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Document Content -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-6 prose max-w-none">
            {!! $document->content !!}
        </div>

        <!-- Signature Section -->
        @if($document->isSigned())
            <!-- Show Signature -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Signature</h3>
                <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                    <img src="{{ $document->signature_data }}" alt="Signature" class="max-w-full h-auto">
                </div>
                <div class="mt-4 text-sm text-gray-600">
                    <p><strong>Signed by:</strong> {{ $document->client->first_name }} {{ $document->client->last_name }}</p>
                    <p><strong>Date:</strong> {{ $document->signed_at->format('M j, Y \a\t g:i A') }}</p>
                    <p><strong>IP Address:</strong> {{ $document->ip_address }}</p>
                </div>
            </div>
        @elseif($document->isPending())
            <!-- Signature Form -->
            <form action="{{ route('bookings.documents.sign', [$booking, $document]) }}" method="POST" id="signatureForm">
                @csrf
                
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Sign Document</h3>
                    
                    <!-- Canvas for signature -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Draw your signature below:
                        </label>
                        <div class="border-2 border-gray-300 rounded-lg bg-white">
                            <canvas id="signatureCanvas" 
                                    width="800" 
                                    height="200" 
                                    class="w-full cursor-crosshair touch-none"
                                    style="touch-action: none;"></canvas>
                        </div>
                        <div class="mt-2 flex gap-2">
                            <button type="button" 
                                    id="clearSignature" 
                                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                                Clear Signature
                            </button>
                        </div>
                        <input type="hidden" name="signature" id="signatureData" required>
                        @error('signature')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Agreement checkbox -->
                    <div class="mb-6">
                        <label class="flex items-start gap-3">
                            <input type="checkbox" 
                                   name="agreed" 
                                   value="1" 
                                   required
                                   class="mt-1 h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-sm text-gray-700">
                                I have read and understood this document. I agree to all terms and conditions stated above. 
                                I confirm that all information provided is accurate and complete.
                            </span>
                        </label>
                        @error('agreed')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action buttons -->
                    <div class="flex gap-3">
                        <button type="submit" 
                                id="signButton"
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                            Sign Document
                        </button>
                        
                        <button type="button"
                                onclick="if(confirm('Are you sure you want to decline this document? This may affect your booking.')) { document.getElementById('declineForm').submit(); }"
                                class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                            Decline Document
                        </button>
                    </div>
                </div>
            </form>

            <!-- Hidden decline form -->
            <form id="declineForm" 
                  action="{{ route('bookings.documents.decline', [$booking, $document]) }}" 
                  method="POST" 
                  class="hidden">
                @csrf
            </form>

            <!-- Signature Pad Script -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const canvas = document.getElementById('signatureCanvas');
                    const ctx = canvas.getContext('2d');
                    const clearButton = document.getElementById('clearSignature');
                    const signatureData = document.getElementById('signatureData');
                    const form = document.getElementById('signatureForm');
                    
                    let isDrawing = false;
                    let lastX = 0;
                    let lastY = 0;

                    // Set up canvas
                    ctx.strokeStyle = '#000';
                    ctx.lineWidth = 2;
                    ctx.lineCap = 'round';
                    ctx.lineJoin = 'round';

                    function getCoordinates(e) {
                        const rect = canvas.getBoundingClientRect();
                        const scaleX = canvas.width / rect.width;
                        const scaleY = canvas.height / rect.height;
                        
                        if (e.touches) {
                            return {
                                x: (e.touches[0].clientX - rect.left) * scaleX,
                                y: (e.touches[0].clientY - rect.top) * scaleY
                            };
                        }
                        return {
                            x: (e.clientX - rect.left) * scaleX,
                            y: (e.clientY - rect.top) * scaleY
                        };
                    }

                    function startDrawing(e) {
                        e.preventDefault();
                        isDrawing = true;
                        const coords = getCoordinates(e);
                        lastX = coords.x;
                        lastY = coords.y;
                    }

                    function draw(e) {
                        if (!isDrawing) return;
                        e.preventDefault();
                        
                        const coords = getCoordinates(e);
                        
                        ctx.beginPath();
                        ctx.moveTo(lastX, lastY);
                        ctx.lineTo(coords.x, coords.y);
                        ctx.stroke();
                        
                        lastX = coords.x;
                        lastY = coords.y;
                    }

                    function stopDrawing() {
                        isDrawing = false;
                    }

                    // Mouse events
                    canvas.addEventListener('mousedown', startDrawing);
                    canvas.addEventListener('mousemove', draw);
                    canvas.addEventListener('mouseup', stopDrawing);
                    canvas.addEventListener('mouseout', stopDrawing);

                    // Touch events
                    canvas.addEventListener('touchstart', startDrawing);
                    canvas.addEventListener('touchmove', draw);
                    canvas.addEventListener('touchend', stopDrawing);

                    // Clear button
                    clearButton.addEventListener('click', function() {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        signatureData.value = '';
                    });

                    // Form submission
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        // Check if canvas is empty
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const isEmpty = !imageData.data.some(channel => channel !== 0);
                        
                        if (isEmpty) {
                            alert('Please provide your signature before submitting.');
                            return;
                        }
                        
                        // Convert canvas to data URL
                        signatureData.value = canvas.toDataURL('image/png');
                        
                        // Submit the form
                        form.submit();
                    });
                });
            </script>
        @endif
    </div>
</div>
@endsection