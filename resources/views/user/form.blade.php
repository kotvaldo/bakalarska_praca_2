<div class="form-group text-danger mb-2">
    @foreach ($errors->all() as $error)
        {{ $error }}<br>
    @endforeach
</div>
<form method="post" action="{{ $action }}">
    @csrf
    @method($method)
    <div class="form-group mb-2">
        <label for="name">Full name <span style="color: red">*</span></label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Full name" value="{{ old('name', @$model->name) }}">
    </div>
    <div class="form-group mb-2">
        <label for="email">Email address <span style="color: red">*</span></label>
        <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" placeholder="Enter email"
               value="{{ old('email', @$model->email) }}">
        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
    </div>
    <div class="form-group mb-2">
        <label for="password">Password <span style="color: red">*</span></label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
    </div>
    <div class="form-group mb-2">
        <label for="password"> Password again <span style="color: red">*</span></label>
        <input type="password" class="form-control" id="password" name="password_confirmation" placeholder="Password">
    </div>
    <input type="button" class="btn btn-warning" value="Cancel" onclick="history.back()">
    <input type="submit" class="btn btn-success" value="Sumbit">
</form>
