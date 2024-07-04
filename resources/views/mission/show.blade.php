@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <div class="container">
        <h1>{{ $mission->name }}</h1>
        <p>{{ $mission->description }}</p>

        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-section="data-records-section">Data Records</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section="drones-section">Drones</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section="control-points-section">Control Points</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section="statistics-section">Statistics</a>
            </li>
        </ul>

        <div id="data-records-section" class="page-section active">
            <!-- Data records will be loaded here asynchronously -->
        </div>

        <div id="drones-section" class="page-section">
            <!-- Drones will be loaded here asynchronously -->
        </div>

        <div id="control-points-section" class="page-section">
            <!-- Control points will be loaded here asynchronously -->
        </div>

        <div id="statistics-section" class="page-section">
            <!-- Statistics will be loaded here asynchronously -->
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const navLinks = document.querySelectorAll('.nav-link');
            const sections = document.querySelectorAll('.page-section');

            navLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetSection = document.getElementById(this.getAttribute('data-section'));

                    navLinks.forEach(link => link.classList.remove('active'));
                    sections.forEach(section => section.classList.remove('active'));

                    this.classList.add('active');
                    targetSection.classList.add('active');

                    // Hide all sections except the target section
                    sections.forEach(section => {
                        if (section !== targetSection) {
                            section.style.display = 'none';
                        } else {
                            section.style.display = 'block';
                        }
                    });

                    // Load content asynchronously
                    loadSectionContent(targetSection);
                });
            });

            // Initial load for the active section
            const activeSection = document.querySelector('.page-section.active');
            activeSection.style.display = 'block';
            loadSectionContent(activeSection);
        });

        function loadSectionContent(section) {
            const sectionId = section.id;
            let url;

            switch (sectionId) {
                case 'data-records-section':
                    url = '{{ route("dataRecord.async", $mission->id) }}';
                    break;
                case 'drones-section':
                    url = '{{ route("drones.async", $mission->id) }}';
                    break;
                case 'control-points-section':
                    url = '{{ route("controlPoints.async", $mission->id) }}';
                    break;
                case 'statistics-section':
                    url = '{{ route("dataRecord.async", $mission->id) }}';
                    break;
                default:
                    return;
            }

            fetch(url)
                .then(response => response.text())
                .then(html => {
                    section.innerHTML = html;
                    if (sectionId === 'data-records-section') {
                        setupDataRecordsForm();
                    }
                });
        }

        function setupDataRecordsForm() {
            document.getElementById('create-data-record-form').addEventListener('submit', function (e) {
                e.preventDefault();
                createDataRecord();
            });
        }

        function createDataRecord() {
            let form = document.getElementById('create-data-record-form');
            let formData = new FormData(form);

            fetch('{{ route("dataRecord.store") }}', {
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
                    <td>${dataRecord.mission.name ?? 'None'}</td>
                    <td>${dataRecord.control_point.name ?? 'None'}</td>
                    <td>${dataRecord.drone.name ?? 'None'}</td>
                    <td>${dataRecord.data_quality}</td>
                    <td>${dataRecord.created_at}</td>
                    <td>
                        <a href="/data-records/${dataRecord.id}/edit" class="btn btn-sm btn-primary">Edit</a>
                        <form action="/data-records/${dataRecord.id}" method="POST" style="display:inline;">
                            @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
`;
                    tbody.innerHTML += row;
                    form.reset();  // Reset the form fields
                });
        }
    </script>
@endsection
