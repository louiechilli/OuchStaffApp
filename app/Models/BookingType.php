<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingType extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'icon',
        'display_order',
        'active',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_booking_type')
            ->withPivot([
                'is_enabled',
                'default_duration_minutes',
                'default_price',
                'requires_deposit',
                'default_deposit',
                'form_schema',
            ])
            ->withTimestamps()
            ->wherePivot('is_enabled', true);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'booking_type_id');
    }
}
