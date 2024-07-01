<?php

namespace App\Http\Controllers;

use App\Models\Drone;
use Illuminate\Http\Request;

class DroneController extends Controller
{
    public function index()
    {
        // Získanie všetkých dronov z databázy
        $drones = Drone::all();

        // Zobrazenie pohľadu s dronmi
        return view('drones.index', compact('drones'));
    }
}
