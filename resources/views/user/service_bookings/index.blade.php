@extends('layouts.user')

@section('content')
@php
    $lang = session('app_locale', 'en') == 'ro';
@endphp

<div class="row mt-3">
    <div class="col-10 mx-auto">
        <div class="card">
            <div class="card-header bg-primary">
                <h2 class="card-title text-white">{{ $lang ? 'Rezervări Servicii' : 'Service Bookings' }}</h2>
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
                            <th class="text-center">{{ $lang ? 'ID Rezervare' : 'Booking ID' }}</th>
                            <th class="text-center">{{ $lang ? 'Data' : 'Date' }}</th>
                            <th class="text-center">{{ $lang ? 'Prioritate' : 'Priority' }}</th>
                            <th class="text-center">{{ $lang ? 'Serviciu' : 'Service' }}</th>
                            <th class="text-center">{{ $lang ? 'Data Programată' : 'Scheduled Date' }}</th>
                            <th class="text-center">{{ $lang ? 'Ora' : 'Time' }}</th>
                            <th class="text-center">{{ $lang ? 'Factura' : 'Invoice' }}</th>
                            <th class="text-center">{{ $lang ? 'Status' : 'Status' }}</th>
                            <th class="text-center">{{ $lang ? 'Detalii' : 'Details' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                        <tr>
                            <td class="text-center">#{{ $booking->id }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @switch($booking->type)
                                    @case(1)
                                        <span class="badge bg-danger">{{ $lang ? 'Urgent' : 'Emergency' }}</span>
                                        @break
                                    @case(2)
                                        <span class="badge bg-warning">{{ $lang ? 'Prioritar' : 'Prioritized' }}</span>
                                        @break
                                    @case(3)
                                        <span class="badge bg-info">{{ $lang ? 'În afara orelor' : 'Outside Hours' }}</span>
                                        @break
                                    @case(4)
                                        <span class="badge bg-success">{{ $lang ? 'Standard' : 'Standard' }}</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $lang ? 'Necunoscut' : 'Unknown' }}</span>
                                @endswitch
                            </td>
                            <td class="text-center">{{ $lang ? $booking->service->title_romanian : $booking->service->title_english }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($booking->date)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                {{ $booking->time ? \Carbon\Carbon::parse($booking->time)->format('h:i A') : '-' }}
                            </td>
                            <td class="text-center">
                                @if ($booking->invoices->count() > 0)
                                <a href="{{ route('service.booking.invoice', $booking->id) }}" class="btn btn-primary">
                                    {{ $lang ? 'Factură' : 'Invoice' }}
                                </a>
                                @else
                                {{ $lang ? 'Fără factură' : 'No Invoice' }}
                                @endif
                            </td>
                            <td class="text-center">
                                @switch($booking->status)
                                    @case(1)
                                        <span class="badge bg-info">{{ $lang ? 'Plasat' : 'Placed' }}</span>
                                        @break
                                    @case(2)
                                        <span class="badge bg-warning text-dark">{{ $lang ? 'Confirmat' : 'Confirmed' }}</span>
                                        @break
                                    @case(3)
                                        <span class="badge bg-success">{{ $lang ? 'Finalizat' : 'Completed' }}</span>
                                        @break
                                    @case(4)
                                        <span class="badge bg-danger">{{ $lang ? 'Anulat' : 'Cancelled' }}</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $lang ? 'Necunoscut' : 'Unknown' }}</span>
                                @endswitch
                            </td>
                            <td class="text-center">
                                <a href="{{ route('service.booking.details', $booking->id) }}" class="btn btn-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($booking->status == 1 && $booking->type != 1)
                                    <a href="{{ route('service.booking.edit', $booking->id) }}" class="btn btn-warning d-none">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-3 mt-3">
                    {{ $bookings->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection