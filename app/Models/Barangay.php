<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Barangay extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'barangay';

    protected $primary_key = 'id';

    protected $fillable = [
        'barangay_name',
        'longitude',
        'latitude',
        'created_by',
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

    public function purok () 
    {
        return $this->hasMany(Purok::class, 'id', 'barangay_id');
    }

    public function scopeFilter (Builder $query, Request $request) 
    {
        $query->when($request->city_id, function($query, $city_id) {
            $query->where('city_id', $city_id);
        })->when($request->barangay_name, function($query, $barangay_name) {
            $query->where('barangay_name', 'like', '%'.$barangay_name.'%');
        });
    }
}
