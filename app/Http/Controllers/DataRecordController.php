<?php

namespace App\Http\Controllers;

use Aginev\Datagrid\Datagrid;
use App\Models\ControlPoint;
use App\Models\DataRecord;
use App\Models\Drone;
use App\Models\Mission;
use Illuminate\Http\Request;

class DataRecordController extends Controller
{
    public function index(Mission $mission) {

        return view('data_record.index');
    }
    public function async(Mission $mission)
    {
        // Načítanie údajov misie, dronov a kontrolných bodov
        $controlPoints = ControlPoint::where('mission_id', $mission->id)->get();
        $drones = Drone::where('mission_id', $mission->id)->get();
        $dataRecords = DataRecord::where('mission_id', $mission->id)->get();

        return view('partials.data-records', compact('dataRecords', 'mission', 'drones', 'controlPoints'));
    }

    public function store(Request $request) {

        $drone = Drone::find($request->input('drone_id'));
        $controlPoint = ControlPoint::find($request->input('control_point_id'));

        $dataQuality = null;

        if ($drone->type != $controlPoint->data_type) {
            $dataQuality = 0; // Neakceptované údaje
        } else {
            $randomNumber = rand(1, 100);

            if ($randomNumber <= 60) {
                $dataQuality = 1; // 60% -> Prijateľné údaje
            } elseif ($randomNumber <= 90) {
                $dataQuality = 2; // 30% -> Vynikajúce údaje
            } else {
                $dataQuality = 3; // 10% -> Nezozbierané údaje (porucha)
            }
        }

        // Vytvoríme nový záznam
        $dataRecord = new DataRecord();
        $dataRecord->mission_id = $request->input('mission_id');
        $dataRecord->drone_id = $request->input('drone_id');
        $dataRecord->control_point_id = $request->input('control_point_id');
        $dataRecord->data_quality = $dataQuality;
        $dataRecord->save();

        // Aktualizujeme údaje o misii
        $mission = Mission::find($request->input('mission_id'));
        $mission->w += 1; // Zvýšime celkový počet dátových záznamov

        if ($dataQuality == 0) {
            $mission->z0 += 1; // Neakceptované údaje
        } elseif ($dataQuality == 1) {
            $mission->z1 += 1; // Prijateľné údaje
        } elseif ($dataQuality == 2) {
            $mission->z2 += 1; // Vynikajúce údaje
        } elseif ($dataQuality == 3) {
            $mission->zn += 1; // Nezozbierané údaje (porucha)
        }

        $mission->save();

        return response()->json($dataRecord);
    }
}
