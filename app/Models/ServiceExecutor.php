<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceExecutor extends Model
{
    protected $table = 'services_executors';

    protected $fillable = [
        'title',
        'description',
        'requisites',
        'service_group_id',
        'published',
        'price',
    ];

    public function group()
    {
        return $this->belongsTo('App\Models\ServiceGroup', 'service_group_id'); //Прямая связь
    }
}
