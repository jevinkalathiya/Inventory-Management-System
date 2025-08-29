@extends('layouts.masterlayout')

@section('title'){{ $type=='category' ?  'Create Category' : 'Create Product'}} @endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
<div class="col-xl">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $type=='category' ?  'Create Category' : 'Create Product'}}</h5>
            <small class="text-body float-end"><a href="{{ $type=='category' ?  route('list','category') : route('list','product') }}" class="btn btn-primary">View list</a></small>
        </div>
        <div class="card-body">
            @if ($errors->all())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <form action="{{ $type=='category' ?  route('create','category') : route('create','product') }}" method="POST">
                @csrf
                @if ($type == 'category')
                    <div class="mb-6">
                    <label class="form-label" for="basic-default-categoryname">Category Name</label>
                    <input type="text" class="form-control" id="basic-default-categoryname" name="category-name" placeholder="Electronics" />
                </div>
                @else
                    <div class="mb-6">
                        <label class="form-label" for="basic-default-company">Company</label>
                        <input type="text" class="form-control" id="basic-default-company" placeholder="ACME Inc." />
                    </div>
                    <div class="mb-6">
                        <label class="form-label" for="basic-default-email">Email</label>
                        <div class="input-group input-group-merge">
                            <input type="text" id="basic-default-email" class="form-control" placeholder="john.doe"
                                aria-label="john.doe" aria-describedby="basic-default-email2" />
                            <span class="input-group-text" id="basic-default-email2">@example.com</span>
                        </div>
                        <div class="form-text">You can use letters, numbers & periods</div>
                    </div>
                    <div class="mb-6">
                        <label class="form-label" for="basic-default-phone">Phone No</label>
                        <input type="text" id="basic-default-phone" class="form-control phone-mask"
                            placeholder="658 799 8941" />
                    </div>
                    <div class="mb-6">
                        <label class="form-label" for="basic-default-message">Message</label>
                        <textarea id="basic-default-message" class="form-control" placeholder="Hi, Do you have a moment to talk Joe?"></textarea>
                    </div>
                @endif
                
                <input type="submit" class="btn btn-primary"></input>
            </form>
        </div>
    </div>
</div>
</div>
@endsection
