<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $fillable = [
        'user_id',
        'destination_name',
        'start_time',
        'expected_end_time',
        'actual_end_time',
        'status',
        'trip_type',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'expected_end_time' => 'datetime',
        'actual_end_time' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function locationHistory()
    {
        return $this->hasMany(LocationHistory::class);
    }
}
