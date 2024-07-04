<?php

namespace App\Http\Controllers;

use App\Models\DataRecord;
use App\Models\Mission;
use Illuminate\Http\Request;

class DataRecordController extends Controller
{
    public function index(Mission $mission)
    {
        $dataRecords = DataRecord::where('mission_id', $mission->id)->get();
        return response()->json($dataRecords);
    }

    public function store(Request $request)
    {
        $dataRecord = DataRecord::create($request->all());
        return response()->json($dataRecord);
    }
}
