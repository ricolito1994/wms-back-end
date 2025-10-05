<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UnitRoute extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "unit_route";

    protected $fillable = [
        'unit_id',
        'created_by',
        'created_at',
        'order',
        'unit_route_template_id',
        'lat',
        'lng'
    ];

    public function unitRouteTemplate(): HasOne
    {
        return $this->hasOne(UnitRouteTemplate::class, 'unit_route_template_id', 'id');
    }
}
