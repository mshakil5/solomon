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
                  <th>Requested By</th>
                  <th>Message</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($serviceRequest as $key => $data)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $data->user->name ?? '' }} {{ $data->user->first_name ?? '' }}</td>
                    <td>{!! $data->need !!}</td>
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