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

        <!-- Statické miesto na zobrazenie výberov dronu a CP -->
        <div id="statistics-filters" class="mb-3" style="display: none;">
            <div class="row">
                <div class="col">
                    <label for="control_point_select" class="form-label">Control Point:</label>
                    <select id="control_point_select" name="control_point_select" class="form-select">
                        <option value="0">All Control_Points</option>
                        @foreach ($controlPoints as $controlPoint)
                            <option value="{{ $controlPoint->id }}">ID:{{ $controlPoint->id }}
                                | X:{{ $controlPoint->latitude }} | Y:{{ $controlPoint->longitude }}
                                | DATA_TYPE:{{$controlPoint->data_type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <label for="drone_select" class="form-label">Drone:</label>
                    <select id="drone_select" name="drone_select" class="form-select">
                        <option value="0">All drones</option>
                        @foreach ($drones as $drone)
                            <option value="{{ $drone->id }}">ID:{{ $drone->id }} | NAME:{{ $drone->name }} |
                                DATA_TYPE: {{ $drone->type }}</option>
                        @endforeach
                    </select>
                </div>
                @if($mission->automatic === false)
                    <div class="col-auto d-flex align-items-end">
                        <button id="recalculate-button" class="btn btn-primary">Recalculate Statistics</button>
                    </div>
                @endif

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
        const statisticsFilters = document.getElementById('statistics-filters');
        const missionAutomatic = {{ $mission->automatic ? 'true' : 'false' }};
        const formContainer = document.getElementById('dynamic-form-container');

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

                    if (targetSection.id === 'statistics-section') {
                        statisticsFilters.style.display = 'block';
                    } else {
                        statisticsFilters.style.display = 'none';
                    }
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
                    if (!newUrl) {
                        url = '{{ route("statistics.async", $mission->id) }}';
                    } else {
                        url = newUrl;
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
                    if (sectionId === "data-records-section") {
                        initializeFormEvents();
                    }
                    if (sectionId === "statistics-section") {
                        initializeEventListeners();
                    }
                })
                .catch(error => {
                    console.error('Error loading section content:', error);
                });
        }

        function recalculateStatistics(controlPointId, droneId) {
            if (!controlPointId) controlPointId = 0;
            if (!droneId) droneId = 0;

            const url = `/missions/{{ $mission->id }}/statistics-recalculate?control_point_id=${controlPointId}&drone_id=${droneId}`;
            console.log(`Communicating with server at: ${url}`);
            console.log(`control_point_id: ${controlPointId}, drone_id: ${droneId}`);

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Network response was not ok: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Data received from server:', data); // Log received data
                    if (data) {
                        updateStatisticsSection(data);
                        console.log('Section content updated');
                    } else {
                        throw new Error('Data is undefined');
                    }
                    initializeEventListeners();
                })
                .catch(error => {
                    console.error('Error loading section content:', error);
                });
        }

        function updateStatisticsSection(data) {
            const statistics = data.statistics;

            document.getElementById('total-records').innerText = statistics.w;
            document.getElementById('unacceptable-data').innerText = statistics.z0;
            document.getElementById('acceptable-data').innerText = statistics.z1;
            document.getElementById('excellent-data').innerText = statistics.z2;
            document.getElementById('uncollected-data').innerText = statistics.zn;
            document.getElementById('unacceptable-percentage').innerText = statistics.p0.toFixed(2);
            document.getElementById('acceptable-percentage').innerText = statistics.p1.toFixed(2);
            document.getElementById('excellent-percentage').innerText = statistics.p2.toFixed(2);
        }

        function initializeEventListeners() {
            let controlPointSelect = document.getElementById('control_point_select');
            let droneSelect = document.getElementById('drone_select');
            let recalculateButton = document.getElementById('recalculate-button');

            if (controlPointSelect) {
                if (missionAutomatic) {
                    controlPointSelect.onchange = function () {
                        console.log('Control Point Select changed');
                        recalculateStatistics(controlPointSelect.value || 0, droneSelect ? droneSelect.value || 0 : 0);
                    };
                } else {
                    console.log('Mission is not automatic. Control Point Select event not added.');
                }
            } else {
                console.error('Control Point Select element not found.');
            }

            if (droneSelect) {
                if (missionAutomatic) {
                    droneSelect.onchange = function () {
                        console.log('Drone Select changed');
                        recalculateStatistics(controlPointSelect ? controlPointSelect.value || 0 : 0, droneSelect.value || 0);
                    };
                } else {
                    console.log('Mission is not automatic. Drone Select event not added.');
                }
            } else {
                console.error('Drone Select element not found.');
            }
            if (recalculateButton) {
                recalculateButton.onclick = function () {
                    recalculateStatistics(controlPointSelect ? controlPointSelect.value || 0 : 0, droneSelect ? droneSelect.value || 0 : 0);
                };
            }
        }

        function initializeFormEvents() {
            const controlPoints = @json($controlPoints);

            const addRowButton = document.getElementById('add-row');
            const controlPointsContainer = document.getElementById('control-points-path');

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
                    const rowCount = document.querySelectorAll('#control-points-path .row').length;
                    let newRow = document.createElement('div');
                    newRow.className = 'row mb-3';
                    newRow.innerHTML = `
                <div class="col">
                    <label for="control_point_${rowCount}" class="form-label">Control Point:</label>
                    <select id="control_point_${rowCount}" name="control_point[]" class="form-select" required>
                        ${controlPoints.map(controlPoint => `
                        <option value="${controlPoint.id}">
                            ID:${controlPoint.id} |
                            X:${controlPoint.longitude} |
                            Y:${controlPoint.latitude} |
                            DATATYPE:${controlPoint.data_type} |
                            DRONE_LIMIT:${controlPoint.drone_id ?? 'NONE'}
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
            document.querySelectorAll('.edit-record').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();

                    const recordId = this.getAttribute('data-id');
                    const url = `/data-records/${recordId}/edit-ajax`;

                    // Fetch data for the record
                    fetch(url)
                        .then(response => response.json())
                        .then(dataRecord => {
                            // Zobrazenie formulára na úpravu v dynamickom div-e
                            const formContainer = document.getElementById('dynamic-form-container');
                            formContainer.style.display = 'block';
                            formContainer.innerHTML = `
 <div class="row justify-content-center">
            <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Edit Data Record</h5>
                            <form id="edit-data-record-form">
                                <div class="form-group mb-2">
                                    <p class="form-control-plaintext"><strong>Data Record ID: </strong> ${dataRecord.id}</p>
                                </div>
                                <div class="form-group mb-2">
                                    <p class="form-control-plaintext"><strong>CP ID: </strong> ${dataRecord.control_point_id}</p>
                                </div>
                                <div class="form-group mb-2">
                                    <p class="form-control-plaintext"><strong>Drone ID: </strong>${dataRecord.drone_id}</p>
                                </div>
                                <div class="form-group mb-2">
                                    <strong>Data Quality: </strong>
                                    <select class="form-control" id="edit-data-quality" name="data_quality" required>
                                        <option value="" ${dataRecord.data_quality === null ? 'selected' : ''}>Select Data Quality</option>
                                        <option value="0" ${dataRecord.data_quality === 0 ? 'selected' : ''}>Unacceptable Data</option>
                                        <option value="1" ${dataRecord.data_quality === 1 ? 'selected' : ''}>Acceptable Data</option>
                                        <option value="2" ${dataRecord.data_quality === 2 ? 'selected' : ''}>Excellent Data</option>
                                        <option value="3" ${dataRecord.data_quality === 3 ? 'selected' : ''}>Uncollected Data (Malfunction)</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="button" class="btn btn-secondary" id="cancel-button">Cancel</button>
                            </form>
                        </div>
                    </div>
</div>
                    </div>
                `;

                            // Handle form submission
                            document.getElementById('edit-data-record-form').addEventListener('submit', function (e) {
                                e.preventDefault();

                                const formData = new FormData(this);

                                // Log FormData for debugging
                                console.log('FormData entries:');
                                for (let [key, value] of formData.entries()) {
                                    console.log(`${key}: ${value}`);
                                }

                                fetch(`/data-records/${dataRecord.id}/update-ajax`, {
                                    method: 'PUT',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify(Object.fromEntries(formData))
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            console.log(`Record with id ${dataRecord.id} updated successfully`);
                                            console.log('Server response:', data); // Log server response for debugging
                                            loadSectionContent(document.getElementById('data-records-section')); // Reload section content
                                            formContainer.style.display = 'none';
                                            formContainer.innerHTML = '';
                                        } else {
                                            alert('Failed to update the data record.');
                                        }
                                    })
                                    .catch(error => console.error('Error:', error));
                            });

                            // Handle cancel button
                            document.getElementById('cancel-button').addEventListener('click', function () {
                                formContainer.style.display = 'none';
                                formContainer.innerHTML = '';
                            });
                        })
                        .catch(error => console.error('Error fetching data record:', error));
                });
            });
        }

    });
</script>
