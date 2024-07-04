<?php

namespace App\Http\Controllers;

use Aginev\Datagrid\Datagrid;
use App\Models\ControlPoint;
use App\Models\Mission;
use Illuminate\Http\Request;

class ControlPointController extends Controller
{
    public function async(Mission $mission)
    {
        // Načítanie kontrolných bodov priradených k misii
        $controlPoints = ControlPoint::where('mission_id', $mission->id)->get();

        // Vrátenie pohľadu s priradenými kontrolnými bodmi
        return view('partials.control-points', compact('controlPoints', 'mission'));
    }
}
