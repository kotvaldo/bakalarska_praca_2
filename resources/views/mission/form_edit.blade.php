<div class="form-group text-danger mb-2">
    @foreach ($errors->all() as $error)
        {{ $error }}<br>
    @endforeach
</div>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<form action="{{ $action }}" method="POST">
    @csrf
    @method($method)
    <div class="form-group mb-2">
        <label for="name">Mission name <span style="color: red">*</span></label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name: " value="{{ old('name', $model->name) }}" required>
    </div>
    <div class="form-group mb-2">
        <label for="description">Mission description <span style="color: red">*</span></label>
        <textarea class="form-control" id="description" name="description" placeholder="Enter Description:" rows="4" required>{{ old('description', @$model->description) }}</textarea>
    </div>
    <div class="form-group mb-2">
        <label for="automatic">Automatic <span style="color: red">*</span></label>
        <select class="form-control" id="automatic" name="automatic" required>
            <option value="1" {{ old('automatic', $model->automatic) == 1 ? 'selected' : '' }}>True</option>
            <option value="0" {{ old('automatic', $model->automatic) == 0 ? 'selected' : '' }}>False</option>
        </select>
    </div>
    <div class="form-group mb-2">
        <label for="active">Active <span style="color: red">*</span></label>
        <select class="form-control" id="active" name="active" required>
            <option value="1" {{ old('active', $model->active) == 1 ? 'selected' : '' }}>True</option>
            <option value="0" {{ old('active', $model->active) == 0 ? 'selected' : '' }}>False</option>
        </select>
    </div>
    <div class="form-group mb-2">
        <label for="total_cp_count">Total CP Count <span style="color: red">*</span></label>
        <input type="number" class="form-control" id="total_cp_count" name="total_cp_count" placeholder="Enter Total CP Count: " value="{{ old('total_cp_count', $model->total_cp_count) }}" required>
    </div>
    <div class="form-group mb-2">
        <label for="drones">Select Drones <span style="color: red">*</span></label>
        <select class="form-control large-select" id="drones" name="drones[]" multiple required style="height: 400px;">
            @foreach($drones as $drone)
                <option value="{{ $drone->id }}" {{ in_array($drone->id, old('drones', $selectedDrones)) ? 'selected' : '' }}>ID:{{ $drone->id }} | Name:{{ $drone->name }} | DATA_TYPE:{{ $drone->type }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Update Mission</button>
</form>
