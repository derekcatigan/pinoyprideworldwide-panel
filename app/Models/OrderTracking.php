<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['order_id', 'container_id', 'status', 'remarks', 'created_by', 'pick_up_date'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function container()
    {
        return $this->belongsTo(Container::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
