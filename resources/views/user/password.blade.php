@extends('layouts.user')

@section('content')

@php
    $lang = session('app_locale', 'en') == 'ro';
@endphp

<div class="row mt-3">
    <div class="col-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary">
                <h4 class="card-title text-white">
                    {{ $lang ? 'Schimbă parola' : 'Change Password' }}
                </h4>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('user.update.password') }}">
                    @csrf

                    <div class="row">
                        <div class="col-8 mx-auto">
                            <div class="form-group">
                                <label for="current_password" class="form-label">
                                    {{ $lang ? 'Parola actuală' : 'Current Password' }} <span class="text-danger">*</span>
                                </label>
                                <input id="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" required>
                                
                                @error('current_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-8 mx-auto">
                            <div class="form-group">
                                <label for="new_password" class="form-label">
                                    {{ $lang ? 'Parola nouă' : 'New Password' }} <span class="text-danger">*</span>
                                </label>
                                <input id="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror" name="new_password" required>
                                
                                @error('new_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-8 mx-auto">
                            <div class="form-group">
                                <label for="confirm_password" class="form-label">
                                    {{ $lang ? 'Confirmă parola nouă' : 'Confirm New Password' }} <span class="text-danger">*</span>
                                </label>
                                <input id="confirm_password" type="password" class="form-control" name="confirm_password" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-8 mx-auto">
                            <button type="submit" class="btn btn-primary">
                                {{ $lang ? 'Schimbă parola' : 'Change Password' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection