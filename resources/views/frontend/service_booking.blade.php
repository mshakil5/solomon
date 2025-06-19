@extends('layouts.master')

@section('content')
@php
    $lang = session('app_locale', 'ro') == 'ro';
@endphp

{{-- @include('frontend.inc.hero') --}}

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
                  {{ $lang ? 'Finalizează rezervarea pentru' : 'Complete Your Booking for' }} 
                  {{ $lang ? $service->title_romanian : $service->title_english }}
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
                      <input type="hidden" name="service_id" value="{{ $service->id }}">

                      <!-- Step 1: Service Details -->
                      <div class="step step-1">
                          <div class="row mb-4">
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
                          </div>
                          
                          <h4 class="mb-3 border-bottom pb-2">{{ $lang ? 'Informații suplimentare' : 'Additional Information' }}</h4>
                          
                          <div class="form-group">
                              <label for="job_description">{{ $lang ? 'Descriere serviciu' : 'Job Description' }}</label>
                              <textarea class="form-control" id="job_description" name="description" rows="4"></textarea>
                          </div>
                          
                          <div class="form-group mt-4">
                              <label>{{ $lang ? 'Încarcă fișiere (imagini, video, PDF) - Max 10MB fiecare' : 'Upload Files (Images, Videos, PDFs) - Max 10MB each' }}</label>
                              <input type="file" class="filepond" name="files[]" multiple data-max-file-size="10MB">
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
                           <input type="hidden" name="selected_type" value="{{ $type }}" id="selected_type">
                          @if(!is_null($type) && $type == 1)
                          <div class="row">
                              <div class="row">
                                <label for="time" class="col-4 col-form-label">Pick Time</label>
                                <div class="col-8">
                                  <div class="input-group clockpicker">
                                      <input type="text" id="timeInput" name="date_time" class="form-control" />
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
                          @else
                          <div class="row">
                              <div class="row">
                                <label for="time" class="col-4 col-form-label">Pick Date and Time</label>
                                <div class="col-8">
                                  <div class="input-group clockpicker">
                                      <input type="text" id="date_time_input" name="date_time" class="form-control" />
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
                          @endif
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
                                                          @if($address->primary_billing == 1) selected style="background-color: #d3f9d8;" @endif>
                                                          {{ $address->name }}, {{ $address->first_name }}, {{ $address->phone }}
                                                      </option>
                                                  @endforeach
                                              </select>       
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
                                                          @if($address->primary_billing == 1) selected style="background-color: #d3f9d8;" @endif>
                                                          {{ $address->name }}, {{ $address->first_name }}, {{ $address->phone }}
                                                      </option>
                                                  @endforeach
                                              </select>
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
                          
                          @if ($service->information)
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
                    <div class="row">
                      <div class="col-6">
                        <div class="mb-3">
                          <label class="form-label">{{ $lang ? 'Nume' : 'Name' }} <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="mb-3">
                          <label class="form-label">{{ $lang ? 'Prenume' : 'First Name' }} <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" name="first_name" required id="first_name">
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="mb-3">
                          <label class="form-label">{{ $lang ? 'Telefon' : 'Phone' }} <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" name="phone" required id="phone">
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="mb-3">
                          <label class="form-label">{{ $lang ? 'Scara' : 'District' }} <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" name="district" required id="district">
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="mb-3">
                            <label class="form-label">{{ $lang ? 'Judet' : 'County' }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="first_line" required id="first_line">
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
                          <label class="form-label">{{ $lang ? 'Numar' : 'Number' }}</label>
                          <input type="text" class="form-control" name="third_line" id="third_line">
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="mb-3">
                          <label class="form-label">{{ $lang ? 'Bloc' : 'Block' }} <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" name="town" required id="town">
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="mb-3">
                          <label class="form-label">{{ $lang ? 'Cod poștal' : 'Post Code' }} <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" name="post_code" required id="post_code">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $lang ? 'Închide' : 'Close' }}</button>
                    <button type="button" class="btn btn-primary" id="saveAddressBtn">{{ $lang ? 'Salvează adresa' : 'Save Address' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
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

  .filepond--list{
    padding-top: 10px;
  }
</style>

@endsection

@section('script')

<script>
  const timeInput = document.getElementById('timeInput');
  if (timeInput) {
    new tempusDominus.TempusDominus(timeInput, {
      defaultDate: new Date(),
      display: {
        components: {
          calendar: false,
          date: false,
          month: false,
          year: false,
          decades: false,
          hours: true,
          minutes: true,
          seconds: false
        },
        buttons: {
          today: false,
          clear: false,
          close: true
        }
      },
      localization: {
        format: 'dd/MM/yyyy HH:mm'
      }
    });
  }

  const dateTimeInput = document.getElementById('date_time_input');
  if (dateTimeInput) {
    new tempusDominus.TempusDominus(dateTimeInput, {
      defaultDate: new Date(),
      display: {
        components: {
          calendar: true,
          date: true,
          month: true,
          year: true,
          decades: false,
          hours: true,
          minutes: true,
          seconds: false
        },
        buttons: {
          today: true,
          clear: true,
          close: true
        },
      },
      localization: {
        format: 'dd/MM/yyyy HH:mm'
      }
    });
  }
</script>

<script>
  function updateFee() {
    let date, time;
    const val = $('#date_time_input').val() || $('#timeInput').val() || '';
    [date, time] = val.split(' ');

      $.ajax({
          url: '{{ route("booking.calculateFee") }}',
          method: 'POST',
          data: {
              _token: '{{ csrf_token() }}',
              date: date,
              time: time
          },
          success: function(response) {
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
          }
      });
  }

  $('#date_time_input, #timeInput').on('change input', updateFee);

  $(document).ready(function () {
    updateFee();
  });
</script>

<script>
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
        saveAddress();
    });

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
});
</script>


