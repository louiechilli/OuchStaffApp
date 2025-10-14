<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    // Relationships
    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_clients')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'notable');
    }
}
