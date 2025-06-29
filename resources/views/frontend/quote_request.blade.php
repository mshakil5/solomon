@extends('layouts.master')

@section('content')

@php
    $lang = session('app_locale', 'ro');
@endphp

<style>
    .error-message {
        color: red;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: none;
    }
    .error-field {
        border: 2px solid red !important;
    }
</style>

<section class="contact-section section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12 mx-auto">
                <form id="quoteForm" class="custom-form contact-form" action="{{ route('quote.request') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <h2>{{ $lang == 'ro' ? 'Cerere de ofertă' : 'Request a Quote' }}</h2>
                    
                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-12">
                            <input type="text" name="name" id="name" value="{{ old('name', auth()->check() ? auth()->user()->name : '') }}" class="form-control" placeholder="{{ $lang == 'ro' ? 'Numele tău *' : 'Your Name *' }}">
                            <div id="name-error" class="error-message">{{ $lang == 'ro' ? 'Numele este obligatoriu.' : 'Name is required.' }}</div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="email" name="email" id="email" value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}" class="form-control" placeholder="{{ $lang == 'ro' ? 'Email *' : 'Your Email *' }}">
                            <div id="email-error" class="error-message">{{ $lang == 'ro' ? 'Vă rugăm să introduceți un email valid.' : 'Please enter a valid email.' }}</div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="text" name="phone" id="phone" value="{{ old('phone', auth()->check() ? auth()->user()->phone : '') }}" class="form-control" placeholder="{{ $lang == 'ro' ? 'Număr de telefon *' : 'Your Phone Number *' }}" >
                            <div id="phone-error" class="error-message">{{ $lang == 'ro' ? 'Vă rugăm să introduceți un număr de telefon valid.' : 'Please enter a valid phone number.' }}</div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-12 col-md-6 col-12">
                            <input type="text" name="address_first_line" id="address_first_line" class="form-control" value="{{ old('address_first_line', auth()->user()->address_first_line ?? '') }}" placeholder="{{ $lang == 'ro' ? 'Adresă linia 1 *' : 'Address Line 1 *' }}" >
                            <div id="address_first_line-error" class="error-message">{{ $lang == 'ro' ? 'Adresa linia 1 este obligatorie.' : 'Address Line 1 is required.' }}</div>
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
                            <input type="text" name="town" id="town" class="form-control" placeholder="{{ $lang == 'ro' ? 'Oraș *' : 'Town *' }}" value="{{ old('town', auth()->user()->town ?? '') }}">
                            <div id="town-error" class="error-message">{{ $lang == 'ro' ? 'Orașul este obligatoriu.' : 'Town is required.' }}</div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="text" name="postcode" id="postcode" class="form-control" placeholder="{{ $lang == 'ro' ? 'Cod poștal *' : 'Postcode *' }}"  value="{{ old('postcode', auth()->user()->postcode ?? '') }}">
                            <div id="postcode-error" class="error-message">{{ $lang == 'ro' ? 'Codul poștal este obligatoriu.' : 'Postcode is required.' }}</div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <textarea name="details" id="details" class="form-control" rows="5" placeholder="{{ $lang == 'ro' ? 'Detalii cerere *' : 'Details of Your Request *' }}" >{{ old('details') }}</textarea>
                            <div id="details-error" class="error-message">{{ $lang == 'ro' ? 'Detaliile sunt obligatorii (10-500 caractere).' : 'Details are required (10-500 characters).' }}</div>
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
                            <div id="file-error" class="error-message">{{ $lang == 'ro' ? 'Fișierul trebuie să fie o imagine sau un videoclip mai mic de 10MB.' : 'File must be an image or video smaller than 10MB.' }}</div>
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

    $('#quoteForm').on('submit', function(e) {
        let isValid = true;
        $('.error-message').hide();
        $('.form-control').removeClass('error-field');
        hideLoader();

        // Name validation
        let name = $('#name').val().trim();
        if (!name) {
            $('#name-error').show();
            $('#name').addClass('error-field').focus();
            isValid = false;
        }

        // Email validation
        let email = $('#email').val().trim();
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email || !emailRegex.test(email)) {
            $('#email-error').show();
            $('#email').addClass('error-field').focus();
            isValid = false;
        }

        // Phone validation
        let phone = $('#phone').val().trim();
        let phoneRegex = /^\+?[\d\s-]{10,}$/;
        if (!phone || !phoneRegex.test(phone)) {
            $('#phone-error').show();
            $('#phone').addClass('error-field').focus();
            isValid = false;
        }

        // Address first line validation
        let addressFirstLine = $('#address_first_line').val().trim();
        if (!addressFirstLine) {
            $('#address_first_line-error').show();
            $('#address_first_line').addClass('error-field').focus();
            isValid = false;
        }

        // Town validation
        let town = $('#town').val().trim();
        if (!town) {
            $('#town-error').show();
            $('#town').addClass('error-field').focus();
            isValid = false;
        }

        // Postcode validation
        let postcode = $('#postcode').val().trim();
        if (!postcode) {
            $('#postcode-error').show();
            $('#postcode').addClass('error-field').focus();
            isValid = false;
        }

        // Details validation
        let details = $('#details').val().trim();
        if (!details || details.length < 10 || details.length > 500) {
            $('#details-error').show();
            $('#details').addClass('error-field').focus();
            isValid = false;
        }

        // File validation
        let file = $('#file')[0].files[0];
        if (file) {
            let fileSize = file.size / 1024 / 1024; // Size in MB
            let validTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/mpeg', 'video/webm'];
            if (fileSize > 10 || !validTypes.includes(file.type)) {
                $('#file-error').show();
                $('#file').addClass('error-field').focus();
                isValid = false;
            }
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    // Real-time validation on input
    $('.form-control').on('input change', function() {
        let id = $(this).attr('id');
        let value = $(this).val().trim();
        
        if (id === 'name' && value) {
            $(this).removeClass('error-field');
            $('#name-error').hide();
        }
        if (id === 'email' && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            $(this).removeClass('error-field');
            $('#email-error').hide();
        }
        if (id === 'phone' && /^\+?[\d\s-]{10,}$/.test(value)) {
            $(this).removeClass('error-field');
            $('#phone-error').hide();
        }
        if (id === 'address_first_line' && value) {
            $(this).removeClass('error-field');
            $('#address_first_line-error').hide();
        }
        if (id === 'town' && value) {
            $(this).removeClass('error-field');
            $('#town-error').hide();
        }
        if (id === 'postcode' && value) {
            $(this).removeClass('error-field');
            $('#postcode-error').hide();
        }
        if (id === 'details' && value.length >= 10 && value.length <= 500) {
            $(this).removeClass('error-field');
            $('#details-error').hide();
        }
        if (id === 'file') {
            let file = this.files[0];
            if (file) {
                let fileSize = file.size / 1024 / 1024;
                let validTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/mpeg', 'video/webm'];
                if (fileSize <= 10 && validTypes.includes(file.type)) {
                    $(this).removeClass('error-field');
                    $('#file-error').hide();
                }
            } else {
                $(this).removeClass('error-field');
                $('#file-error').hide();
            }
        }
    });

    // City suggestions (unchanged)
    $('#town').on('input', function() {
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

                        $('#town').before(message);
                    },
                    error: function(xhr) {
                        $('#city-message').remove();
                        let message = $('<p id="city-message" class="mt-2 text-danger"></p>');
                        $('#town').before(message);
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
        $('#town').val(selectedCity);
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

                $('#town').before(message);
            },
            error: function(xhr) {
                $('#city-message').remove();
                let message = $('<p id="city-message" class="mt-2 text-danger"></p>');
                $('#town').before(message);
            }
        });
    });
});
</script>
@endsection