<!-- FilePond -->
<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>

<script>
  // Register plugins
  FilePond.registerPlugin(
      FilePondPluginImagePreview,
      FilePondPluginFileValidateSize,
      FilePondPluginFileValidateType
  );
  
  // Create FilePond instance
  const pond = FilePond.create(document.querySelector('.filepond'), {
      acceptedFileTypes: ['image/*', 'video/*', 'application/pdf'],
      maxFileSize: '10MB',
      allowMultiple: true,
      allowImagePreview: true,
      labelIdle: 'Drag & Drop your files or <span class="filepond--label-action">Browse</span>',
      credits: false
  });

  pond.setOptions({
      storeAsFile: true
  });
</script>

<script>
  $(document).ready(function() {
    // Step navigation
    $('.next-step').click(function() {
        const currentStep = $(this).data('step');
        const nextStep = currentStep + 1;
        
        if (validateStep(currentStep)) {
            $('.step-' + currentStep).hide();
            $('.step-' + nextStep).show();
            updateProgressBar(nextStep);
            
            // Update review section when moving to step 4
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
        
        if (step === 1) {
            // Validation for step 1
        } else if (step === 2) {
            if ($('#date').val() === '') {
                alert('{{ $lang ? "Vă rugăm să selectați o dată" : "Please select a date" }}');
                isValid = false;
            } else if ($('#time').val() === '') {
                alert('{{ $lang ? "Vă rugăm să selectați o oră" : "Please select a time" }}');
                isValid = false;
            }
        } else if (step === 3) {
          if (!$('select[name="billing_address_id"]').length || $('select[name="billing_address_id"]').val() === '') {
              alert('{{ $lang ? "Vă rugăm să selectați o adresă de facturare" : "Please select a billing address" }}');
              isValid = false;
          }
          
          if (!$('select[name="shipping_address_id"]').length || $('select[name="shipping_address_id"]').val() === '') {
              alert('{{ $lang ? "Vă rugăm să selectați o adresă de livrare" : "Please select a Delivery address" }}');
              isValid = false;
          }
      }
        
        return isValid;
    }
    
    function updateReviewSection() {
        // Update date and time
        const dateTimeVal = $('#date_time_input').val();
        const timeOnlyVal = $('#timeInput').val();

        if (dateTimeVal) {
          const [datePart, timePart] = dateTimeVal.split(' ');
          $('#review-date').text(datePart || '');
          $('#review-time').text(timePart || '');
        } else if (timeOnlyVal) {
          const [datePart, timePart] = timeOnlyVal.split(' ');
          $('#review-date').text(datePart || '');
          $('#review-time').text(timePart || '');
        }
        
        // Update billing address
        if ($('select[name="billing_address_id"]').length) {
            const selectedOption = $('select[name="billing_address_id"] option:selected');
            $('#review-billing-address').html(selectedOption.text());
        }
        
        // Update Delivery address
        if ($('select[name="shipping_address_id"]').length) {
            const selectedOption = $('select[name="shipping_address_id"] option:selected');
            $('#review-shipping-address').html(selectedOption.text());
        }
        
        // Update file previews
        $('#file-previews').empty();
        const files = pond.getFiles();
        files.forEach(file => {
            if (file.fileType.match('image.*')) {
                $('#file-previews').append(`
                    <div class="border p-2 rounded" style="width: 120px;">
                        <img src="${URL.createObjectURL(file.file)}" class="img-thumbnail" style="height: 100px; object-fit: cover;">
                        <small class="d-block text-truncate">${file.filename}</small>
                    </div>
                `);
            } else {
                $('#file-previews').append(`
                    <div class="border p-2 rounded" style="width: 120px;">
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 100px;">
                            <i class="fas fa-file-alt fa-3x text-secondary"></i>
                        </div>
                        <small class="d-block text-truncate">${file.filename}</small>
                    </div>
                `);
            }
        });

        //Update description
        $('#description-preview').text($('#job_description').val());

    }
  });
</script>
@endsection