<div id="statistics-section" class="row justify-content-center">
    <div class="col-md-10 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title justify-content-center">Statistics</h5>
                <div class="row">
                    <div class="col-md-3">
                        <label for="control_point_select" class="form-label">Control Point:</label>
                        <select id="control_point_select" name="control_point_select" class="form-select control_point_select">
                            <option value="">All Control Points</option>
                            @foreach($controlPoints as $controlPoint)
                                <option value="{{ $controlPoint->id }}">{{ $controlPoint->id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="drone_select" class="form-label">Drone:</label>
                        <select id="drone_select" name="drone_select" class="form-select drone_select">
                            <option value="">All Drones</option>
                            @foreach($drones as $drone)
                                <option value="{{ $drone->id }}">{{ $drone->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <p>Total Records: <span id="total_w">{{ $statistics['w'] }}</span></p>
                        <p>Unacceptable Data: <span id="total_z0">{{ $statistics['z0'] }}</span> |
                            Unacceptable Data Probability: <span id="percent_p0">{{ number_format($statistics['p0'], 2) }}%</span></p>
                        <p>Acceptable Data: <span id="total_z1">{{ $statistics['z1'] }}</span> |
                            Acceptable Data Probability: <span id="percent_p1">{{ number_format($statistics['p1'], 2) }}%</span></p>
                        <p>Excellent Data: <span id="total_z2">{{ $statistics['z2'] }}</span> |
                            Excellent Data Probability: <span id="percent_p2">{{ number_format($statistics['p2'], 2) }}%</span></p>
                        <p>Uncollected Data (Malfunction): <span id="total_zn">{{ $statistics['zn'] }}</span> |
                            Uncollected Data Probability: <span id="percent_pn">{{ number_format($statistics['pn'], 2) }}%</span></p>
                    </div>
                </div>

                @if(!$mission->automatic)
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <button id="recalculate-button" name="recalculate-button" class="btn btn-primary">Recalculate</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
