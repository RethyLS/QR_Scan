<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'amount',
        'note',
        'status',
        'transaction_id',
        'created_at',
        'updated_at',
    ];

    // Default value for status when creating a new payment
    protected $attributes = [
        'status' => 'pending',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
