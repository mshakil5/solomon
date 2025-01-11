@extends('layouts.master')
@section('content')

@include('frontend.inc.hero')

<div class="categories mt-5">

    <div class="container col-10">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
    @endif
    </div>

    <h2>
        Browse our most popular categories
    </h2>

    <div class="category-container">
        <div class="row justify-content-center">
            <div class="category-list col-12">
                <div class="row justify-content-center">
                    @foreach ($categories as $category)
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4 d-flex justify-content-center">
                            <a href="{{ route('category.show', $category->slug) }}" class="custom-category text-center mx-auto">
                                {{-- <i class=""></i> --}}
                                <img src="{{ asset('images/category/' . $category->image) }}" alt="{{ $category->name }}" class="custom-category-image">
                                <p class="custom-category-title">{{ $category->name }}</p>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="info-section">
    <div class="info-box">
        <i class="fas fa-check-circle">
        </i>
        <p>
            Every Checkatrade tradesperson has passed up to 12 rigorous checks
        </p>
    </div>
    <div class="info-box">
        <i class="fas fa-star">
        </i>
        <p>
            Over 6.2 million reviews have been published on Checkatrade
        </p>
    </div>
    <div class="info-box">
        <i class="fas fa-shield-alt">
        </i>
        <p>
            We guarantee Checkatrade tradespeople's work, claim up to £1000 - 10000
        </p>
    </div>
</div>

<div class="info-section">
    <div class="info-box">
        <img alt="Leave a Review" src="{{ asset('frontend/images/review.jpg') }}"/>
        <p>Have you completed a project recently? Let your tradesperson know how they did.</p>
        <a href="{{ route('review') }}">Leave A Review</a>
    </div>
    <div class="info-box">
        <img alt="Tradesperson Sign Up Image" height="200" src="{{ asset('frontend/images/join-with-us.jpg') }}"/>
        <p>
            Over 1 million homeowners visit our site looking for approved and quality tradespeople like you.
        </p>
        <a href="{{ route('join.us') }}">
            Join Us
        </a>
    </div>
    <div class="info-box">
        <img alt="Request a Quote Image" height="200" src="{{ asset('frontend/images/Request-for-Quotation.jpg') }}"/>
        <p>
            Tell us what you're looking for and we'll pass your request on to three approved tradespeople.
        </p>
        <a href="{{ route('quote.form') }}">
            Request A Quote
        </a>
    </div>
</div>

<section class="contact-section section-padding" id="contact">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-12 ms-auto mb-5 mb-lg-0">
                <div class="contact-info-wrap">
                    <h2>Get in touch</h2>

                    <div class="contact-info">
                        <h5 class="mb-3">Contact Infomation</h5>

                        <p class="d-flex mt-3">
                            <i class="bi-geo-alt me-2"></i>
                            {!! $companyDetails->address1 !!}
                        </p>

                        <p class="d-flex mb-2">
                            <i class="bi-telephone me-2"></i>

                            <a href="tel: 0203-994-7611">
                                {{ $companyDetails->phone1 }}
                            </a>
                        </p>

                        <p class="d-flex">
                            <i class="bi-envelope me-2"></i>

                            <a href="mailto:solomon@.co.uk">
                            {{ $companyDetails->email1 }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-12 mx-auto">
                <form class="custom-form contact-form" id="contactForm" action="{{route('contactMessage')}}" method="post" role="form">
                    @csrf
                    <h2>Contact form</h2>

                    @if ($message = Session::get('message'))
                        <div class="alert alert-primary alert-dismissible fade show" role="alert">
                            <strong>{{ $message }}</strong>
                            <button type="button" class="close btn-sm" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="text" name="firstname" id="firstname" class="form-control" placeholder="Jack" value="{{ auth()->check() ? auth()->user()->name : '' }}" required>
                            @error('firstname')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="text" name="lastname" id="lastname" class="form-control" placeholder="Doe" value="{{ auth()->check() ? auth()->user()->surname : '' }}" required>

                            @error('lastname')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>
                    </div>

                    <input type="email" name="contactemail" id="contactemail" pattern="[^ @]*@[^ @]*" class="form-control" placeholder="Jackdoe@gmail.com" value="{{ auth()->check() ? auth()->user()->email : '' }}" required>
                    @error('contactemail')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                    <textarea name="contactmessage" rows="5" class="form-control" id="contactmessage" placeholder="How can we help you?" required></textarea>

                    <button type="submit" class="form-control">Send Message</button>

                    <div id='loading' style='display:none ;'>
                        <img src="{{ asset('loader.gif') }}" id="loading-image" alt="Loading..." />
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
        #loading {
        position: fixed;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0.7;
        background-color: #fff;
        z-index: 99;
    }

    #loading-image {
        z-index: 100;
    }
</style>

@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('contactForm');
        const loadingDiv = document.getElementById('loading');

        form.addEventListener('submit', function() {
            loadingDiv.style.display = 'flex';
        });
    });
</script>
@endsection