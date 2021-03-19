<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NormativeDocs extends Model
{
    protected $fillable = [
        'id',
        'title',
        'file_path'
    ];
}
