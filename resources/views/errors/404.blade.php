@extends('layouts.errorlayout')

@section('error-code', '404')

@section('content')
    <div class="misc-wrapper">
        <h1 class="mb-2 mx-2" style="line-height: 6rem;font-size: 6rem;">404</h1>
        <h4 class="mb-2 mx-2">Page Not Found️ ⚠️</h4>
        <p class="mb-6 mx-2">we couldn't find the page you are looking for</p>
        <a href="{{ route('index') }}" class="btn btn-primary">Back to home</a>
        <div class="mt-6">
            <img src="{{ asset('assets/img/illustrations/404-error.png') }}" alt="404 Error" width="500" class="img-fluid"
                data-app-light-img="illustrations/404-error.png" data-app-dark-img="illustrations/404-error.png" />
        </div>
    </div>
@endsection