<?php

namespace App\Models;

use App\Models\Users\Area;
use Illuminate\Database\Eloquent\Model;

class MetersData extends Model
{
    protected $table = 'meters_data';

    protected $fillable = [
        'id',
        'area_id',
        'water',
        'last_electricity_day',
        'last_electricity_night',
        'electricity_day',
        'electricity_night',
        'created_at',
        'updated_at'
    ];

    public function area()
    {
        return $this->belongsToMany(Area::class, 'areas');
    }
}
