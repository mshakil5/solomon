@extends('layouts.master')

@section('content')
{{-- 
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Request a Quote</h2>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('quote.request') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Your Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Your Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control" required>
                                    @error('email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="phone" class="form-label">Your Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="phone" class="form-control" required>
                            @error('phone')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="details" class="form-label">Details of Your Request <span class="text-danger">*</span></label>
                            <textarea name="details" id="details" class="form-control" rows="5" required></textarea>
                            @error('details')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
--}}

<section class="contact-section section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12 mx-auto">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <form class="custom-form contact-form" action="{{ route('quote.request') }}" method="POST">
                    @csrf
                    <h2>Request a Quote</h2>

                    @if ($errors->any())
                        <p class="text-danger">
                            {{ $errors->first() }}
                        </p>
                    @endif
                    
                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-12">
                            <input type="text" name="name" id="name" value="{{ auth()->check() ? auth()->user()->name : '' }}" class="form-control" placeholder="Your Name *" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="email" name="email" id="email" value="{{ auth()->check() ? auth()->user()->email : '' }}" class="form-control" placeholder="Your Email *" required>
                        </div>

                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="text" name="phone" id="phone" value="{{ auth()->check() ? auth()->user()->phone : '' }}" class="form-control" placeholder="Your Phone Number *" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-12 col-md-6 col-12">
                            <input type="text" name="city" id="city" class="form-control" placeholder="Area/City *" required>
                            <ul id="suggestions-list" class="list-unstyled mt-2"></ul>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <textarea name="details" id="details" class="form-control" rows="5" placeholder="Details of Your Request *" required></textarea>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="form-control btn btn-primary">Submit Request</button>
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
                            let message = $('<p id="city-message" class="mt-2 text-danger">An error occurred. Please try again.</p>');
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
                    let message = $('<p id="city-message" class="mt-2 text-danger">An error occurred. Please try again.</p>');
                    $('#city').before(message);
                }
            });
        });
    });
</script>

@endsection