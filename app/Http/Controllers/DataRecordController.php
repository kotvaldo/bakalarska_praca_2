<?php

namespace App\Http\Controllers;

use Aginev\Datagrid\Datagrid;
use App\Models\DataRecord;
use App\Models\Mission;
use Illuminate\Http\Request;

class DataRecordController extends Controller
{
    public function async(Mission $mission)
    {
        $dataRecords = DataRecord::where('mission_id', $mission->id)->get();
        return view('partials.data-records', compact('dataRecords', 'mission'));
    }

    public function store(Request $request)
    {
        $dataRecord = DataRecord::create($request->all());
        return response()->json($dataRecord);
    }
}
