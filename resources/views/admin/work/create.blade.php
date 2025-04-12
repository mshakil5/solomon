@extends('admin.layouts.admin')

@section('content')

<div id='loading' style='display:none ;'>
    <img src="{{ asset('loader.gif') }}" id="loading-image" alt="Loading..." />
</div>


    <!-- Main content -->
    <section class="content pt-3" id="addThisFormContainer">
      <div class="container-fluid">
        <div class="row justify-content-md-center">
          <!-- right column -->
          <div class="col-md-10">
            <!-- general form elements disabled -->
            <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Add new job</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                
                @if ($success = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ $success }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <form id="createThisForm"  action="{{route('job.store')}}" method="post" role="form" enctype="multipart/form-data" class="d-none">
                  @csrf

                <div class="row">
                  <div class="col-sm-6">
                      <div class="form-group">
                          <label for="service_id">Select Service <span class="text-danger">*</span></label>
                          <select name="service_id" id="service_id" class="form-control select2">
                              <option value="">Select Category</option>
                              @foreach ($services as $service)
                                  <option value="{{$service->id}}">{{$service->title_english}}</option>
                              @endforeach
                          </select>
                      </div>
                  </div>
                  <div class="col-sm-6">
                      <div class="form-group">
                          <label for="user_id">Select User <span class="text-danger">*</span></label>
                          <select name="user_id" id="user_id" class="form-control select2">
                              <option value="">Select User</option>
                              @foreach ($users as $user)
                                  <option value="{{$user->id}}">{{$user->name}} {{$user->surname}}</option>
                              @endforeach
                          </select>
                      </div>
                  </div>
                  <div class="col-sm-6">
                      <div class="form-group">
                          <label for="user_id">Select Address <span class="text-danger">*</span></label>
                          <select name="user_id" id="user_id" class="form-control select2">
                              <option value="">Select Address</option>
                              @foreach ($users as $user)
                                  <option value="{{$user->id}}">{{$user->name}} {{$user->surname}}</option>
                              @endforeach
                          </select>
                      </div>
                  </div>
                </div>

                <div id="imageContainer">
                    <div class="row image-row" style="margin-top: 10px;">
                        <div class="col-lg-6 col-12">
                            <label for="">Image/Video <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <input type="file" class="form-control image-upload" name="images[]" accept="image/*,video/*" required>
                            </div>
                        </div>
                        <div class="col-lg-5 col-8">
                            <label for="">Description <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <textarea class="form-control description resizable" placeholder="Description" rows="3" name="descriptions[]" required></textarea>
                            </div>
                        </div>
                        <div class="col-lg-1 col-2 text-end">
                            <label for="add-row" class="form-label" style="visibility: hidden;">Action</label>
                            <button class="btn btn-success add-row" id="add-row" type="button">+</button>
                        </div>
                    </div>
                </div>


                <!-- /.card-body -->
                <div class="card-footer">
                    <button type="submit" class="btn btn-secondary">Create</button>
                </div>
                <!-- /.card-footer -->


                  
                </form>
              </div>

              
              
              <!-- /.card-body -->
            </div>
          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->


@endsection
@section('script')
<script>
$(function () {
    $("#example1").DataTable({
    "responsive": true, "lengthChange": false, "autoWidth": false,
    "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": true,
    });
});
</script>

<script>
  $(document).ready(function() {
    $('.select2').select2();
  });
</script>
  

<!-- Loader start-->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.custom-form');
        const loadingDiv = document.getElementById('loading');

        form.addEventListener('submit', function() {
            loadingDiv.style.display = 'flex';
        });
    });
</script>
<!-- Loader end-->

<script>
    $(document).ready(function(){
        function addNewRow() {
            var newRow = `
                <div class="row image-row" style="margin-top: 10px;">
                    <div class="col-lg-6 col-12">
                        <div class="input-group mb-3">
                            <input type="file" class="form-control image-upload" name="images[]" accept="image/*,video/*" required>
                        </div>
                    </div>
                    <div class="col-lg-5 col-12">
                        <div class="input-group mb-3">
                            <textarea class="form-control description resizable" placeholder="Description" rows="3" name="descriptions[]" required></textarea>
                        </div>
                    </div>
                    <div class="col-lg-1 col-12 text-end">
                        <button class="btn btn-danger remove-row" type="button">-</button>
                    </div>
                </div>
            `;
            $('#imageContainer').append(newRow);
            $('#imageContainer').children('.row').last().find('.add-row').removeClass('btn-success add-row').addClass('btn-danger remove-row').html('-');
        }

        $(document).on('click', '.add-row', function(){
            addNewRow();
        });

        $(document).on('click', '.remove-row', function(){
            $(this).closest('.row').remove();
        });

        $('#submitBtn').click(function(){
            @guest
                toastr.error('Please login first to submit the form.', 'Error');
                return false;
            @endguest
        });
    });
</script>


@endsection