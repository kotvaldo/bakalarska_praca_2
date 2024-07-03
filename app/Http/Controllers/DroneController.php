<?php

namespace App\Http\Controllers;

use Aginev\Datagrid\Datagrid;
use App\Models\Drone;
use Illuminate\Http\Request;

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
                'has_filters' => true
            ]);
        return view('drone.index', [
            'grid' => $grid
        ]);
    }


    public function create() {
        $droneTypes = ['IMAGE', 'TEXT', 'NUMBER'];
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

}
