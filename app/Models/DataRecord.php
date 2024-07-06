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
}
