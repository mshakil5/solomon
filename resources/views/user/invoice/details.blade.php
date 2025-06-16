@extends('layouts.user')

@section('content')
@php
    $lang = session('app_locale', 'en') == 'ro';
@endphp

<div class="row mt-3">
    <div class="col-10 mx-auto">
        <a href="{{ route('user.service.bookings') }}" class="btn btn-primary mb-3">
            {{ $lang ? 'Înapoi' : 'Go Back' }}
        </a>
        
        @if ($invoices)
            @foreach ($invoices as $invoice)
                <div class="card mt-2">
                    <div class="card-header bg-primary">
                        <h2 class="card-title text-white">
                            {{ $lang ? 'Detalii Factură' : 'Invoice Details' }}
                        </h2>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <th>{{ $lang ? 'Dată:' : 'Date:' }}</th>
                                <td class="text-center">{{ \Carbon\Carbon::parse($invoice->date)->format('d/m/Y') }}</td>
                            </tr>    
                            <tr>
                                <th>{{ $lang ? 'ID Factură:' : 'Invoice ID:' }}</th>
                                <td class="text-center">{{ $invoice->invoiceid }}</td>
                            </tr>
                            <tr>
                                <th>{{ $lang ? 'Factură:' : 'Invoice:' }}</th>
                                <td class="text-center">
                                    @if(isset($invoice->img))
                                        <a class="btn btn-primary" href="{{ asset($invoice->img) }}" target="_blank">
                                            {{ $lang ? 'Vezi Factura' : 'View Invoice' }}
                                        </a>
                                    @else
                                        <span>{{ $lang ? 'Nicio factură disponibilă' : 'No invoice available' }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ $lang ? 'Sumă:' : 'Amount:' }}</th>
                                <td class="text-center">{{ number_format($invoice->amount, 2) }} RON</td>
                            </tr>
                            <tr>
                                <th>{{ $lang ? 'Status:' : 'Status:' }}</th>
                                <td class="text-center">
                                    @if ($invoice->status == 0)
                                        <button class="btn btn-primary" disabled>
                                            {{ $lang ? 'Plătită' : 'Paid' }}
                                        </button>
                                    @elseif ($invoice->status != 0)
                                        <form action="{{ route('payment', $invoice->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="amount" value="{{ $invoice->amount }}">
                                            <input type="hidden" name="work_id" value="{{ $invoice->work_id }}">
                                            <button type="submit" class="btn btn-primary">
                                                {{ $lang ? 'Plătește' : 'Pay' }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-muted">
                {{ $lang ? 'Nu s-a găsit nicio factură pentru această lucrare.' : 'No invoice found for this work.' }}
            </p>
        @endif
    </div>
</div>
@endsection