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
            <h3 class="card-title">Hero Content</h3>
          </div>
          
          <!-- /.card-header -->
          <div class="card-body">
            <div class="ermsg"></div>
            <form id="createThisForm" action="{{ route('admin.homeFooter') }}" method="POST">
                @csrf <!-- Add CSRF token for security -->
                <div class="col-sm-12">
                    <div class="form-group">
                    <label>Hero Content <span style="color: red;">*</span></label>
                        <textarea name="footer_content" id="footer_content" class="form-control @error('footer_content') is-invalid @enderror summernote" cols="30" rows="3">{!!$companyDetails->footer_content!!}</textarea>
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
            height: 500,
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

            var footer_content = $('.summernote').summernote('code');
            if (footer_content == '') {
                swal({
                    title: 'Error',
                    text: 'Please enter about us.',
                    icon: 'error',
                    button: 'OK'
                })
                return;
            } else {
              $.ajax({
                url: '{{ route('admin.homeFooter') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    footer_content: footer_content 
                },
                success: function(response) {
                    swal({
                        title: 'Success',
                        text: 'Updated successfully!',
                        icon: 'success',
                        button: 'OK'
                    }).then(function() {
                        location.reload();
                    })
                },
                error: function(xhr, status, error) {
                  console.error(xhr.responseText);
                    swal({
                        title: 'Error',
                        text: 'Failed to update ',
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