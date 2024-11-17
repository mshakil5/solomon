@extends('admin.layouts.admin')

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
            <div class="card-header">
              <h3 class="card-title"><b>Work List</b></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                
              <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Job ID</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Client Details</th>
                    <th>Address</th>
                    <th>Assign</th>
                    <th>Status</th>
                    <th>Details</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach ($data as $key => $data)
                    <tr>
                      <td>{{ $data->orderid }}</td>    
                      <td>{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y') }}</td>
                      <td>{{ $data->category->name }}</td>
                      <td style="text-align: left">
                          {{$data->name}} </br> <br>
                          {{$data->email}} </br> <br>
                          {{$data->phone}}
                      </td>
                      <td>
                          {{$data->address_first_line}} </br>
                          {{$data->address_second_line}}</br>
                          {{$data->address_third_line}}</br>
                          {{$data->town}}</br>
                          {{$data->post_code}}
                      </td>
                      <td>
                          <button class="btn btn-secondary assign-staff" 
                                  data-work-id="{{ $data->id }}" 
                                  data-toggle="modal" 
                                  data-target="#assignStaffModal">
                              Assign Staff
                          </button>
                      </td>

                      <td>
                        <div class="btn-group">
                          <button type="button" class="btn btn-secondary">
                            <span id="stsval{{$data->id}}">
                            @if ($data->status == 1) New
                            @elseif($data->status == 2) In progress
                            @elseif($data->status == 3) Completed
                            @elseif($data->status == 4) Cancelled
                            @endif
                          </span>
                        </button>
                          <button type="button" class="btn btn-secondary dropdown-toggle dropdown-hover dropdown-icon" data-toggle="dropdown">
                            <span class="sr-only">Toggle Dropdown</span>
                          </button>
                          <div class="dropdown-menu" role="menu">
                            <a class="dropdown-item stsBtn" style="cursor: pointer;" data-id="{{$data->id}}" value="1" >New</a>
                            <a class="dropdown-item stsBtn" style="cursor: pointer;" data-id="{{$data->id}}" value="2">In Progress</a>
                            <a class="dropdown-item stsBtn" style="cursor: pointer;" data-id="{{$data->id}}" value="3">Completed</a>
                            <a class="dropdown-item stsBtn" style="cursor: pointer;" data-id="{{$data->id}}" value="4">Cancelled</a>
                          </div>
                        </div>
                      </td>
                     
                      <td>
                          <a href="{{ route('admin.work.details', $data->id) }}" class="btn btn-secondary">
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

<!-- Assign Staff Modal -->
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
                    <input type="hidden" id="workId" name="work_id">
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
    });


  $(function () {
    

    $('.stsBtn').click(function() {
      var url = "{{URL::to('/admin/change-work-status')}}";
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
            $("#stsval"+d.id).html(d.stsval);
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

  $(document).ready(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });  
  });
</script>

<script>
    $(document).ready(function() {
      $('.assign-staff').on('click', function() {
          var workId = $(this).data('work-id');
  
          $('#workId').val(workId);

          $('#assignStaffModal').modal('show');
      });

      $('#submitAssignStaff').on('click', function() {
          $('#loading').show();
          var formData = {
              work_id: $('#workId').val(),
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
  });
</script>


@endsection