@extends('admin.layouts.admin')

@section('content')

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-6">
                <a href="{{ url()->previous() }}">
                    <button type="button" class="btn btn-secondary my-3"> <i class="fas fa-arrow-left"></i> Go back</button>
                </a>
                <a href="{{ route('invoice.create', ['work_id' => $work->id]) }}" class="btn btn-success">Create new invoice</a>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Job Id : {{$work->id}}</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Booking ID</th>
                  <th>Image</th>
                  <th>Date</th>
                  <th>Amount</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($work->invoices as $key => $data)
                  <tr>
                    <td>{{$data->serviceBooking->id}}</td>
                    <td>
                        @if ($data->img)
                            <p><a href="{{ asset($data->img) }}" target="_blank">View Invoice</a></p>
                        @else
                            <p>No file found</p>
                        @endif    
                    </td>
                    <td>{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y') }}</td>
                    <td>{{$data->amount}}</td>
                    <td>
                      <form action="{{ route('invoices.destroy', $data->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn mt-3 btn-danger">Delete Invoice</button>
                    </form>


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
    "responsive": true, "lengthChange": false, "autoWidth": false,
    "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
    "paging": true,
    "lengthChange": true,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": true,
    });
});
</script>
@endsection
