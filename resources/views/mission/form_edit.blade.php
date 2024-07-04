<form action="{{ $action }}" method="POST">
    @csrf
    @method($method)
    <div class="form-group mb-2">
        <label for="name">Mission name <span style="color: red">*</span></label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name: " value="{{ old('name', $model->name) }}" required>
    </div>
    <div class="form-group mb-2">
        <label for="automatic">Automatic <span style="color: red">*</span></label>
        <select class="form-control" id="automatic" name="automatic" required>
            <option value="1" {{ old('automatic', $model->automatic) == 1 ? 'selected' : '' }}>True</option>
            <option value="0" {{ old('automatic', $model->automatic) == 0 ? 'selected' : '' }}>False</option>
        </select>
    </div>
    <div class="form-group mb-2">
        <label for="total_cp_count">Total CP Count <span style="color: red">*</span></label>
        <input type="number" class="form-control" id="total_cp_count" name="total_cp_count" placeholder="Enter Total CP Count: " value="{{ old('total_cp_count', $model->total_cp_count) }}" required>
    </div>
    <div class="form-group mb-2">
        <label for="drones">Select Drones <span style="color: red">*</span></label>
        <select class="form-control" id="drones" name="drones[]" multiple required>
            @foreach($drones as $drone)
                <option value="{{ $drone->id }}" {{ in_array($drone->id, old('drones', $selectedDrones)) ? 'selected' : '' }}>{{ $drone->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Update Mission</button>
