@extends('layouts.app')

@section('content')
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
                        <p class="card-text"><strong>User
                                ID:</strong> {{ $mission->user ? $mission->user->name : 'User was removed' }}</p>
                        <p class="card-text"><strong>Status:</strong> {{ $mission->active ? 'Active' : 'Inactive' }}</p>
                        <p class="card-text"><strong>Automatic:</strong> {{ $mission->automatic ? 'True' : 'False' }}
                        </p>
                        <p class="card-text"><strong>Created At:</strong> {{ $mission->created_at }}</p>
                        <p class="card-text"><strong>Total Control Points Count:</strong> {{ $mission->total_cp_count }}
                        </p>
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
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const navLinks = document.querySelectorAll('.nav-link[data-section]');
        const sections = document.querySelectorAll('.page-section');

        if (sections.length > 0 && navLinks.length > 0) {
            navLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetSection = document.getElementById(this.getAttribute('data-section'));

                    if (!targetSection) {
                        console.error('Target section not found:', this.getAttribute('data-section'));
                        return;
                    }

                    navLinks.forEach(link => {
                        if (link.classList) {
                            link.classList.remove('active');
                        }
                    });
                    sections.forEach(section => {
                        if (section.classList) {
                            section.classList.remove('active');
                        }
                    });

                    this.classList.add('active');
                    targetSection.classList.add('active');

                    sections.forEach(section => {
                        section.style.display = section === targetSection ? 'block' : 'none';
                    });

                    loadSectionContent(targetSection);
                });
            });

            const activeSection = document.querySelector('.page-section.active');
            if (activeSection) {
                activeSection.style.display = 'block';
                loadSectionContent(activeSection);
            } else {
                console.error('Active section not found');
            }
        }

        function loadSectionContent(section, newUrl = null) {
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
                    if(!newUrl) {
                        url = '{{ route("statistics.async", $mission->id) }}';
                    } else {
                        url = newUrl
                    }
                    break;
                default:
                    console.error('Invalid section id:', sectionId);
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
                    console.log('Section content updated');
                    initializeFormEvents();
                })
                .catch(error => {
                    console.error('Error loading section content:', error);
                });
        }

        let missionAutomatic = {{ $mission->automatic ? 'true' : 'false' }};
        let controlPointSelect = document.getElementById('control_point_select');
        let droneSelect = document.getElementById('drone_select');
        let recalculateButton = document.getElementById('recalculate-button');

        if (missionAutomatic === true) {
            if (controlPointSelect) {
                controlPointSelect.addEventListener('change', () => {
                    recalculateStatistics(controlPointSelect.value || 0, droneSelect.value || 0);
                });
            }
            if (droneSelect) {
                droneSelect.addEventListener('change', () => {
                    recalculateStatistics(controlPointSelect.value || 0, droneSelect.value || 0);
                });
            }
        }

        if (recalculateButton) {
            recalculateButton.addEventListener('click', () => {
                recalculateStatistics(controlPointSelect.value || 0, droneSelect.value || 0);
            });
            console.log('Event listener added to recalculateButton');
        }

        function recalculateStatistics(controlPointId, droneId) {
            if (!controlPointId) controlPointId = 0;
            if (!droneId) droneId = 0;

            const url = `/missions/{{ $mission->id }}/statistics-recalculate?control_point_id=${controlPointId}&drone_id=${droneId}`;
            console.log(`Communicating with server at: ${url}`);
            console.log(`control_point_id: ${controlPointId}, drone_id: ${droneId}`);

            loadSectionContent(document.getElementById('statistics-section'), url);
        }

        function initializeFormEvents() {
            const controlPoints = @json($controlPoints);

            const addRowButton = document.getElementById('add-row');
            const controlPointsContainer = document.getElementById('control-points');

            if (controlPointsContainer) {
                controlPointsContainer.addEventListener('click', function (e) {
                    if (e.target && e.target.classList.contains('remove-row')) {
                        console.log('Remove button clicked');
                        e.target.closest('.row').remove();
                    }
                });
            }

            if (addRowButton) {
                addRowButton.addEventListener('click', function () {
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
                createDataRecordForm.addEventListener('submit', function (e) {
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
                            loadSectionContent(document.getElementById('data-records-section'));
                        })
                        .catch(error => {
                            console.error('Error creating data records:', error);
                        });
                });
            }

            document.querySelectorAll('.delete-record').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();

                    if (!confirm('Are you sure you want to delete this data record?')) {
                        return;
                    }

                    const recordId = this.getAttribute('data-id');
                    const url = `/data-records-ajax/${recordId}`;

                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`Network response was not ok: ${response.statusText}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                console.log(`Record with id ${recordId} deleted successfully`);
                                loadSectionContent(document.getElementById('data-records-section')); // Reload section content
                            } else {
                                alert('Failed to delete the data record.');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            });
        }
    });
</script>
