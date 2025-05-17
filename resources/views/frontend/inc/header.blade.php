<div class="header navbar navbar-expand-md navbar-light bg-white">
    <a class="navbar-brand" href="{{ route('homepage') }}">
        <img src="{{ asset('images/company/'.$companyDetails->company_logo)}}" alt="Logo" style="height: 40px; width: auto;">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('aboutUs') }}">
                    {{ session('app_locale', 'ro') == 'ro' ? 'Despre' : 'About' }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('homepage') }}#contact">
                    {{ session('app_locale', 'ro') == 'ro' ? 'Contact' : 'Contact' }}
                </a>
            </li>
            @if(Auth::check())
                <li class="nav-item">
                    <a class="nav-link" href="{{ auth()->user()->is_type == '1' ? route('admin.dashboard') : (auth()->user()->is_type == '0' ? route('user.profile') : route('staff.home')) }}">
                        {{ Auth::user()->name }}
                    </a>
                </li>
            @else
                <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ session('app_locale', 'ro') == 'ro' ? 'Autentificare' : 'Login' }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">{{ session('app_locale', 'ro') == 'ro' ? 'Înregistrare' : 'Register' }}</a></li>
            @endif
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle btn btn-primary text-white" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    {{ session('app_locale', 'ro') == 'en' ? 'English' : 'Română' }}
                </a>
                <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                    <li><a class="dropdown-item" href="{{ route('change.language', 'en') }}">English</a></li>
                    <li><a class="dropdown-item" href="{{ route('change.language', 'ro') }}">Română</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>