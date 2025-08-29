@extends('layouts.errorlayout')

@section('error-code', '403')

@section('content')
    <div class="misc-wrapper">
        <h1 class="mb-2 mx-2" style="line-height: 6rem;font-size: 6rem;">403</h1>
        <h4 class="mb-2 mx-2">You are not authorized! 🔐</h4>
        <p class="mb-6 mx-2">You don’t have permission to access this page. Go Home!</p>
        <a href="{{ route('index') }}" class="btn btn-primary">Back to home</a>
        <div class="mt-6">
            <img src="{{ asset('assets/img/illustrations/401-error.png') }}" alt="401 Error"
                width="500" class="img-fluid" data-app-light-img="illustrations/401-error.png"
                data-app-dark-img="illustrations/401-error.png" />
        </div>
    </div>
@endsection