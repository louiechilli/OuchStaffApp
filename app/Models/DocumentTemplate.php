<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'content',
        'is_active',
        'requires_signature',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_signature' => 'boolean',
    ];

    // Services that require this document
    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_document_templates')
            ->withPivot('is_required', 'display_order')
            ->withTimestamps();
    }

    // Signed instances of this document
    public function bookingDocuments()
    {
        return $this->hasMany(BookingDocument::class);
    }

    // Scope for active templates
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Replace placeholders in content with actual data
    public function renderContent(array $data = []): string
    {
        $content = $this->content;

        foreach ($data as $key => $value) {
            $content = str_replace("{{" . $key . "}}", $value, $content);
        }

        return $content;
    }
}