<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'title',
        'description',
        'reference_notes',
        'quoted_total_amount',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'notable');
    }
}
