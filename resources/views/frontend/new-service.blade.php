@extends('layouts.master')

@section('content')

@php
    $lang = session('app_locale', 'ro') == 'ro';
@endphp

@if ($sliders->count() > 0)
<section class="slider">
    <div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach($sliders as $key => $slider)
                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                    <img src="{{ asset('images/slider/' . $slider->image) }}" class="d-block w-100 h desktop-img" alt="Slider Image">
                    <div class="carousel-caption text-center">
     
                            <div class="slider-content">
                                @if($slider->title)
                                    <h1 class="slider-title">{{ $slider->title }}</h1>
                                @endif
                                @if($slider->sub_title)
                                    <h3 class="slider-subtitle">{{ $slider->sub_title }}</h3>
                                @endif
                            </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<div class="container mt-5 text-start">
    <div class="row">
        <form action="{{ route('new.services.store') }}" method="post">
            @csrf
            <div class="col-12 mx-auto">
              <div class="mb-3">
                <label for="need" class="form-label fw-bold">
                    Spune-ne mai jos ce ai nevoie, iar noi îți pregătim o ofertă personalizată
                </label>
                <textarea id="need" name="need" class="form-control" rows="4" placeholder="Scrie aici..." required></textarea>
              </div>
              <div class="d-flex justify-content-center my-5">
                  <div class="col-6">
                      <button type="submit" class="form-control btn btn-primary">
                          Mai departe
                      </button>
                  </div>
              </div>
            </div>
        </form>
    </div>
</div>


<style>
  .slider .desktop-img {
      height: 400px;
      width: 100%;
      object-fit: cover;
  }

  .carousel-caption {
      top: 0;
      left: 0;
      height: 100%;
      width: 100%;
      display: flex !important;
      justify-content: center;
      align-items: center;
      text-align: center;
      bottom: 0;
      transform: none !important;
  }

  .slider-content {
      display: flex;
      flex-direction: column;
      gap: 10px;
      align-items: center;
      justify-content: center;
  }

  .slider-title {
      color: white;
      padding: 10px 20px;
      font-size: 30px;
      font-weight: bold;
      word-break: break-word;
  }

  .slider-subtitle {
      color: white;
      padding: 8px 16px;
      font-size: 30px;
      font-weight: bold;
      word-break: break-word;
  }

  .slider-link {
      text-decoration: none;
      color: inherit;
  }

  @media (max-width: 767px) {
      .slider {
          height: 200px;
      }

      .slider-title {
          font-size: 24px;
          font-weight: semibold;
          padding: 4px 8px;
      }

      .slider-subtitle {
          font-size: 24px;
          font-weight: semibold;
          padding: 4px 8px;
      }

      .carousel-control-prev-icon,
      .carousel-control-next-icon {
          display: none;
      }
  }

</style>

@endsection