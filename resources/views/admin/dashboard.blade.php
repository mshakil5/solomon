@extends('admin.layouts.admin')

@section('content')


  <!-- content area -->
  <section class="content">
    <div class="container-fluid">
      <!-- Small boxes (Stat box) -->
      <div class="row pt-3">

        <div class="col-lg-3 col-6">
            <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <div class="small-box bg-warning d-flex justify-content-between align-items-center">
                    <div class="inner">
                        <h5>New Reviews</h5>
                        <p>New Work Reviews</p>
                    </div>
                    <div class="badge badge-light" style="font-size: 1.5rem;">
                        {{ $newReviewsCount }}
                    </div>
                    <div class="icon">
                        <i class="ion ion-star"></i>
                    </div>
                </div>
            </a>
            <div class="dropdown-menu">
                @if($newReviewsCount > 0)
                    @foreach($newReviews as $review)
                    <a class="dropdown-item" href="{{ route('admin.work.review', $review->work_id) }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold text-dark">{{ $review->work->name }}</span>
                            <span class="badge bg-primary">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                    @endforeach
                @else
                    <span class="dropdown-item text-muted">No new reviews</span>
                @endif
            </div>
        </div>
      
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <a href="{{ route('admin.new') }}">
                <div class="small-box bg-success d-flex justify-content-between align-items-center">
                    <div class="inner">
                        <h5>New Job</h5>
                        <p>Newly created Jobs</p>
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
            <a href="{{ route('admin.processing') }}">
                <div class="small-box bg-warning d-flex justify-content-between align-items-center">
                    <div class="inner">
                        <h5>Job In Progress</h5>
                        <p>Jobs are in progress</p>
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
            <a href="{{ route('admin.complete') }}">
                <div class="small-box bg-secondary d-flex justify-content-between align-items-center">
                    <div class="inner">
                        <h5>Completed</h5>
                        <p>Completed Jobs</p>
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
    </div>

      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </section>

@endsection

@section('script')

@endsection