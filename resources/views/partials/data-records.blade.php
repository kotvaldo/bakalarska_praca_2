<div class="container mt-5">
    <h2>Data Records</h2>
    <table id="data-records-table" class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Mission</th>
            <th>Control Point X</th>
            <th>Control Point Y</th>
            <th>Control Point DataType</th>
            <th>Drone Name</th>
            <th>Drone DataType</th>
            <th>Data Quality</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($dataRecords as $dataRecord)
            <tr>
                <td>{{ $dataRecord->id }}</td>
                <td>{{ $dataRecord->mission_id ? $dataRecord->mission->name : 'None' }}</td>
                <td>{{ $dataRecord->control_point_id ? $dataRecord->controlPoint->latitude: 'CP was Removed' }}</td>
                <td>{{ $dataRecord->control_point_id ? $dataRecord->controlPoint->longitude : 'CP was Removed' }}</td>
                <td>{{ $dataRecord->control_point_id ?  $dataRecord->controlPoint->data_type: 'CP was Removed' }}</td>
                <td>{{ $dataRecord->drone_id ? $dataRecord->drone->name : 'Drone was Removed' }}</td>
                <td>{{ $dataRecord->drone_id ? $dataRecord->drone->type : 'Drone was Removed' }}</td>
                <td>
                    @switch($dataRecord->data_quality)
                        @case(0)
                            Unacceptable Data
                            @break
                        @case(1)
                            Acceptable Data
                            @break
                        @case(2)
                            Excellent Data
                            @break
                        @case(3)
                            Uncollected Data (Failure)
                            @break
                        @default
                            Unknown Quality
                    @endswitch
                </td>
                <td>{{ $dataRecord->created_at }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger delete-record" data-id="{{ $dataRecord->id }}">Delete</button>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
</div>

<div class="container mt-5">
    @if ($mission->active)
        <h2>Create New Data Record</h2>
        <form id="create-data-record-form">
            @csrf
            <div class="mb-3">
                <label for="drone" class="form-label">Drone:</label>
                <select id="drone" name="drone_id" class="form-select" required>
                    @foreach($drones as $drone)
                        <option value="{{ $drone->id }}">ID:{{ $drone->id }} | NAME:{{ $drone->name }} | DATATYPE:{{ $drone->type }}</option>
                    @endforeach
                </select>
            </div>

            <input type="hidden" name="mission_id" value="{{ $mission->id }}">

            <h3>Path</h3>
            <div id="control-points">
                <div class="row mb-3">
                    <div class="col">
                        <label for="control_point" class="form-label">Control Point:</label>
                        <select name="control_point[]" class="form-select" required>
                            @foreach($controlPoints as $controlPoint)
                                <option value="{{ $controlPoint->id }}">
                                    ID:{{ $controlPoint->id }} |
                                    X:{{ $controlPoint->longitude }} |
                                    Y:{{ $controlPoint->latitude }} |
                                    DATATYPE:{{ $controlPoint->data_type }} |
                                    DRONE_LIMIT:{{ $controlPoint->drone_id ?? 'NONE' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto d-flex align-items-end">
                        <button type="button" class="btn btn-success" id="add-row">+</button>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    @else
        <div class="alert alert-warning" role="alert">
            The mission is inactive. You cannot create new data records.
        </div>
    @endif
</div>


