<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Crew extends Model
{
    use HasFactory;

    use HasFactory, SoftDeletes;

    protected $table = 'crew';

    protected $primary_key = 'id';

    protected $fillable = [
        'user_id',
        'unit_id',
        'is_team_leader',
        'is_driver',
        'created_by'
    ];

    protected $dates = [
        'deleted_at',
        'created_at',
        'update_at',
    ];

    protected $casts = [];

    public function unit() 
    {
        return $this->hasOne('App\Models\Unit', 'id', 'unit_id');
    }

    public function employee() 
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function createdBy() 
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }
}
