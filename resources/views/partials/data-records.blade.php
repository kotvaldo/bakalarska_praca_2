<h2>Data Records</h2>
<table id="data-records-table" class="table table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>Mission</th>
        <th>Control Point</th>
        <th>Drone</th>
        <th>Data Quality</th>
        <th>Created At</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach($dataRecords as $dataRecord)
        <tr>
            <td>{{ $dataRecord->id }}</td>
            <td>{{ $dataRecord->mission->name ?? 'None' }}</td>
            <td>{{ $dataRecord->controlPoint->name ?? 'None' }}</td>
            <td>{{ $dataRecord->drone->name ?? 'None' }}</td>
            <td>{{ $dataRecord->data_quality }}</td>
            <td>{{ $dataRecord->created_at }}</td>
            <td>
                <a href="{{ route('dataRecord.edit', $dataRecord->id) }}" class="btn btn-sm btn-primary">Edit</a>
                <form action="{{ route('dataRecord.destroy', $dataRecord->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<h2>Create New Data Record</h2>
<form id="create-data-record-form">
    @csrf
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="description">Description:</label>
        <textarea id="description" name="description" class="form-control" required></textarea>
    </div>
    <input type="hidden" name="mission_id" value="{{ $mission->id }}">
    <button type="submit" class="btn btn-primary">Create</button>
</form>
