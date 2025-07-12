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
            <div class="card-header bg-success text-white">
              <h3 class="card-title"><b>Completed Tasks</b></h3>
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
                                    $totalDurationInSeconds = 0;

                                    foreach ($data->serviceBooking->workTimes as $workTime) {
                                        if ($workTime->start_time && $workTime->end_time && !$workTime->is_break) {
                                            $totalDurationInSeconds += $workTime->duration;
                                        }
                                    }

                                    $hours = floor($totalDurationInSeconds / 3600);
                                    $minutes = floor(($totalDurationInSeconds % 3600) / 60);
                                    $seconds = $totalDurationInSeconds % 60;
                                @endphp

                                <span>{{ $hours }}h {{ $minutes }}m {{ $seconds }}s</span>
                            </td>
                            
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
      var url = "{{URL::to('/staff/change-work-status')}}";
      var id = $(this).data('id');
      var status = $(this).attr('value');
      $.ajax({
        type: "GET",
        dataType: "json",
        url: url,
        data: {'status': status, 'id': id},
        success: function(d){
          if (d.status == 303) {
            alert(d.message);
          } else if(d.status == 300) {
              swal({
                  title: "Success!",
                  text: "Status chnaged successfully",
                  icon: "success",
                  button: "OK",
              });
            window.setTimeout(function(){location.reload()},2000);
          }
        },
        error: function (d) {
          console.log(d);
        }
      });
    });
  });

  $(document).ready(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });  
  });
</script>

@endsection