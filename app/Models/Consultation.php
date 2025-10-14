<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'title',
        'description',
        'notes_internal',
        'payment_link_url',
        'external_contact_link',
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
