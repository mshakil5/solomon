@php
    $companyDetails = \App\Models\CompanyDetails::select('facebook', 'twitter', 'instagram', 'youtube', 'linkedin')->first();
@endphp

<div class="footer">
    <p>
        <a href="{{ route('aboutUs') }}">
            {{ session('app_locale', 'ro') == 'ro' ? 'Despre noi' : 'About Us' }}
        </a>
        |
        <a href="{{ route('homepage') }}#contact">
            {{ session('app_locale', 'ro') == 'ro' ? 'Contactați-ne' : 'Contact Us' }}
        </a>
        |
        <a href="{{ route('privacy') }}">
            {{ session('app_locale', 'ro') == 'ro' ? 'Privacy Policy' : 'Privacy Policy' }}
        </a>
        |
        <a href="{{ route('terms') }}">
            {{ session('app_locale', 'ro') == 'ro' ? 'Terms & Conditions' : 'Terms & Conditions' }}
        </a>
    </p>
    <div class="social-icons">
        @if($companyDetails->facebook)
            <a href="{{ $companyDetails->facebook }}" target="_blank" class="social-link">
                <i class="fab fa-facebook-f"></i>
            </a>
        @endif

        @if($companyDetails->twitter)
            <a href="{{ $companyDetails->twitter }}" target="_blank" class="social-link">
                <i class="fab fa-twitter"></i>
            </a>
        @endif

        @if($companyDetails->instagram)
            <a href="{{ $companyDetails->instagram }}" target="_blank" class="social-link">
                <i class="fab fa-instagram"></i>
            </a>
        @endif

        @if($companyDetails->youtube)
            <a href="{{ $companyDetails->youtube }}" target="_blank" class="social-link">
                <i class="fab fa-youtube"></i>
            </a>
        @endif

        @if($companyDetails->linkedin)
            <a href="{{ $companyDetails->linkedin }}" target="_blank" class="social-link">
                <i class="fab fa-linkedin-in"></i>
            </a>
        @endif
    </div>
    <p>
        © {{ $companyDetails->company_name }} {{ date('Y') }}
        {{ session('app_locale', 'ro') == 'ro' ? 'Toate drepturile rezervate' : 'All rights reserved' }}
    </p>
</div>