@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><b>{{ __('Edit the Mission') }}</b></div>
                    <div class="card-body">
                        @include('mission.form_edit')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
