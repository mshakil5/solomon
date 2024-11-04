@php
    $companyDetails = \App\Models\CompanyDetails::select('facebook', 'twitter', 'instagram', 'youtube', 'linkedin')->first();
@endphp

<div class="footer">
    <p>
        <a href="{{ route('aboutUs') }}">
            About us
        </a>
        |
        <a href="{{ route('homepage') }}#contact">
            Contact us
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
        ©  {{ $companyDetails->company_name }} {{ date('Y') }} all rights reserved
    </p>
</div>