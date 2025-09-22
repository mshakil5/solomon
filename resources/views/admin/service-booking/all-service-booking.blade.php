@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title"><b>Service Booking List</b></h3>
            </div>
            <div class="card-body">
                
              <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>Status</th>
                    <th>Booking Date</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Type</th>
                    {{-- <th>
                      Service Fee
                    </th> --}}
                    <th>Client</th>
                    <th>Service</th>
                    {{-- <th>Billing Address</th> --}}
                    {{-- <th>Delivery Address</th> --}}
                    <th>Description</th>
                    <th>Transaction</th>
                    <th>Details</th>
                    {{-- <th>Action</th> --}}
                  </tr>
                  </thead>
                  <tbody>
                    @foreach ($bookings as $key => $data)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>
                        <div class="btn-group">
                          <button type="button" class="btn btn-secondary">
                            <span id="stsval{{$data->id}}">
                              @if ($data->status == 1) Placed
                              @elseif($data->status == 2) Confirmed
                              @elseif($data->status == 3) Completed
                              @elseif($data->status == 4) Cancelled
                              @endif
                            </span>
                          </button>
                          <button type="button" class="btn btn-secondary dropdown-toggle dropdown-hover dropdown-icon" data-toggle="dropdown">
                            <span class="sr-only">Toggle Dropdown</span>
                          </button>
                          <div class="dropdown-menu" role="menu">
                            <a class="dropdown-item stsBtn" style="cursor: pointer;" data-id="{{$data->id}}" value="1" >Placed</a>
                            <a class="dropdown-item stsBtn" style="cursor: pointer;" data-id="{{$data->id}}" value="2">Confirmed</a>
                            <a class="dropdown-item stsBtn" style="cursor: pointer;" data-id="{{$data->id}}" value="3">Completed</a>
                            <a class="dropdown-item stsBtn" style="cursor: pointer;" data-id="{{$data->id}}" value="4">Cancelled</a>
                          </div>
                        </div>


                        {{-- @if (!$data->workAssign)
                          <button class="btn btn-secondary assign-staff mt-2" 
                                  data-booking-id="{{ $data->id }}" 
                                  data-toggle="modal" 
                                  data-target="#assignStaffModal">
                              Assign Staff
                          </button>
                        @else
                          <p class="mt-2 text-success">
                            Assigned to: {{ $data->workAssign->staff->name ?? '' }}
                          </p>
                              @php
                                $totalDurationInSeconds = 0;

                                  foreach ($data->workTimes as $workTime) {
                                      if ($workTime->start_time && $workTime->end_time && !$workTime->is_break) {
                                          $totalDurationInSeconds += $workTime->duration;
                                      }
                                  }

                                  $hours = floor($totalDurationInSeconds / 3600);
                                  $minutes = floor(($totalDurationInSeconds % 3600) / 60);
                                  $seconds = $totalDurationInSeconds % 60;
                              @endphp

                                <span>{{ $hours }}h {{ $minutes }}m {{ $seconds }}s</span>

                        @endif --}}

                      </td>
                      <td>{{ date('d/m/Y', strtotime($data->created_at)) }}</td>    
                      <td>{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y') }}</td>
                      <td>{{ $data->time }}</td>
                      <td>
                          <span class="badge 
                              @if($data->type == 1) bg-danger
                              @elseif($data->type == 2) bg-warning
                              @elseif($data->type == 3) bg-info
                              @else bg-success @endif">
                              @if($data->type == 1) Emergency
                              @elseif($data->type == 2) Prioritized
                              @elseif($data->type == 3) Outside Hours
                              @else Standard @endif
                          </span>
                      </td>
                      {{-- <td>
                          {{ $data->service_fee }}
                      </td> --}}
                    
                      <td style="text-align: left">
                          {{$data->user->name}} </br> <br>
                          {{$data->user->surname}} </br> <br>
                          {{$data->user->email}} </br> <br>
                          {{$data->user->phone}}
                      </td>
                      <td>
                          {{$data->service->title_english}} ( {{$data->service->title_romanian}} )
                      </td>
                      {{-- <td>
                          {{$data->billingAddress->name ?? ''}} </br>
                          {{$data->billingAddress->first_name ?? ''}}</br>
                          {{$data->billingAddress->phone ?? ''}}</br>
                          {{$data->billingAddress->district ?? ''}}</br>
                          {{$data->billingAddress->first_line ?? ''}}</br>
                          {{$data->billingAddress->second_line ?? ''}}</br>
                          {{$data->billingAddress->third_line ?? ''}}</br>
                          {{$data->billingAddress->town ?? ''}}</br>
                          {{$data->billingAddress->post_code ?? ''}}</br>
                          {{$data->billingAddress->floor ?? ''}}</br>
                          {{$data->billingAddress->apartment ?? ''}}</br>
                      </td> --}}

                      {{-- <td>
                          {{$data->billingAddress->name ?? ''}} </br>
                          {{$data->billingAddress->first_name ?? ''}}</br>
                          {{$data->billingAddress->phone ?? ''}}</br>
                          {{$data->billingAddress->district ?? ''}}</br>
                          {{$data->billingAddress->first_line ?? ''}}</br>
                          {{$data->billingAddress->second_line ?? ''}}</br>
                          {{$data->billingAddress->third_line ?? ''}}</br>
                          {{$data->billingAddress->town ?? ''}}</br>
                          {{$data->billingAddress->post_code ?? ''}}</br>
                          {{$data->billingAddress->floor ?? ''}}</br>
                          {{$data->billingAddress->apartment ?? ''}}</br>
                      </td> --}}
                      <td>
                          {!! $data->description !!}
                      </td>

                      <td>
                          <a href="{{ route('admin.booking.transactions', $data->id) }}" class="btn btn-secondary">
                              <i class="fas fa-money"></i>
                          </a>
                      </td>

                      
                     
                      <td>
                            <a href="{{ route('admin.booking.details', $data->id) }}" class="btn btn-secondary">
                                <i class="fas fa-eye"></i>
                            </a>
                      </td>
                      {{-- <td>
                        @if ($data->status == 3)
                          @php $invoiceCount = $data->invoices->count(); @endphp
                          <a href="{{ route('admin.booking.invoices', $data->id) }}" class="btn btn-primary btn-sm" title="Invoice">
                            <i class="fas fa-file-invoice"></i>
                            @if($invoiceCount > 0)
                              <span class="badge badge-light">{{ $invoiceCount }}</span>
                            @endif
                          </a>
                        @endif
                        <button class="btn btn-info btn-sm set-price-btn" 
                                data-id="{{ $data->id }}" 
                                data-service-fee="{{ $data->service_fee }}" 
                                title="Set Price">
                          <i class="fas fa-pound-sign"></i>
                        </button>
                      </td> --}}
  
                    </tr>
                    @endforeach
                  
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>

<div class="modal fade" id="assignStaffModal" tabindex="-1" role="dialog" aria-labelledby="assignStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignStaffModalLabel">Assign Staff</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="assignStaffForm">
                    <input type="hidden" id="bookingId" name="service_booking_id">
                    <div class="form-group">
                        <label for="staffSelect">Staff<span class="text-danger">*</span></label>
                        <select class="form-control" id="staffSelect" name="staff_id" required>
                            <option value="" disabled selected>Select Staff</option>
                            @foreach ($staffs as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }} {{ $staff->surname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                    <div class="form-group col-md-6">
                        <label for="startDate">Start Date<span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="startDate" name="start_date" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="endDate">End Date<span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="endDate" name="end_date" required>
                    </div>
                    </div>
                    <div class="row">
                    <div class="form-group col-md-6">
                        <label for="startTime">Start Time</label>
                        <input type="time" class="form-control" id="startTime" name="start_time">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="endTime">End Time</label>
                        <input type="time" class="form-control" id="endTime" name="end_time">
                    </div>
                    </div>
                    <div class="form-group">
                      <label for="note">Note</label>
                      <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitAssignStaff">Assign</button>
                <div id='loading' style='display:none ;'>
                    <img src="{{ asset('loader.gif') }}" id="loading-image" alt="Loading..." />
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="setPriceModal" tabindex="-1" aria-labelledby="setPriceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="setPriceForm">
      @csrf
      <input type="hidden" id="booking_id" name="booking_id" />
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="setPriceModalLabel">Set Service Fee</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="service_fee">Service Fee</label>
            <input type="number" step="0.01" class="form-control" id="service_fee" name="service_fee" placeholder="Enter service fee" />
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<style>
    #loading {
      position: fixed;
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      opacity: 0.7;
      background-color: #fff;
      z-index: 99;
    }

    #loading-image {
        z-index: 100;
    }
