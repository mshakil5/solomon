@extends('layouts.user')

@section('content')

@php
    $lang = session('app_locale', 'ro') == 'ro';
@endphp

<div class="row mt-3">
    <div class="col-10 mx-auto">
        <div class="card">
            <div class="card-header bg-primary">
                <h4 class="card-title text-white">{{ $lang ? 'Profil' : 'Profile' }}</h4>
            </div>
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('user.profile.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label">
                                    {{ $lang ? 'Nume' : 'Name' }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="surname" class="form-label">
                                    {{ $lang ? 'Prenume' : 'First Name' }}
                                </label>
                                <input type="text" class="form-control" id="surname" name="surname" value="{{ $user->surname }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">
                                    {{ $lang ? 'Email' : 'Email' }} <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone" class="form-label">
                                    {{ $lang ? 'Telefon' : 'Phone' }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row d-none">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address_first_line" class="form-label">
                                    {{ $lang ? 'Adresă linia 1' : 'Address First Line' }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="address_first_line" name="address_first_line" value="{{ $user->address_first_line }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address_second_line" class="form-label">
                                    {{ $lang ? 'Adresă linia 2' : 'Address Second Line' }}
                                </label>
                                <input type="text" class="form-control" id="address_second_line" name="address_second_line" value="{{ $user->address_second_line }}">
                            </div>
                        </div>
                    </div>

                    <div class="row d-none">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="town" class="form-label">{{ $lang ? 'Oraș' : 'Town' }}</label>
                                <input type="text" class="form-control" id="town" name="town" value="{{ $user->town }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="postcode" class="form-label">
                                    {{ $lang ? 'Cod poștal' : 'Post Code' }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="postcode" name="postcode" value="{{ $user->postcode }}" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ $lang ? 'Actualizează profilul' : 'Update Profile' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        IdealPostcodes.AddressFinder.watch({
            apiKey: "ak_lt4ocv0eHLLo4meBRGHWK4HU0SBxa",
            outputFields: {
                line_1: "#address_first_line",
                line_2: "#address_second_line",
                line_3: "#address_third_line",
                post_town: "#town",
                postcode: "#postcode"
            }
        });
    });
</script>
@endsection