@extends('layouts.master')

@section('content')
@php
    $lang = session('app_locale', 'ro') == 'ro';
@endphp

<div class="container">
    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>{{ $lang ? 'Eroare:' : 'Error:' }}</strong> {{ $errors->first() }}
        </div>
    @endif

    <div class="alert alert-danger" role="alert">
        <strong>{{ $lang ? 'Eroare:' : 'Error:' }}</strong> {{ $lang ? 'PayPal - Tranzacție respinsă' : 'PayPal Error - Transaction Declined' }}
    </div>
</div>
@endsection