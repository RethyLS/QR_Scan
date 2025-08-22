<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'stall_id',
        'name',
        'owner',
        'group',
        'default_amount',
        'status',
    ];

    // Add this relationship
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
