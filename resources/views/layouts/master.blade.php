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

    <title>Tot Pro</title>

    <link rel="icon" href="{{ asset('images/company/' . $companyDetails->fav_icon) }}">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.7.7/dist/css/tempus-dominus.min.css">


    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <link href="{{ asset('frontend/css/style.css')}}" rel="stylesheet">

    <div id="global-loader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.6); z-index:9999; text-align:center; padding-top:200px;">
        <img src="{{ asset('loader.gif') }}" alt="Loading..." />
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body id="section_1">

    <div class="sticky-container">
        @include('frontend.inc.header')

        <div class="header-line"></div> 
    </div>

    @yield('content')

    <div class="header-line"></div> 
    @include('frontend.inc.footer')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.7.7/dist/js/tempus-dominus.min.js"></script>

    <script>
      function showToast(type, message, title = '') {
          const options = {
              closeButton: true,
              progressBar: true,
              positionClass: 'toast-top-right',
              timeOut: 5000
          };

          if (type === 'success') {
              toastr.success(message, title, options);
          } else if (type === 'error') {
              toastr.error(message, title, options);
          }
      }
    </script>

    <script>
        function showLoader() {
            $('#global-loader').show();
        }

        function hideLoader() {
            $('#global-loader').hide();
        }

        $(document).ready(function () {
            // Auto-show loader on any form submit
            $('form').on('submit', function () {
                showLoader();
            });
        });
    </script>

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