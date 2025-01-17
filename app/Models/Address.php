<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'street',
        'address_type',
        'lot_number',
        'street_id',
        'purok_id',
        'barangay_id',
        'population',
        'longitude',
        'latitude',
        'created_by',
        'updated_by',
        'city_id',
    ];

    protected $dates = [
        'deleted_at',
        'created_at',
        'update_at',
    ];

    protected $table = 'address';

    protected $primary_key = 'id';


    public function city () 
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }

    public function createdBy () 
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function purok () 
    {
        return $this->hasOne(Purok::class, 'id', 'purok_id');
    }

    public function barangay () 
    {
        return $this->hasOne(Barangay::class, 'id', 'barangay_id');
    }

    public function street () 
    {
        return $this->hasOne(Address::class, 'id', 'street_id');
    }

    public function scopeFilter (Builder $query, Request $request) 
    {
        $query->when($request->city_id, function($query, $city_id) {
            $query->where('city_id', $city_id);
        })->when($request->city_id, function($query, $city_id) {
            $query->where('barangay_id', $barangay_id);
        })->when($request->purok_id, function($query, $purok_id) {
            $query->where('purok_id', $purok_id);
        })->when($request->city_id, function($query, $city_id) {
            $query->where('city_id', $city_id);
        })->when($request->address_type , function($query, $address_type) use ($request) {
            if (isset($request->address_name)) return;
            if ($address_type === 'lot_address')
                $query->where('street', 'like', '%' .$request->address_name. '%');
            else $query->where('lot_number', 'like', '%' .$request->address_name. '%');
        })->when($request->address_name, function($query, $address_name) {
            $query->where (function ($q) use ($address_name) {
                $q->where ("street", 'LIKE', "%{$adress_name}%")
                  ->orWhere ("lot_number", 'LIKE', "%{$adress_name}%");
            });
        });
    }

}
