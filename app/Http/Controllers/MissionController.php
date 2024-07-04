<?php

namespace App\Http\Controllers;

use Aginev\Datagrid\Datagrid;
use App\Models\ControlPoint;
use App\Models\Drone;
use App\Models\Mission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MissionController extends Controller
{
    public function index(Request $request)
    {
        $missions = Mission::query()->filter($request->get('f', []))->get();

        $grid = new Datagrid($missions, $request->get('f', []));

        $grid->setColumn('name', 'Name', [
            'sortable' => true,
            'has_filters' => true,
            'wrapper' => function ($value, $row) {
                return '<a href="' . route('mission.show', [$row->id]) . '">' . e($value) . '</a>';
            }
        ])
            ->setColumn('active', 'Active', [
                'sortable' => true,
                'has_filters' => true,
                'wrapper' => function ($value) {
                    return $value ? 'Active' : 'Inactive';
                }
            ])
            ->setColumn('automatic', 'Automatic recalculation', [
                'sortable' => true,
                'has_filters' => true,
                'wrapper' => function ($value) {
                    return $value ? 'Enabled' : 'Disabled';
                }
            ])
            ->setColumn('p0', 'P0', [
                'sortable' => true,
                'has_filters' => true,
                'wrapper' => function ($value) {
                    return number_format($value, 1) . '%';
                }
            ])
            ->setColumn('p1', 'P1', [
                'sortable' => true,
                'has_filters' => true,
                'wrapper' => function ($value) {
                    return number_format($value, 1) . '%';
                }
            ])
            ->setColumn('p2', 'P2', [
                'sortable' => true,
                'has_filters' => true,
                'wrapper' => function ($value) {
                    return number_format($value, 1) . '%';
                }
            ])
            ->setColumn('pn', 'PN', [
                'sortable' => true,
                'has_filters' => true,
                'wrapper' => function ($value) {
                    return number_format($value, 1) . '%';
                }
            ])
            ->setColumn('total_cp_count', 'CP Count', [
                'sortable' => true,
                'has_filters' => true,
            ])
            ->setColumn('drones_count', 'Drones Count', [
                'sortable' => true,
                'has_filters' => true,
            ])
            ->setActionColumn([
                'wrapper' => function ($value, $row) {
                    return (Auth::user()->can('update', $row->getData()) ? '<a href="' . route('mission.edit', [$row->id]) . '" title="Edit" class="btn btn-sm btn-primary"><i class="bi bi-pencil-square"></i></a> ' : '') .
                        (Auth::user()->can('delete', $row->getData()) ? '<a href="' . route('mission.delete', $row->id) . '" title="Delete" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure ?\')"><i class="bi bi-trash"></i></a>' : '');
                }
            ]);
        return view('mission.index', [
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
            'total_cp_count' => 'required|integer|min:1',
            'drones' => 'required|array|min:1|max:10',
        ]);


        $mission = Mission::create([
            'name' => $request->name,
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
        $drones = Drone::where('mission_id', $mission->id)->get();
        return view('mission.show', [
            'mission' => $mission,
            'drones' => $drones
        ]);
    }


    public function destroy(Mission $mission)
    {
        ControlPoint::where('mission_id', $mission->id)->update(['mission_id' => null]);

        Drone::where('mission_id', $mission->id)->update(['mission_id' => null]);

        // Vymazanie všetkých DataRecord priradených k misii (ak je nastavený ON DELETE CASCADE v databáze, nemusíme explicitne mazať)
        DataRecord::where('mission_id', $mission->id)->delete();

        // Odstránenie misie
        $mission->delete();

        return redirect()->route('mission.index')->with('alert', 'Mission was successfully removed!');
    }
}