</style>

@endsection

@section('script')

<script>
  $(document).ready(function () {
    $('#example1').DataTable({
      order: [],
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $('.stsBtn').click(function() {
      var url = "{{URL::to('/admin/change-booking-status')}}";
      var id = $(this).data('id');
      var status = $(this).attr('value');
      console.log(status);
      $.ajax({
        type: "GET",
        dataType: "json",
        url: url,
        data: {'status': status, 'id': id},
        success: function(d){
          if (d.status == 303) {
            alert(d.message);
          } else if(d.status == 300) {
            alert('Status Changed Successfully');
              setTimeout(function() {
                window.location.reload();
            }, 100);
          }
        },
        error: function (d) {
          console.log(d);
        }
      });
    });
  });

  $(document).on('click', '.set-price-btn', function() {
    const bookingId = $(this).data('id');
    const serviceFee = $(this).data('service-fee') || '';

    $('#booking_id').val(bookingId);
    $('#service_fee').val(serviceFee);
    $('#setPriceModal').modal('show');
  });

  $('#setPriceForm').submit(function(e) {
    e.preventDefault();
    const bookingId = $('#booking_id').val();
    const serviceFee = $('#service_fee').val();
    const url = "{{ route('admin.booking.setPrice') }}"; // Create this route

    $.ajax({
      url: url,
      method: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        booking_id: bookingId,
        service_fee: serviceFee
      },
      success: function(res) {
        alert('Service fee updated');
        $('#setPriceModal').modal('hide');
        location.reload();
      },
      error: function(err) {
        alert('Error updating service fee');
      }
    });
  });

  $('.assign-staff').on('click', function() {
      var bookingId = $(this).data('booking-id');

      $('#bookingId').val(bookingId);

      $('#assignStaffModal').modal('show');
  });

  $('#submitAssignStaff').on('click', function() {
      $('#loading').show();
      var formData = {
          booking_id: $('#bookingId').val(),
          staff_id: $('#staffSelect').val(),
          start_date: $('#startDate').val(),
          end_date: $('#endDate').val(),
          start_time: $('#startTime').val(),
          end_time: $('#endTime').val(),
          note: $('#note').val(),
          _token: '{{ csrf_token() }}'
      };

      var requiredFields = [];
      if (!formData.staff_id) {
          requiredFields.push('Staff');
      }
      if (!formData.start_date) {
          requiredFields.push('Start Date');
      }
      if (!formData.end_date) {
          requiredFields.push('End Date');
      }

      if (requiredFields.length > 0) {
          $('#loading').hide();
          alert('Please fill in the following required fields: ' + requiredFields.join(', '));
          return;
      }

      $.ajax({
          url: '/admin/assign-staff',
          type: 'POST',
          data: formData,
          success: function(response) {
              $('#loading').hide();
              alert('Assigned Successfully');
              $('#assignStaffModal').modal('hide');
              setTimeout(function() {
                  window.location.reload();
              }, 100);
          },
          error: function(xhr, status, error) {
            alert(xhr.responseText);
              $('#loading').hide();
              console.error(xhr.responseText);
          }
      });
  });
</script>

@endsection