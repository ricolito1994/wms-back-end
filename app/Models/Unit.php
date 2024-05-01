<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'units';

    protected $primary_key = 'id';

    protected $fillable = [
        'model_name',
        'plate_number',
        'chassis_number',
        'gross_weight',
        'created_by',
        'assigned_to',
        'alias',
        'gps_tracking_unique'
    ];

    protected $dates = [
        'deleted_at',
        'created_at',
        'update_at',
    ];

    protected $casts = [];
}
