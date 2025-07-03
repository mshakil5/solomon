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
                        {{ $placedJobsCount }}
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

        <div class="col-lg-12">
            <section class="connectedSortable">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="ion ion-clipboard mr-1"></i>
                            <b id="newJobsCount">{{ count($newJobs) }}</b> New Bookings
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped" id="newJobsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($newJobs as $job)
                                    @php
                                        $createdAt = \Carbon\Carbon::parse($job->created_at);
                                        $timeDiff = $createdAt->diffForHumans();
                                        $timeDiffInHours = $createdAt->diffInHours();
                                        $badgeClass = match(true) {
                                            $timeDiffInHours <= 1 => 'badge-primary',
                                            $timeDiffInHours <= 6 => 'badge-secondary',
                                            $timeDiffInHours <= 24 => 'badge-info',
                                            $timeDiffInHours <= 168 => 'badge-warning',
                                            default => 'badge-danger',
                                        };
                                    @endphp
                                    <tr id="job-{{ $job->id }}">
                                        <td>
                                            <input type="checkbox" onclick="return markAsNotified(this, {{ $job->id }})" style="cursor: pointer;" title="Mark as notified">
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($job->created_at)->format('d-m-Y') }}</td>
                                        <td><b>{{ ($job->user->name ?? '') . ' ' . ($job->user->surname ?? '') ?: 'Unknown' }}</b></td>
                                        <td>
                                            @php
                                                $typeText = match($job->type) {
                                                    1 => 'Emergency',
                                                    2 => 'Prioritized',
                                                    3 => 'Outside Hours',
                                                    default => 'Standard',
                                                };
                                                $badgeClass = match($job->type) {
                                                    1 => 'bg-danger',
                                                    2 => 'bg-warning',
                                                    3 => 'bg-info',
                                                    default => 'bg-success',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $typeText }}</span>
                                        </td>

                                        <td>
                                            @php
                                                $statusLabel = match($job->status) {
                                                    1 => 'Placed',
                                                    2 => 'Confirmed',
                                                    3 => 'Completed',
                                                    4 => 'Cancelled',
                                                    default => 'Unknown',
                                                };
                                            @endphp
                                            {{ $statusLabel }}
                                        </td>
                                        <td>
                                            <small class="badge {{ $badgeClass }}">
                                                <i class="far fa-clock"></i> {{ $timeDiff }}
                                            </small>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.booking.details', $job->id) }}" class="btn btn-sm btn-info">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </section>

@endsection

@section('script')

<script>
  function markAsNotified(checkbox, bookingId) {
      if (!confirm('Mark this booking as notified?')) {
          checkbox.checked = false;
          return false;
      }

      $.ajax({
          url: '{{ route("service.bookings.notify") }}',
          method: 'POST',
          data: {
              _token: '{{ csrf_token() }}',
              booking_id: bookingId
          },
          success: function (res) {
              if (res.success) {
                  $('#job-' + bookingId).fadeOut();
                  let count = parseInt($('#newJobsCount').text()) || 0;
                  $('#newJobsCount').text(count - 1);
              }
          },
          error: function (err) {
              checkbox.checked = false;
              console.error(err);
          }
      });

      return true;
  }
  $('#newJobsTable').DataTable({
      responsive: true,
      autoWidth: false,
  });
</script>

@endsection