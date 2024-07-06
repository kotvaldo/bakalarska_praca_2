<?php

namespace App\Models;

use Aginev\SearchFilters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class DataRecord extends Model
{
    use HasApiTokens, HasFactory, Notifiable, Filterable;

    // Definovanie plniteľných atribútov
    protected $fillable = [
        'mission_id',
        'control_point_id',
        'drone_id',
        'data_quality'
    ];

    // Definovanie vzťahov
    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

    public function controlPoint()
    {
        return $this->belongsTo(ControlPoint::class);
    }

    public function drone()
    {
        return $this->belongsTo(Drone::class);
    }

    // Definovanie filtrov
    public function setFilters()
    {
        $this->filter->like('mission_id')
            ->equal('control_point_id')
            ->equal('drone_id')
            ->equal('data_quality');
    }
}
