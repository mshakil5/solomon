@extends('admin.layouts.admin')

@section('content')

<section class="content" id="newBtnSection">
    <div class="container-fluid">
      <div class="row">
        <div class="col-2">
            <a href="{{ url()->previous() }}">
                <button type="button" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Go back</button>
            </a>
            <button type="button" class="btn btn-secondary my-3" id="addNewWorkTimeBtn">Add New Work Time</button>
        </div>
      </div>
    </div>
</section>

<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title"><b>Work Timer Details</b></h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Duration</th>
                                    <th>Is Break</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($workTimes as $workTime)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($workTime->start_time)->format('d M Y, h:i A') }}</td>
                                    <td>
                                        @if ($workTime->end_time)
                                            {{ \Carbon\Carbon::parse($workTime->end_time)->format('d M Y, h:i A') }}
                                        @else
                                            Not ended
                                        @endif
                                    </td>
                                    <td>
                                        {{ \Carbon\CarbonInterval::seconds($workTime->duration)->cascade()->forHumans() }}
                                    </td>
                                    <td>{{ $workTime->is_break ? 'Yes' : 'No' }}</td>
                                    <td>
                                        <button class="btn btn-primary edit-button" data-id="{{ $workTime->id }}" data-start="{{ $workTime->start_time }}" data-end="{{ $workTime->end_time }}" data-is-break="{{ $workTime->is_break }}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <button class="btn btn-danger delete-button" data-id="{{ $workTime->id }}" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
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

<div class="modal fade" id="editWorkTimeModal" tabindex="-1" role="dialog" aria-labelledby="editWorkTimeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editWorkTimeModalLabel">Edit Work Time</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editWorkTimeForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="workTimeId">
                    <div class="form-group">
                        <label for="start_time">Start Time</label>
                        <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
                    </div>
                    <div class="form-group">
                        <label for="end_time">End Time</label>
                        <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
                    </div>
                    <div class="form-group">
                        <label for="is_break">Is Break</label>
                        <select class="form-control" id="is_break" name="is_break">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addNewWorkTimeModal" tabindex="-1" role="dialog" aria-labelledby="addNewWorkTimeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNewWorkTimeModalLabel">Add New Work Time</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addNewWorkTimeForm">
                <div class="modal-body">
                    <input type="hidden" name="work_id" id="workId" value="{{ $workId }}">
                    <div class="form-group">
                        <label for="new_start_time">Start Time</label>
                        <input type="datetime-local" class="form-control" id="new_start_time" name="new_start_time" required>
                    </div>
                    <div class="form-group">
                        <label for="new_end_time">End Time</label>
                        <input type="datetime-local" class="form-control" id="new_end_time" name="new_end_time" required>
                    </div>
                    <div class="form-group">
                        <label for="new_is_break">Is Break</label>
                        <select class="form-control" id="new_is_break" name="new_is_break">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Work Time</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {

        $('#example1').DataTable({
           order: [],
          "responsive": true, "lengthChange": false, "autoWidth": false,
          "buttons": ["copy", "csv", "excel", "pdf", "print"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        
        $('.edit-button').on('click', function() {
            const id = $(this).data('id');
            const start = $(this).data('start');
            const end = $(this).data('end');
            const isBreak = $(this).data('is-break');

            $('#workTimeId').val(id);
            $('#start_time').val(start);
            $('#end_time').val(end);
            $('#is_break').val(isBreak);
            $('#editWorkTimeModal').modal('show');
        });

        $('#editWorkTimeForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#workTimeId').val();
            const startTime = $('#start_time').val();
            const endTime = $('#end_time').val();
            const isBreak = $('#is_break').val();

            $.ajax({
                url: `/admin/worktime/${id}`,
                method: 'PUT',
                data: {
                    start_time: startTime,
                    end_time: endTime,
                    is_break: isBreak,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#editWorkTimeModal').modal('hide');
                    swal({
                        title: "Success!",
                        text: "Work time updated successfully.",
                        icon: "success",
                        button: "OK",
                    });
                    window.setTimeout(function(){location.reload()},2000);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('An error occurred while updating the work time.');
                }
            });
        });

        $('#addNewWorkTimeBtn').on('click', function() {
            $('#addNewWorkTimeModal').modal('show');
        });

        $('#addNewWorkTimeForm').on('submit', function(e) {
            e.preventDefault();
            const workId = $('#workId').val();
            const startTime = $('#new_start_time').val();
            const endTime = $('#new_end_time').val();
            const isBreak = $('#new_is_break').val();

            $.ajax({
                url: `/admin/worktime-store`,
                method: 'POST',
                data: {
                    work_id: workId,
                    start_time: startTime,
                    end_time: endTime,
                    is_break: isBreak,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#addNewWorkTimeModal').modal('hide');
                        swal({
                            title: "Success!",
                            text: "Work time added successfully.",
                            icon: "success",
                            button: "OK",
                        });
                        window.setTimeout(function(){location.reload()},2000);
                },
                error: function(xhr) {
                    alert('An error occurred while adding the work time.');
                }
            });
        });

        $('.delete-button').on('click', function() {
            const id = $(this).data('id');
            
            if(confirm('Are you sure you want to delete this work time?')) {
                $.ajax({
                    url: `/admin/worktime/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert('Work time deleted successfully.');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('An error occurred while deleting the work time.');
                    }
                });
            }
        });
    });
</script>
@endsection