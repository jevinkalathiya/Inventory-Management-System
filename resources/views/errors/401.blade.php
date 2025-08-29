@extends('layouts.errorlayout')

@section('error-code', '401')

@section('content')
    <div class="misc-wrapper">
        <h1 class="mb-2 mx-2" style="line-height: 6rem;font-size: 6rem;">401</h1>
        <h4 class="mb-2 mx-2">Unauthorized Access 🔒</h4>
        <p class="mb-6 mx-2">You don’t have permission to access this page.</p>
        <a href="{{ route('index') }}" class="btn btn-primary">Back to home</a>
        <div class="mt-6">
            <img src="{{ asset('assets/img/illustrations/403.png') }}" alt="403 Error" width="500" class="img-fluid"
                data-app-light-img="illustrations/403.png" data-app-dark-img="illustrations/403.png" />
        </div>
    </div>
@endsection