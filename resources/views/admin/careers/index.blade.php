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
                  <th>Date</th>
                  <th>Name</th>
                  <th>Email/Phone</th>
                  <th>Address</th>
                  <th>Note</th>
                  <th>CV</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($data as $key => $data)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ date('d-m-Y', strtotime($data->created_at)) }}</td>
                    <td>{{$data->name}}</td>
                    <td>{{$data->email}} <br> {{$data->phone}}</td>
                    <td>{{$data->address_first_line}} <br> {{$data->address_second_line}} <br> {{$data->address_third_line}} <br> {{$data->town}} <br> {{$data->post_code}}</td>
                    <td>{!! $data->note !!}</td>
                    <td><a class="btn btn-secondary" href="{{ asset($data->cv) }}" target="_blank">View CV</a></td>
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

@endsection