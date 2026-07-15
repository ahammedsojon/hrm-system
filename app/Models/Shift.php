<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'working_hours',
    ];

    protected function casts(): array
    {
        return [
            'working_hours' => 'decimal:2',
        ];
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
