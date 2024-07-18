<div class="container mt-5">
    <h2>Control Points</h2>
    <table id="control-points-table" class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Data Type</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Drone limitation</th>
        </tr>
        </thead>
        <tbody>
        @foreach($controlPoints as $controlPoint)
            <tr>
                <td>{{ $controlPoint->id }}</td>
                <td>{{ $controlPoint->data_type }}</td>
                <td>{{ $controlPoint->latitude }}</td>
                <td>{{ $controlPoint->longitude }}</td>
                <td>{{ $controlPoint->drone_id  ?? 'No limitations'}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
