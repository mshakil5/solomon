@extends('layouts.user')

@section('content')
<div class="row mt-3">
    <div class="col-10 mx-auto">
        <a href="{{ route('user.service.bookings') }}" class="btn btn-primary mb-3">Go Back</a>
        <div class="card">
            <div class="card-header bg-primary">
              @php
                $priority = match($booking->type) {
                  1 => 'Emergency',
                  2 => 'Prioritized',
                  3 => 'Outside Working Hours',
                  4 => 'Standard Service',
                  default => 'Unknown',
                };
              @endphp
                
            <h2 class="card-title text-white">
              Booking Details - <small>{{ $priority }}</small>
            </h2>
            </div>
            <div class="card-body">
                <div class="row" style="border-bottom: 1px solid #dee2e6; padding-bottom: 10px;">
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">Booking Date:</label>
                            <p>{{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">Booking ID:</label>
                            <p>#{{ $booking->id }}</p>
                        </div>
                    </div>
                </div>

                <div class="row" style="border-bottom: 1px solid #dee2e6; padding-bottom: 10px;">
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">Service:</label>
                            <p>{{ $booking->service->title_english }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">Status:</label>
                            <p>
                                @switch($booking->status)
                                    @case(1)
                                        <span class="badge bg-secondary">Placed</span>
                                        @break
                                    @case(1)
                                        <span class="badge bg-primary">Confirmed</span>
                                        @break
                                    @case(3)
                                        <span class="badge bg-warning">Completed</span>
                                        @break
                                    @case(4)
                                        <span class="badge bg-success">Cancelled</span>
                                        @break
                                @endswitch
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row" style="border-bottom: 1px solid #dee2e6; padding-bottom: 10px;">
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">Scheduled Date:</label>
                            <p>{{ \Carbon\Carbon::parse($booking->date)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">Scheduled Time:</label>
                            <p>
                              @if($booking->time)
                                {{ \Carbon\Carbon::parse($booking->time)->format('h:i A') }}
                              @else
                              @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row" style="border-bottom: 1px solid #dee2e6; padding-bottom: 10px;">
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">Service Fee:</label>
                            <p>{{ number_format($booking->service_fee, 2) }} RON</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">Additional Fee:</label>
                            <p>{{ number_format($booking->additional_fee, 2) }} RON</p>
                        </div>
                    </div>
                </div>

                <div class="row" style="border-bottom: 1px solid #dee2e6; padding-bottom: 10px;">
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">Total Fee:</label>
                            <p class="font-weight-bold">{{ number_format($booking->total_fee, 2) }} RON</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">Booking Type:</label>
                            <p>
                                @switch($booking->type)
                                    @case(1)
                                        Emergency
                                        @break
                                    @case(2)
                                        Priortized
                                        @break
                                    @case(3)
                                        Outside Hours
                                        @break
                                    @case(4)
                                        Standard Servicew
                                        @break
                                @endswitch
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">Job Description:</label>
                            <p>{!! nl2br(e($booking->description)) !!}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5>Billing Address</h5>
                            </div>
                            <div class="card-body">
                                <p>
                                    <strong>{{ $booking->billingAddress->name }}</strong><br>
                                    {{ $booking->billingAddress->first_line }}<br>
                                    @if($booking->billingAddress->second_line)
                                        {{ $booking->billingAddress->second_line }}<br>
                                    @endif
                                    @if($booking->billingAddress->third_line)
                                        {{ $booking->billingAddress->third_line }}<br>
                                    @endif
                                    {{ $booking->billingAddress->town }}<br>
                                    {{ $booking->billingAddress->post_code }}<br>
                                    Phone: {{ $booking->billingAddress->phone }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5>Service Address</h5>
                            </div>
                            <div class="card-body">
                                <p>
                                    <strong>{{ $booking->shippingAddress->name }}</strong><br>
                                    {{ $booking->shippingAddress->first_line }}<br>
                                    @if($booking->shippingAddress->second_line)
                                        {{ $booking->shippingAddress->second_line }}<br>
                                    @endif
                                    @if($booking->shippingAddress->third_line)
                                        {{ $booking->shippingAddress->third_line }}<br>
                                    @endif
                                    {{ $booking->shippingAddress->town }}<br>
                                    {{ $booking->shippingAddress->post_code }}<br>
                                    Phone: {{ $booking->shippingAddress->phone }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($booking->files->count() > 0)
                <div class="row mt-2">
                    <div class="col-12">
                        <h4>Attached Files</h4>
                        <div class="row">
                            @foreach($booking->files as $file)
                            <div class="col-md-3 mb-3">
                                <div class="card">
                                    @if(in_array(pathinfo($file->file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                        <img src="{{ asset('images/service/' . $file->file) }}" class="card-img-top img-fluid" alt="File" style="height: 200px; object-fit: cover;">
                                    @elseif(in_array(pathinfo($file->file, PATHINFO_EXTENSION), ['pdf']))
                                        <div class="card-body text-center">
                                            <i class="fas fa-file-pdf fa-5x text-danger"></i>
                                        </div>
                                    @else
                                        <div class="card-body text-center">
                                            <i class="fas fa-file-alt fa-5x text-primary"></i>
                                        </div>
                                    @endif
                                    <div class="card-footer">
                                        <a href="{{ asset('images/service/' . $file->file) }}" target="_blank" class="btn btn-sm btn-primary">
                                            View
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                @if($booking->invoices->count() > 0)
                  <div class="row mt-2">
                    <div class="col-12">
                      <h4>Invoices</h4>
                      <table class="table">
                        <thead>
                          <tr>
                            <th>Invoice ID</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($booking->invoices as $invoice)
                            <tr>
                              <td>{{ $invoice->invoiceid }}</td>
                              <td>{{ \Carbon\Carbon::parse($invoice->date)->format('d/m/Y') }}</td>
                              <td>{{ number_format($invoice->amount, 2) }} RON</td>
                              <td>
                                @if($invoice->status == 0)
                                  <span class="badge bg-success">Paid</span>
                                @else
                                  <form action="{{ route('payment', $invoice->id) }}" method="POST">
                                      @csrf
                                      <input type="hidden" name="amount" value="{{ $invoice->amount }}">
                                      <input type="hidden" name="work_id" value="{{ $invoice->work_id }}">
                                      <button type="submit" class="badge bg-warning border-0">Pay</button>
                                  </form>
                                @endif
                              </td>
                              @if ($invoice->img)   
                              <td>
                                <a href="{{ asset($invoice->img) }}" class="btn btn-sm btn-primary" target="_blank" download>
                                  View Invoice
                                </a>
                              </td>
                              @endif
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                @endif

                @if($booking->transactions->count() > 0)
                  <div class="row mt-4">
                    <div class="col-12">
                      <h4>Transactions</h4>
                      <table class="table">
                        <thead>
                          <tr>
                            <th>Date</th>
                            <th>Transaction ID</th>
                            <th>Amount</th>
                            <th>Payment Type</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($booking->transactions as $transaction)
                            <tr>
                              <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</td>
                              <td>#{{ $transaction->tranid  }}</td>
                              <td>{{ number_format($transaction->amount, 2) }} RON</td>
                              <td>{{ $transaction->payment_type }}</td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection