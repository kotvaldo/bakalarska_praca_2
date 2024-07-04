<?php

namespace App\Http\Controllers;

use Aginev\Datagrid\Datagrid;
use App\Models\ControlPoint;
use App\Models\DataRecord;
use App\Models\Drone;
use App\Models\Mission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MissionController extends Controller
{
    public function async(Mission $mission, Request $request)
    {
        $dataRecords = DataRecord::query()->where('mission_id', $mission->id)->filter($request->get('f', []))->get();

        $grid = new Datagrid($dataRecords, $request->get('f', []));

        $grid->setColumn('id', 'ID', ['sortable' => true, 'has_filters' => true])
            ->setColumn('mission_id', 'Mission', [
                'sortable' => true,
                'has_filters' => true,
                'display' => function($row) {
                    return $row->mission->name ?? 'None';
                }
            ])
            ->setColumn('control_point_id', 'Control Point', [
                'sortable' => true,
                'has_filters' => true,
                'display' => function($row) {
                    return $row->controlPoint->name ?? 'None';
                }
            ])
            ->setColumn('drone_id', 'Drone', [
                'sortable' => true,
                'has_filters' => true,
                'display' => function($row) {
                    return $row->drone->name ?? 'None';
                }
            ])
            ->setColumn('data_quality', 'Data Quality', ['sortable' => true, 'has_filters' => true])
            ->setColumn('created_at', 'Created At', ['sortable' => true, 'has_filters' => true])
            ->setActionColumn([
                'wrapper' => function ($value, $row) {
                    return (Auth::user()->can('update', $row->getData()) ? '<a href="' . route('dataRecord.edit', [$row->id]) . '" title="Edit" class="btn btn-sm btn-primary"><i class="bi bi-pencil-square"></i></a> ' : '') .
                        (Auth::user()->can('delete', $row->getData()) ? '<a href="' . route('dataRecord.delete', $row->id) . '" title="Delete" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure ?\')"><i class="bi bi-trash"></i></a>' : '');
                }
            ]);

        return view('partials.grid', [
            'grid' => $grid
        ]);
    }


    public function create()
    {
        $drones = Drone::whereNull('mission_id')->get();
        return view('mission.create', compact('drones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'automatic' => 'required|boolean',
            'description' => 'required',
            'total_cp_count' => 'required|integer|min:1',
            'drones' => 'required|array|min:1|max:10',
        ]);


        $mission = Mission::create([
            'name' => $request->name,
            'description' => $request->description,
            'automatic' => $request->automatic,
            'total_cp_count' => $request->total_cp_count,
            'drones_count' => count($request->drones),
            'user_id' => auth()->id()
        ]);

        // Aktualizácia mission_id pre vybrané drony
        Drone::whereIn('id', $request->drones)->update(['mission_id' => $mission->id]);

        // Vytvorenie Control Points pomocou Factory
        ControlPoint::factory()
            ->count($request->total_cp_count)
            ->withMissionId($mission->id)
            ->create();

        return redirect()->route('mission.index')->with('alert', 'Mission was successfully created!');
    }

    public function edit(Mission $mission)
    {
        $allocatedDrones = Drone::where('mission_id', $mission->id)->get();
        $unallocatedDrones = Drone::whereNull('mission_id')->get();
        $drones = $allocatedDrones->merge($unallocatedDrones);

        return view('mission.edit', [
            'action' => route('mission.update', $mission->id),
            'method' => 'put',
            'model' => $mission,
            'drones' => $drones,
            'selectedDrones' => $allocatedDrones->pluck('id')->toArray(),
        ]);
    }
    public function update(Request $request, Mission $mission)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'automatic' => 'required|boolean',
            'total_cp_count' => 'required|integer|min:1',
            'drones' => 'required|array|min:1|max:10',
        ]);
        $currentCpCount = $mission->total_cp_count;
        $newCpCount = $request->total_cp_count;

        if ($newCpCount < $currentCpCount) {
            // Odstránenie náhodných nadbytočných kontrolných bodov
            ControlPoint::where('mission_id', $mission->id)
                ->inRandomOrder()
                ->take($currentCpCount - $newCpCount)
                ->delete();
        } elseif ($newCpCount > $currentCpCount) {
            // Pridanie nových kontrolných bodov
            ControlPoint::factory()
                ->count($request->total_cp_count)
                ->withMissionId($mission->id)
                ->create();
        }
        // Aktualizácia misie
        $mission->update([
            'name' => $request->name,
            'automatic' => $request->automatic,
            'description' => $request->description,
            'total_cp_count' => $request->total_cp_count,
            'drones_count' => count($request->drones),
        ]);

        // Odstránenie predchádzajúceho priradenia dronov
        Drone::where('mission_id', $mission->id)->update(['mission_id' => null]);

        // Priradenie nových dronov
        Drone::whereIn('id', $request->drones)->update(['mission_id' => $mission->id]);

        return redirect()->route('mission.index')->with('alert', 'Mission was successfully updated!');
    }
    public function show(Mission $mission) {
          return view('mission.show', [
            'mission' => $mission
        ]);
    }


    public function destroy(Mission $mission)
    {

        Drone::where('mission_id', $mission->id)->update(['mission_id' => null]);

        // Vymazanie všetkých DataRecord priradených k misii (ak je nastavený ON DELETE CASCADE v databáze, nemusíme explicitne mazať)
        DataRecord::where('mission_id', $mission->id)->delete();

        ControlPoint::where('mission_id', $mission->id)->delete();
        // Odstránenie misie
        $mission->delete();

        return redirect()->route('mission.index')->with('alert', 'Mission was successfully removed!');
    }
}
