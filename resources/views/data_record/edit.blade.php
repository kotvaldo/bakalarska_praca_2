@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><b>{{ __('Edit the Data Record Quality') }}</b></div>
                    <div class="card-body">
                        @include('data_record.form')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
