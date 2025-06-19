@extends('layouts.master')
@section('content')

{{-- @include('frontend.inc.hero') --}}

<div class="categories">
  @if(session('success'))
      <div class="alert alert-success">
          {{ session('success') }}
      </div>
  @endif
</div>

@if ($sliders->count() > 0)
<section class="slider">
    <div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach($sliders as $key => $slider)
                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                    <img src="{{ asset('images/slider/' . $slider->image) }}" class="d-block w-100 h desktop-img" alt="Slider Image">
                    <div class="carousel-caption text-center">
                        @if($slider->link)
                            <a href="{{ $slider->link }}" class="slider-link" data-type="{{ $slider->id == 1 ? 1 : 0 }}">
                                <div class="slider-content">
                                    @if($slider->title)
                                        <h1 class="slider-title">{{ $slider->title }}</h1>
                                    @endif
                                    @if($slider->sub_title)
                                        <h3 class="slider-subtitle">{{ $slider->sub_title }}</h3>
                                    @endif
                                </div>
                            </a>
                        @else
                            <div class="slider-content">
                                @if($slider->title)
                                    <h1 class="slider-title">{{ $slider->title }}</h1>
                                @endif
                                @if($slider->sub_title)
                                    <h3 class="slider-subtitle">{{ $slider->sub_title }}</h3>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<div class="categories mt-5 d-none">

    <div class="container col-10">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
    @endif
    </div>

    <h2>
        {{ session('app_locale', 'ro') == 'ro' ? 'Răsfoiește cele mai populare categorii ale noastre' : 'Browse our most popular categories' }}
    </h2>

    <div class="category-container">
        <div class="row justify-content-center">
            <div class="category-list col-12">
                <div class="row justify-content-center">
                    @foreach ($categories as $key => $category)
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4 d-flex justify-content-center">
                            @if ($category->subcategories->isEmpty())
                                <a href="{{ route('category.show', ['category' => $category->slug]) }}" class="btn w-100 bg-white text-left p-2">
                                    <img src="{{ asset('images/category/' . $category->image) }}" alt="{{ $category->name }}" class="custom-category-image">
                                    <p class="custom-category-title">
                                        {{ session('app_locale', 'ro') == 'ro' ? $category->romanian_name : $category->name }}
                                    </p>
                                </a>
                            @else 
                                <a type="button" class="custom-category text-center mx-auto" data-toggle="modal" data-target="#exampleModal{{ $key }}">
                                    <img src="{{ asset('images/category/' . $category->image) }}" alt="{{ $category->name }}" class="custom-category-image">
                                    <p class="custom-category-title">
                                        {{ session('app_locale', 'ro') == 'ro' ? $category->romanian_name : $category->name }}
                                    </p>
                                </a>
                            @endif
                        </div>
                    
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal{{ $key }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            {{ session('app_locale', 'ro') == 'ro' ? 'De ce tip de ' . $category->romanian_name . ' ai nevoie?' : 'What type of ' . $category->name . ' do you need?' }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="p-2">
                                            @foreach ($category->subcategories as $subcat)
                                                <div class="card sub-category">
                                                    <a href="{{ route('category.show', ['category' => $category->slug, 'subcategory' => $subcat->slug]) }}" class="btn w-100 bg-white text-left p-2">
                                                        {{ session('app_locale', 'ro') == 'ro' ? $subcat->romanian_name : $subcat->name }}
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>        
                    @endforeach    
                </div>
            </div>
        </div>
    </div>
</div>

<div class="booking-type-section py-5 bg-light">
  <div class="container">
    <h2 class="text-primary fw-semibold mb-5 text-center">
      {{ session('app_locale', 'ro') == 'ro' ? 'Alegeți tipul de rezervare' : 'Choose Booking Type' }}
    </h2>

    <div class="row g-3 justify-content-center">
      @php
        $workTypes = [
            1 => [
              'title' => session('app_locale', 'ro') == 'ro' ? 'Serviciu de urgență' : 'Emergency Service',
              'image' => asset('urgent.png'),
              'price' => session('app_locale', 'ro') == 'ro' ? 'Suprataxă de 400 RON' : 'Surcharge of 400 RON',
              'desc' => session('app_locale', 'ro') == 'ro'
                ? 'Dacă serviciul solicitat este necesar într-un interval <u style="color:green; font-weight:600;">mai scurt de 2 ore de la momentul comenzii</u>, acesta va fi tratat ca serviciu de urgență, fiind prioritizat imediat în programul de lucru.'
                : 'If the requested service is needed within <u style="color:green; font-weight:600;">less than 2 hours from the time of order</u>, it will be treated as an emergency service and prioritized immediately in the work schedule.',
            ],
            2 => [
              'title' => session('app_locale', 'ro') == 'ro' ? 'Serviciu prioritar în ziua curentă' : 'Prioritized Same Day Service',
              'image' => asset('prio.png'),
              'price' => session('app_locale', 'ro') == 'ro' ? 'Suprataxă de 250 RON' : 'Surcharge of 250 RON',
              'desc' => session('app_locale', 'ro') == 'ro'
                  ? 'Pentru solicitările care necesită <u style="color:green; font-weight:600;">prestarea serviciului în aceeași zi, dar la un interval mai mare de 2 ore de la momentul comenzii</u>, se va percepe o taxă de prioritate, având în vedere necesitatea programării accelerate în cadrul zilei respective.'
                  : 'For requests requiring <u style="color:green; font-weight:600;">same-day service but more than 2 hours after the order time</u>, a priority surcharge applies due to the need for accelerated scheduling within the day.',
            ],
            3 => [
              'title' => session('app_locale', 'ro') == 'ro' ? 'Serviciu în afara programului' : 'Outside Working Hours',
              'image' => asset('afara.png'),
              'price' => session('app_locale', 'ro') == 'ro' ? 'Suprataxă de 300 RON' : 'Surcharge of 300 RON',
              'desc' => session('app_locale', 'ro') == 'ro'
                ? 'Dacă serviciul ales este solicitat <u style="color:green; font-weight:600;">în afara programului normal de lucru</u>, se va aplica o suprataxă pentru prestare în regim special.'
                : 'If the selected service is requested <u style="color:green; font-weight:600;">outside normal working hours</u>, a surcharge will apply for special scheduling.',
            ],
          // 4 => ['title' => session('app_locale', 'ro') == 'ro' ? 'Serviciu Standard' : 'Standard Service', 'image' => asset('prio.png')],
        ];
      @endphp

      @foreach ($workTypes as $value => $type)
        <div class="col-md-4 col-sm-6">
          <a href="{{ route('booking.type.select', ['type' => $value]) }}"
            data-type="{{ $value }}"
            class="booking-type-option d-block text-decoration-none text-center work-type-link h-100"
            style="min-height: 200px; display: flex; flex-direction: column; justify-content: space-between;">
            
            <div>
              <div class="row">
                <div class="col-4">
                  <div class="booking-type-icon mb-2">
                    <img src="{{ $type['image'] }}" alt="{{ $type['title'] }}" style="width: 50px; height: 50px;">
                  </div>
                </div>
                <div class="col-8 text-start">
                  <div class="fw-semibold text-dark fs-5">{{ $type['title'] }}</div>
                  <div class="text-danger fw-bold">{{ $type['price'] }}</div>
                </div>
              </div>
              <p class="text-dark mt-2 text-start" style="font-size: 0.95rem;">{!! $type['desc'] !!}</p>
            </div>
            
          </a>
        </div>
      @endforeach
    </div>
  </div>
</div>

<script>
  document.querySelectorAll('.booking-type-option input').forEach(input => {
    input.addEventListener('change', function () {
      document.querySelectorAll('.booking-type-option').forEach(opt => opt.classList.remove('selected'));
      this.closest('.booking-type-option').classList.add('selected');
    });
  });
</script>

<div class="services-by-type bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="text-primary fw-semibold">
        {{ session('app_locale', 'ro') == 'ro' ? 'Serviciile noastre' : 'Our Services' }}
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

<div class="info-section">
    <div class="info-box">
        <i class="fas fa-check-circle"></i>
        <p>
            {{ session('app_locale', 'ro') == 'ro' 
                ? 'Fiecare meseriaș Tot Pro a trecut până la 12 verificări riguroase' 
                : 'Every Tot Pro tradesperson has passed up to 12 rigorous checks' }}
        </p>
    </div>
    <div class="info-box">
        <i class="fas fa-star"></i>
        <p>
            {{ session('app_locale', 'ro') == 'ro' 
                ? 'Peste 6,2 milioane de recenzii au fost publicate pe Checkatrade' 
                : 'Over 6.2 million reviews have been published on Checkatrade' }}
        </p>
    </div>
    <div class="info-box">
        <i class="fas fa-shield-alt"></i>
        <p>
            {{ session('app_locale', 'ro') == 'ro' 
                ? 'Garantăm lucrarea meseriașilor Tot Pro, reclamații până la £1000 - 10000' 
                : 'We guarantee Tot Pro tradespeople\'s work, claim up to £1000 - 10000' }}
        </p>
    </div>
</div>

<div class="info-section">
  <div class="info-box">
      <img alt="{{ session('app_locale', 'ro') == 'ro' ? 'Lasă un Review' : 'Leave a Review' }}" src="{{ asset('frontend/images/review.jpg') }}"/>
      <p>
          {{ session('app_locale', 'ro') == 'ro' ? 'Ai finalizat recent un proiect? Spune-i meșteșugarului cum s-a descurcat.' : 'Have you completed a project recently? Let your tradesperson know how they did.' }}
      </p>
      <a href="{{ route('review') }}">
          {{ session('app_locale', 'ro') == 'ro' ? 'Lasă un Review' : 'Leave A Review' }}
      </a>
  </div>
  <div class="info-box">
      <img alt="{{ session('app_locale', 'ro') == 'ro' ? 'Înregistrează-te ca meșteșugar' : 'Tradesperson Sign Up Image' }}" height="200" src="{{ asset('frontend/images/join-with-us.jpg') }}"/>
      <p>
          {{ session('app_locale', 'ro') == 'ro' ? 'Peste 1 milion de proprietari vizitează site-ul nostru căutând meșteșugari aprobați și de calitate, ca tine.' : 'Over 1 million homeowners visit our site looking for approved and quality tradespeople like you.' }}
      </p>
      <a href="{{ route('join.us') }}">
          {{ session('app_locale', 'ro') == 'ro' ? 'Alătură-te nouă' : 'Join Us' }}
      </a>
  </div>
  <div class="info-box">
      <img alt="{{ session('app_locale', 'ro') == 'ro' ? 'Cere un Ofertă' : 'Request a Quote Image' }}" height="200" src="{{ asset('frontend/images/Request-for-Quotation.jpg') }}"/>
      <p>
          {{ session('app_locale', 'ro') == 'ro' ? 'Spune-ne ce îți dorești și vom trimite cererea ta celor trei meșteșugari aprobați.' : 'Tell us what you\'re looking for and we\'ll pass your request on to three approved tradespeople.' }}
      </p>
      <a href="{{ route('quote.form') }}">
          {{ session('app_locale', 'ro') == 'ro' ? 'Cere o Ofertă' : 'Request A Quote' }}
      </a>
  </div>
</div>

<section class="contact-section section-padding" id="contact">
  <div class="container">
      <div class="row">
          <div class="col-lg-4 col-12 ms-auto mb-5 mb-lg-0">
              <div class="contact-info-wrap">
                  <h2>{{ session('app_locale', 'ro') == 'ro' ? 'Contactează-ne' : 'Get in touch' }}</h2>

                  <div class="contact-info">
                      <h5 class="mb-3">{{ session('app_locale', 'ro') == 'ro' ? 'Informații de contact' : 'Contact Information' }}</h5>

                      <p class="d-flex mt-3">
                          <i class="bi-geo-alt me-2"></i>
                          {!! session('app_locale', 'ro') == 'ro' ? $companyDetails->address1 : $companyDetails->address1 !!}
                      </p>

                      <p class="d-flex mb-2">
                          <i class="bi-telephone me-2"></i>
                          <a href="tel:{{ $companyDetails->phone1 }}">
                              {{ $companyDetails->phone1 }}
                          </a>
                      </p>

                      <p class="d-flex">
                          <i class="bi-envelope me-2"></i>
                          <a href="mailto:{{ $companyDetails->email1 }}">
                              {{ $companyDetails->email1 }}
                          </a>
                      </p>
                  </div>
              </div>

              <div class="contact-info-wrap mt-2">
                <h2>{{ session('app_locale', 'ro') == 'ro' ? 'Sună-ne' : 'Call Us' }}</h2>
            
                <div class="contact-info">
                    <h5 class="mb-3">{{ session('app_locale', 'ro') == 'ro' ? 'Ai nevoie de ajutor? Sună-ne' : 'Need Help? Call Us' }}</h5>

                    @if(session('callback_message'))
                        <div class="alert alert-success">
                            {{ session('callback_message') }}
                        </div>
                    @endif
                    
                    @if(session('callback_error'))
                        <div class="alert alert-danger">
                            {{ session('callback_error') }}
                        </div>
                    @endif     
            
                    @if(Auth::check())
                        <form action="{{ route('callRequest') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
            
                            <button type="submit" class="btn btn-primary w-100 mt-3">
                                {{ session('app_locale', 'ro') == 'ro' ? 'Solicită un apel' : 'Request a Call' }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-warning w-100 mt-3">
                            {{ session('app_locale', 'ro') == 'ro' ? 'Autentifică-te pentru a ne suna' : 'Login to Call Us' }}
                        </a>
                    @endif
                </div>
            </div>            
          </div>

          <div class="col-lg-5 col-12 mx-auto">
              <form class="custom-form contact-form" id="contactForm" action="{{route('contactMessage')}}" method="post" role="form">
                  @csrf
                  <h2>{{ session('app_locale', 'ro') == 'ro' ? 'Formular de contact' : 'Contact form' }}</h2>

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
                          <input type="text" name="firstname" id="firstname" class="form-control" placeholder="{{ session('app_locale', 'ro') == 'ro' ? 'Jack' : 'First Name' }}" value="{{ old('firstname', auth()->check() ? auth()->user()->name : '') }}" required>
                          @error('firstname')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>

                      <div class="col-lg-6 col-md-6 col-12">
                          <input type="text" name="lastname" id="lastname" class="form-control" placeholder="{{ session('app_locale', 'ro') == 'ro' ? 'Doe' : 'Last Name' }}" value="{{ old('lastname', auth()->check() ? auth()->user()->surname : '') }}" required>
                          @error('lastname')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                  </div>

                  <input type="email" name="contactemail" id="contactemail" pattern="[^ @]*@[^ @]*" class="form-control" placeholder="{{ session('app_locale', 'ro') == 'ro' ? 'Jackdoe@gmail.com' : 'Email Address' }}" value="{{ old('contactemail', auth()->check() ? auth()->user()->email : '') }}" required>
                  @error('contactemail')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror

                  <textarea name="contactmessage" rows="5" class="form-control" id="contactmessage" placeholder="{{ session('app_locale', 'ro') == 'ro' ? 'Cum te putem ajuta?' : 'How can we help you?' }}" required>{{ old('contactmessage') }}</textarea>

                  <button type="submit" class="form-control">
                      {{ session('app_locale', 'ro') == 'ro' ? 'Trimite mesajul' : 'Send Message' }}
                  </button>

                  <div id='loading' style='display:none ;'>
                      <img src="{{ asset('loader.gif') }}" id="loading-image" alt="Loading..." />
                  </div>
              </form>
          </div>
      </div>
  </div>
</section>

<style>
  .slider .desktop-img {
      height: 300px;
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

@section('script')
<script>
  $(document).on('click', '.work-type-link', function (e) {
    e.preventDefault();
    const type = $(this).data('type');
    const url = $(this).attr('href');

    if ([1, 2, 3].includes(type)) {
      let msg = '';
      if (type == 1) msg = 'Te informăm că, întrucât ai optat pentru regimul de urgență, serviciul implică costuri suplimentare. Pentru ca echipa noastră să poată interveni prompt, este necesară efectuarea unei plăți în avans.';
      else if (type == 2) msg = 'Te informăm că, deoarece ai optat pentru regimul de prioritizare, serviciul implică costuri suplimentare. Pentru ca echipa noastră să acorde prioritate intervenției tale, este necesară efectuarea unei plăți în avans.';
      else if (type == 3) msg = 'Te informăm că, deoarece solicitarea ta este în afara orelor de program, serviciul implică costuri suplimentare. Pentru ca echipa noastră să poată interveni în afara programului obișnuit, este necesară efectuarea unei plăți în avans.?';

      Swal.fire({
        title: 'Atenție!',
        text: msg,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Da, continuă',
        cancelButtonText: 'Anulează'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = url;
        }
      });
    } else {
      window.location.href = url;
    }
  });

  $(document).on('click', '.slider-link', function (e) {
    e.preventDefault();
    const type = parseInt($(this).data('type'));
    const url = $(this).attr('href');

    if (type == 1) {
      Swal.fire({
        title: 'Atenție!',
        text: 'Te informăm că, întrucât ai optat pentru regimul de urgență, serviciul implică costuri suplimentare. Pentru ca echipa noastră să poată interveni prompt, este necesară efectuarea unei plăți în avans.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Da, continuă',
        cancelButtonText: 'Anulează'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = url;
        }
      });
    } else {
      window.location.href = url;
    }
  });

</script>

@endsection