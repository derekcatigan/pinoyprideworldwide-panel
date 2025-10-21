<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'branch_id',
        'location_id',
        'invoice_number',
        'sender_name',
        'sender_contact',
        'sender_email',
        'sender_address',
        'receiver_name',
        'receiver_contact',
        'receiver_email',
        'receiver_address',
        'status',
    ];

    public function orderBoxes()
    {
        return $this->hasMany(OrderBox::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function boxLocation()
    {
        return $this->belongsTo(BoxLocation::class, 'location_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trackings()
    {
        return $this->hasMany(OrderTracking::class);
    }

    public function containerOrders()
    {
        return $this->hasMany(ContainerOrder::class);
    }
}
