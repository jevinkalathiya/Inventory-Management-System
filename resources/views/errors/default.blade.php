@extends('layouts.errorlayout')

@section('error-code', 'Unknown Error')

@section('content')
    <div class="misc-wrapper">
        <h4 class="mb-2 mx-2">Unknown Error ⚠️</h4>
        <p class="mb-6 mx-2">Access denied. It seems you don’t have the necessary permissions. Go back to the homepage.</p>
        <a href="{{ route('index') }}" class="btn btn-primary">Back to home</a>
        <div class="mt-6">
            <img src="{{ asset('assets/img/illustrations/unkown-error.png') }}" alt="Unkown Error"
                width="500" class="img-fluid" data-app-light-img="illustrations/unkown-error.png"
                data-app-dark-img="illustrations/unkown-error.png" />
        </div>
    </div>
@endsection
