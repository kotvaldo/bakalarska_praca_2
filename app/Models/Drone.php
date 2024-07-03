<?php

namespace App\Models;

use Aginev\SearchFilters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Drone extends Model
{
    use HasApiTokens, HasFactory, Notifiable, Filterable;

    protected $fillable = [
        'name',
        'type',
        'serial_number',
        'mission_id'
    ];
    public function setFilters()
    {
        $this->filter->like('name')
            ->like('type')
            ->like('serial_number')
            ->like('mission_id');
    }

}
