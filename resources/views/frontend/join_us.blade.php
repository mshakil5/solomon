@extends('layouts.master')

@section('content')

<section class="contact-section section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-12 mx-auto">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <form class="custom-form contact-form" action="{{ route('join.us.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <h2>Join Us</h2>

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
                            <input type="text" name="address_first_line" id="address_first_line" class="form-control" value="{{ old('address_first_line', auth()->user()->address_first_line ?? '') }}"  placeholder="Address Line 1 *" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-6 col-md-6 col-6">
                            <input type="text" name="address_second_line" id="address_second_line" class="form-control" placeholder="Address Line 2" value="{{ old('address_second_line', auth()->user()->address_second_line ?? '') }}">
                        </div>
                        <div class="col-lg-6 col-md-6 col-6">
                            <input type="text" name="address_third_line" id="address_third_line" class="form-control" placeholder="Address Line 3" value="{{ old('address_third_line', auth()->user()->address_third_line ?? '') }}">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="text" name="town" id="town" class="form-control" placeholder="Town *" required value="{{ old('town', auth()->user()->town ?? '') }}">
                        </div>

                        <div class="col-lg-6 col-md-6 col-12">
                            <input type="text" name="postcode" id="postcode" class="form-control" placeholder="Postcode *" required value="{{ old('post_code', auth()->user()->postcode ?? '') }}">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-12 col-md-6 col-12">
                            <label for="category_ids">Select Categories :</label>
                            <div class="category-checkboxes">
                                @foreach($categories as $category)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="category_ids[]" id="category_{{ $category->id }}" value="{{ $category->id }}">
                                        <label class="form-check-label" for="category_{{ $category->id }}"></i> {{ $category->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-12 col-md-6 col-12">
                            <label for="cv">Upload Your CV :</label>
                            <input type="file" name="cv" id="cv" class="form-control" placeholder="Upload Your CV" accept=".pdf,.docx">
                            <small class="form-text text-muted">
                                Please upload your CV in PDF or DOCX format. Maximum file size: 2MB.
                            </small>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="form-control btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection