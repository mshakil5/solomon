@extends('admin.layouts.admin')

@section('content')


  <!-- content area -->
  <section class="content">
    <div class="container-fluid">
      <!-- Small boxes (Stat box) -->
      <div class="row pt-3">
      
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <a href="{{ route('admin.service.bookings.new') }}">
                <div class="small-box bg-warning d-flex justify-content-between align-items-center">
                    <div class="inner">
                        <h5>Placed</h5>
                        <p>Placed Bookings</p>
                    </div>
                    <div class="badge badge-light" style="font-size: 1.5rem;">
                        {{ $newJobsCount }}
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-6">
            <a href="{{ route('admin.service.bookings.processing') }}">
                <div class="small-box bg-info d-flex justify-content-between align-items-center">
                    <div class="inner">
                        <h5>Confirmed</h5>
                        <p>Confirmed Bookings</p>
                    </div>
                    <div class="badge badge-light" style="font-size: 1.5rem;">
                        {{ $processingJobsCount }}
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-6">
            <a href="{{ route('admin.service.bookings.completed') }}">
                <div class="small-box bg-success d-flex justify-content-between align-items-center">
                    <div class="inner">
                        <h5>Completed</h5>
                        <p>Completed Bookings</p>
                    </div>
                    <div class="badge badge-light" style="font-size: 1.5rem;">
                        {{ $completedJobsCount }}
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-6">
            <a href="{{ route('admin.service.bookings.cancelled') }}">
                <div class="small-box bg-danger d-flex justify-content-between align-items-center">
                    <div class="inner">
                        <h5>Cancelled</h5>
                        <p>Cancelled Bookings</p>
                    </div>
                    <div class="badge badge-light" style="font-size: 1.5rem;">
                        {{ $cancelledJobsCount }}
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </section>

@endsection

@section('script')

@endsection