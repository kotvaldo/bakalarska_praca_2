<div id="statistics-section" class="row justify-content-center">
    <div class="col-md-10 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title justify-content-center">Statistics</h5>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div id="statistics-content">
                            <div id="statistics-content">
                                <p>W -> Total Records: <span id="total-records">{{ $statistics['w'] }}</span></p>
                                <p>Z0 -> Unacceptable Data: <span id="unacceptable-data">{{ $statistics['z0'] }}</span></p>
                                <p>Z1 -> Acceptable Data: <span id="acceptable-data">{{ $statistics['z1'] }}</span></p>
                                <p>Z2 -> Excellent Data: <span id="excellent-data">{{ $statistics['z2'] }}</span></p>
                                <p>Zn -> Uncollected Data: <span id="uncollected-data">{{ $statistics['zn'] }}</span></p>
                                <p>P0 -> Unacceptable + Uncollected Data Percentage: <span id="unacceptable-percentage">{{ number_format($statistics['p0'], 2) }}</span>%</p>
                                <p>P1 -> Acceptable Data Percentage: <span id="acceptable-percentage">{{ number_format($statistics['p1'], 2) }}</span>%</p>
                                <p>P2 -> Excellent Data Percentage: <span id="excellent-percentage">{{ number_format($statistics['p2'], 2) }}</span>%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
