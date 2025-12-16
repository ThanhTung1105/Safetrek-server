<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationHistory extends Model
{
    // Disable default timestamps since we use custom timestamp field
    public $timestamps = false;

    protected $fillable = [
        'trip_id',
        'latitude',
        'longitude',
        'battery_level',
        'timestamp',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'battery_level' => 'integer',
        'timestamp' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
