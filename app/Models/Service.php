<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'base_price',
        'duration_minutes',
        'active',
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function bookingTypes()
    {
        return $this->belongsToMany(BookingType::class, 'service_booking_type')
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
        return $this->belongsToMany(Booking::class, 'booking_services')
            ->withPivot(['unit_price', 'qty', 'line_total'])
            ->withTimestamps();
    }

    public function documentTemplates()
    {
        return $this->belongsToMany(DocumentTemplate::class, 'service_document_templates')
            ->withPivot('is_required', 'display_order')
            ->withTimestamps()
            ->orderBy('pivot_display_order');
    }

    /**
     * Get only required document templates
     */
    public function requiredDocumentTemplates()
    {
        return $this->documentTemplates()->wherePivot('is_required', true);
    }

    /**
     * Check if this service has any required documents
     */
    public function hasRequiredDocuments(): bool
    {
        return $this->requiredDocumentTemplates()->exists();
    }

    /**
     * Get count of required documents
     */
    public function getRequiredDocumentsCount(): int
    {
        return $this->requiredDocumentTemplates()->count();
    }
}
