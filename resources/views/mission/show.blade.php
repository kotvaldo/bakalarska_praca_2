@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <div class="container">
        <h1>{{ $mission->name }}</h1>
        <p>{{ $mission->description }}</p>

        <h2>Drones</h2>
        <ul>
            @foreach($drones as $drone)
                <li>{{ $drone->name }}</li>
            @endforeach
        </ul>

        <h2>Data Records</h2>
        <table id="data-records-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <!-- Data records will be loaded here asynchronously -->
            </tbody>
        </table>

        <h2>Create New Data Record</h2>
        <form id="create-data-record-form">
            @csrf
            <div>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div>
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <input type="hidden" name="mission_id" value="{{ $mission->id }}">
            <button type="submit">Create</button>
        </form>

        <h2>Statistics</h2>
        <!-- Add your statistics calculations and display here -->
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            loadDataRecords();

            document.getElementById('create-data-record-form').addEventListener('submit', function (e) {
                e.preventDefault();
                createDataRecord();
            });
        });

        function loadDataRecords() {
            fetch('{{ route('dataRecord.index', $mission->id) }}')
                .then(response => response.json())
                .then(dataRecords => {
                    let tbody = document.querySelector('#data-records-table tbody');
                    tbody.innerHTML = '';
                    dataRecords.forEach(record => {
                        let row = `
                        <tr>
                            <td>${record.id}</td>
                            <td>${record.name}</td>
                            <td>${record.description}</td>
                            <td>
                                <!-- Actions such as edit or delete -->
                                <a href="/data-records/${record.id}/edit">Edit</a>
                                <form action="/data-records/${record.id}" method="POST" style="display:inline;">
                                    @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
`;
                        tbody.innerHTML += row;
                    });
                });
        }

        function createDataRecord() {
            let form = document.getElementById('create-data-record-form');
            let formData = new FormData(form);

            fetch('{{ route('dataRecord.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
                .then(response => response.json())
                .then(dataRecord => {
                    let tbody = document.querySelector('#data-records-table tbody');
                    let row = `
                <tr>
                    <td>${dataRecord.id}</td>
                    <td>${dataRecord.name}</td>
                    <td>${dataRecord.description}</td>
                    <td>
                        <!-- Actions such as edit or delete -->
                        <a href="/data-records/${dataRecord.id}/edit">Edit</a>
                        <form action="/data-records/${dataRecord.id}" method="POST" style="display:inline;">
                            @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
`;
                    tbody.innerHTML += row;
                    form.reset();
                });
        }
    </script>
@endsection
