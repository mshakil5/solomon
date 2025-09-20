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
    <div class="social-icons mb-1">
      <a href="https://play.google.com/store/apps/details?id=com.tot.pro&pcampaignid=web_share"
        target="_blank"
        class="d-inline-block me-2">
          <img src="https://play.google.com/intl/en_us/badges/static/images/badges/ro_badge_web_generic.png"
              alt="{{ session('app_locale', 'ro') == 'ro' ? 'Descarcă de pe Google Play' : 'Get it on Google Play' }}"
              style="height:40px;">
      </a>

      <a href="https://apps.apple.com/gb/app/tot-pro/id6741846121"
        target="_blank"
        class="d-inline-block">
          <img src="https://tools.applemediaservices.com/api/badges/download-on-the-app-store/black/ro-ro?size=250x83"
            alt="{{ session('app_locale', 'ro') == 'ro' ? 'Descarcă din App Store' : 'Download on the App Store' }}"
            style="height:30px;">
      </a>

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
    <p>
        {{ session('app_locale', 'ro') == 'ro' ? 'Proiectat și dezvoltat de' : 'Designed & Developed by' }}
        <a href="https://mentosoftware.com" target="_blank">Mentosoftware</a>
    </p>
</div>