@extends('layouts.app')

@section('content')
    <div class="container">
        @guest
            <div class="text-center mt-5">
                <h1 class="display-4">For more actions, please log in</h1>
            </div>
        @elseauth
            <div class="container mt-5 text-center">
                <h1 class="display-3 font-weight-bold mb-4 text-primary">Welcome to Drone Monitoring System</h1>
                <img src="{{ asset('images/drone_image_home.png') }}" class="img-fluid mb-4" alt="Drone Image" style="width: 100%; max-width: 500px;">
                <div class="d-flex justify-content-center">
                    <a href="{{route('mission.create')}}" class="btn btn-primary mx-2">Create Mission</a>
                    <a href="{{route('mission.index')}}" class="btn btn-secondary mx-2">Browse Missions</a>
                </div>
            </div>
        @endauth

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    </div>
@endsection
