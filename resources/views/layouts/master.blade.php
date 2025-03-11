<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@php
    $companyDetails = \App\Models\CompanyDetails::select('fav_icon', 'footer_content', 'company_name', 'company_logo')->first();
@endphp

<head>
    <meta charset="utf-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">
    <meta name="author" content="">

    <title>Solomon Maintainance</title>

    <link rel="icon" href="{{ asset('images/company/' . $companyDetails->fav_icon) }}">
    
    <!-- CSS FILES -->
    <!-- <link href="{{ asset('frontend/css/bootstrap.min.css')}}" rel="stylesheet"> -->
    <!-- <link href="{{ asset('frontend/css/bootstrap-icons.css')}}" rel="stylesheet"> -->
    <!-- <link href="{{ asset('frontend/css/templatemo-kind-heart-charity.css')}}" rel="stylesheet"> -->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>

    <link href="{{ asset('frontend/css/style.css')}}" rel="stylesheet">


</head>

<body id="section_1">

    <div class="sticky-container">
        @include('frontend.inc.header')

        <div class="header-line"></div> 
    </div>

    @yield('content')

    <div class="header-line"></div> 
    @include('frontend.inc.footer')

    <!-- JAVASCRIPT FILES -->
    <!-- <script src="{{ asset('frontend/js/jquery.min.js')}}"></script>
    <script src="{{ asset('frontend/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('frontend/js/counter.js')}}"></script>
    <script src="{{ asset('frontend/js/custom.js')}}"></script> -->

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    {{-- <script type="text/javascript">
        function googleTranslateElementInit() {
            var elements = document.querySelectorAll('.google_translate_element');
            elements.forEach(function(element) {
                new google.translate.TranslateElement({
                    pageLanguage: 'ro',
                    includedLanguages: 'en,ro',
                    layout: google.translate.TranslateElement.InlineLayout.SIMPLE
                }, element);
            });
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script> --}}

    @yield('script')

</body>

</html>