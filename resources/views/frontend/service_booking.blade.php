
@extends('layouts.master')

@section('content')
@php
    $lang = session('app_locale', 'ro') == 'ro';
@endphp

<style>
    .watch-container {
        background: linear-gradient(145deg, #e6e6e6, #ffffff);
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        text-align: center;
        margin-bottom: 30px;
    }
    .watch {
        font-size: 2.5rem;
        font-weight: bold;
        color: #0f0;
        background: #000;
        padding: 15px;
        border-radius: 10px;
        display: inline-block;
        font-family: 'Courier New', monospace;
        cursor: pointer;
    }
    .watch:hover {
        background: #333;
    }
    .calendar-container {
        background: #ffffff;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 5px;
        text-align: center;
    }
    .calendar-day {
        padding: 10px;
        border-radius: 5px;
        background: #f8f9fa;
        cursor: pointer;
    }
    .calendar-day:hover {
        background: #e0e0e0;
    }
    .calendar-day.current {
        background: #007bff;
        color: white;
    }
    .calendar-day.selected {
        background: #28a745;
        color: white;
    }
    .day-name {
        font-weight: bold;
        color: #555;
        padding: 10px;
    }
    .form-container {
        background: #ffffff;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    @media (max-width: 576px) {
        .watch {
            font-size: 1.8rem;
        }
        .calendar-day {
            padding: 5px;
            font-size: 0.9rem;
        }
    }
    .step {
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 20px;
        background-color: #f8f9fa;
    }
    .progress {
        height: 25px;
        margin-bottom: 30px;
        border-radius: 12px;
    }
    .progress-bar {
        transition: width 0.5s ease;
        font-weight: 500;
    }
    .filepond--drop-label {
        border: 2px dashed #ccc;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .filepond--file {
        background-color: #0058A2;
    }
    .filepond--list {
        padding-top: 10px;
    }
    .is-invalid {
        border-color: #dc3545;
    }
    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 0.25rem;
    }
    .time-input-container {
        display: none;
        margin-top: 15px;
    }
    .address-details {
        margin-top: 10px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
    }
