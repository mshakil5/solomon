@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row pt-3">
            <div class="col-12">
              <a href="{{ url()->previous() }}" class="btn btn-secondary">
                  <i class="fas fa-arrow-left"></i> Back
              </a>
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Address Details for User - {{ $userName }}</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                      <table class="table table-bordered table-striped" id="example1"> 
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>First Name</th>
                                <th>Phone</th>
                                <th>District</th>
                                <th>Address Line 1</th>
                                <th>Address Line 2</th>
                                <th>Address Line 3</th>
                                <th>Town/City</th>
                                <th>Post Code</th>
                                <th>Floor</th>
                                <th>Apartment</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($address as $key => $address)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $address['name'] ?? 'N/A' }}</td>
                                <td>{{ $address['first_name'] ?? 'N/A' }}</td>
                                <td>{{ $address['phone'] ?? 'N/A' }}</td>
                                <td>{{ $address['district'] ?? 'N/A' }}</td>
                                <td>{{ $address['first_line'] ?? 'N/A' }}</td>
                                <td>{{ $address['second_line'] ?? 'N/A' }}</td>
                                <td>{{ $address['third_line'] ?? 'N/A' }}</td>
                                <td>{{ $address['town'] ?? 'N/A' }}</td>
                                <td>{{ $address['post_code'] ?? 'N/A' }}</td>
                                <td>{{ $address['floor'] ?? 'N/A' }}</td>
                                <td>{{ $address['apartment'] ?? 'N/A' }}</td>
                                <td>{{ $address['type'] ?? 'N/A' }}</td>
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
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print"],
            "paging": false,
            "searching": false,
            "ordering": false,
            "info": false
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>
@endsection