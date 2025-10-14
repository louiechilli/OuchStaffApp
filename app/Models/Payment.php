<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'currency',
        'purpose',
        'method',
        'status',
        'provider',
        'provider_reference',
        'raw_payload',
        'captured_at',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'captured_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
