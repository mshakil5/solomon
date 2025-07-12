@extends('layouts.staff')

@section('content')

<!-- Main content -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
      <div class="row">
        <div class="col-2">
          
        </div>
      </div>
    </div>
</section>
<!-- /.content -->


<!-- Main content -->
<section class="content mt-3" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="card card-secondary">
            <div class="card-header bg-warning text-white">
              <h3 class="card-title"><b>Due Tasks</b></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Type</th>
                    <th>Client</th>
                    <th>Service</th>
                    <th>Description</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Timer</th>
                    {{-- <th>Details</th> --}}
                  </tr>
                  </thead>
                  <tbody>
                    @foreach ($data as $key => $data)

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($data->serviceBooking->date)->format('d/m/Y') }}</td>
                        <td>{{ $data->serviceBooking->time }}</td>
                        <td>
                            <span class="badge 
                                @if( $data->serviceBooking->type == 1) bg-danger
                                @elseif( $data->serviceBooking->type == 2) bg-warning
                                @elseif( $data->serviceBooking->type == 3) bg-info
                                @else bg-success @endif">
                                @if( $data->serviceBooking->type == 1) Emergency
                                @elseif( $data->serviceBooking->type == 2) Prioritized
                                @elseif( $data->serviceBooking->type == 3) Outside Hours
                                @else Standard @endif
                            </span>
                        </td>
                        
                        <td style="text-align: left">
                            {{$data->serviceBooking->user->name}} </br> <br>
                            {{$data->serviceBooking->user->surname}} </br> <br>
                            {{$data->serviceBooking->user->email}} </br> <br>
                            {{$data->serviceBooking->user->phone}}
                        </td>
                         <td>
                            {{$data->serviceBooking->service->title_english}} ( {{$data->serviceBooking->service->title_romanian}} )
                        </td>
                        <td>
                            {!! $data->description !!}
                        </td>

                        <td style="text-align: left">
                            {{ $data->serviceBooking->shippingAddress->first_line }}<br>
                            {{ $data->serviceBooking->shippingAddress->second_line ?? '' }}<br>
                            {{ $data->serviceBooking->shippingAddress->third_line ?? '' }}<br>
                            {{ $data->serviceBooking->shippingAddress->town }}<br>
                            {{ $data->serviceBooking->shippingAddress->post_code }}<br>
                            @if($data->serviceBooking->shippingAddress->floor) Floor: {{ $data->serviceBooking->shippingAddress->floor }}<br>@endif
                            @if($data->serviceBooking->shippingAddress->apartment) Apartment: {{ $data->serviceBooking->shippingAddress->apartment }}@endif
                        </td>

                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-secondary" 
                                        @if (!$data->serviceBooking->workTimes->isEmpty()) data-toggle="dropdown" @else disabled @endif>
                                    <span id="stsval{{$data->id}}">
                                        @if ($data->serviceBooking->status == 1) Placed
                                        @elseif($data->serviceBooking->status == 2) Confirmed
                                        @elseif($data->serviceBooking->status == 3) Completed
                                        @elseif($data->serviceBooking->status == 4) Cancelled
                                        @endif
                                    </span>
                                </button>
                                
                                @if (!$data->serviceBooking->workTimes->isEmpty())
                                <button type="button" class="btn btn-secondary dropdown-toggle dropdown-hover dropdown-icon" data-toggle="dropdown">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item stsBtn" style="cursor: pointer;" data-id="{{ $data->serviceBooking->id }}" value="2">In Progress</a>
                                    <a class="dropdown-item stsBtn" style="cursor: pointer;" data-id="{{ $data->serviceBooking->id }}" value="3">Completed</a>
                                </div>
                                @endif
                            </div>
                        </td>

                        <td>
                            @php
                                $workTimes = $data->serviceBooking->workTimes;
                                $hasActiveWorkTime = false;
                                $workTimeId = null;
                                $hasEndTime = false;

                                foreach ($workTimes as $workTime) {
                                    if ($workTime->start_time && !$workTime->end_time && !$workTime->is_break) {
                                        $hasActiveWorkTime = true;
                                        $workTimeId = $workTime->id;
                                        break;
                                    }
                                    if ($workTime->end_time && !$workTime->is_break) {
                                        $hasEndTime = true;
                                    }
                                }
                            @endphp

                            @if ($data->serviceBooking->status == 2)
                                @if ($hasActiveWorkTime)
                                    <button type="button" class="btn btn-secondary stop-button" data-worktime-id="{{ $workTimeId }}" data-work-id="{{ $data->serviceBooking->id }}">
                                        Stop
                                    </button>
                                @else
                                    <button type="button" class="btn btn-secondary start-button" data-work-id="{{ $data->serviceBooking->id }}">
                                        Start
                                    </button>
                                @endif
                            @endif
                        </td>

                        {{-- <td>
                            <a href="{{ route('staff.work.details', $data->serviceBooking->id) }}" class="btn btn-secondary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td> --}}

                    </tr>

                    @endforeach
                 </tbody>
                </table>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->

@endsection

@section('script')

<script>
  $(function () {
    $("#example1").DataTable({
       order: [],
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $('.stsBtn').click(function() {
      var url = "{{ URL::to('/staff/change-work-status') }}";
      var id = $(this).data('id');
      var status = $(this).attr('value');

      $.ajax({
          type: "GET",
          dataType: "json",
          url: url,
          data: {'status': status, 'id': id},
          success: function(d) {
              if (d.status == 303) {
                  alert(d.message);
              } else if (d.status == 300) {
                  if (d.new_status == 3) {
                      swal({
                          title: "Success!",
                          text: "Status changed successfully",
                          icon: "success",
                          buttons: {
                              cancel: "No",
                              confirm: "Yes"
                          },
                      });
                  } else {
                      swal({
                          title: "Success!",
                          text: "Status changed successfully.",
                          icon: "success",
                          button: "OK",
                      });
                      window.setTimeout(function() { location.reload() }, 2000);
                  }
              }
          },
          error: function(xhr) {
                console.error(xhr.responseText);
            }
      });
    });
  });

  $(document).ready(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });  
  });
</script>

<!-- Timer start and stop -->
<script>
  $(document).ready(function() {
      $('.start-button').click(function() {
          var workId = $(this).data('work-id');
          // console.log(workId);

          $.ajax({
              url: '{{ route("worktime.start") }}', 
              method: 'POST',
              data: {
                  work_id: workId,
                  _token: '{{ csrf_token() }}'
              },
              success: function(response) {
                    swal({
                      title: "Success!",
                      text: "Timer started",
                      icon: "success",
                      button: "OK",
                  });
                window.setTimeout(function(){location.reload()},2000);
              },
              error: function(xhr) {
                  console.error(xhr.responseText);
              }
          });
      });
  });
</script>

<script>
  $(document).ready(function() {
      $('.stop-button').click(function() {
          var workTimeId = $(this).data('worktime-id');
          var workId = $(this).data('work-id');

          $.ajax({
              url: '{{ route("worktime.stop") }}',
              method: 'POST',
              data: {
                  work_time_id: workTimeId,
                  work_id: workId,
                  _token: '{{ csrf_token() }}'
              },
              success: function(response) {
                  swal({
                    title: "Success!",
                    text: "Timer stopped",
                    icon: "success",
                    button: "OK",
                });
                window.setTimeout(function(){location.reload()},2000);
              },
              error: function(xhr) {
                  console.error(xhr.responseText);
              }
          });
      });
  });
</script>

@endsection