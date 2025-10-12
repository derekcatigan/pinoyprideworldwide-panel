<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderBox extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'order_id',
        'box_type',
        'quantity',
        'length',
        'height',
        'width',
        'fee',
        'description',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
