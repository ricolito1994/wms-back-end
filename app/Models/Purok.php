<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purok extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'barangay';

    protected $primary_key = 'id';

    protected $fillable = [
        'purok_name',
        'longitude',
        'latitude',
        'created_by',
        'barangay_id',
        'city_id',
    ];

    protected $dates = [
        'deleted_at',
        'created_at',
        'update_at',
    ];

    public function city () 
    {
        return $this->hasOne(City::class, 'city_id', 'id');
    }

    public function createdBy () 
    {
        return $this->hasOne(User::class, 'created_by', 'id');
    }

    public function barangay () 
    {
        return $this->hasOne(Barangay::class, 'barangay_id', 'id');
    }

    public function scopeFilter (Builder $query, Request $request) 
    {
        $query->when($request->city_id, function($query, $city_id) {
            $query->where('city_id', $city_id);
        })->when($request->barangay_name, function($query, $purok_name) {
            $query->where('barangay_name', 'like', '%'.$purok_name.'%');
        });
    }
}
