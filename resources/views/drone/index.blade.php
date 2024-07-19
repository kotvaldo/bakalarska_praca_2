@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if (session('alert'))
                    <div class="row">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> {{ session('alert') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif
                    <div class="form-group text-danger mb-2">
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                <div class="row">
                    @can('create', App\Models\Drone::class)
                        <div class="mb-3">
                            <a href="{{ route('drone.create') }}" class="btn btn-sm btn-success" role="button"><i
                                    class="bi bi-plus-circle"></i> {{ __('Add new drone') }}</a>
                        </div>
                    @endcan
                        <div class="mb-3">
                            <form action="{{ route('drone.factory') }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <input type="number" name="count" id="count" class="form-control" placeholder="{{ __('Number of drones') }}" min="1" required>
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-plus-circle"></i> {{ __('Factory drones') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                </div>
                <div class="row">
                    {!! $grid->show() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
