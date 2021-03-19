<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $fillable = [
        'area_id',
        'balance',
    ];

    protected $table = 'user_balance';

    public $timestamps  = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
