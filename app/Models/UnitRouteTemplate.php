<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitRouteTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "unit_route_template";

    protected $fillable = [
        'unit_id',
        'is_active',
        'route_template_name',
        'description',
        'schedule_day_of_week',
        'created_by'
    ];

    public function unitRoutes (): HasMany
    {
        return $this->hasMany(UnitRoute::class, 'unit_route_template_id', 'id');
    }

    public function scopeFilter($query, $filters)
    {
        return $query->when(isset($filters['is_active']), function ($q) use ($filters) {
                $q->where('is_active', $filters['is_active']);
            })->when(isset($filters['unit_id']), function ($q) use ($filters) {
                $q->where('unit_id', $filters['unit_id']);
            })->when(isset($filters['route_template_name']), function ($q) use ($filters) {
                $q->where('route_template_name', 'like', '%' . $filters['route_template_name'] . '%');
            })->when(isset($filters['schedule_day_of_week']), function ($q) use ($filters) {
                $q->where('schedule_day_of_week', $filters['schedule_day_of_week']);
            })->when(isset($filters['created_by']), function ($q) use ($filters) {
                $q->where('created_by', $filters['created_by']);
            });
    }
}
