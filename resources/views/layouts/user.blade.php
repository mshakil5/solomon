<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@php
    $companyDetails = \App\Models\CompanyDetails::first();
@endphp

<head>
    <meta charset="utf-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">
    <meta name="author" content="">

    <title>Tot Pro</title>

    <link rel="icon" type="image/x-icon" href="">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('frontend/css/bootstrap-icons.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/admin/lightbox/lightbox.min.css')}}">

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>

    <link href="{{ asset('frontend/css/style.css')}}" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body id="section_1">

    <div class="sticky-container">
    @php
        $lang = session('app_locale', 'ro') == 'ro';
    @endphp

    <div class="header navbar navbar-expand-md navbar-light bg-white">
        <a class="logo" href="{{ route('homepage') }}">
            <img src="{{ asset('images/company/'.$companyDetails->company_logo)}}" alt="Logo" style="height: 40px; width: auto;">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <nav class="navbar-nav ml-auto d-none d-md-flex">
                <a class="nav-item nav-link" href="{{ route('aboutUs') }}">
                    {{ $lang ? 'Despre' : 'About' }}
                </a>
                <a class="nav-item nav-link" href="{{ route('homepage') }}#contact">
                    {{ $lang ? 'Contact' : 'Contact' }}
                </a>

                @if(Auth::check())
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                        <strong>{{ Auth::user()->name }}</strong>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('user.profile') }}">{{ $lang ? 'Profil' : 'Profile' }}</a>
                        <a class="dropdown-item" href="{{ route('user.password') }}">{{ $lang ? 'Schimbă parola' : 'Change Password' }}</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('user.addresses.index') }}">{{ $lang ? 'Adresă' : 'Address' }}</a>
                        <a class="dropdown-item" href="{{ route('user.service.bookings') }}">{{ $lang ? 'Istoric rezervări' : 'Booking History' }}</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="me-50" data-feather="power"></i> {{ $lang ? 'Deconectare' : 'Logout' }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </div>
                </li>
                @endif
            </nav>

            <nav class="navbar-nav d-md-none">

                @if(Auth::check())
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                        <strong>{{ Auth::user()->name }}</strong>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('user.profile') }}">{{ $lang ? 'Profil' : 'Profile' }}</a>
                        <a class="dropdown-item" href="{{ route('user.password') }}">{{ $lang ? 'Schimbă parola' : 'Change Password' }}</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('user.addresses.index') }}">{{ $lang ? 'Adresă' : 'Address' }}</a>
                        <a class="dropdown-item" href="{{ route('user.service.bookings') }}">{{ $lang ? 'Istoric rezervări' : 'Booking History' }}</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="me-50" data-feather="power"></i> {{ $lang ? 'Deconectare' : 'Logout' }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </div>
                </li>
                @endif
            </nav>
        </div>
    </div>

    <div class="header-line"></div>
    </div>

    <div class="container">
    @yield('content')
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@ideal-postcodes/address-finder-bundled@4"></script>
    <script src="{{ asset('assets/admin/lightbox/lightbox.min.js')}}"></script>

    {{-- <script type="text/javascript">
        function googleTranslateElementInit() {
            var elements = document.querySelectorAll('.google_translate_element');
            elements.forEach(function(element) {
                new google.translate.TranslateElement({
                    pageLanguage: 'en',
                    includedLanguages: 'en,ro',
                    layout: google.translate.TranslateElement.InlineLayout.SIMPLE
                }, element);
            });
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script> --}}

    @yield('script')

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: @json(session('success')),
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: @json($errors->first()),
            });
        </script>
    @endif

</body>
</html>