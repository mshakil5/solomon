@extends('admin.layouts.admin')

@section('content')
<section class="content">
  <div class="container-fluid">
    <div class="row pt-3">
      <div class="col-12">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i> Back
        </a>

        <!-- Delivery Address Table -->
        <div class="card mt-3">
          <div class="card-header">
            <h3 class="card-title">Delivery Addresses - {{ $userName }}</h3>
          </div>
          <div class="card-body">
            <table class="table table-bordered" id="deliveryTable">
              <thead>
                <tr>
                  <th>SL</th><th>Name</th><th>First Name</th><th>Phone</th><th>District</th>
                  <th>Address Line 1</th><th>Line 2</th><th>Line 3</th><th>Town</th><th>Post Code</th>
                  <th>Floor</th><th>Apartment</th><th>Type</th>
                </tr>
              </thead>
              <tbody>
                @foreach($deliveryAddresses as $key => $address)
                <tr class="{{ $address->primary_shipping ? 'table-success' : '' }}">
                  <td>{{ $key + 1 }}</td>
                  <td>{{ $address->name ?? 'N/A' }}</td>
                  <td>{{ $address->first_name ?? 'N/A' }}</td>
                  <td>{{ $address->phone ?? 'N/A' }}</td>
                  <td>{{ $address->district ?? 'N/A' }}</td>
                  <td>{{ $address->first_line ?? 'N/A' }}</td>
                  <td>{{ $address->second_line ?? 'N/A' }}</td>
                  <td>{{ $address->third_line ?? 'N/A' }}</td>
                  <td>{{ $address->town ?? 'N/A' }}</td>
                  <td>{{ $address->post_code ?? 'N/A' }}</td>
                  <td>{{ $address->floor ?? 'N/A' }}</td>
                  <td>{{ $address->apartment ?? 'N/A' }}</td>
                  <td>Delivery</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

        <!-- Billing Address Table -->
        <div class="card mt-3">
          <div class="card-header">
            <h3 class="card-title">Billing Addresses - {{ $userName }}</h3>
          </div>
          <div class="card-body">
            <table class="table table-bordered" id="billingTable">
              <thead>
                <tr>
                  <th>SL</th><th>Name</th><th>First Name</th><th>Phone</th><th>District</th>
                  <th>Address Line 1</th><th>Line 2</th><th>Line 3</th><th>Town</th><th>Post Code</th>
                  <th>Floor</th><th>Apartment</th><th>Type</th>
                </tr>
              </thead>
              <tbody>
                @foreach($billingAddresses as $key => $address)
                <tr class="{{ $address->primary_billing ? 'table-success' : '' }}">
                  <td>{{ $key + 1 }}</td>
                  <td>{{ $address->name ?? 'N/A' }}</td>
                  <td>{{ $address->first_name ?? 'N/A' }}</td>
                  <td>{{ $address->phone ?? 'N/A' }}</td>
                  <td>{{ $address->district ?? 'N/A' }}</td>
                  <td>{{ $address->first_line ?? 'N/A' }}</td>
                  <td>{{ $address->second_line ?? 'N/A' }}</td>
                  <td>{{ $address->third_line ?? 'N/A' }}</td>
                  <td>{{ $address->town ?? 'N/A' }}</td>
                  <td>{{ $address->post_code ?? 'N/A' }}</td>
                  <td>{{ $address->floor ?? 'N/A' }}</td>
                  <td>{{ $address->apartment ?? 'N/A' }}</td>
                  <td>Billing</td>
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
@endsection

@section('script')
<script>
  $(function () {
      $("#deliveryTable, #billingTable").DataTable({
      order: [], 
      pageLength: 50,
      "responsive": true, "lengthChange": true, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>
@endsection