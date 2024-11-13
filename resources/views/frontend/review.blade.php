@extends('layouts.master')

@section('content')

<section class="contact-section section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12 mx-auto">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <form class="custom-form contact-form" id="reviewForm" action="{{ route('review.store') }}" method="post" enctype="multipart/form-data" role="form">
                    @csrf
                    <h2>Review Form</h2>

                    @if ($errors->any())
                        <p class="text-danger">
                            {{ $errors->first() }}
                        </p>
                    @endif
                    
                    <div class="row">
                        <div class="col-lg-12 col-md-6 col-12">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Your Name *" value="{{ auth()->check() ? auth()->user()->name : '' }}" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="email" name="email" id="email" pattern="[^ @]*@[^ @]*" class="form-control" placeholder="Email *" value="{{ auth()->check() ? auth()->user()->email : '' }}" required>
                        </div>

                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone Number *" value="{{ auth()->check() ? auth()->user()->phone : '' }}" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="stars" class="form-label">Rate Us *</label>
                            <div id="star-rating" class="star-rating">
                                <input type="radio" name="stars" value="5" id="star5" checked><label for="star5">&#9733;</label>
                                <input type="radio" name="stars" value="4" id="star4"><label for="star4">&#9733;</label>
                                <input type="radio" name="stars" value="3" id="star3"><label for="star3">&#9733;</label>
                                <input type="radio" name="stars" value="2" id="star2"><label for="star2">&#9733;</label>
                                <input type="radio" name="stars" value="1" id="star1"><label for="star1">&#9733;</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <textarea name="review" rows="5" class="form-control" id="review" placeholder="Your Review *" required></textarea>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="form-control">Submit Review</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@if($reviews->count() > 0)
<section class="mt-3">
    <div class="col-12">
        <h2 class="text-center mb-5 text-primary fw-bold">Reviews</h2>
        <div class="row justify-content-center">
            @foreach($reviews as $review)
                <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                    <div class="card review-card p-4 h-100 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0 text-primary">{{ $review->name }}</h5>
                            <div class="star-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->stars)
                                        <span class="text-warning">&#9733;</span>
                                    @else
                                        <span class="text-muted">&#9733;</span>
                                    @endif
                                @endfor
                            </div>
                        </div>

                        <p class="text-muted text-justify">
                            {{ \Illuminate\Support\Str::limit($review->review, 150) }}
                            @if(strlen($review->review) > 150)
                                <button type="button" class="btn btn-link p-0" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#reviewModal{{ $review->id }}" 
                                        data-review="{{ addslashes($review->review) }}">
                                    Read More
                                </button>
                            @endif
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($reviews->count() > 0)
<div class="modal fade" id="reviewModal{{ $review->id }}" tabindex="-1" aria-labelledby="reviewModalLabel{{ $review->id }}" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel{{ $review->id }}">Full Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody{{ $review->id }}" style="max-height: 400px; overflow-y: auto;">
            </div>
        </div>
    </div>
</div>
@endif

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
@endsection

@section('script')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modals = document.querySelectorAll('.modal');
        modals.forEach(function(modal) {
            modal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var reviewText = button.getAttribute('data-review');
                var modalBody = modal.querySelector('.modal-body');
                modalBody.innerHTML = reviewText;
            });
        });
    });
</script>

@endsection