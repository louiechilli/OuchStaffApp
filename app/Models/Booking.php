<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'status',
        'scheduled_start_at',
        'scheduled_end_at',
        'location',
        'total_amount',
        'deposit_required_amount',
        'created_by',
        'assigned_to',
    ];

    protected $casts = [
        'scheduled_start_at' => 'datetime',
        'scheduled_end_at'   => 'datetime',
    ];

    // Relationships
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'booking_clients')
            ->withPivot('role');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'booking_services')
            ->withPivot(['unit_price','qty','line_total']);
    }

    public function consultation()
    {
        return $this->hasOne(Consultation::class);
    }

    public function session()
    {
        return $this->hasOne(BookingSession::class);
    }

    public function bookingType()
    {
        return $this->belongsTo(BookingType::class, 'booking_type_id');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'notable');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Documents associated with this booking
     */
    public function documents()
    {
        return $this->hasMany(BookingDocument::class);
    }

    /**
     * Get pending documents
     */
    public function pendingDocuments()
    {
        return $this->documents()->pending();
    }

    /**
     * Get signed documents
     */
    public function signedDocuments()
    {
        return $this->documents()->signed();
    }

    /**
     * Check if all required documents are signed
     */
    public function allDocumentsSigned(): bool
    {
        $totalDocuments = $this->documents()->count();
        
        if ($totalDocuments === 0) {
            return true; // No documents required
        }
        
        $signedDocuments = $this->signedDocuments()->count();
        
        return $totalDocuments === $signedDocuments;
    }

    /**
     * Check if there are pending documents
     */
    public function hasPendingDocuments(): bool
    {
        return $this->pendingDocuments()->exists();
    }

    /**
     * Get documents signing progress
     */
    public function getDocumentsProgress(): array
    {
        $total = $this->documents()->count();
        $signed = $this->signedDocuments()->count();
        $pending = $this->pendingDocuments()->count();
        $declined = $this->documents()->declined()->count();
        
        return [
            'total' => $total,
            'signed' => $signed,
            'pending' => $pending,
            'declined' => $declined,
            'percentage' => $total > 0 ? round(($signed / $total) * 100) : 100,
            'all_signed' => $total > 0 && $total === $signed,
        ];
    }

    /**
     * Check if booking requires any documents
     */
    public function requiresDocuments(): bool
    {
        return $this->documents()->exists();
    }
}
