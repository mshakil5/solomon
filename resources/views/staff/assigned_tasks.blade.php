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
                    <th>Job ID</th>
                    <th>Date</th>
                    <th>Client Details</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Timer</th>
                    <th>Details</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach ($data as $key => $data)

                    <tr>
                        <td>{{ $data->work->orderid }}</td>
                        <td>{{ \Carbon\Carbon::parse($data->work->date)->format('d/m/Y') }}</td>
                        
                        <td style="text-align: left">
                            {{ $data->work->name }} <br><br>
                            {{ $data->work->email }} <br><br>
                            {{ $data->work->phone }}
                        </td>

                        <td style="text-align: left">
                            {{ $data->work->address_first_line }} <br>
                            {{ $data->work->address_second_line }} <br>
                            {{ $data->work->address_third_line }} <br>
                            {{ $data->work->town }} <br>
                            {{ $data->work->post_code }}
                        </td>

                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-secondary" 
                                        @if (!$data->work->workTimes->isEmpty()) data-toggle="dropdown" @else disabled @endif>
                                    <span id="stsval{{$data->id}}">
                                        @if ($data->work->status == 1) New
                                        @elseif($data->work->status == 2) In progress
                                        @elseif($data->work->status == 3) Completed
                                        @elseif($data->work->status == 4) Cancelled
                                        @endif
                                    </span>
                                </button>
                                
                                @if (!$data->work->workTimes->isEmpty())
                                <button type="button" class="btn btn-secondary dropdown-toggle dropdown-hover dropdown-icon" data-toggle="dropdown">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item stsBtn" style="cursor: pointer;" data-id="{{ $data->work->id }}" value="2">In Progress</a>
                                    <a class="dropdown-item stsBtn" style="cursor: pointer;" data-id="{{ $data->work->id }}" value="3">Completed</a>
                                </div>
                                @endif
                            </div>
                        </td>

                        <td>
                            @php
                                $workTimes = $data->work->workTimes;
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

                            @if ($data->work->status == 2)
                                @if ($hasActiveWorkTime)
                                    <button type="button" class="btn btn-secondary stop-button" data-worktime-id="{{ $workTimeId }}" data-work-id="{{ $data->work->id }}">
                                        Stop
                                    </button>
                                @else
                                    <button type="button" class="btn btn-secondary start-button" data-work-id="{{ $data->work->id }}">
                                        Start
                                    </button>
                                @endif
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('staff.work.details', $data->work->id) }}" class="btn btn-secondary">
                                <i class="fas fa-eye"></i>
                            </a>
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
                          text: "Status changed successfully. Do you want to upload an image?",
                          icon: "success",
                          buttons: {
                              cancel: "No",
                              confirm: "Yes"
                          },
                      }).then((willUpload) => {
                          if (willUpload) {
                              window.location.href = "{{ url('/staff/upload/') }}" + '/' + d.id;
                          } else {
                              window.setTimeout(function() { location.reload() }, 2000);
                          }
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