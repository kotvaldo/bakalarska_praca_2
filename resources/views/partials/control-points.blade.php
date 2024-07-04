<h2>Control Points</h2>
<table id="control-points-table" class="table table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>Data Type</th>
        <th>Latitude</th>
        <th>Longitude</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach($controlPoints as $controlPoint)
        <tr>
            <td>{{ $controlPoint->data_type }}</td>
            <td>{{ $controlPoint->latitude }}</td>
            <td>{{ $controlPoint->longitude }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
