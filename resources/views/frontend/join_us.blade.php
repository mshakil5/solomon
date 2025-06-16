@extends('layouts.master')

@section('content')

@php
    $lang = session('app_locale', 'en') == 'ro';
@endphp

<section class="contact-section section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12 mx-auto">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <form class="custom-form contact-form" action="{{ route('join.us.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <h2 class="text-primary text-center mb-4">
                        {{ $lang ? 'Alătură-te nouă' : 'Join Us' }}
                    </h2>

                    @if ($errors->any())
                        <p class="text-danger">{{ $errors->first() }}</p>
                    @endif

                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-12">
                            <input type="text" name="name" id="name" value="{{ old('name', auth()->check() ? auth()->user()->name : '') }}" class="form-control" placeholder="{{ $lang ? 'Numele tău *' : 'Your Name *' }}" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="email" name="email" id="email" value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}" class="form-control" placeholder="{{ $lang ? 'Emailul tău *' : 'Your Email *' }}" required>
                        </div>

                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="text" name="phone" id="phone" value="{{ old('phone', auth()->check() ? auth()->user()->phone : '') }}" class="form-control" placeholder="{{ $lang ? 'Număr de telefon *' : 'Your Phone Number *' }}" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-12 col-md-6 col-12">
                            <input type="text" name="address_first_line" id="address_first_line" class="form-control" value="{{ old('address_first_line', auth()->user()->address_first_line ?? '') }}" placeholder="{{ $lang ? 'Adresă linia 1 *' : 'Address Line 1 *' }}" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-6 col-md-6 col-6">
                            <input type="text" name="address_second_line" id="address_second_line" class="form-control" placeholder="{{ $lang ? 'Adresă linia 2' : 'Address Line 2' }}" value="{{ old('address_second_line', auth()->user()->address_second_line ?? '') }}">
                        </div>
                        <div class="col-lg-6 col-md-6 col-6">
                            <input type="text" name="address_third_line" id="address_third_line" class="form-control" placeholder="{{ $lang ? 'Adresă linia 3' : 'Address Line 3' }}" value="{{ old('address_third_line', auth()->user()->address_third_line ?? '') }}">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="text" name="town" id="town" class="form-control" placeholder="{{ $lang ? 'Oraș *' : 'Town *' }}" required value="{{ old('town', auth()->user()->town ?? '') }}">
                        </div>

                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="text" name="postcode" id="postcode" class="form-control" placeholder="{{ $lang ? 'Cod poștal *' : 'Postcode *' }}" required value="{{ old('post_code', auth()->user()->postcode ?? '') }}">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-12 col-md-6 col-12">
                            <label for="cv">
                                {{ $lang ? 'Încarcă CV-ul tău' : 'Upload Your CV' }} <span class="text-danger">*</span> :
                            </label>
                            <input type="file" name="cv" id="cv" class="form-control" accept=".pdf,.docx">
                            <small class="form-text text-muted">
                                {{ $lang ? 'Te rugăm să încarci CV-ul în format PDF sau DOCX. Dimensiunea maximă: 2MB.' : 'Please upload your CV in PDF or DOCX format. Maximum file size: 2MB.' }}
                            </small>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="form-control btn btn-primary">
                                {{ $lang ? 'Trimite' : 'Submit' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection