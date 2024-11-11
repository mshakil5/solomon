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

                        <p class="text-muted text-justify">{{ \Illuminate\Support\Str::limit($review->review, 150) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<style>
    .review-card {
        background-color: #f9f9f9;
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .review-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .star-rating span {
        font-size: 1.2rem;
    }

    .card h5 {
        font-weight: 600;
    }

    .card p {
        font-size: 1rem;
        line-height: 1.6;
    }

</style>

@endsection