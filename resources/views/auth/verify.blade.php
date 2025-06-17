@extends('layouts.app')

@section('content')
@php
    $lang = session('app_locale', 'ro') == 'ro';
@endphp

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ $lang ? 'Verifică-ți adresa de e-mail' : 'Verify Your Email Address' }}
                </div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ $lang ? 'Un nou link de verificare a fost trimis pe adresa ta de e-mail.' : 'A fresh verification link has been sent to your email address.' }}
                        </div>
                    @endif

                    {{ $lang ? 'Înainte de a continua, verifică-ți adresa de e-mail pentru link-ul de confirmare.' : 'Before proceeding, please check your email for a verification link.' }}
                    <br>
                    {{ $lang ? 'Dacă nu ai primit e-mailul' : 'If you did not receive the email' }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">
                            {{ $lang ? 'click aici pentru a cere altul' : 'click here to request another' }}
                        </button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection