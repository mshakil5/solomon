@extends('layouts.master')

@section('content')
<div class="container my-5">
  <div class="row justify-content-center">
      <div class="col-lg-10 col-md-12 col-sm-12">
          <div class="card">
              <div class="card-header text-center bg-primary text-white">
                  <h2>Edit Your Booking for {{ $booking->service->title_english }}</h2>
              </div>
              
              <div class="card-body">
                  <form id="bookingForm" action="{{ route('service.booking.update', $booking->id) }}" method="post" role="form" enctype="multipart/form-data">
                      @csrf
                      @method('PUT')
                      
                      <div class="row mb-4">
                          <div class="col-md-4">
                              <img src="{{ asset('images/service/' . $booking->service->image) }}" 
                                   alt="{{ $booking->service->title_english }}" 
                                   class="img-fluid rounded" 
                                   style="max-height: 200px; object-fit: cover;">
                          </div>
                          <div class="col-md-8">
                              <h3 class="text-primary">{{ $booking->service->title_english }}</h3>
                              <p class="lead">{!! $booking->service->des_english !!}</p>
                              <h4 class="text-success d-none">Price: {{ number_format($booking->service->price, 2) }}RON</h4>
                          </div>
                      </div>
                      
                      <div class="form-group">
                          <label for="job_description">Job Description</label>
                          <textarea class="form-control" id="job_description" name="description" rows="4">{{ old('description', $booking->description) }}</textarea>
                      </div>
                      
                      <div class="form-group mt-4">
                          <label>Current Files</label>
                          <div class="d-flex flex-wrap gap-2 mb-3">
                              @foreach($booking->files as $file)
                              <div class="border p-2 rounded position-relative" style="width: 120px;">
                                  @if(Str::startsWith($file->fileType, 'image'))
                                    <a href="{{ asset('images/service/' . $file->file) }}" target="_blank">
                                        <img src="{{ asset('images/service/' . $file->file) }}" class="img-thumbnail" style="height: 100px; object-fit: cover;">
                                    </a>
                                  @else
                                    <a href="{{ asset('images/service/' . $file->file) }}" target="_blank">
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 100px;">
                                            <i class="fas fa-file-alt fa-3x text-secondary"></i>
                                        </div>
                                    </a>
                                  @endif
                                  <small class="d-block text-truncate">{{ $file->file }}</small>
                                  <div class="form-check position-absolute top-0 end-0 m-1">
                                      <input class="form-check-input" type="checkbox" name="remove_files[]" value="{{ $file->id }}" id="remove_file_{{ $file->id }}">
                                      <label class="form-check-label" for="remove_file_{{ $file->id }}" title="Remove file">
                                          <i class="fas fa-times text-danger"></i>
                                      </label>
                                  </div>
                              </div>
                              @endforeach
                          </div>
                          
                          <label>Upload New Files (Images, Videos, PDFs) - Max 10MB each</label>
                          <input type="file" class="filepond" name="files[]" multiple data-max-file-size="10MB">
                      </div>
                      
                      <div class="row mt-4">
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="date">Date <span class="text-danger">*</span></label>
                                  <input type="date" name="date" id="date" class="form-control" 
                                         min="{{ date('Y-m-d') }}" 
                                         value="{{ old('date', $booking->date) }}" required>
                              </div>
                          </div>
                          @if($booking->type != 1)
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="time">Time <span class="text-danger">*</span></label>
                                  <input type="time" name="time" id="time" class="form-control" 
                                         value="{{ old('time', \Carbon\Carbon::parse($booking->time)->format('H:i')) }}" required>
                              </div>
                          </div>
                          @endif
                      </div>
                      
                      <div class="row mt-4">
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
                                                      <option value="{{ $address->id }}" 
                                                          {{ $booking->billing_address_id == $address->id ? 'selected' : '' }}
                                                          {{ $address->primary_billing ? 'style="background-color: #d3f9d8;"' : '' }}>
                                                          {{ $address->name }}, {{ $address->first_name }}, {{ $address->phone }}
                                                      </option>
                                                  @endforeach
                                              </select>
                                          </div>
                                      @else
                                          <p class="text-muted">No saved billing addresses found.</p>
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
                                                      <option value="{{ $address->id }}" 
                                                          {{ $booking->shipping_address_id == $address->id ? 'selected' : '' }}
                                                          {{ $address->primary_shipping ? 'style="background-color: #d3f9d8;"' : '' }}>
                                                          {{ $address->name }}, {{ $address->first_name }}, {{ $address->phone }}
                                                      </option>
                                                  @endforeach
                                              </select>
                                          </div>
                                      @else
                                          <p class="text-muted">No saved shipping addresses found.</p>
                                      @endif
                                  </div>
                              </div>
                          </div>
                      </div>
                      
                      <div class="row mt-4">
                          <div class="col-6">
                              <a href="{{ route('user.service.bookings') }}" class="btn btn-secondary">Cancel</a>
                          </div>
                          <div class="col-6 text-end">
                              <button type="submit" class="btn btn-primary">Update Booking</button>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
</div>

<script>
  document.getElementById('bookingForm').addEventListener('submit', function(e) {
    const confirmUpdate = confirm('Warning: Updating your booking may change your work priority based on our system. Do you want to proceed?');
    if (!confirmUpdate) {
      e.preventDefault();
    }
  });
</script>

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
@endsection