@php
    $companyDetails = \App\Models\CompanyDetails::select('footer_content', 'company_logo')->first();
@endphp
<div class="hero">
    <div class="hero-content">
        {!! $companyDetails->footer_content !!}
    </div>
    <div class="hero-image">
        <img src="{{ asset('images/company/'.$companyDetails->company_logo)}}" alt="Hero Image" />
    </div>
</div>