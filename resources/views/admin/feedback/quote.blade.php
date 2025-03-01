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
                  <th>City</th>
                  <th>Address</th>
                  <th>Quote</th>
                  <th>Image/Video</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($data as $key => $data)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{$data->name}}</td>
                    <td>{{$data->email}} <br> {{$data->phone}}</td>
                    <td>{{$data->city}}</td>
                    <td>{!!$data->address!!}</td>
                    <td>{!! $data->details !!}</td>
                    <td>
                      @if (!empty($data->file))
                          @php 
                              $fileExt = pathinfo($data->file, PATHINFO_EXTENSION);
                              $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
                              $videoExtensions = ['mp4', 'mov', 'avi', 'mkv', 'wmv', 'flv'];
                          @endphp
                  
                          @if (in_array($fileExt, $imageExtensions))
                              <a href="{{ asset($data->file) }}" target="_blank">
                                  <img src="{{ asset($data->file) }}" alt="Uploaded File" width="50">
                              </a>
                          @elseif (in_array($fileExt, $videoExtensions))
                              <a href="{{ asset($data->file) }}" target="_blank">View Video</a>
                          @else
                              <a href="{{ asset($data->file) }}" target="_blank">Download File</a>
                          @endif
                      @else
                          No file uploaded
                      @endif
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

@endsection