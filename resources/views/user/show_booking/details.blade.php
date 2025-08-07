@extends('layouts.user')

@section('content')

<style>
    .star-rating input {
        display: none;
    }
    .star-rating label {
        font-size: 1.5rem;
        color: #ccc;
        cursor: pointer;
    }
    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #ffc107;
    }
</style>

<div class="row mt-3">
    <div class="col-10 mx-auto">
        @php
            $lang = session('app_locale', 'ro') == 'ro';
        @endphp
        
        <a href="{{ route('user.service.bookings') }}" class="btn btn-primary mb-3">
            {{ $lang ? 'Înapoi' : 'Go Back' }}
        </a>
        
        <div class="card">
            <div class="card-header bg-primary">
              @php
                $priority = match($booking->type) {
                  1 => $lang ? 'Urgență' : 'Emergency',
                  2 => $lang ? 'Prioritar' : 'Prioritized',
                  3 => $lang ? 'În afara orelor de lucru' : 'Outside Working Hours',
                  4 => $lang ? 'Serviciu standard' : 'Standard Service',
                  default => $lang ? 'Necunoscut' : 'Unknown',
                };
              @endphp
                
            <h2 class="card-title text-white">
              {{ $lang ? 'Detalii rezervare' : 'Booking Details' }} - <small>{{ $priority }}</small>
            </h2>
            </div>
            <div class="card-body">
                <div class="row" style="border-bottom: 1px solid #dee2e6; padding-bottom: 10px;">
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">{{ $lang ? 'Data rezervării' : 'Booking Date' }}:</label>
                            <p>{{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">{{ $lang ? 'ID rezervare' : 'Booking ID' }}:</label>
                            <p>#{{ $booking->id }}</p>
                        </div>
                    </div>
                </div>

                <div class="row" style="border-bottom: 1px solid #dee2e6; padding-bottom: 10px;">
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">{{ $lang ? 'Serviciu' : 'Service' }}:</label>
                            <p>{{ $lang ? $booking->service->title_romanian : $booking->service->title_english }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">{{ $lang ? 'Stare' : 'Status' }}:</label>
                            <p>
                                @switch($booking->status)
                                    @case(1)
                                        <span class="badge bg-secondary">{{ $lang ? 'Plasată' : 'Placed' }}</span>
                                        @break
                                    @case(2)
                                        <span class="badge bg-primary">{{ $lang ? 'Confirmată' : 'Confirmed' }}</span>
                                        @break
                                    @case(3)
                                        <span class="badge bg-warning">{{ $lang ? 'Finalizată' : 'Completed' }}</span>
                                        @break
                                    @case(4)
                                        <span class="badge bg-success">{{ $lang ? 'Anulată' : 'Cancelled' }}</span>
                                        @break
                                @endswitch
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row" style="border-bottom: 1px solid #dee2e6; padding-bottom: 10px;">
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">{{ $lang ? 'Data programată' : 'Scheduled Date' }}:</label>
                            <p>{{ \Carbon\Carbon::parse($booking->date)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">{{ $lang ? 'Ora programată' : 'Scheduled Time' }}:</label>
                            <p>
                              @if($booking->time)
                                {{ \Carbon\Carbon::parse($booking->time)->format('h:i A') }}
                              @else
                                {{ $lang ? 'Nespecificat' : 'Not specified' }}
                              @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row" style="border-bottom: 1px solid #dee2e6; padding-bottom: 10px;">
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">{{ $lang ? 'Taxă serviciu' : 'Service Fee' }}:</label>
                            <p>{{ number_format($booking->service_fee, 2) }} RON</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">{{ $lang ? 'Taxă suplimentară' : 'Additional Fee' }}:</label>
                            <p>{{ number_format($booking->additional_fee, 2) }} RON</p>
                        </div>
                    </div>
                </div>

                <div class="row" style="border-bottom: 1px solid #dee2e6; padding-bottom: 10px;">
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">{{ $lang ? 'Taxă totală' : 'Total Fee' }}:</label>
                            <p class="font-weight-bold">{{ number_format($booking->total_fee, 2) }} RON</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">{{ $lang ? 'Tip rezervare' : 'Booking Type' }}:</label>
                            <p>
                                @switch($booking->type)
                                    @case(1)
                                        {{ $lang ? 'Urgență' : 'Emergency' }}
                                        @break
                                    @case(2)
                                        {{ $lang ? 'Prioritar' : 'Prioritized' }}
                                        @break
                                    @case(3)
                                        {{ $lang ? 'În afara orelor de lucru' : 'Outside Hours' }}
                                        @break
                                    @case(4)
                                        {{ $lang ? 'Serviciu standard' : 'Standard Service' }}
                                        @break
                                @endswitch
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-1">
                            <label class="mb-1" for="name">{{ $lang ? 'Descriere serviciu' : 'Job Description' }}:</label>
                            <p>{!! nl2br(e($booking->description)) !!}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5>{{ $lang ? 'Adresă facturare' : 'Billing Address' }}</h5>
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
                                    {{ $lang ? 'Telefon' : 'Phone' }}: {{ $booking->billingAddress->phone }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5>{{ $lang ? 'Adresă serviciu' : 'Service Address' }}</h5>
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
                                    {{ $lang ? 'Telefon' : 'Phone' }}: {{ $booking->shippingAddress->phone }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($booking->files->count() > 0)
                <div class="row mt-2">
                    <div class="col-12">
                        <h4>{{ $lang ? 'Fișiere atașate' : 'Attached Files' }}</h4>
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
                                            {{ $lang ? 'Vizualizare' : 'View' }}
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
                  <div class="row mt-2 d-none">
                    <div class="col-12">
                      <h4>{{ $lang ? 'Facturi' : 'Invoices' }}</h4>
                      <div class="table-responsive">         
                      <table class="table">
                        <thead>
                          <tr>
                            <th>{{ $lang ? 'ID Factură' : 'Invoice ID' }}</th>
                            <th>{{ $lang ? 'Dată' : 'Date' }}</th>
                            <th>{{ $lang ? 'Sumă' : 'Amount' }}</th>
                            <th>{{ $lang ? 'Stare' : 'Status' }}</th>
                            <th>{{ $lang ? 'Acțiune' : 'Action' }}</th>
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
                                  <span class="badge bg-success">{{ $lang ? 'Plătită' : 'Paid' }}</span>
                                @else
                                  <form action="{{ route('payment', $invoice->id) }}" method="POST">
                                      @csrf
                                      <input type="hidden" name="amount" value="{{ $invoice->amount }}">
                                      <input type="hidden" name="work_id" value="{{ $invoice->work_id }}">
                                      <button type="submit" class="badge bg-warning border-0">{{ $lang ? 'Plătește' : 'Pay' }}</button>
                                  </form>
                                @endif
                              </td>
                              @if ($invoice->img)   
                              <td>
                                <a href="{{ asset($invoice->img) }}" class="btn btn-sm btn-primary" target="_blank" download>
                                  {{ $lang ? 'Vezi factura' : 'View Invoice' }}
                                </a>
                              </td>
                              @endif
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                       </div>
                    </div>
                  </div>
                @endif

                @if($booking->transactions->count() > 0)
                  <div class="row mt-4">
                    <div class="col-12">
                      <h4>{{ $lang ? 'Tranzacții' : 'Transactions' }}</h4>
                      <div class="table-responsive">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>{{ $lang ? 'Dată' : 'Date' }}</th>
                            <th>{{ $lang ? 'ID Tranzacție' : 'Transaction ID' }}</th>
                            <th>{{ $lang ? 'Sumă' : 'Amount' }}</th>
                            <th>{{ $lang ? 'Tip plată' : 'Payment Type' }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($booking->transactions as $transaction)
                            <tr>
                              <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</td>
                              <td>#{{ $transaction->tranid  }}</td>
                              <td>{{ number_format($transaction->amount, 2) }} RON</td>
                              <td>
                                @if($transaction->payment_type == 'card')
                                    {{ $lang ? 'Card' : 'Card' }}
                                @elseif($transaction->payment_type == 'cash')
                                    {{ $lang ? 'Numerar' : 'Cash' }}
                                @else
                                    {{ $transaction->payment_type }}
                                @endif
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                      </div>
                    </div>
                  </div>
                @endif

                @if($booking->status == 1)
                  <div class="row mt-4">
                      <div class="col-12 text-center">
                          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#cancelBookingModal">
                              {{ $lang ? 'Anulează rezervarea' : 'Cancel Booking' }}
                          </button>
                      </div>
                  </div>

                  <div class="modal fade" id="cancelBookingModal" tabindex="-1" role="dialog" aria-labelledby="cancelBookingModalLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                          <div class="modal-content">
                              <div class="modal-header bg-danger text-white">
                                  <h5 class="modal-title" id="cancelBookingModalLabel">{{ $lang ? 'Confirmă anularea' : 'Confirm Cancellation' }}</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                  </button>
                              </div>
                              <div class="modal-body">
                                  {{ $lang ? 'Sigur doriți să anulați această rezervare?' : 'Are you sure you want to cancel this booking?' }}
                              </div>
                              <div class="modal-footer">
                                  <form action="{{ route('user.bookings.cancel', $booking->id) }}" method="POST">
                                      @csrf
                                      @method('PUT')
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $lang ? 'Nu' : 'No' }}</button>
                                      <button type="submit" class="btn btn-danger">{{ $lang ? 'Da, anulează' : 'Yes, Cancel' }}</button>
                                  </form>
                              </div>
                          </div>
                      </div>
                  </div>
                @endif

                @if($booking->status == 3)
                    <div class="row mt-4">
                        <div class="col-4">
                            <h4>{{ $lang ? 'Recenzie' : 'Review' }}</h4>

                            @if($booking->serviceReview)
                                  <p><strong>{{ $lang ? 'Stele acordate' : 'Given Stars' }}:</strong></p>
                                  <div class="text-warning">
                                      @for($i = 1; $i <= 5; $i++)
                                          <i class="fa fa-star{{ $i <= $booking->serviceReview->review_star ? '' : '-o' }}"></i>
                                      @endfor
                                  </div>
                            @else
                                <form action="{{ route('user.bookings.review.store', $booking->id) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label>{{ $lang ? 'Evaluare' : 'Rating' }}</label>
                                        <div class="star-rating d-flex flex-row-reverse justify-content-start">
                                            @for($i = 5; $i >= 1; $i--)
                                                <input type="radio" name="review_star" id="star{{ $i }}" value="{{ $i }}" required />
                                                <label for="star{{ $i }}"><i class="fa fa-star"></i></label>
                                            @endfor
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-2">{{ $lang ? 'Trimite recenzia' : 'Submit Review' }}</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection