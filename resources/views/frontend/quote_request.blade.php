@extends('layouts.master')

@section('content')

@php
    $lang = session('app_locale', 'ro');
@endphp

<section class="contact-section section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12 mx-auto">
                <form class="custom-form contact-form" action="{{ route('quote.request') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <h2>{{ $lang == 'ro' ? 'Cerere de ofertă' : 'Request a Quote' }}</h2>
                    
                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-12">
                            <input type="text" name="name" id="name" value="{{ old('name', auth()->check() ? auth()->user()->name : '') }}" class="form-control" placeholder="{{ $lang == 'ro' ? 'Numele tău *' : 'Your Name *' }}" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="email" name="email" id="email" value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}" class="form-control" placeholder="{{ $lang == 'ro' ? 'Email *' : 'Your Email *' }}" required>
                        </div>

                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="text" name="phone" id="phone" value="{{ old('phone', auth()->check() ? auth()->user()->phone : '') }}" class="form-control" placeholder="{{ $lang == 'ro' ? 'Număr de telefon *' : 'Your Phone Number *' }}" required>
                        </div>
                    </div>

                    
                    <div class="row mt-3">
                        <div class="col-lg-12 col-md-6 col-12">
                            <input type="text" name="address_first_line" id="address_first_line" class="form-control" value="{{ old('address_first_line', auth()->user()->address_first_line ?? '') }}" placeholder="{{ $lang == 'ro' ? 'Adresă linia 1 *' : 'Address Line 1 *' }}" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-6 col-md-6 col-6">
                             <input type="text" name="address_second_line" id="address_second_line" class="form-control" placeholder="{{ $lang == 'ro' ? 'Adresă linia 2' : 'Address Line 2' }}" value="{{ old('address_second_line', auth()->user()->address_second_line ?? '') }}">
                        </div>
                        <div class="col-lg-6 col-md-6 col-6">
                            <input type="text" name="address_third_line" id="address_third_line" class="form-control" placeholder="{{ $lang == 'ro' ? 'Adresă linia 3' : 'Address Line 3' }}" value="{{ old('address_third_line', auth()->user()->address_third_line ?? '') }}">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="text" name="town" id="town" class="form-control" placeholder="{{ $lang == 'ro' ? 'Oraș *' : 'Town *' }}" required value="{{ old('town', auth()->user()->town ?? '') }}">
                        </div>

                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="text" name="postcode" id="postcode" class="form-control" placeholder="{{ $lang == 'ro' ? 'Cod poștal *' : 'Postcode *' }}" required value="{{ old('postcode', auth()->user()->postcode ?? '') }}">
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <textarea name="details" id="details" class="form-control" rows="5" placeholder="{{ $lang == 'ro' ? 'Detalii cerere *' : 'Details of Your Request *' }}" required>{{ old('details') }}</textarea>
                            <small class="form-text text-muted">
                                {{ $lang == 'ro' ? 'Minim 10 caractere, maxim 500 caractere.' : 'Minimum 10 characters, maximum 500 characters.' }}
                            </small>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="file">{{ $lang == 'ro' ? 'Încarcă imagine/video' : 'Upload Image/Video' }}</label>
                            <small class="form-text text-muted">
                                {{ $lang == 'ro' ? 'Dimensiune maximă fișier: ' : 'Max file size: ' }}<strong>10MB</strong>
                            </small>
                            <input type="file" name="file" id="file" class="form-control" accept="image/*,video/*">
                            <small class="form-text text-muted">
                                {{ $lang == 'ro' ? 'Puteți încărca imagine sau video.' : 'You can upload image or video.' }}
                            </small>
                        </div>
                    </div>                  

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="form-control btn btn-primary">
                                {{ $lang == 'ro' ? 'Trimite cererea' : 'Submit Request' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let suggestionsList = $('#suggestions-list');

        $('#city').on('input', function() {
            let city = $(this).val();
            
            suggestionsList.empty();

            if (city.length >= 2) {
                $.ajax({
                    url: '{{ route('suggest.city') }}',
                    method: 'GET',
                    data: { city: city },
                    success: function(suggestions) {
                        let uniqueSuggestions = new Set(suggestions);
                        suggestionsList.empty();

                        if (uniqueSuggestions.size > 0) {
                            uniqueSuggestions.forEach(function(suggestion) {
                                suggestionsList.append('<li class="suggestion-item">' + suggestion + '</li>');
                            });
                        } else {
                            suggestionsList.append('<li>No suggestions found</li>');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching suggestions:', xhr);
                    }
                });
            }

            clearTimeout($.data(this, 'timer'));
            let timeout = setTimeout(function() {
                if (city.length >= 2) {
                    $.ajax({
                        url: '{{ route('check.city') }}',
                        method: 'GET',
                        data: { city: city },
                        success: function(response) {
                            $('#city-message').remove();
                            let message = $('<p id="city-message" class="mt-2"></p>');

                            if (response.success) {
                                message.addClass('text-success').text(response.message);
                            } else {
                                message.addClass('text-danger').text(response.message);
                            }

                            $('#city').before(message);
                        },
                        error: function(xhr) {
                            $('#city-message').remove();
                            let message = $('<p id="city-message" class="mt-2 text-danger"></p>');
                            $('#city').before(message);
                        }
                    });
                } else {
                    $('#city-message').remove();
                }
            }, 500);
            $(this).data('timer', timeout);
        });

        $(document).on('click', '.suggestion-item', function() {
            let selectedCity = $(this).text();
            $('#city').val(selectedCity);
            $('#suggestions-list').empty();

            $.ajax({
                url: '{{ route('check.city') }}',
                method: 'GET',
                data: { city: selectedCity },
                success: function(response) {
                    $('#city-message').remove();
                    let message = $('<p id="city-message" class="mt-2"></p>');

                    if (response.success) {
                        message.addClass('text-success').text(response.message);
                    } else {
                        message.addClass('text-danger').text(response.message);
                    }

                    $('#city').before(message);
                },
                error: function(xhr) {
                    $('#city-message').remove();
                    let message = $('<p id="city-message" class="mt-2 text-danger"></p>');
                    $('#city').before(message);
                }
            });
        });
    });
</script>

@endsection