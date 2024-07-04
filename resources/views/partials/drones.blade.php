<h2>Drones</h2>
<table id="drones-table" class="table table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Type</th>
        <th>Serial Number</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach($drones as $drone)
        <tr>
            <td>{{ $drone->id }}</td>
            <td>{{ $drone->name }}</td>
            <td>{{ $drone->type }}</td>
            <td>{{ $drone->serial_number }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
