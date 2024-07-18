<div class="form-group text-danger mb-2">
    @foreach ($errors->all() as $error)
        {{ $error }}<br>
    @endforeach
</div>
<form method="post" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @method($method)

    <div class="form-group mb-2">
        <label>ID</label>
        <p class="form-control-plaintext">{{ $model->id }}</p>
    </div>

    <div class="form-group mb-2">
        <label>Mission</label>
        <p class="form-control-plaintext">{{ $model->mission->name }}</p>
    </div>

    <div class="form-group mb-2">
        <label>Control Point</label>
        <p class="form-control-plaintext">
            ID: {{ $model->control_point_id }} |
            X: {{ $model->controlPoint->longitude }} |
            Y: {{ $model->controlPoint->latitude }} |
            DATATYPE: {{ $model->controlPoint->data_type }}
        </p>
    </div>

    <div class="form-group mb-2">
        <label>Drone</label>
        <p class="form-control-plaintext">ID: {{$model->drone->id}} | NAME: {{$model->drone->name}} | DATATYPE: {{ $model->drone->type}}</p>
    </div>
    <div class="form-group mb-2">
        <label>Data</label>
        <p class="form-control-plaintext">Temporarily unavailable</p>
    </div>
    <div class="form-group mb-2">
        <label for="data_quality">Data Quality <span style="color: red">*</span></label>
        <select class="form-control" id="data_quality" name="data_quality" required>
            <option value="0" {{ $model->data_quality == 0 ? 'selected' : '' }}>Unacceptable Data</option>
            <option value="1" {{ $model->data_quality == 1 ? 'selected' : '' }}>Acceptable Data</option>
            <option value="2" {{ $model->data_quality == 2 ? 'selected' : '' }}>Excellent Data</option>
            <option value="3" {{ $model->data_quality == 3 ? 'selected' : '' }}>Uncollected Data (Malfunction)
            </option>
        </select>
    </div>


    <a href="{{ route('data_record.index') }}" class="btn btn-warning">Cancel</a>
    <input type="submit" class="btn btn-success" value="Submit">
</form>
