<?php

namespace App\Http\Controllers;

use Aginev\Datagrid\Datagrid;
use App\Models\ControlPoint;
use App\Models\DataRecord;
use App\Models\Drone;
use App\Models\Mission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DataRecordController extends Controller
{

    public function index(Request $request)
    {
        $dataRecords = DataRecord::query()->filter($request->get('f', []))->get();

        $grid = new Datagrid($dataRecords, $request->get('f', []));

        $grid->setColumn('id', 'ID', ['sortable' => true, 'has_filters' => true])
            ->setColumn('mission_id', 'Mission', [
                'sortable' => true,
                'has_filters' => true,
                'filters' => Mission::pluck('name', 'id')->toArray(),
                'wrapper' => function ($value, $row) {
                    $missionName = Mission::find($value)->name ?? 'None';
                    return $missionName;
                }
            ])
            ->setColumn('control_point_id', 'Control Point ID', [
                'sortable' => true,
                'has_filters' => true,
                'filters' => ControlPoint::pluck('id', 'id')->toArray(),
                'wrapper' => function ($value, $row) {
                    return $value ?: 'None';
                }
            ])
            ->setColumn('control_point_latitude', 'Control Point Latitude', [
                'sortable' => true,
                'has_filters' => false,
                'wrapper' => function ($value, $row) {
                    $controlPointLatitude = ControlPoint::find($row->control_point_id)->latitude ?? 'None';
                    return $controlPointLatitude;
                }
            ])
            ->setColumn('control_point_longitude', 'Control Point Longitude', [
                'sortable' => true,
                'has_filters' => false,
                'wrapper' => function ($value, $row) {
                    $controlPointLongitude = ControlPoint::find($row->control_point_id)->longitude ?? 'None';
                    return $controlPointLongitude;
                }
            ])
            ->setColumn('control_point_type', 'Control Point Type', [
                'sortable' => true,
                'has_filters' => false,
                'wrapper' => function ($value, $row) {
                    $controlPointType = ControlPoint::find($row->control_point_id)->data_type ?? 'None';
                    return $controlPointType;
                }
            ])
            ->setColumn('drone_name', 'Drone Name', [
                'sortable' => true,
                'has_filters' => true,
                'filters' => Drone::pluck('name')->toArray(),
                'wrapper' => function ($value, $row) {
                    $droneName = Drone::find($row->drone_id)->name ?? 'None';
                    return $droneName;
                }
            ])
            ->setColumn('drone_type', 'Drone Type', [
                'sortable' => true,
                'has_filters' => false,
                'wrapper' => function ($value, $row) {
                    $droneType = Drone::find($row->drone_id)->type ?? 'None';
                    return $droneType;
                }
            ])
            ->setColumn('data_quality', 'Data Quality', [
                'sortable' => true,
                'has_filters' => true,
                'wrapper' => function ($value, $row) {
                    switch ($value) {
                        case 0:
                            return 'Unacceptable Data';
                        case 1:
                            return 'Acceptable Data';
                        case 2:
                            return 'Excellent Data';
                        case 3:
                            return 'Uncollected Data (Malfunction)';
                        default:
                            return 'Unknown';
                    }
                }
            ])
            ->setColumn('created_at', 'Created At', ['sortable' => true, 'has_filters' => true])
            ->setActionColumn([
                'wrapper' => function ($value, $row) {
                    return (Auth::user()->can('update', $row->getData()) ? '<a href="' . route('data_record.edit', [$row->id]) . '" title="Edit" class="btn btn-sm btn-primary"><i class="bi bi-pencil-square"></i></a> ' : '') .
                        (Auth::user()->can('delete', $row->getData()) ? '<a href="' . route('dataRecord.destroy', $row->id) . '" title="Delete" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure ?\')"><i class="bi bi-trash"></i></a>' : '');
                }
            ]);

        return view('data_record.index', [
            'grid' => $grid
        ]);
    }

    public function edit(DataRecord $dataRecord)
    {
        return view('data_record.edit', [
            'action' => route('data_record.update', $dataRecord->id),
            'method' => 'put',
            'model' => $dataRecord
        ]);
    }

    public function update(Request $request, DataRecord $dataRecord)
    {
        $request->validate([
            'data_quality' => 'required|integer|between:0,3',
        ]);

        $dataRecord->update(['data_quality' => $request->data_quality]);

        $dataQuality = $request->data_quality;
        $mission = Mission::find($dataRecord->mission_id);
        $this->updateMissionDetails($mission, $dataQuality);

        return redirect()->route('data_record.index')->with('alert', 'Data quality updated successfully.');
    }
    public function async(Mission $mission)
    {
        // Načítanie údajov misie, dronov a kontrolných bodov
        $controlPoints = ControlPoint::where('mission_id', $mission->id)->get();
        $drones = Drone::where('mission_id', $mission->id)->get();
        $dataRecords = DataRecord::where('mission_id', $mission->id)->get();

        return view('partials.data-records', compact('dataRecords', 'mission', 'drones', 'controlPoints'));
    }

    public function store(Request $request)
    {
        $drone = Drone::find($request->input('drone_id'));
        $controlPoint = ControlPoint::find($request->input('control_point_id'));

        $dataQuality = null;

        if ($drone->type != $controlPoint->data_type) {
            $dataQuality = 0;
        }
        if($controlPoint->drone_id != null) {
            if($drone->id != $controlPoint->drone_id) {
                $dataQuality = 0;
            }
        }

        // Vytvoríme nový záznam
        $dataRecord = new DataRecord();
        $dataRecord->mission_id = $request->input('mission_id');
        $dataRecord->drone_id = $request->input('drone_id');
        $dataRecord->control_point_id = $request->input('control_point_id');
        $dataRecord->data_quality = $dataQuality;
        $dataRecord->save();

        $mission = Mission::find($request->input('mission_id'));
        $this->updateMissionDetails($mission, $dataQuality);
        return response()->json($dataRecord);
    }

    public function destroy(DataRecord $dataRecord)
    {
        $this->missionFix($dataRecord);
        $dataRecord->delete();
        return redirect()->route('data_record.index')->with('alert', 'DataRecord was successfully removed!');
    }

    public function destroyAjax(DataRecord $dataRecord)
    {
        $this->missionFix($dataRecord);
        $dataRecord->delete();
        return response()->json(['success' => 'Data record deleted successfully.']);
    }

    public function missionFix(DataRecord $dataRecord)
    {
        $mission = Mission::find($dataRecord->mission_id);

        $dataQuality = $dataRecord->data_quality;

        // Aktualizácia počtov v misii
        $mission->w -= 1;
        if ($dataQuality == 0) {
            $mission->z0 -= 1;
        } elseif ($dataQuality == 1) {
            $mission->z1 -= 1;
        } elseif ($dataQuality == 2) {
            $mission->z2 -= 1;
        } elseif ($dataQuality == 3) {
            $mission->zn -= 1;
        }
        $totalRecords = $mission->w;

        if ($totalRecords > 0) {
            $mission->p0 = (($mission->z0 + $mission->zn) / $totalRecords) * 100;
            $mission->p1 = ($mission->z1 / $totalRecords) * 100;
            $mission->p2 = ($mission->z2 / $totalRecords) * 100;
        } else {
            $mission->p0 = 0;
            $mission->p1 = 0;
            $mission->p2 = 0;
        }

        $mission->save();
    }
    public function editAjax($id)
    {
        $dataRecord = DataRecord::findOrFail($id);
        return response()->json($dataRecord);
    }

    public function updateAjax(Request $request, $id)
    {
        try {
            $dataRecord = DataRecord::findOrFail($id);
            Log::info('Update request data:', $request->all());

            $dataQuality = $request->input('data_quality');

            // Check if data_quality is null
            if (is_null($dataQuality)) {
                Log::info('data_quality is null');
            } else {
                Log::info('data_quality is not null', ['data_quality' => $dataQuality]);
            }

            // Log current state of the record before updating
            Log::info('Current DataRecord:', $dataRecord->toArray());

            // Update the data quality
            $dataRecord->data_quality = $dataQuality;
            Log::info('Data Quality to be updated:', ['data_quality' => $dataRecord->data_quality]);

            // Save the record
            $dataRecord->save();

            // Log updated state of the record
            Log::info('Updated DataRecord:', $dataRecord->toArray());

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // Log any exception that occurs
            Log::error('Error updating DataRecord:', ['message' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);

            // Return a detailed error response
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function updateMissionDetails($mission, $dataQuality)
    {
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

        $totalRecords = $mission->w;

        if ($totalRecords > 0) {
            $mission->p0 = (($mission->z0 + $mission->zn) / $totalRecords) * 100;
            $mission->p1 = ($mission->z1 / $totalRecords) * 100;
            $mission->p2 = ($mission->z2 / $totalRecords) * 100;
        } else {
            $mission->p0 = 0;
            $mission->p1 = 0;
            $mission->p2 = 0;
        }
        $mission->save();
    }

}