</style>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12 col-sm-12">
            <div class="card">
                @php
                    $typeNames = [
                        1 => 'Emergency Service',
                        2 => 'Prioritized Same Day Service',
                        3 => 'Outside Working Hours',
                    ];
                @endphp

                <div class="card-header text-center bg-primary text-white">
                    <h2>
                      @if($service)
                        {{ $lang ? 'Finalizează rezervarea pentru' : 'Complete Your Booking for' }} 
                        {{ $lang ? $service->title_romanian : $service->title_english }}
                      @else
                        {{ $lang ? 'Solicitare serviciu personalizat' : 'Custom service request' }}
                      @endif
                        @if(!is_null($type) && isset($typeNames[$type]))
                            <span class="badge bg-info ms-3" style="font-size: 0.5em;">
                                {{ $lang ? 'Ați ales: ' : 'You have chosen: ' }}{{ $typeNames[$type] }}
                            </span>
                        @endif
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="progress mb-4" style="height: 20px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" 
                             style="width: 25%; font-size: 14px; line-height: 20px;" 
                             aria-valuenow="25" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ $lang ? 'Pasul 1 din 4 - Detalii serviciu' : 'Step 1 of 4 - Service Details' }}
                        </div>
                    </div>

                    <form id="bookingForm" action="{{ route('booking.store') }}" method="post" role="form" enctype="multipart/form-data">
                        @csrf
                        @if($service)
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        @else
                            <input type="hidden" name="description" value="{{ $description }}">
                        @endif
                        <input type="hidden" name="selected_type" value="{{ $type }}" id="selected_type">
                        <input type="hidden" name="date_time" id="date_time" value="">

                        <!-- Step 1: Service Details -->
                        <div class="step step-1">
                            <div class="row mb-4">
                              @if($service)
                                <div class="col-md-4">
                                    <img src="{{ asset('images/service/' . $service->image) }}" 
                                         alt="{{ $lang ? $service->title_romanian : $service->title_english }}" 
                                         class="img-fluid rounded" 
                                         style="max-height: 200px; object-fit: cover;">
                                </div>
                                <div class="col-md-8">
                                    <h3 class="text-primary">{{ $lang ? $service->title_romanian : $service->title_english }}</h3>
                                    <p class="lead">{!! $lang ? $service->des_romanian : $service->des_english !!}</p>
                                    <h4 class="text-success d-none">{{ $lang ? 'Preț:' : 'Price:' }} {{ number_format($service->price, 2) }}RON</h4>
                                </div>
                                @else
                                    <div class="col-12">
                                        <h3 class="text-primary">{{ $description }}</h3>
                                        <p class="lead">{{ $lang ? 'Serviciu personalizat' : 'Custom service' }}</p>
                                    </div>
                                @endif
                            </div>
                            
                            <h4 class="mb-3 border-bottom pb-2">{{ $lang ? 'Informații suplimentare' : 'Additional Information' }}</h4>
                            
                            <div class="form-group mb-3">
                                <label for="job_description">{{ $lang ? 'Descriere serviciu' : 'Job Description' }}</label>
                                <textarea class="form-control" id="job_description" name="description" rows="4"></textarea>
                                <div class="invalid-feedback">
                                    {{ $lang ? 'Descrierea este obligatorie și trebuie să fie de maxim 1000 de caractere.' : 'Description is required and must be at most 1000 characters.' }}
                                </div>
                            </div>
                            
                            <div class="form-group mt-4">
                                <label>{{ $lang ? 'Încarcă fișiere (imagini, video, PDF) - Max 10MB fiecare' : 'Upload Files (Images, Videos, PDFs) - Max 10MB each' }}</label>
                                <input type="file" class="filepond" name="files[]" multiple data-max-file-size="10MB">
                                <div class="invalid-feedback">
                                    {{ $lang ? 'Vă rugăm să încărcați cel puțin un fișier.' : 'Please upload at least one file.' }}
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-12 text-end">
                                    <button type="button" class="btn btn-primary next-step" data-step="1">{{ $lang ? 'Următorul' : 'Next' }}</button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Schedule -->
                        <div class="step step-2" style="display: none;">
                            <h4 class="mb-4 border-bottom pb-2">{{ $lang ? 'Programează serviciul' : 'Schedule Your Service' }}</h4>
                            <div class="row">
                                <!-- Calendar Column -->
                                <div class="col-md-6">
                                    <!-- Calendar Section -->
                                    <div class="calendar-container">
                                        <div class="calendar-header">
                                            <button type="button" class="btn btn-primary" onclick="changeMonth(-1)">← Prev</button>
                                            <h2 id="month-year">January 2025</h2>
                                            <button type="button" class="btn btn-primary" onclick="changeMonth(1)">Next →</button>
                                        </div>
                                        <div class="calendar-grid" id="calendar">
                                            <!-- Day Names -->
                                            <div class="day-name">Sun</div>
                                            <div class="day-name">Mon</div>
                                            <div class="day-name">Tue</div>
                                            <div class="day-name">Wed</div>
                                            <div class="day-name">Thu</div>
                                            <div class="day-name">Fri</div>
                                            <div class="day-name">Sat</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Column -->
                                <div class="col-md-6">
                                    <!-- Watch Section -->
                                    <div class="watch-container">
                                        <h4>{{ $lang ? 'Apasă pe ceas pentru a seta ora' : 'Click the watch to set working time' }}</h4>
                                        <div class="watch" id="watch" onclick="toggleTimeInput()">00:00:00</div>
                                        <div class="time-input-container" id="timeInputContainer">
                                            <div class="mb-3">
                                                <label for="inlineDateInput" class="form-label">{{ $lang ? 'Data' : 'Date' }}</label>
                                                <input type="text" class="form-control" id="inlineDateInput" placeholder="dd/mm/yyyy">
                                                <div class="invalid-feedback">
                                                    {{ $lang ? 'Data trebuie să fie în formatul dd/mm/yyyy.' : 'Date must be in dd/mm/yyyy format.' }}
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="inlineTimeInput" class="form-label">{{ $lang ? 'Ora' : 'Time' }}</label>
                                                <input type="time" class="form-control" id="inlineTimeInput">
                                                <div class="invalid-feedback">
                                                    {{ $lang ? 'Ora este obligatorie.' : 'Time is required.' }}
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary" onclick="setInlineTime()">{{ $lang ? 'Setează ora' : 'Set Time' }}</button>
                                        </div>
                                    </div>
                                    <div class="form-container">
                                        <div class="mb-3">
                                            <label for="eventDate" class="form-label">{{ $lang ? 'Data selectată' : 'Selected Date' }}</label>
                                            <input type="text" class="form-control" id="eventDate" readonly required>
                                            <div class="invalid-feedback">
                                                {{ $lang ? 'Vă rugăm să selectați o dată.' : 'Please select a date.' }}
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="eventTime" class="form-label">{{ $lang ? 'Ora selectată' : 'Selected Time' }}</label>
                                            <input type="text" class="form-control" id="eventTime" readonly required>
                                            <div class="invalid-feedback">
                                                {{ $lang ? 'Vă rugăm să selectați o oră.' : 'Please select a time.' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-6">
                                    <button type="button" class="btn btn-secondary prev-step" data-step="2">{{ $lang ? 'Anterior' : 'Previous' }}</button>
                                </div>
                                <div class="col-6 text-end">
                                    <button type="button" class="btn btn-primary next-step" data-step="2">{{ $lang ? 'Următorul' : 'Next' }}</button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Addresses -->
                        <div class="step step-3" style="display: none;">
                            <h4 class="mb-4 border-bottom pb-2">{{ $lang ? 'Informații adresă' : 'Address Information' }}</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header bg-light">
                                            <h5>{{ $lang ? 'Adresă facturare' : 'Billing Address' }} 
                                                <button type="button" class="btn btn-sm btn-success ms-2" onclick="event.preventDefault(); openAddressModal(2); return false;">
                                                    {{ $lang ? 'Adaugă' : 'Add' }}
                                                </button>
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>{{ $lang ? 'Selectează adresa de facturare' : 'Select Billing Address' }}</label>
                                                <select class="form-select" name="billing_address_id" required>
                                                    <option value="">{{ $lang ? 'Selectează adresa' : 'Select Address' }}</option>
                                                    @foreach($billingAddresses as $address)
                                                        <option value="{{ $address->id }}"
                                                                                                                                                            data-details="{!! htmlspecialchars(json_encode([
                                                                                                                                                                'name' => $address->name,
                                                                                                                                                                'first_name' => $address->first_name,
                                                                                                                                                                'phone' => $address->phone,
                                                                                                                                                                'district' => $address->district,
                                                                                                                                                                'first_line' => $address->first_line,
                                                                                                                                                                'second_line' => $address->second_line,
                                                                                                                                                                'third_line' => $address->third_line,
                                                                                                                                                                'town' => $address->town,
                                                                                                                                                                'post_code' => $address->post_code,
                                                                                                                                                                'floor' => $address->floor,
                                                                                                                                                                'apartment' => $address->apartment
                                                                                                                                                            ]), ENT_QUOTES, 'UTF-8', false) !!}"
                                                                                                                                                            @if($address->primary_billing == 1) selected style="background-color: #d3f9d8;" @endif>
                                                                                                                                                            {{ $address->name }}, {{ $address->first_name }}, {{ $address->phone }}
                                                                                                                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">
                                                    {{ $lang ? 'Vă rugăm să selectați o adresă de facturare.' : 'Please select a billing address.' }}
                                                </div>
                                                <div class="address-details" id="billing-address-details"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h5>{{ $lang ? 'Adresă livrare' : 'Delivery Address' }} 
                                                <button type="button" class="btn btn-sm btn-success ms-2" 
                                                        onclick="event.preventDefault(); openAddressModal(1); return false;">
                                                    {{ $lang ? 'Adaugă' : 'Add' }}
                                                </button>
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>{{ $lang ? 'Selectează adresa de livrare' : 'Select Delivery Address' }}</label>
                                                <select class="form-select" name="shipping_address_id" required>
                                                    <option value="">{{ $lang ? 'Selectează adresa' : 'Select Address' }}</option>
                                                    @foreach($shippingAddresses as $address)
                                                        <option value="{{ $address->id }}"
                                                            data-details="{!! htmlspecialchars(json_encode([
                                                                'name' => $address->name,
                                                                'first_name' => $address->first_name,
                                                                'phone' => $address->phone,
                                                                'district' => $address->district,
                                                                'first_line' => $address->first_line,
                                                                'second_line' => $address->second_line,
                                                                'third_line' => $address->third_line,
                                                                'town' => $address->town,
                                                                'post_code' => $address->post_code,
                                                                'floor' => $address->floor,
                                                                'apartment' => $address->apartment
                                                            ]), ENT_QUOTES, 'UTF-8', false) !!}"
                                                            @if($address->primary_billing == 1) selected style="background-color: #d3f9d8;" @endif>
                                                            {{ $address->name }}, {{ $address->first_name }}, {{ $address->phone }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">
                                                    {{ $lang ? 'Vă rugăm să selectați o adresă de livrare.' : 'Please select a delivery address.' }}
                                                </div>
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" id="sameAsBilling">
                                                    <label class="form-check-label" for="sameAsBilling">
                                                        {{ $lang ? 'La fel ca adresa de facturare' : 'Same as billing address' }}
                                                    </label>
                                                </div>
                                                <div class="address-details" id="shipping-address-details"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-6">
                                    <button type="button" class="btn btn-secondary prev-step" data-step="3">{{ $lang ? 'Anterior' : 'Previous' }}</button>
                                </div>
                                <div class="col-6 text-end">
                                    <button type="button" class="btn btn-primary next-step" data-step="3">{{ $lang ? 'Următorul' : 'Next' }}</button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Review & Submit -->
                        <div class="step step-4" style="display: none;">
                            <h4 class="mb-4 border-bottom pb-2">{{ $lang ? 'Verifică rezervarea' : 'Review Your Booking' }}</h4>
                            @if($service)
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $lang ? 'Detalii serviciu' : 'Service Details' }}</h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <img src="{{ asset('images/service/' . $service->image) }}" 
                                                 alt="{{ $lang ? $service->title_romanian : $service->title_english }}" 
                                                 class="img-fluid rounded" 
                                                 style="max-height: 150px; object-fit: cover;">
                                        </div>
                                        <div class="col-md-9">
                                            <h5>{{ $lang ? $service->title_romanian : $service->title_english }}</h5>
                                            <p>{!! $lang ? $service->des_romanian : $service->des_english !!}</p>
                                            <h5 class="text-success d-none">{{ $lang ? 'Preț:' : 'Price:' }} {{ number_format($service->price, 2) }}RON</h5>
                                            <div id="additional-fee-container" style="display: none;">
                                                <p><strong>{{ $lang ? 'Taxă suplimentară de plată:' : 'Additional Fee to Pay:' }}</strong> <span id="additional-fee-display">0.00</span> RON (<small id="additional-fee-type" class="text-muted"></small>)</p>      
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $lang ? 'Programare' : 'Schedule' }}</h5>
                                    <div class="row">
                                        <div class="col-6">
                                            <p><strong>{{ $lang ? 'Dată:' : 'Date:' }}</strong> <span id="review-date"></span></p>
                                        </div>
                                        <div class="col-6">
                                            <p><strong>{{ $lang ? 'Ora:' : 'Time:' }}</strong> <span id="review-time"></span></p>
                                        </div>
                                    </div>    
                                </div>
                            </div>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $lang ? 'Adrese' : 'Addresses' }}</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>{{ $lang ? 'Adresă facturare:' : 'Billing Address:' }}</h5>
                                            <div id="review-billing-address"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>{{ $lang ? 'Adresă livrare:' : 'Delivery Address:' }}</h5>
                                            <div id="review-shipping-address"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $lang ? 'Fișiere' : 'Files' }}</h5>
                                    <div id="file-previews" class="d-flex flex-wrap gap-2"></div>
                                </div>
                            </div>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $lang ? 'Descriere' : 'Description' }}</h5>
                                    <div id="description-preview" class="d-flex flex-wrap gap-2 w-100" style="white-space: normal; word-break: break-word;"></div>
                                </div>
                            </div>
                            @if ($service && $service->information)
                                <div class="form-check mb-4">
                                    <label class="form-check-label">
                                        {{ $lang ? 'Informații' : 'Information' }}
                                    </label>
                                    <p>{!! $service->information !!}</p>
                                </div>
                            @endif
                            <div class="row mt-4">
                                <div class="col-6">
                                    <button type="button" class="btn btn-secondary prev-step" data-step="4">{{ $lang ? 'Anterior' : 'Previous' }}</button>
                                </div>
                                <div class="col-6 text-end">
                                    <input type="hidden" id="additional_fee" name="additional_fee" value="0">
                                    <input type="hidden" id="type" name="type" value="4">
                                    <button type="submit" class="btn btn-success" id="submit-button">{{ $lang ? 'Finalizează rezervarea' : 'Complete Booking' }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Address Modal -->
<div class="modal fade" id="addressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">{{ $lang ? 'Adaugă adresă' : 'Add Address' }}</h5>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addressForm">
                <div class="modal-body">
                    <input type="hidden" name="type" id="addressType" value="1">
                    <div class="card shadow-sm mb-3">
                        <div class="card-header d-flex align-items-center">
                            <i class="bi bi-person-fill me-2"></i>
                            <h5 class="mb-0">{{ $lang ? 'Persoana de contact' : 'Contact Person' }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ $lang ? 'Nume' : 'Name' }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" required value="{{ auth()->user()->name ?? '' }}">
                                        <div class="invalid-feedback">
                                            {{ $lang ? 'Numele este obligatoriu.' : 'Name is required.' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ $lang ? 'Prenume' : 'First Name' }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="first_name" required id="first_name" value="{{ auth()->user()->first_name ?? '' }}">
                                        <div class="invalid-feedback">
                                            {{ $lang ? 'Prenumele este obligatoriu.' : 'First name is required.' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ $lang ? 'Telefon' : 'Phone' }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="phone" required id="phone" value="{{ auth()->user()->phone ?? '' }}">
                                        <div class="invalid-feedback">
                                            {{ $lang ? 'Numărul de telefon este obligatoriu și trebuie să fie valid.' : 'Phone number is required and must be valid.' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-header d-flex align-items-center">
                            <i class="bi bi-geo-alt me-2"></i>
                            <h5 class="mb-0">{{ $lang ? 'Adresă' : 'Address' }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ $lang ? 'Scara' : 'District' }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="district" required id="district">
                                        <div class="invalid-feedback">
                                            {{ $lang ? 'Sectorul este obligatoriu.' : 'District is required.' }}
                                        </div>
                                    </div>
                                </div>

                                @php
                                  $cities = ['București'];
                                @endphp

                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ $lang ? 'Județ' : 'City' }} <span class="text-danger">*</span></label>
                                        <select class="form-control" name="first_line" id="first_line" required>
                                          @foreach ($cities as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                          @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ $lang ? 'Județul este obligatoriu.' : 'City is required.' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ $lang ? 'Strada' : 'Street' }}</label>
                                        <input type="text" class="form-control" name="second_line" id="second_line">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ $lang ? 'Număr' : 'Number' }}</label>
                                        <input type="text" class="form-control" name="third_line" id="third_line">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ $lang ? 'Bloc' : 'Block' }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="town" required id="town">
                                        <div class="invalid-feedback">
                                            {{ $lang ? 'Blocul este obligatoriu.' : 'Block is required.' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ $lang ? 'Cod poștal' : 'Post Code' }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="post_code" required id="post_code">
                                        <div class="invalid-feedback">
                                            {{ $lang ? 'Codul poștal este obligatoriu și trebuie să fie valid.' : 'Post code is required and must be valid.' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ $lang ? 'Etaj' : 'Floor' }}</label>
                                        <input type="text" class="form-control" name="floor" id="floor">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ $lang ? 'Apartament' : 'Apartment' }}</label>
                                        <input type="text" class="form-control" name="apartment" id="apartment">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $lang ? 'Închide' : 'Close' }}</button>
                    <button type="button" class="btn btn-primary" id="saveAddressBtn">{{ $lang ? 'Salvează adresa' : 'Save Address' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<!-- Bootstrap 5 JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
<!-- FilePond -->
<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>

<script>
// FilePond initialization
FilePond.registerPlugin(
    FilePondPluginImagePreview,
    FilePondPluginFileValidateSize,
    FilePondPluginFileValidateType
);
const pond = FilePond.create(document.querySelector('.filepond'), {
    acceptedFileTypes: ['image/*', 'video/*', 'application/pdf'],
    maxFileSize: '10MB',
    allowMultiple: true,
    allowImagePreview: true,
    labelIdle: 'Drag & Drop your files or <span class="filepond--label-action">Browse</span>',
    credits: false,
    storeAsFile: true
});

// Function to clean up modal artifacts
function cleanupModals() {
    document.body.classList.remove('modal-open');
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    const modalEl = document.getElementById('addressModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }
    }
}

// Address Modal Handling
$(document).ready(function() {
    const addressModal = new bootstrap.Modal(document.getElementById('addressModal'));

    window.openAddressModal = function(type) {
        const title = type === 1 ? '{{ $lang ? "Livrare" : "Delivery" }}' : '{{ $lang ? "Facturare" : "Billing" }}';
        $('#modalTitle').text(`{{ $lang ? "Adaugă adresă" : "Add" }} ${title}`);
        $('#addressType').val(type);
        $('#addressForm')[0].reset(); 
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        addressModal.show();
    };

    $('#saveAddressBtn').on('click', function(e) {
        e.preventDefault();
        if (validateAddressForm()) {
            saveAddress();
        }
    });

    function validateAddressForm() {
        let isValid = true;
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        const fields = [
            { id: 'name', message: '{{ $lang ? "Numele este obligatoriu." : "Name is required." }}' },
            { id: 'first_name', message: '{{ $lang ? "Prenumele este obligatoriu." : "First name is required." }}' },
            { id: 'phone', message: '{{ $lang ? "Numărul de telefon este obligatoriu și trebuie să fie valid." : "Phone number is required and must be valid." }}', pattern: /^\+?\d{10,15}$/ },
            { id: 'district', message: '{{ $lang ? "Sectorul este obligatoriu." : "District is required." }}' },
            { id: 'first_line', message: '{{ $lang ? "Județul este obligatoriu." : "City is required." }}' },
            { id: 'town', message: '{{ $lang ? "Blocul este obligatoriu." : "Block is required." }}' },
            { id: 'post_code', message: '{{ $lang ? "Codul poștal este obligatoriu și trebuie să fie valid." : "Post code is required and must be valid." }}', pattern: /^\d{5,6}$/ }
        ];

        fields.forEach(field => {
            const input = $(`#${field.id}`);
            const value = input.val().trim();
            if (!value || (field.pattern && !field.pattern.test(value))) {
                input.addClass('is-invalid');
                input.after(`<div class="invalid-feedback">${field.message}</div>`);
                isValid = false;
            }
        });

        if (!isValid) {
            toastr.error('{{ $lang ? "Vă rugăm să completați toate câmpurile obligatorii corect." : "Please fill all required fields correctly." }}');
        }

        return isValid;
    }

    async function saveAddress() {
        const form = document.getElementById('addressForm');
        const type = $('#addressType').val();
        const isShipping = type === '1';
        
        const formData = new FormData();
        formData.append('type', type);
        formData.append('name', $('#name').val());
        formData.append('first_name', $('#first_name').val());
        formData.append('phone', $('#phone').val());
        formData.append('district', $('#district').val());
        formData.append('first_line', $('#first_line').val());
        formData.append('second_line', $('#second_line').val());
        formData.append('third_line', $('#third_line').val());
        formData.append('town', $('#town').val());
        formData.append('post_code', $('#post_code').val());
        formData.append('floor', $('#floor').val());
        formData.append('apartment', $('#apartment').val());

        $('#saveAddressBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ $lang ? "Se salvează..." : "Saving..." }}');

        try {
            const response = await $.ajax({
                url: '/addresses-store',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (response.code === 201) {
                addressModal.hide();
                cleanupModals();
                toastr.success('{{ $lang ? "Adresă adăugată cu succes!" : "Address added successfully!" }}');
                updateAddressDropdown(isShipping ? 'shipping' : 'billing', response.address);
            }
        } catch (error) {
            handleFormErrors(error);
        } finally {
            $('#saveAddressBtn').prop('disabled', false).text('{{ $lang ? "Salvează adresa" : "Save Address" }}');
        }
    }

    function updateAddressDropdown(addressType, address) {
        const selectElement = $(`select[name="${addressType}_address_id"]`);
        const addressText = `${address.name}, ${address.first_name}, ${address.phone}`;
        const newOption = new Option(addressText, address.id, true, true);
        newOption.dataset.details = JSON.stringify({
            name: address.name,
            first_name: address.first_name,
            phone: address.phone,
            district: address.district,
            first_line: address.first_line,
            second_line: address.second_line,
            third_line: address.third_line,
            town: address.town,
            post_code: address.post_code,
            floor: address.floor,
            apartment: address.apartment
        });
        selectElement.append(newOption).trigger('change');
    }

    function handleFormErrors(error) {
        if (error.status === 422) {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $.each(error.responseJSON.errors, function(key, value) {
                const input = $(`[name="${key}"]`);
                input.addClass('is-invalid');
                input.after(`<div class="invalid-feedback">${value[0]}</div>`);
            });
        } else {
            toastr.error('{{ $lang ? "A apărut o eroare. Te rugăm să încerci din nou." : "An error occurred. Please try again." }}');
        }
    }

    // Address Details Display
    function displayAddressDetails(addressType, details) {
        const container = $(`#${addressType}-address-details`);
        container.empty();
        if (details) {
            const fields = [
                { key: 'district', label: '{{ $lang ? "Sector" : "District" }}' },
                { key: 'first_line', label: '{{ $lang ? "Județ" : "City" }}' },
                { key: 'second_line', label: '{{ $lang ? "Strada" : "Street" }}' },
                { key: 'third_line', label: '{{ $lang ? "Număr" : "Number" }}' },
                { key: 'town', label: '{{ $lang ? "Bloc" : "Block" }}' },
                { key: 'post_code', label: '{{ $lang ? "Cod poștal" : "Post Code" }}' },
                { key: 'floor', label: '{{ $lang ? "Etaj" : "Floor" }}' },
                { key: 'apartment', label: '{{ $lang ? "Apartament" : "Apartment" }}' }
            ];
            const ul = $('<ul class="mt-2 mb-0 ps-3 small text-muted"></ul>');
            fields.forEach(field => {
                if (details[field.key]) {
                    ul.append(`<li>${field.label}: ${details[field.key]}</li>`);
                }
            });
            container.append(ul);
        }
    }

    // Handle address selection
    $('select[name="billing_address_id"]').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const details = selectedOption.data('details') || {};
        displayAddressDetails('billing', details);
        if ($('#sameAsBilling').is(':checked')) {
            const billingVal = $(this).val();
            $('select[name="shipping_address_id"]').val(billingVal).trigger('change');
        }
    });

    $('select[name="shipping_address_id"]').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const details = selectedOption.data('details') || {};
        displayAddressDetails('shipping', details);
    });

    // Handle "Same as billing" checkbox
    $('#sameAsBilling').on('change', function() {
        if ($(this).is(':checked')) {
            const billingVal = $('select[name="billing_address_id"]').val();
            if (billingVal) {
                $('select[name="shipping_address_id"]').val(billingVal).trigger('change');
            }
        }
    });

    // Initialize address details for pre-selected addresses
    $('select[name="billing_address_id"]').trigger('change');
    $('select[name="shipping_address_id"]').trigger('change');
});

// Update Fee Function
function updateFee() {
    const date = $('#eventDate').val().trim();
    const time = $('#eventTime').val().trim();
    if (!date || !time) return;
    
    // console.log(date, time);

    $.ajax({
        url: '{{ route("booking.calculateFee") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            date: date,
            time: time
        },
        success: function(response) {
            $('#type').val(response.type);
            // console.log(response);
            if (response.error) {
                $('#additional-fee-container').hide();
                $('#additional_fee').val(0);
                let text = '{{ $lang ? "Nu este disponibil" : "Not Available" }}';
                $('#submit-button').prop('disabled', true).text(text);
                Swal.fire({
                    icon: 'error',
                    title: '{{ $lang ? "Eroare" : "Error" }}',
                    text: text,
                    confirmButtonText: 'OK'
                });
                return;
            }

            const additionalFee = parseFloat(response.fee);
            if (additionalFee > 0) {
                $('#additional-fee-display').text(additionalFee.toFixed(2));
                $('#additional_fee').val(additionalFee.toFixed(2));
                $('#additional-fee-type').text(response.type_label);
                $('#additional-fee-container').show();
                $('#submit-button').text('{{ $lang ? "Plătește" : "Pay" }} ' + additionalFee.toFixed(2) + ' RON');
            } else {
                $('#additional_fee').val(0);
                $('#additional-fee-container').hide();
                $('#submit-button').text('{{ $lang ? "Finalizează rezervarea" : "Complete Booking" }}');
            }
            $('#submit-button').prop('disabled', false);
        },
        error: function(xhr, status, error) {
            console.error('Fee calculation error:', error, xhr.responseText);
            toastr.error('{{ $lang ? "Eroare la calcularea taxei." : "Error calculating fee." }}');
        }
    });
}

