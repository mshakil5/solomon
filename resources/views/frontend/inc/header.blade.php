<div class="header navbar navbar-expand-md navbar-light bg-white">
    <a class="logo" href="{{ route('homepage') }}">
      <img src="{{ asset('images/company/'.$companyDetails->company_logo)}}" alt="Logo" style="height: 40px; width: auto;">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <nav class="navbar-nav ml-auto">
            <a class="nav-item nav-link" href="{{ route('aboutUs') }}">
                {{ session('app_locale', 'ro') == 'ro' ? 'Despre' : 'About' }}
            </a>
            <a class="nav-item nav-link" href="{{ route('homepage') }}#contact">
                {{ session('app_locale', 'ro') == 'ro' ? 'Contact' : 'Contact' }}
            </a>
            @if(Auth::check())
                @if(auth()->user()->is_type == '1')
                <a class="nav-item nav-link" href="{{ route('admin.dashboard') }}">
                {{ Auth::user()->name }}
                </a>
                @elseif(auth()->user()->is_type == '0')
                <a class="nav-item nav-link" href="{{ route('user.profile') }}">
                {{ Auth::user()->name }}
                </a>
                @elseif(auth()->user()->is_type == '2')
                <a class="nav-item nav-link" href="{{ route('staff.home') }}">
                {{ Auth::user()->name }}
                </a>
                @endif

                @else
                <a class="nav-item nav-link" href="{{ route('login') }}">
                    {{ session('app_locale', 'ro') == 'ro' ? 'Autentificare' : 'Login' }}
                </a>
                <a class="nav-item nav-link" href="{{ route('register') }}">
                    {{ session('app_locale', 'ro') == 'ro' ? 'Înregistrare' : 'Register' }}
                </a>
            @endif

              <div class="dropdown">
                  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                      {{ session('app_locale', 'ro') == 'en' ? 'English' : 'Română' }}
                  </button>
                  <div class="dropdown-menu">
                      <a class="dropdown-item" href="{{ route('change.language', 'en') }}">English</a>
                      <a class="dropdown-item" href="{{ route('change.language', 'ro') }}">Română</a>
                  </div>
              </div>
          
              <div class="nav-item d-none">
                  <div class="google_translate_element"></div>
              </div>
        </nav>
    </div>
</div>  