@extends('layouts.master')
@section('content')

<div class="services-by-type py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="text-primary fw-semibold">
        {{ session('app_locale', 'en') == 'ro' ? 'Serviciile noastre' : 'Our Services' }}
      </h2>
    </div>

    @foreach ($types as $type)
      <div class="mb-5">
        <h4 class="mb-4 text-primary text-center">
          {{ session('app_locale', 'ro') == 'ro' ? $type->title_romanian : $type->title_english }}
        </h4>

        <div class="row g-3 justify-content-center">
          @foreach ($type->services as $service)
            <div class="col-md-3 col-sm-6">
              <div class="service-option shadow-sm">
                <img src="{{ asset('images/service/' . $service->image) }}" 
                    alt="{{ $service->title_english }}" 
                    class="service-img img-fluid">

                <h5 class="fw-semibold mb-2">
                  {{ session('app_locale', 'ro') == 'ro' ? $service->title_romanian : $service->title_english }}
                </h5>

                @php
                    $description = session('app_locale', 'ro') == 'ro' ? $service->des_romanian : $service->des_english;
                @endphp

                @if (!empty($description))
                  <p class="text-muted mb-3">
                    {{ Str::limit(strip_tags(html_entity_decode($description)), 60) }}
                  </p>
                @endif

                @if(Auth::check())
                  <a href="{{ route('service.booking', $service->slug) }}" class="btn btn-sm btn-primary">
                    {{ session('app_locale', 'ro') == 'ro' ? 'Trimite Lucrarea' : 'Submit Work' }}
                  </a>
                @else
                  <a href="/login" class="btn btn-sm btn-secondary">
                    {{ session('app_locale', 'ro') == 'ro' ? 'Autentifică-te pentru detalii' : 'Login to View Details' }}
                  </a>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>
</div>

@endsection

@section('script')

@endsection