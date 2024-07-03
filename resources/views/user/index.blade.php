@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
    @auth

        <div class="container mt-5">
            <div class="container text-center mt-5">
                <img src="{{asset('images/' . $model['profile_photo']) }}" alt="Profile Image" class="rounded-circle"
                     style="width: 100px; height: 100px; object-fit: cover;">
                <h4 class="mt-3">{{@$model->name}}</h4>
                <span class="mt-3">{{@$model->email}}</span>
            </div>

            <hr class="my-4">

            @if (session('alert'))
                <div class="row">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ session('alert') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            <div class="container d-flex justify-content-center editing" role="group">
                <a href="{{ route('user.edit', @$model ) }}" class="btn btn-primary">Edit Profile</a>

                <form action="{{ route('user.destroy', @$model ) }}" method="post" class="form-delete">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete Profile</button>
                </form>
            </div>
        </div>

    @else
    @endauth
@endsection
