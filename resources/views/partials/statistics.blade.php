<div id="statistics-section" class="row justify-content-center">
    <div class="col-md-10 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title justify-content-center">Statistics</h5>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div id="statistics-content">
                            <p>Total Records: <span id="total-records">{{ $statistics['w'] }}</span></p>
                            <p>Unacceptable Data: <span id="unacceptable-data">{{ $statistics['z0'] }}</span> (<span id="unacceptable-percentage">{{ number_format($statistics['p0'], 2) }}</span>%)</p>
                            <p>Acceptable Data: <span id="acceptable-data">{{ $statistics['z1'] }}</span> (<span id="acceptable-percentage">{{ number_format($statistics['p1'], 2) }}</span>%)</p>
                            <p>Excellent Data: <span id="excellent-data">{{ $statistics['z2'] }}</span> (<span id="excellent-percentage">{{ number_format($statistics['p2'], 2) }}</span>%)</p>
                            <p>Uncollected Data: <span id="uncollected-data">{{ $statistics['zn'] }}</span> (<span id="uncollected-percentage">{{ number_format($statistics['pn'], 2) }}</span>%)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
