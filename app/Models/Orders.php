<?php

namespace App\Models;

use App\Models\Deishes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orders extends Model
{
    use HasFactory;
    protected $fillable = [
        'approved'
    ];

    public function dish()
    {
        return $this->belongsTo(Dishes::class, 'dish_id');
    }
}