<div class="form-group text-danger mb-2">
    @foreach ($errors->all() as $error)
        {{ $error }}<br>
    @endforeach
</div>
<form method="post" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @method($method)
    <div class="form-group mb-2">
        <label for="name">Name <span style="color: red">*</span></label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name: " value="{{ old('name', @$model->name) }}">
    </div>
    <div class="form-group mb-2">
        <label for="type">Drone Type <span style="color: red"> *</span></label>
        <select class="form-control" id="type" name="type" required >
            @foreach($droneTypes as $type)
                <option value="{{ $type }}" {{old('type',@$model->type) == $type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group mb-2">
        <label for="description">Serial Number <span style="color: red">*</span></label>
        <input class="form-control" id="serial_number" name="serial_number" placeholder="Enter Serial Number:">{{ old('description', @$model->serial_number) }}</input>
    </div>

    <div class="form-group mb-2">
        <label for="image">Image</label>
        <input type="file" class="form-control" id="image" name="image">
    </div>

    <a href="{{ route('drone.index') }}" class="btn btn-warning">Cancel</a>
    <input type="submit" class="btn btn-success" value="Submit">
</form>
