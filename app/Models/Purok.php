<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Purok extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'purok';

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
        return $this->hasOne(City::class, 'id', 'city_id');
    }

    public function createdBy () 
    {
        return $this->hasOne(User::class, 'created_by', 'id');
    }

    public function barangay () 
    {
        return $this->hasOne(Barangay::class, 'id', 'barangay_id');
    }

    public function scopeFilter (Builder $query, Request $request) 
    {
        $query->when($request->city_id, function($query, $city_id) {
            $query->where('city_id', $city_id);
        })->when($request->barangay_id, function($query, $barangay_id) {
            $query->where('barangay_id', $barangay_id);
        })->when($request->purok_name, function($query, $purok_name) {
            $query->where('purok_name', 'like', '%'.$purok_name.'%');
        });
    }
}
