<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'document_template_id',
        'client_id',
        'content',
        'status',
        'signature_data',
        'ip_address',
        'user_agent',
        'signed_at',
        'viewed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'viewed_at' => 'datetime',
    ];

    // Relationships
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSigned($query)
    {
        return $query->where('status', 'signed');
    }

    public function scopeDeclined($query)
    {
        return $query->where('status', 'declined');
    }

    // Helper methods
    public function isSigned(): bool
    {
        return $this->status === 'signed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDeclined(): bool
    {
        return $this->status === 'declined';
    }

    public function markAsViewed(): void
    {
        if (!$this->viewed_at) {
            $this->update(['viewed_at' => now()]);
        }
    }

    public function sign(string $signatureData, array $metadata = []): bool
    {
        return $this->update([
            'status' => 'signed',
            'signature_data' => $signatureData,
            'signed_at' => now(),
            'ip_address' => $metadata['ip_address'] ?? request()->ip(),
            'user_agent' => $metadata['user_agent'] ?? request()->userAgent(),
        ]);
    }

    public function decline(): bool
    {
        return $this->update([
            'status' => 'declined',
        ]);
    }
}