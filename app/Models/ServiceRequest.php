<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $table = 'service_requests';

    protected $fillable = [
        'id',
        'area_id',
        'service_id',
        'description',
        'status',
        'created_at',
        'updated_at',
        'text_free',
        'execution_date',
        'execution_hours',
        'quantity',
        'rating'
    ];

	
	public function area()
    {
        return $this->belongsTo('App\Models\Users\Area', 'area_id'); //Прямая связь
    }
}
