@extends('admin.layouts.admin')

@section('content')


<!-- Main content -->
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="card">
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sl</th>
                  <th>Name</th>
                  <th>Email/Phone</th>
                  <th>Rating</th>
                  <th>Review</th>
                  <th>Approved</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($data as $key => $data)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{$data->name}}</td>
                    <td>{{$data->email}} <br> {{$data->phone}}</td>
                    <td>{{ $data->stars }} / 5</td>
                    <td>{!! $data->review !!}</td>
                    <td>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus{{ $data->id }}" data-id="{{ $data->id }}" {{ $data->status == 1 ? 'checked' : '' }}>
                            <label class="custom-control-label" for="customSwitchStatus{{ $data->id }}"></label>
                        </div>
                    </td>
                  </tr>
                  @endforeach
                
                </tbody>
              </table>
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
    "responsive": true, "lengthChange": false, "autoWidth": false, "pageLength": 50,
    "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
});
</script>

<script>
  $('.toggle-status').change(function() {
    var isChecked = $(this).is(':checked');
    var reviewId = $(this).data('id');

    $.ajax({
        url: '/admin/toggle-review-status',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            id: reviewId,
            status: isChecked ? 1 : 0
        },
        success: function(response) {
            swal({
                text: "Review status updated successfully!",
                icon: "success",
                button: {
                    text: "OK",
                    className: "swal-button--confirm"
                }
            });
        },
        error: function(xhr) {
            console.error(xhr.responseText);
        }
    });
});

</script>

@endsection