// Watch and Calendar Functionality
function updateWatch() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('watch').textContent = `${hours}:${minutes}:${seconds}`;
}
setInterval(updateWatch, 1000);
updateWatch();

function toggleTimeInput() {
    const watch = document.getElementById('watch');
    const timeInputContainer = document.getElementById('timeInputContainer');
    watch.style.display = 'none';
    timeInputContainer.style.display = 'block';
    // Pre-fill with current values if available
    const eventDate = document.getElementById('eventDate').value;
    const eventTime = document.getElementById('eventTime').value;
    if (eventDate) {
        document.getElementById('inlineDateInput').value = eventDate;
    }
    if (eventTime) {
        const [hour, minute] = eventTime.split(':');
        document.getElementById('inlineTimeInput').value = `${hour}:${minute}`;
    }
}

function setInlineTime() {
    const dateInput = document.getElementById('inlineDateInput').value;
    const timeInput = document.getElementById('inlineTimeInput').value;
    let isValid = true;

    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').hide();

    // Validate date format (dd/mm/yyyy)
    const datePattern = /^(\d{2})\/(\d{2})\/(\d{4})$/;
    if (!dateInput || !datePattern.test(dateInput)) {
        $('#inlineDateInput').addClass('is-invalid');
        $('#inlineDateInput').next('.invalid-feedback').show();
        isValid = false;
    }

    // Validate time
    if (!timeInput) {
        $('#inlineTimeInput').addClass('is-invalid');
        $('#inlineTimeInput').next('.invalid-feedback').show();
        isValid = false;
    }

    if (isValid) {
        const [day, month, year] = dateInput.split('/');
        const [hour, minute] = timeInput.split(':');
        document.getElementById('eventDate').value = dateInput;
        document.getElementById('eventTime').value = `${hour}:${minute}:00`;
        updateDateTime();
        document.getElementById('watch').style.display = 'inline-block';
        document.getElementById('timeInputContainer').style.display = 'none';
    } else {
        toastr.error('{{ $lang ? "Vă rugăm să introduceți o dată și oră valide." : "Please enter a valid date and time." }}');
    }
}

let currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();
let selectedDate = null;

function renderCalendar(month, year) {
    const calendar = document.getElementById('calendar');
    const monthYear = document.getElementById('month-year');
    const firstDay = new Date(year, month, 1).getDay();
    const lastDate = new Date(year, month + 1, 0).getDate();
    const today = new Date();
    const currentDay = today.getDate();
    const months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    monthYear.textContent = `${months[month]} ${year}`;
    while (calendar.children.length > 7) {
        calendar.removeChild(calendar.lastChild);
    }

    for (let i = 0; i < firstDay; i++) {
        const blankDay = document.createElement('div');
        blankDay.className = 'calendar-day';
        calendar.appendChild(blankDay);
    }

    for (let i = 1; i <= lastDate; i++) {
        const day = document.createElement('div');
        day.className = 'calendar-day';
        if (i === currentDay && month === today.getMonth() && year === today.getFullYear()) {
            day.className += ' current';
        }
        if (selectedDate && i === selectedDate.getDate() && month === selectedDate.getMonth() && year === selectedDate.getFullYear()) {
            day.className += ' selected';
        }
        day.textContent = i;
        day.onclick = () => selectDate(i, month, year);
        calendar.appendChild(day);
    }
}

function selectDate(day, month, year) {
    selectedDate = new Date(year, month, day);
    const formattedDate = `${String(day).padStart(2, '0')}/${String(month + 1).padStart(2, '0')}/${year}`;
    document.getElementById('eventDate').value = formattedDate;
    document.getElementById('inlineDateInput').value = formattedDate; // Sync inline input
    updateDateTime();
    renderCalendar(currentMonth, currentYear);
}

