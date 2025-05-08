@extends('layouts.master')

@section('content')

@include('frontend.inc.hero')

<div class="container my-5">
  <div class="row justify-content-center">
      <div class="col-lg-10 col-md-12 col-sm-12">
          <div class="card">
              <div class="card-header text-center bg-primary text-white">
                  <h2>Complete Your Booking for {{ $service->title_english }}</h2>
              </div>
              
              <div class="card-body">
                  <div class="progress mb-4" style="height: 20px;">
                      <div class="progress-bar progress-bar-striped progress-bar-animated" 
                           role="progressbar" 
                           style="width: 25%; font-size: 14px; line-height: 20px;" 
                           aria-valuenow="25" 
                           aria-valuemin="0" 
                           aria-valuemax="100">
                          Step 1 of 4 - Service Details
                      </div>
                  </div>

                  @if ($success = Session::get('success'))
                      <div class="alert alert-primary alert-dismissible fade show" role="alert">
                          <strong>{{ $success }}</strong>
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                      </div>
                  @endif

                  @if ($errors->any()))
                      <div class="alert alert-danger">
                          <ul>
                              @foreach ($errors->all() as $error)
                                  <li>{{ $error }}</li>
                              @endforeach
                          </ul>
                      </div>
                  @endif

                  <form id="bookingForm" action="{{ route('booking.store') }}" method="post" role="form" enctype="multipart/form-data">
                      @csrf
                      <input type="hidden" name="service_id" value="{{ $service->id }}">

                      <!-- Step 1: Service Details -->
                      <div class="step step-1">
                          <div class="row mb-4">
                              <div class="col-md-4">
                                  <img src="{{ asset('images/service/' . $service->image) }}" 
                                       alt="{{ $service->title_english }}" 
                                       class="img-fluid rounded" 
                                       style="max-height: 200px; object-fit: cover;">
                              </div>
                              <div class="col-md-8">
                                  <h3 class="text-primary">{{ $service->title_english }}</h3>
                                  <p class="lead">{!! $service->des_english !!}</p>
                                  <h4 class="text-success">Price: {{ number_format($service->price, 2) }}RON</h4>
                              </div>
                          </div>
                          
                          <h4 class="mb-3 border-bottom pb-2">Additional Information</h4>
                          
                          <div class="form-group">
                              <label for="job_description">Job Description</label>
                              <textarea class="form-control" id="job_description" name="description" rows="4"></textarea>
                          </div>
                          
                          <div class="form-group mt-4">
                              <label>Upload Files (Images, Videos, PDFs) - Max 10MB each</label>
                              <input type="file" class="filepond" name="files[]" multiple data-max-file-size="10MB">
                          </div>
                          
                          <div class="row mt-4">
                              <div class="col-12 text-end">
                                  <button type="button" class="btn btn-primary next-step" data-step="1">Next</button>
                              </div>
                          </div>
                      </div>

                      <!-- Step 2: Schedule -->
                      <div class="step step-2" style="display: none;">
                          <h4 class="mb-4 border-bottom pb-2">Schedule Your Service</h4>
                          
                          <div class="row">
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label for="date">Date <span class="text-danger">*</span></label>
                                      <input type="date" name="date" id="date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label for="time">Time <span class="text-danger">*</span></label>
                                      <input type="time" name="time" id="time" class="form-control" required>
                                  </div>
                              </div>
                          </div>
                          
                          <div class="row mt-4">
                              <div class="col-6">
                                  <button type="button" class="btn btn-secondary prev-step" data-step="2">Previous</button>
                              </div>
                              <div class="col-6 text-end">
                                  <button type="button" class="btn btn-primary next-step" data-step="2">Next</button>
                              </div>
                          </div>
                      </div>

                      <!-- Step 3: Addresses -->
                      <div class="step step-3" style="display: none;">
                          <h4 class="mb-4 border-bottom pb-2">Address Information</h4>
                          
                          <div class="row">
                              <div class="col-md-6">
                                  <div class="card mb-4">
                                      <div class="card-header bg-light">
                                          <h5>Billing Address</h5>
                                      </div>
                                      <div class="card-body">
                                          @if($billingAddresses->count() > 0)
                                              <div class="form-group">
                                                  <label>Select Billing Address</label>
                                                  <select class="form-select" name="billing_address_id" required>
                                                      @foreach($billingAddresses as $address)
                                                          <option value="{{ $address->id }}">
                                                              {{ $address->name }}, {{ $address->first_name }}, {{ $address->phone }}
                                                          </option>
                                                      @endforeach
                                                  </select>
                                              </div>
                                          @else
                                              <p class="text-muted">No saved billing addresses found.</p>
                                              <a href="#" class="btn btn-sm btn-outline-primary d-none">
                                                  Add New Billing Address
                                              </a>
                                          @endif
                                      </div>
                                  </div>
                              </div>
                              
                              <div class="col-md-6">
                                  <div class="card">
                                      <div class="card-header bg-light">
                                          <h5>Shipping Address</h5>
                                      </div>
                                      <div class="card-body">
                                          @if($shippingAddresses->count() > 0)
                                              <div class="form-group">
                                                  <label>Select Shipping Address</label>
                                                  <select class="form-select" name="shipping_address_id" required>
                                                      @foreach($shippingAddresses as $address)
                                                          <option value="{{ $address->id }}">
                                                            {{ $address->name }}, {{ $address->first_name }},   {{ $address->phone }}
                                                          </option>
                                                      @endforeach
                                                  </select>
                                              </div>
                                          @else
                                              <p class="text-muted">No saved shipping addresses found.</p>
                                              <a href="#" class="btn btn-sm btn-outline-primary d-none">
                                                  Add New Shipping Address
                                              </a>
                                          @endif
                                      </div>
                                  </div>
                              </div>
                          </div>
                          
                          <div class="row mt-4">
                              <div class="col-6">
                                  <button type="button" class="btn btn-secondary prev-step" data-step="3">Previous</button>
                              </div>
                              <div class="col-6 text-end">
                                  <button type="button" class="btn btn-primary next-step" data-step="3">Next</button>
                              </div>
                          </div>
                      </div>

                      <!-- Step 4: Review & Submit -->
                      <div class="step step-4" style="display: none;">
                          <h4 class="mb-4 border-bottom pb-2">Review Your Booking</h4>
                          
                          <div class="card mb-4">
                              <div class="card-body">
                                  <h5 class="card-title">Service Details</h5>
                                  <div class="row">
                                      <div class="col-md-3">
                                          <img src="{{ asset('images/service/' . $service->image) }}" 
                                               alt="{{ $service->title_english }}" 
                                               class="img-fluid rounded" 
                                               style="max-height: 150px; object-fit: cover;">
                                      </div>
                                      <div class="col-md-9">
                                          <h5>{{ $service->title_english }}</h5>
                                          <p>{!! $service->des_english !!}</p>
                                          <h5 class="text-success">Price: £{{ number_format($service->price, 2) }}</h5>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          
                          <div class="card mb-4">
                              <div class="card-body">
                                  <h5 class="card-title">Schedule</h5>
                                  <p><strong>Date:</strong> <span id="review-date"></span></p>
                                  <p><strong>Time:</strong> <span id="review-time"></span></p>
                              </div>
                          </div>
                          
                          <div class="card mb-4">
                              <div class="card-body">
                                  <h5 class="card-title">Addresses</h5>
                                  <div class="row">
                                      <div class="col-md-6">
                                          <h6>Billing Address</h6>
                                          <div id="review-billing-address"></div>
                                      </div>
                                      <div class="col-md-6">
                                          <h6>Shipping Address</h6>
                                          <div id="review-shipping-address"></div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          
                          <div class="card mb-4">
                              <div class="card-body">
                                  <h5 class="card-title">Files</h5>
                                  <div id="file-previews" class="d-flex flex-wrap gap-2"></div>
                              </div>
                          </div>
                          
                          <div class="form-check mb-4">
                              
                              <label class="form-check-label">
                                  Information
                              </label>
                              <p>{!! $service->information !!}</p>
                          </div>
                          
                          <div class="row mt-4">
                              <div class="col-6">
                                  <button type="button" class="btn btn-secondary prev-step" data-step="4">Previous</button>
                              </div>
                              <div class="col-6 text-end">
                                  <button type="submit" class="btn btn-success">Complete Booking</button>
                              </div>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
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
                stepText = 'Step 1 of 4 - Service Details';
                break;
            case 2:
                stepText = 'Step 2 of 4 - Schedule';
                break;
            case 3:
                stepText = 'Step 3 of 4 - Addresses';
                break;
            case 4:
                stepText = 'Step 4 of 4 - Review & Submit';
                break;
        }
        
        $('.progress-bar').css('width', percentage + '%').text(stepText);
    }
    
    function validateStep(step) {
        let isValid = true;
        
        if (step === 1) {
            // if ($('#job_description').val().trim() === '') {
            //     alert('Please provide a job description');
            //     isValid = false;
            // }
        } else if (step === 2) {
            if ($('#date').val() === '') {
                alert('Please select a date');
                isValid = false;
            } else if ($('#time').val() === '') {
                alert('Please select a time');
                isValid = false;
            }
        } else if (step === 3) {
            if ($('select[name="billing_address_id"]').length && $('select[name="billing_address_id"]').val() === '') {
                alert('Please select a billing address');
                isValid = false;
            } else if ($('select[name="shipping_address_id"]').length && $('select[name="shipping_address_id"]').val() === '') {
                alert('Please select a shipping address');
                isValid = false;
            }
        }
        
        return isValid;
    }
    
    function updateReviewSection() {
        // Update date and time
        $('#review-date').text($('#date').val());
        $('#review-time').text($('#time').val());
        
        // Update billing address
        if ($('select[name="billing_address_id"]').length) {
            const selectedOption = $('select[name="billing_address_id"] option:selected');
            $('#review-billing-address').html(selectedOption.text());
        }
        
        // Update shipping address
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
    }
  });
</script>
@endsection