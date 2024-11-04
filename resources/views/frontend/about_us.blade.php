@extends('layouts.master')

@section('content')

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-10">
            <div class="card">
                <div class="card-body">
                    {!! $aboutUs !!}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection