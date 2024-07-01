@extends('layouts.app')

@section('content')
    <div class="container">
        @guest
            <div class="text-center mt-5">
                <h1 class="display-4">For more actions, please log in</h1>
            </div>
        @endguest
        {{-- Ďalší obsah pre prihlásených používateľov --}}
    </div>
@endsection
