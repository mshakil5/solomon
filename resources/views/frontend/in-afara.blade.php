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

<div class="container mt-5 text-center">
    <div class="row">
        <div class="col-12 mx-auto">
            <h1 class="text-primary mb-4 fw-bold">
                Nu locuiești chiar în București? Nicio problemă!
            </h1>
            <p>
                Serviciile noastre se extind și în localitățile din jurul capitalei. Fie că ești în 
                <span style="color: green; text-decoration: underline; cursor: pointer;">Ilfov sau într-o zonă apropiată</span>, livrăm rapid și sigur.
                <br>
                Plasează comanda fără griji – te contactăm pentru detalii și găsim împreună cea mai bună soluție pentru tine.
            </p>
        </div>
    </div>
</div>

<div class="text-center my-4">
    <a href="/booking/select-type?type=3" class="btn btn-primary btn-lg">
        Plasează o comandă
    </a>
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

      .slider + .container {
          margin-top: 220px !important; /* Push form below the slider */
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