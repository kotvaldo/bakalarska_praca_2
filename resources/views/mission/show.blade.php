@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h1>{{ $mission->name }}</h1>
            <p class="lead">{{ $mission->description }}</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title justify-content-center">Mission Details</h5>
                        <p class="card-text"><strong>User ID:</strong> {{ $mission->user_id }}</p>
                        <p class="card-text"><strong>Status:</strong> {{ $mission->active ? 'Active' : 'Inactive' }}</p>
                        <p class="card-text"><strong>Automatic:</strong> {{ $mission->automatic ? 'True' : 'False' }}</p>
                        <p class="card-text"><strong>Created At:</strong> {{ $mission->created_at }}</p>
                        <p class="card-text"><strong>Total Control Points Count:</strong> {{ $mission->total_cp_count }}</p>
                        <p class="card-text"><strong>Total Drones Count:</strong> {{ $mission->drones_count }}</p>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs mt-4">
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

        <div id="data-records-section" class="page-section active mt-3">
            <!-- Data records will be loaded here asynchronously -->
        </div>

        <div id="drones-section" class="page-section mt-3">
            <!-- Drones will be loaded here asynchronously -->
        </div>

        <div id="control-points-section" class="page-section mt-3">
            <!-- Control points will be loaded here asynchronously -->
        </div>

        <div id="statistics-section" class="page-section mt-3">
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
                case "data-records-section":
                    url = '{{ route("dataRecord.async", $mission->id) }}';
                    break;
                case "drones-section":
                    url = '{{ route("drones.async", $mission->id) }}';
                    break;
                case "control-points-section":
                    url = '{{ route("controlPoints.async", $mission->id) }}';
                    break;
                case "statistics-section":
                    url = '{{ route("dataRecord.async", $mission->id) }}';
                    break;
                default:
                    return;
            }

            console.log(`Loading content from: ${url}`);

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Network response was not ok: ${response.statusText}`);
                    }
                    return response.text();
                })
                .then(html => {
                    section.innerHTML = html;
                    initializeFormEvents(); // Reinitialize events
                })
                .catch(error => {
                    console.error('Error loading section content:', error);
                });
        }

        function initializeFormEvents() {
            const controlPoints = @json($controlPoints);

            const addRowButton = document.getElementById('add-row');
            const controlPointsContainer = document.getElementById('control-points');

            if (controlPointsContainer) {
                controlPointsContainer.addEventListener('click', function(e) {
                    if (e.target && e.target.classList.contains('remove-row')) {
                        console.log('Remove button clicked');
                        e.target.closest('.row').remove();
                    }
                });
            }

            if (addRowButton) {
                addRowButton.addEventListener('click', function() {
                    console.log('Add button clicked');
                    let newRow = document.createElement('div');
                    newRow.className = 'row mb-3';
                    newRow.innerHTML = `
                <div class="col">
                    <label for="control_point" class="form-label">Control Point:</label>
                    <select name="control_point[]" class="form-select" required>
                        ${controlPoints.map(controlPoint =>
                        `<option value="${controlPoint.id}">
                                ${controlPoint.longitude} | ${controlPoint.latitude} | ${controlPoint.data_type}
                            </option>`).join('')}
                    </select>
                </div>
                <div class="col-auto d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-row">-</button>
                </div>
            `;
                    controlPointsContainer.appendChild(newRow);
                });
            }

            const createDataRecordForm = document.getElementById('create-data-record-form');
            if (createDataRecordForm) {
                createDataRecordForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    console.log('Form submit');

                    let form = e.target;
                    let formData = new FormData(form);
                    let controlPoints = formData.getAll('control_point[]');

                    Promise.all(controlPoints.map(controlPointId => {
                        let newFormData = new FormData();
                        newFormData.append('drone_id', formData.get('drone_id'));
                        newFormData.append('mission_id', formData.get('mission_id'));
                        newFormData.append('control_point_id', controlPointId);
                        newFormData.append('_token', formData.get('_token'));

                        return fetch('{{ route('dataRecord.store') }}', {
                            method: 'POST',
                            body: newFormData
                        })
                            .then(response => response.json());
                    }))
                        .then(results => {
                            console.log('All data records created', results);
                            loadSectionContent(document.getElementById('data-records-section')); // Reload the data records section
                        })
                        .catch(error => {
                            console.error('Error creating data records:', error);
                        });
                });
            }
        }
    </script>
@endsection
