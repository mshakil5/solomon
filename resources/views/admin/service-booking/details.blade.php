@extends('admin.layouts.admin')

@section('content')
<main class="app-content">
    <div class="row">
        <div class="col-md-12 p-2 m-2">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Booking #{{ $booking->id }}</h3>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header"><strong>Booking Information</strong></div>
                                <div class="card-body p-0">
                                    <table class="table table-bordered mb-0">
                                        <tr><th width="30%">Booking ID</th><td>{{ $booking->id }}</td></tr>
                                        <tr><th>Service</th><td>{{ $booking->service->title_english }} ({{ $booking->service->title_romanian }})</td></tr>
                                        <tr><th>Date & Time</th><td>{{ \Carbon\Carbon::parse($booking->date)->format('d/m/Y') }} at {{ $booking->time }}</td></tr>
                                        <tr>
                                            <th>Type</th>
                                            <td>
                                                <span class="badge 
                                                    @if($booking->type == 1) bg-danger
                                                    @elseif($booking->type == 2) bg-warning
                                                    @elseif($booking->type == 3) bg-info
                                                    @else bg-success @endif">
                                                    @if($booking->type == 1) Emergency
                                                    @elseif($booking->type == 2) Prioritized
                                                    @elseif($booking->type == 3) Outside Hours
                                                    @else Standard @endif
                                                </span>
                                                @if($booking->additional_fee > 0)(+{{ number_format($booking->additional_fee, 2) }} RON)
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Service Fee</th>
                                            <td>
                                              @if($booking->service_fee > 0)
                                                {{ number_format($booking->service_fee, 2) }} RON
                                              @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Additional Fee</th>
                                            <td>
                                              @if($booking->additional_fee > 0)
                                                {{ number_format($booking->additional_fee, 2) }} RON
                                              @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Total Fee</th>
                                            <td>
                                              @if($booking->total_fee > 0)
                                                {{ number_format($booking->total_fee, 2) }} RON
                                              @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                @php $statusClass = ['1'=>'primary','2'=>'info','3'=>'success','4'=>'danger'][$booking->status] ?? 'secondary'; @endphp
                                                <span class="badge bg-{{ $statusClass }}">
                                                    {{ ['1'=>'Placed','2'=>'Confirmed','3'=>'Completed','4'=>'Cancelled'][$booking->status] ?? 'Unknown' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr><th>Created At</th><td>{{ $booking->created_at->format('d/m/Y H:i') }}</td></tr>
                                    </table>
                                </div>
                            </div>

                            <div class="card mt-3">
                              <div class="card-header"><strong>Description</strong></div>
                              <div class="card-body">
                                  {!! $booking->description ?? 'No description provided' !!}
                          
                                  @if($booking->files->count())
                                      <div class="mt-3 row">
                                          @foreach($booking->files as $file)
                                              <div class="col-md-4 mb-3">
                                                  @php
                                                      $ext = pathinfo($file->file, PATHINFO_EXTENSION);
                                                      $fileUrl = asset('images/service/' . $file->file);
                                                  @endphp
                          
                                                  @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']))
                                                      <img src="{{ $fileUrl }}" class="img-fluid rounded" alt="attachment" style="height: 200px; object-fit: cover;">
                                                  @elseif(in_array(strtolower($ext), ['mp4', 'webm']))
                                                      <video src="{{ $fileUrl }}" controls style="max-width: 100%;"></video>
                                                  @else
                                                      <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-secondary">
                                                          Download {{ $file->file }}
                                                      </a>
                                                  @endif
                                              </div>
                                          @endforeach
                                      </div>
                                  @endif
                              </div>
                            </div>
                          
                        </div>

                        <div class="col-md-6 mb-4">

                              @if($booking->invoices->count() > 0)
                                <div class="card">
                                    <div class="card-header"><strong>Invoices</strong></div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered mb-0">
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
                                                                <span class="badge bg-warning">Pending</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                          @if ($invoice->img)                        
                                                          <a href="{{ asset($invoice->img) }}" target="_blank" class="btn btn-sm btn-primary">
                                                              View Invoice
                                                          </a>
                                                          @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                              @endif
                              @if($booking->transactions->count())
                                <div class="card mt-3">
                                  <div class="card-body p-0">
                                    <div class="card-header"><strong>Transactions</strong></div>
                                    <table class="table table-bordered mb-0">
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
                                          <td>#{{ $transaction->tranid }}</td>
                                          <td>{{ number_format($transaction->amount, 2) }} RON</td>
                                          <td>{{ $transaction->payment_type }}</td>
                                        </tr>
                                        @endforeach
                                      </tbody>
                                    </table>
                                  </div>
                                </div>
                              @endif


                            <div class="card mb-3">
                                <div class="card-header"><strong>Client Information</strong></div>
                                <div class="card-body p-0">
                                    <table class="table table-bordered mb-0">
                                        <tr><th width="30%">Name</th><td>{{ $booking->user->name }} {{ $booking->user->surname }}</td></tr>
                                        <tr><th>Email</th><td>{{ $booking->user->email }}</td></tr>
                                        <tr><th>Phone</th><td>{{ $booking->user->phone }}</td></tr>
                                    </table>
                                </div>
                            </div>

                            <div class="card mb-3">
                                <div class="card-header"><strong>Billing Address</strong></div>
                                <div class="card-body p-0">
                                    <table class="table table-bordered mb-0">
                                        @if($booking->billingAddress)
                                            <tr><th>Name</th><td>{{ $booking->billingAddress->name }}</td></tr>
                                            <tr><th>Contact</th><td>{{ $booking->billingAddress->first_name }} ({{ $booking->billingAddress->phone }})</td></tr>
                                            <tr><th>Address</th>
                                                <td>
                                                    {{ $booking->billingAddress->first_line }}<br>
                                                    {{ $booking->billingAddress->second_line ?? '' }}<br>
                                                    {{ $booking->billingAddress->third_line ?? '' }}<br>
                                                    {{ $booking->billingAddress->town }}<br>
                                                    {{ $booking->billingAddress->post_code }}<br>
                                                    @if($booking->billingAddress->floor) Floor: {{ $booking->billingAddress->floor }}<br>@endif
                                                    @if($booking->billingAddress->apartment) Apartment: {{ $booking->billingAddress->apartment }}@endif
                                                </td>
                                            </tr>
                                        @else
                                            <tr><td colspan="2">No billing address provided</td></tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header"><strong>Delivery Address</strong></div>
                                <div class="card-body p-0">
                                    <table class="table table-bordered mb-0">
                                        @if($booking->shippingAddress)
                                            <tr><th>Name</th><td>{{ $booking->shippingAddress->name }}</td></tr>
                                            <tr><th>Contact</th><td>{{ $booking->shippingAddress->first_name }} ({{ $booking->shippingAddress->phone }})</td></tr>
                                            <tr><th>Address</th>
                                                <td>
                                                    {{ $booking->shippingAddress->first_line }}<br>
                                                    {{ $booking->shippingAddress->second_line ?? '' }}<br>
                                                    {{ $booking->shippingAddress->third_line ?? '' }}<br>
                                                    {{ $booking->shippingAddress->town }}<br>
                                                    {{ $booking->shippingAddress->post_code }}<br>
                                                    @if($booking->shippingAddress->floor) Floor: {{ $booking->shippingAddress->floor }}<br>@endif
                                                    @if($booking->shippingAddress->apartment) Apartment: {{ $booking->shippingAddress->apartment }}@endif
                                                </td>
                                            </tr>
                                        @else
                                            <tr><td colspan="2">No Delivery address provided</td></tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($booking->serviceReview)
                    <div class="mt-4">
                        <h4>Service Review</h4>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Rating:</strong> 
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= $booking->serviceReview->review_star ? 'fas' : 'far' }} fa-star text-warning"></i>
                                        @endfor
                                    </div>
                                    <small class="text-muted">Reviewed on {{ $booking->serviceReview->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="mt-2">
                                    <strong>Comment:</strong>
                                    <p>{{ $booking->serviceReview->comment }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>

                <div class="card-footer text-end">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Bookings
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('script')
@endsection