<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'display_order',
        'active',
    ];

    public function services()
    {
        return $this->hasMany(Service::class, 'category_id');
    }
}
