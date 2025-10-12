<?php

namespace App\Models;

use App\Enum\BoxType as EnumBoxType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxType extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'box_type',
        'length',
        'height',
        'width',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'box_type' => EnumBoxType::class,
        ];
    }

    protected $appends = ['display_name'];

    public function getDisplayNameAttribute(): string
    {
        return $this->box_type->displayBoxType();
    }
}