function changeMonth(offset) {
    currentMonth += offset;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    } else if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    renderCalendar(currentMonth, currentYear);
}

function updateDateTime() {
    const date = document.getElementById('eventDate').value;
    const time = document.getElementById('eventTime').value;
    if (date && time) {
        const [hour, minute] = time.split(':');
        document.getElementById('date_time').value = `${date} ${hour}:${minute}`;
        updateFee();
    }
}

// Step Navigation and Validation
$(document).ready(function() {
    $('.next-step').click(function() {
        const currentStep = $(this).data('step');
        const nextStep = currentStep + 1;
        
        if (validateStep(currentStep)) {
            $('.step-' + currentStep).hide();
            $('.step-' + nextStep).show();
            updateProgressBar(nextStep);
            
            if (nextStep === 2) {
                updateFee();
            }
            
            if (nextStep === 4) {
                updateReviewSection();
            }
        }
    });
    
    $('.prev-step').click(function() {
        const currentStep = $(this).data('step');
        const prevStep = currentStep - 1;
        
        $('.step-' + currentStep).hide();
        $('.step-' + prevStep).show();
        updateProgressBar(prevStep);
        cleanupModals();
    });
    
    $('#bookingForm').on('submit', function(e) {
        if (!validateStep(4)) {
            e.preventDefault();
            toastr.error('{{ $lang ? "Vă rugăm să completați toate câmpurile obligatorii." : "Please complete all required fields correctly." }}');
        }
    });

    function updateProgressBar(step) {
        const percentage = (step / 4) * 100;
        let stepText = '';
        
        switch(step) {
            case 1:
                stepText = '{{ $lang ? "Pasul 1 din 4 - Detalii serviciu" : "Step 1 of 4 - Service Details" }}';
                break;
            case 2:
                stepText = '{{ $lang ? "Pasul 2 din 4 - Programare" : "Step 2 of 4 - Schedule" }}';
                break;
            case 3:
                stepText = '{{ $lang ? "Pasul 3 din 4 - Adrese" : "Step 3 of 4 - Addresses" }}';
                break;
            case 4:
                stepText = '{{ $lang ? "Pasul 4 din 4 - Verifică și trimite" : "Step 4 of 4 - Review & Submit" }}';
                break;
        }
        
        $('.progress-bar').css('width', percentage + '%').text(stepText);
    }
    
    function validateStep(step) {
        let isValid = true;
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').hide();

        if (step === 1) {
            const description = $('#job_description').val().trim();
            if (description.length < 1) {
                $('#job_description').addClass('is-invalid');
                $('#job_description').next('.invalid-feedback').show();
                isValid = false;
            }
            const files = pond.getFiles();
            if (files.length === 0) {
                $('.filepond').addClass('is-invalid');
                $('.filepond').next('.invalid-feedback').show();
                isValid = false;
            }
        } else if (step === 2) {
            updateFee();
            const date = $('#eventDate').val().trim();
            const time = $('#eventTime').val().trim();
            if (!date) {
                $('#eventDate').addClass('is-invalid');
                $('#eventDate').next('.invalid-feedback').show();
                isValid = false;
            }
            if (!time) {
                $('#eventTime').addClass('is-invalid');
                $('#eventTime').next('.invalid-feedback').show();
                isValid = false;
            }
            if (date && time) {
                const [day, month, year] = date.split('/').map(Number);
                const [hour, minute] = time.split(':').map(Number);
                const selectedDateTime = new Date(year, month - 1, day, hour, minute);
                const now = new Date();
                if (selectedDateTime <= now) {
                    toastr.error('{{ $lang ? "Data și ora selectate trebuie să fie în viitor." : "Selected date and time must be in the future." }}');
                    $('#eventDate').addClass('is-invalid');
                    $('#eventTime').addClass('is-invalid');
                    isValid = false;
                }
            }
        } else if (step === 3) {
            const billingAddress = $('select[name="billing_address_id"]').val();
            if (!billingAddress) {
                $('select[name="billing_address_id"]').addClass('is-invalid');
                $('select[name="billing_address_id"]').next('.invalid-feedback').show();
                isValid = false;
            }
            const shippingAddress = $('select[name="shipping_address_id"]').val();
            if (!shippingAddress) {
                $('select[name="shipping_address_id"]').addClass('is-invalid');
                $('select[name="shipping_address_id"]').next('.invalid-feedback').show();
                isValid = false;
            }
        } else if (step === 4) {
            const description = $('#job_description').val().trim();
            if (description.length > 1000) {
                isValid = false;
            }
            const files = pond.getFiles();
            if (files.length === 0) {
                isValid = false;
            }
            const date = $('#eventDate').val().trim();
            const time = $('#eventTime').val().trim();
            if (!date || !time) {
                isValid = false;
            } else {
                const [day, month, year] = date.split('/').map(Number);
                const [hour, minute] = time.split(':').map(Number);
                const selectedDateTime = new Date(year, month - 1, day, hour, minute);
                const now = new Date();
                if (selectedDateTime <= now) {
                    isValid = false;
                }
            }
            const billingAddress = $('select[name="billing_address_id"]').val();
            const shippingAddress = $('select[name="shipping_address_id"]').val();
            if (!billingAddress || !shippingAddress) {
                isValid = false;
            }
            if (!isValid) {
                toastr.error('{{ $lang ? "Vă rugăm să completați toate câmpurile obligatorii corect." : "Please complete all required fields correctly." }}');
            }
        }

        if (!isValid) {
            toastr.error('{{ $lang ? "Vă rugăm să completați toate câmpurile obligatorii corect." : "Please complete all required fields correctly." }}');
        }

        return isValid;
    }
    
    function updateReviewSection() {
        const dateTime = $('#date_time').val();
        if (dateTime) {
            const [datePart, timePart] = dateTime.split(' ');
            $('#review-date').text(datePart || '');
            $('#review-time').text(timePart || '');
        } else {
            $('#review-date').text('');
            $('#review-time').text('');
        }
        
        // Billing Address Review
        const billingOption = $('select[name="billing_address_id"] option:selected');
        if (billingOption.length) {
            const details = billingOption.data('details') || {};
            const fields = [
                { key: 'name', label: '{{ $lang ? "Nume" : "Name" }}' },
                { key: 'first_name', label: '{{ $lang ? "Prenume" : "First Name" }}' },
                { key: 'phone', label: '{{ $lang ? "Telefon" : "Phone" }}' },
                { key: 'district', label: '{{ $lang ? "Sector" : "District" }}' },
                { key: 'first_line', label: '{{ $lang ? "Județ" : "City" }}' },
                { key: 'second_line', label: '{{ $lang ? "Strada" : "Street" }}' },
                { key: 'third_line', label: '{{ $lang ? "Număr" : "Number" }}' },
                { key: 'town', label: '{{ $lang ? "Bloc" : "Block" }}' },
                { key: 'post_code', label: '{{ $lang ? "Cod poștal" : "Post Code" }}' },
                { key: 'floor', label: '{{ $lang ? "Etaj" : "Floor" }}' },
                { key: 'apartment', label: '{{ $lang ? "Apartament" : "Apartment" }}' }
            ];
            const ul = $('<ul class="mt-2 mb-0 ps-3 small text-muted"></ul>');
            fields.forEach(field => {
                if (details[field.key]) {
                    ul.append(`<li>${field.label}: ${details[field.key]}</li>`);
                }
            });
            $('#review-billing-address').empty().append(ul);
        }
        
        // Shipping Address Review
        const shippingOption = $('select[name="shipping_address_id"] option:selected');
        if (shippingOption.length) {
            const details = shippingOption.data('details') || {};
            const fields = [
                { key: 'name', label: '{{ $lang ? "Nume" : "Name" }}' },
                { key: 'first_name', label: '{{ $lang ? "Prenume" : "First Name" }}' },
                { key: 'phone', label: '{{ $lang ? "Telefon" : "Phone" }}' },
                { key: 'district', label: '{{ $lang ? "Sector" : "District" }}' },
                { key: 'first_line', label: '{{ $lang ? "Județ" : "City" }}' },
                { key: 'second_line', label: '{{ $lang ? "Strada" : "Street" }}' },
                { key: 'third_line', label: '{{ $lang ? "Număr" : "Number" }}' },
                { key: 'town', label: '{{ $lang ? "Bloc" : "Block" }}' },
                { key: 'post_code', label: '{{ $lang ? "Cod poștal" : "Post Code" }}' },
                { key: 'floor', label: '{{ $lang ? "Etaj" : "Floor" }}' },
                { key: 'apartment', label: '{{ $lang ? "Apartament" : "Apartment" }}' }
            ];
            const ul = $('<ul class="mt-2 mb-0 ps-3 small text-muted"></ul>');
            fields.forEach(field => {
                if (details[field.key]) {
                    ul.append(`<li>${field.label}: ${details[field.key]}</li>`);
                }
            });
            $('#review-shipping-address').empty().append(ul);
        }
        
        // File Previews
        $('#file-previews').empty();
        const files = pond.getFiles();
        files.forEach(file => {
            if (file.fileType.match('image.*')) {
                $('#file-previews').append(
                    `<div class="border p-2 rounded" style="width: 120px;">
                        <img src="${URL.createObjectURL(file.file)}" class="img-thumbnail" style="height: 100px; object-fit: cover;">
                        <small class="d-block text-truncate">${file.filename}</small>
                    </div>`
                );
            } else {
                $('#file-previews').append(
                    `<div class="border p-2 rounded" style="width: 120px;">
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 100px;">
                            <i class="fas fa-file-alt fa-3x text-secondary"></i>
                        </div>
                        <small class="d-block text-truncate">${file.filename}</small>
                    </div>`
                );
            }
        });

        // Description Preview
        $('#description-preview').text($('#job_description').val());
    }
});

// Initialize
renderCalendar(currentMonth, currentYear);
</script>

@endsection
