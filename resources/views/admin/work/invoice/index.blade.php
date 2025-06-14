@extends('admin.layouts.admin')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-6">
                <a href="{{ url()->previous() }}">
                    <button type="button" class="btn btn-secondary my-3"> <i class="fas fa-arrow-left"></i> Go back</button>
                </a>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#createInvoiceModal">
                    Create new invoice
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Job Id : {{$work->id}} <br> Service Fee: {{$work->service_fee}}RON </h3> 
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Booking ID</th>
                  <th>Invoice ID</th>
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
                    <td>{{$data->invoiceid}}</td>
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
                        <button class="btn btn-primary btn-sm edit-btn" 
                                data-id="{{ $data->id }}"
                                data-date="{{ $data->date }}"
                                data-amount="{{ $data->amount }}"
                                data-img="{{ $data->img }}"
                                data-toggle="modal" 
                                data-target="#editInvoiceModal">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        
                        <form action="{{ route('invoices.destroy', $data->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this invoice?');">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
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

<!-- Create Invoice Modal -->
<div class="modal fade" id="createInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="createInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createInvoiceModalLabel">Create New Invoice</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createInvoiceForm" action="{{ route('invoices.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="service_booking_id" value="{{ $work->id }}">
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="form-group">
                        <label for="img">Invoice File</label>
                        <input type="file" class="form-control-file" id="img" name="img">
                        <small class="form-text text-muted">Allowed file types: jpeg, png, jpg, gif, pdf, docx. Max size: 5MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Invoice Modal -->
<div class="modal fade" id="editInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="editInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editInvoiceModalLabel">Edit Invoice</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editInvoiceForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="service_booking_id" value="{{ $work->id }}">
                    <div class="form-group">
                        <label for="edit_date">Date</label>
                        <input type="date" class="form-control" id="edit_date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_amount">Amount</label>
                        <input type="number" step="0.01" class="form-control" id="edit_amount" name="amount" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_img">Invoice File</label>
                        <input type="file" class="form-control-file" id="edit_img" name="img">
                        <small class="form-text text-muted">Allowed file types: jpeg, png, jpg, gif, pdf, docx. Max size: 5MB</small>
                        <div id="currentFile" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
$(function () {
    $("#example1").DataTable({
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    
    $('.edit-btn').click(function() {
        var invoiceId = $(this).data('id');
        var date = $(this).data('date');
        var amount = $(this).data('amount');
        
        $('#editInvoiceForm').attr('action', '/admin/invoices/' + invoiceId);
        
        $('#edit_date').val(date);
        $('#edit_amount').val(amount);
        
    });
    
    // Handle form submission success
    $('#createInvoiceForm, #editInvoiceForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(form[0]);
        
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Close modal
                $('.modal').modal('hide');
                // Show success message
                swal("Success!", response.message || "Operation successful", "success");
                // Reload page to see changes
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    swal("Error!", value[0], "error");
                });
            }
        });
    });
});
</script>
@endsection