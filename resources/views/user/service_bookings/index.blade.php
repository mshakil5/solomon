@extends('layouts.user')

@section('content')
<div class="row mt-3">
    <div class="col-10 mx-auto">
        <div class="card">
            <div class="card-header bg-primary">
                <h2 class="card-title text-white">Service Bookings</h2>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-center">Booking ID</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Service</th>
                            <th class="text-center">Scheduled Date</th>
                            <th class="text-center">Time</th>
                            <th class="text-center">Total Fee</th>
                            <th class="text-center">Invoice</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                        <tr>
                            <td class="text-center">#{{ $booking->id }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y') }}</td>
                            <td class="text-center">{{ $booking->service->title_english }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($booking->date)->format('d/m/Y') }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($booking->time)->format('h:i A') }}</td>
                            <td class="text-center">{{ number_format($booking->total_fee, 2) }} RON</td>
                            <td class="text-center">
                                @if ($booking->invoices->count() > 0)
                                <a href="{{ route('service.booking.invoice', $booking->id) }}" class="btn btn-primary">
                                    Invoice
                                </a>
                                @else
                                No Invoice
                                @endif
                            </td>
                            <td class="text-center">
                                @switch($booking->status)
                                    @case(0)
                                        <span class="badge bg-secondary">Pending</span>
                                        @break
                                    @case(1)
                                        <span class="badge bg-primary">Confirmed</span>
                                        @break
                                    @case(2)
                                        <span class="badge bg-warning">In Progress</span>
                                        @break
                                    @case(3)
                                        <span class="badge bg-success">Completed</span>
                                        @break
                                    @case(4)
                                        <span class="badge bg-danger">Cancelled</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="text-center">
                                <a href="{{ route('service.booking.details', $booking->id) }}" class="btn btn-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection