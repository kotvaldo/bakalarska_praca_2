<?php

namespace App\Models;

use Aginev\SearchFilters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Mission extends Model
{
    use HasApiTokens, HasFactory, Notifiable, Filterable;

    protected $fillable = [
        'name',
        'user_id',
        'active',
        'description',
        'automatic',
        'total_cp_count',
        'drones_count',
        'p0',
        'p1',
        'p2',
        'w',
        'z0',
        'z1',
        'z2',
        'z3',

    ];
    public function setFilters()
    {
        $this->filter->like('name')
            ->like('user_id')
            ->equal('active')
            ->equal('automatic')
            ->equal('total_cp_count')
            ->equal('drones_count')
            ->like('p0')
            ->like('p1')
            ->like('p2')
            ->equal('total_cp_count')
            ->equal('drones_count');
    }
}
