@extends('layouts.master')

@section('content')


<style>
    @media (max-width: 768px) {
        .about-us-image {
            max-width: 100%;
        }
    }
</style>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-10">
            {!! $aboutUs !!}
        </div>
    </div>
</div>

@endsection