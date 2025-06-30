@extends('layouts.master')

@section('content')

@php
    $lang = session('app_locale', 'ro');
@endphp

<section class="contact-section section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12 mx-auto">

                <form class="custom-form contact-form" id="reviewForm" action="{{ route('review.store') }}" method="post" enctype="multipart/form-data" role="form">
                    @csrf

                    <h2 class="text-primary fw-semibold mb-4 text-center">
                        {{ $lang == 'ro' ? 'Formular de recenzie' : 'Review Form' }}
                    </h2>

                    <div class="row">
                        <div class="col-12">
                            <input type="text" name="name" id="name" class="form-control" placeholder="{{ $lang == 'ro' ? 'Numele tău *' : 'Your Name *' }}" value="{{ old('name', auth()->check() ? auth()->user()->name : '') }}" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <input type="email" name="email" id="email" class="form-control" placeholder="{{ $lang == 'ro' ? 'Email *' : 'Email *' }}" value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="{{ $lang == 'ro' ? 'Număr de telefon *' : 'Phone Number *' }}" value="{{ old('phone', auth()->check() ? auth()->user()->phone : '') }}" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <label class="form-label">{{ $lang == 'ro' ? 'Evaluează-ne *' : 'Rate Us *' }}</label>
                            <div id="star-rating" class="star-rating">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="stars" value="{{ $i }}" id="star{{ $i }}" {{ old('stars') == $i ? 'checked' : ($i == 5 ? 'checked' : '') }}>
                                    <label for="star{{ $i }}">&#9733;</label>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <textarea name="review" rows="5" class="form-control" id="review" placeholder="{{ $lang == 'ro' ? 'Recenzia ta *' : 'Your Review *' }}" required>{{ old('review') }}</textarea>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="form-control btn btn-primary">
                                {{ $lang == 'ro' ? 'Trimite recenzia' : 'Submit Review' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@if($reviews->count() > 0)
<div class="container">
    <section class="mt-5">
        <h2 class="text-center mb-4 text-primary fw-bold">{{ $lang == 'ro' ? 'Recenzii' : 'Reviews' }}</h2>
        <div class="row justify-content-center">
            @foreach($reviews as $review)
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card review-card p-4 h-100 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0 text-primary">{{ $review->name }}</h5>
                        <div class="star-rating">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= $review->stars ? 'text-warning' : 'text-muted' }}">&#9733;</span>
                            @endfor
                        </div>
                    </div>
                    <p class="text-muted">
                        {{ \Illuminate\Support\Str::limit($review->review, 150) }}
                        @if(strlen($review->review) > 150)
                            <button class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $review->id }}" data-review="{{ addslashes($review->review) }}">
                                {{ $lang == 'ro' ? 'Citește mai mult' : 'Read More' }}
                            </button>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="reviewModal{{ $review->id }}" tabindex="-1">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $review->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">{{ $review->review }}</div>
                    </div>
                </div>
            </div>

            @endforeach
        </div>
    </section>
</div>
@endif

@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modals = document.querySelectorAll('.modal');
        modals.forEach(function(modal) {
            modal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var reviewText = button.getAttribute('data-review');
                modal.querySelector('.modal-body').innerHTML = reviewText;
            });
        });
    });
</script>
@endsection