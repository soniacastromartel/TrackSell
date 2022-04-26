@extends('layouts.app')

@section('content')
    
     @if (Route::has('login'))
        <div class="top-right links">
            @auth 
            @else
                @include('auth.login')
            @endauth
        </div>
     @endif
@endsection