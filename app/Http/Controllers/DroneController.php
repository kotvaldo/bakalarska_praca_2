<?php

namespace App\Http\Controllers;

use Aginev\Datagrid\Datagrid;
use App\Models\ControlPoint;
use App\Models\Drone;
use App\Models\Mission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DroneController extends Controller
{
    public function index(Request $request)
    {
        $drones = Drone::query()->filter($request->get('f', []))->get();

        $grid = new Datagrid($drones, $request->get('f', []));

        $grid->setColumn('name', 'Name', ['sortable' => true, 'has_filters' => true])
            ->setColumn('type', 'Type', ['sortable' => true, 'has_filters' => true])
            ->setColumn('serial_number', 'Serial Number', ['sortable' => true, 'has_filters' => true])
            ->setColumn('mission_id', 'Mission', [
                'sortable' => true,
                'has_filters' => true,
                'display' => function($row) {
                    return $row->mission_id ?: 'None';
                }

            ])
            ->setActionColumn([
                'wrapper' => function ($value, $row) {
                    return (Auth::user()->can('update', $row->getData()) ? '<a href="' . route('drone.edit', [$row->id]) . '" title="Edit" class="btn btn-sm btn-primary"><i class="bi bi-pencil-square"></i></a> ' : '') .
                        (Auth::user()->can('delete', $row->getData()) ? '<a href="' . route('drone.delete', $row->id) . '" title="Delete" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure ?\')"><i class="bi bi-trash"></i></a>' : '');
                }
            ]);
        return view('drone.index', [
            'grid' => $grid
        ]);
    }


    public function create() {
        $droneTypes = ['IMAGE', 'SIGNAL', 'NUMBER'];
        return view('drone.create', [
            'action' => route('drone.store'),
            'method' => 'post',
            'droneTypes' => $droneTypes
        ]);

    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'serial_number' => 'required',
            'type' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        $avatarName = null;
        if ($request->hasFile('image')) {
            $avatarName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('images'), $avatarName);
        }

        $drone = Drone::create([
            'name' => $request->name,
            'serial_number' => $request->serial_number,
            'type' => $request->type,
            'image' => $avatarName,
        ]);

        $drone->save();

        return redirect()->route('drone.index')->with('alert', 'Drone was successfully created!');
    }

    public function edit(Drone $drone)
    {
        $droneTypes = ['IMAGE', 'SIGNAL', 'NUMBER'];
        return view('drone.edit', [
            'action' => route('drone.update', $drone->id),
            'method' => 'put',
            'model' => $drone,
            'droneTypes' => $droneTypes

        ]);

    }
    public function update(Request $request, Drone $drone)
    {
        $request->validate([
            'name' => 'required',
            'serial_number' => 'required',
            'type' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $oldImage = null;
        $avatarName = null;

        if ($request->hasFile('image')) {
            $oldImage = $drone->image;
            $avatarName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('images'), $avatarName);

        }

        $drone->update($request->all());
        $drone->update(['image' => $avatarName]);

        if ($oldImage) {
            $oldImagePath = public_path('images') . '/' . $oldImage;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
    }
    public function destroy(Drone $drone)
    {
        $oldImage = $drone->image;
        if ($oldImage) {
            $oldImagePath = public_path('images') . '/' . $oldImage;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        // Nastavenie mission_id na null pre všetky Control Points, ktoré patria tomuto dronu
        ControlPoint::where('drone_id', $drone->id)->update(['drone_id' => null]);

        // Nastavenie mission_id na null pre všetky misie, ktoré patria tomuto dronu
        Mission::where('drone_id', $drone->id)->update(['drone_id' => null]);

        $drone->delete();

        return redirect()->route('drone.index')->with('alert', 'Drone was successfully removed!');
    }
    public function async(Mission $mission)
    {
        $drones = Drone::where('mission_id', $mission->id)->get();
        return view('partials.drones', compact('drones', 'mission'));
    }

}
