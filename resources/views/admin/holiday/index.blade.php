@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
      <div class="row">
        <div class="col-2">
            <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
        </div>
      </div>
    </div>
</section>
<!-- /.content -->


<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-8">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add new holiday</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Title</label>
                                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter holiday title" required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Select Holiday Date</label>
                                        <input type="text" class="form-control" id="holiday_date" name="holiday_date" placeholder="e.g. March 21" required>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                        <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Holidays</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Title</th>
                                    <th>Date</th>
                                    {{-- <th>Status</th> --}}
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $holiday)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $holiday->title }}</td>
                                    <td>{{ $holiday->month }} {{ $holiday->day }}</td>
                                    {{-- <td>
                                      <div class="custom-control custom-switch">
                                          <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus{{ $holiday->id }}" data-id="{{ $holiday->id }}" {{ $holiday->status == 1 ? 'checked' : '' }}>
                                          <label class="custom-control-label" for="customSwitchStatus{{ $holiday->id }}"></label>
                                      </div>
                                  </td> --}}
                                    <td>
                                        <a id="EditBtn" rid="{{ $holiday->id }}">
                                            <i class="fa fa-edit" style="color: #2196f3; font-size:16px;"></i>
                                        </a>
                                        <a id="deleteBtn" rid="{{ $holiday->id }}">
                                            <i class="fa fa-trash-o" style="color: red; font-size:16px;"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('script')


<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    $(function () {
      $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

<script>

flatpickr("#holiday_date", {
    dateFormat: "F j",
    defaultDate: null,
    allowInput: true,
    onReady: function(selectedDates, dateStr, instance) {
        instance.calendarContainer.querySelector(".flatpickr-monthDropdown-months").nextSibling.style.display = "none";
    }
});

  $(document).ready(function() {
      $('.toggle-status').change(function() {
          var holiday_id = $(this).data('id');
          var status = $(this).prop('checked') ? 1 : 0;

          $.ajax({
              url: '/admin/holiday-status',
              method: "POST",
              data: {
                    title: $("#title").val(),
                    holiday_date: $("#holiday_date").val()
                },
              success: function(d) {
                  swal({
                      text: "Status changed successfully",
                      icon: "success",
                      button: {
                          text: "OK",
                          className: "swal-button--confirm"
                      }
                  });
              },
              error: function(xhr, status, error) {
                  console.error(xhr.responseText);
              }
          });
      });
  });
</script>

<script>
  $(document).ready(function () {
      $("#addThisFormContainer").hide();
      $("#newBtn").click(function(){
          clearform();
          $("#newBtn").hide(100);
          $("#addThisFormContainer").show(300);

      });
      $("#FormCloseBtn").click(function(){
          $("#addThisFormContainer").hide(200);
          $("#newBtn").show(100);
          clearform();
      });

      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
      
      var url = "{{URL::to('/admin/holiday')}}";
      var upurl = "{{URL::to('/admin/holiday-update')}}";

      $("#addBtn").click(function(){
          if($(this).val() == 'Create') {
            
            if ($("#title").val() == '' || $("#holiday_date").val() == '') {
              alert("Please fill all fields");
              return false;
            }
              $.ajax({
                  url: url,
                  method: "POST",
                  data: {
                      title: $("#title").val(),
                      holiday_date: $("#holiday_date").val()
                  },
                  success: function (d) {
                      if (d.status == 303) {
                          $(".ermsg").html(d.message);
                      }else if(d.status == 300){
                          swal({
                              text: "Created Successfully",
                              icon: "success",
                              button: {
                                  text: "OK",
                                  className: "swal-button--confirm"
                              }
                          }).then(() => {
                              location.reload();
                          });
                      }
                  },
                  error: function(xhr, status, error) {
                     console.error(xhr.responseText);
                  }
              });
          }

          if($(this).val() == 'Update'){

            if ($("#title").val() == '' || $("#holiday_date").val() == '') {
              alert("Please fill all fields");
              return false;
            }

              $.ajax({
                  url:upurl,
                  type: "POST",
                  dataType: 'json',
                  data: {
                      title: $("#title").val(),
                      holiday_date: $("#holiday_date").val(),
                      codeid: $("#codeid").val()
                  },
                  success: function(d){
                      if (d.status == 303) {
                          $(".ermsg").html(d.message);
                          pagetop();
                      }else if(d.status == 300){
                          swal({
                            text: "Updated Successfully",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        }).then(() => {
                            location.reload();
                        });
                      }
                  },
                  error: function(xhr, status, error) {
                     console.error(xhr.responseText);
                  }
              });
          }
      });

      //Edit
      $("#contentContainer").on('click','#EditBtn', function(){
          $("#cardTitle").text('Update holiday');
          codeid = $(this).attr('rid');
          info_url = url + '/'+codeid+'/edit';
          $.get(info_url,{},function(d){
              populateForm(d);
              pagetop();
          });
      });

      //Delete
      $("#contentContainer").on('click','#deleteBtn', function(){
            if(!confirm('Sure?')) return;
            codeid = $(this).attr('rid');
            info_url = url + '/'+codeid;
            $.ajax({
                url:info_url,
                method: "GET",
                type: "DELETE",
                data:{
                },
                success: function(d){
                    if(d.success) {
                        swal({
                          text: "Deleted",
                          icon: "success",
                          button: {
                              text: "OK",
                              className: "swal-button--confirm"
                          }
                      }).then(() => {
                          location.reload();
                      });
                    }
                },
                error:function(d){
                    console.log(d);
                }
            });
        });

      function populateForm(data){
          $("#title").val(data.title);
          $("#holiday_date").val(data.month + ' ' + data.day);
          $("#codeid").val(data.id);
          $("#addBtn").val('Update');
          $("#addBtn").html('Update');
          $("#addThisFormContainer").show(300);
          $("#newBtn").hide(100);
      }
      function clearform(){
          $('#createThisForm')[0].reset();
          $("#addBtn").val('Create');
          $("#addBtn").html('Create');
          $("#cardTitle").text('Add new holiday');
      }
  });
</script>

@endsection