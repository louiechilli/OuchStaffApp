<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDocument;
use App\Services\Bookings\BookingDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingDocumentsController extends Controller
{
    public function __construct(
        private BookingDocumentService $documentService
    ) {}

    /**
     * Display documents for a booking
     */
    public function index(Booking $booking)
    {
        Log::info('BookingDocumentsController@index', [
            'booking_id' => $booking->id,
            'user_id' => optional(auth()->user())->id,
        ]);

        $documents = $booking->documents()->with('template')->get();
        $progress = $booking->getDocumentsProgress();

        return view('pages.bookings.documents.index', compact('booking', 'documents', 'progress'));
    }

    /**
     * Show a specific document for signing
     */
    public function show(Booking $booking, BookingDocument $document)
    {
        // Ensure document belongs to this booking
        if ($document->booking_id !== $booking->id) {
            abort(404);
        }

        Log::info('BookingDocumentsController@show', [
            'booking_id' => $booking->id,
            'document_id' => $document->id,
            'user_id' => optional(auth()->user())->id,
        ]);

        // Mark as viewed
        $this->documentService->markDocumentAsViewed($document);

        return view('pages.bookings.documents.show', compact('booking', 'document'));
    }

    /**
     * Sign a document
     */
    public function sign(Request $request, Booking $booking, BookingDocument $document)
    {
        // Ensure document belongs to this booking
        if ($document->booking_id !== $booking->id) {
            abort(404);
        }

        Log::info('BookingDocumentsController@sign', [
            'booking_id' => $booking->id,
            'document_id' => $document->id,
            'user_id' => optional(auth()->user())->id,
        ]);

        $request->validate([
            'signature' => ['required', 'string'],
            'agreed' => ['required', 'accepted'],
        ]);

        $metadata = [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];

        $signed = $this->documentService->signDocument(
            $document,
            $request->input('signature'),
            $metadata
        );

        if ($signed) {
            Log::info('Document signed successfully', [
                'document_id' => $document->id,
                'booking_id' => $booking->id,
            ]);

            return redirect()
                ->route('bookings.documents.index', $booking)
                ->with('success', 'Document signed successfully.');
        }

        return back()->with('error', 'Failed to sign document. Please try again.');
    }

    /**
     * Decline a document
     */
    public function decline(Request $request, Booking $booking, BookingDocument $document)
    {
        // Ensure document belongs to this booking
        if ($document->booking_id !== $booking->id) {
            abort(404);
        }

        Log::info('BookingDocumentsController@decline', [
            'booking_id' => $booking->id,
            'document_id' => $document->id,
            'user_id' => optional(auth()->user())->id,
        ]);

        $declined = $this->documentService->declineDocument($document);

        if ($declined) {
            Log::info('Document declined', [
                'document_id' => $document->id,
                'booking_id' => $booking->id,
            ]);

            return redirect()
                ->route('bookings.documents.index', $booking)
                ->with('warning', 'Document declined.');
        }

        return back()->with('error', 'Failed to decline document. Please try again.');
    }

    /**
     * Download a signed document as PDF
     */
    public function download(Booking $booking, BookingDocument $document)
    {
        // Ensure document belongs to this booking
        if ($document->booking_id !== $booking->id) {
            abort(404);
        }

        // Only allow download of signed documents
        if (!$document->isSigned()) {
            abort(403, 'Document must be signed before downloading.');
        }

        Log::info('BookingDocumentsController@download', [
            'booking_id' => $booking->id,
            'document_id' => $document->id,
            'user_id' => optional(auth()->user())->id,
        ]);

        // You can use a PDF library like dompdf or snappy here
        // For now, returning a simple response
        $filename = sprintf(
            '%s-%s-%s.pdf',
            $document->template->slug,
            $booking->id,
            $document->signed_at->format('Y-m-d')
        );

        // TODO: Implement PDF generation
        // Example: return PDF::loadView('documents.pdf', compact('document'))->download($filename);
        
        return response()->json([
            'message' => 'PDF download not yet implemented',
            'filename' => $filename,
        ]);
    }
}