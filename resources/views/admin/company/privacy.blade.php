@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
<section class="content pt-3" id="addThisFormContainer">

  <div class="container-fluid">
    <div class="row justify-content-md-center">
      <!-- right column -->
      <div class="col-md-12">
        <!-- general form elements disabled -->

        <div class="card card-secondary">
          <div class="card-header">
            <h3 class="card-title">Privacy Policy</h3>
          </div>
          
          <!-- /.card-header -->
          <div class="card-body">
            <div class="ermsg"></div>
            <form id="createThisForm" action="{{ route('admin.privacy-policy') }}" method="POST">
                @csrf <!-- Add CSRF token for security -->
                <div class="col-sm-12">
                    <div class="form-group">
                    <label>Privacy Policy <span style="color: red;">*</span></label>
                        <textarea name="privacy_policy" id="privacy_policy" class="form-control @error('privacy_policy') is-invalid @enderror summernote" cols="30" rows="3">{!!$companyDetails->privacy_policy!!}</textarea>
                    </div>
                </div>
          
          <!-- /.card-body -->
          <div class="card-footer">
            <button type="button" class="btn btn-secondary" id="updateButton">Update</button>
          </div>
        </form>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['list', ['ul', 'ol']],
                ['para', ['paragraph']],
                ['height', ['height']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '22', '24', '26', '28', '30', '32', '34', '36', '38', '40', '42', '44', '46', '48', '50', '52', '54', '56', '58', '60', '62', '64', '66', '68', '70', '72']
        });

        $('#updateButton').on('click', function(e) {
            e.preventDefault();

            var privacyPolicyContent = $('.summernote').summernote('code');
            if (privacyPolicyContent == '') {
                swal({
                    title: 'Error',
                    text: 'Please enter privacy policy.',
                    icon: 'error',
                    button: 'OK'
                })
                return;
            } else {
              $.ajax({
                url: '{{ route('admin.privacy-policy') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    privacy_policy: privacyPolicyContent 
                },
                success: function(response) {
                    swal({
                        title: 'Success',
                        text: 'Privacy policy updated successfully!',
                        icon: 'success',
                        button: 'OK'
                    }).then(function() {
                        location.reload();
                    })
                },
                error: function(xhr) {
                    swal({
                        title: 'Error',
                        text: 'Failed to update privacy policy.',
                        icon: 'error',
                        button: 'OK'
                    });
                }
              }); 
            }
        });
    });
</script>
@endsection