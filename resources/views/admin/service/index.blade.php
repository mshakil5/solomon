@extends('admin.layouts.admin')

@section('content')

<section class="content mt-3" id="newBtnSection">
    <div class="container-fluid">
      <div class="row">
        <div class="col-2">
            <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
        </div>
      </div>
    </div>
</section>

<section class="content" id="addThisFormContainer">
  <div class="container-fluid">
    <div class="row justify-content-md-center">
      <div class="col-md-8">
        <div class="card card-secondary border-theme border-2">
          <div class="card-header">
            <h3 class="card-title">Add new</h3>
          </div>
          <div class="card-body">
            <div class="ermsg"></div>
            <form id="createThisForm">
              @csrf
              <input type="hidden" class="form-control" id="codeid" name="codeid">     
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>Title English <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title_english" name="title_english" placeholder="Enter title english">
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>Title Romanian <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title_romanian" name="title_romanian" placeholder="Enter title romanian">
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="form-group">
                        <label>English Description</label>
                        <textarea class="form-control summernote" id="des_english" name="des_english" placeholder="Enter description"></textarea>
                    </div>
                </div>
                <div class="col-sm-12">
                  <div class="form-group">
                        <label>Romanian Description</label>
                        <textarea class="form-control summernote" id="des_romanian" name="des_romanian" placeholder="Enter description"></textarea>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" id="image" name="image" class="form-control" onchange="previewMetaImage(event)" accept="image/*">
                    </div>
                    <img id="meta_image_preview" src="#" alt="Meta Image Preview" class="pt-3" style="max-width: 150px; height: auto; display: none;"/>
                </div>
              </div>        
            </form>
          </div>
 
          <div class="card-footer">
            <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
            <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="content" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card card-secondary border-theme border-2">
            <div class="card-header">
              <h3 class="card-title">All Data</h3>
            </div>
            <div class="card-body">
              <table id="example1" class="table cell-border table-striped">
                <thead>
                <tr>
                  <th style="text-align: center">Sl</th>
                  <th style="text-align: center">Image</th>
                  <th style="text-align: center">
                    Title English <br>
                    Title Romanian
                  </th>
                  <th style="text-align: center">
                    Description English <br>
                    Description Romanian
                  </th>
                  <th>Status </th>
                  <th style="text-align: center">Action</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($data as $key => $data)
                  <tr>
                    <td style="text-align: center">{{ $key + 1 }}</td>
                    <td style="text-align: center">
                      @if ($data->image != null)
                        <img src="{{ asset('images/service/'.$data->image) }}" alt="Image" style="width: 50px; height: 50px;">
                      @else
                        
                      @endif
                    </td>
                    <td style="text-align: center">{{$data->title_english}} <br> {{$data->title_romanian}}</td>
                    <td style="text-align: center">{!! $data->des_english !!} <br> {!! $data->des_romanian !!}</td>
                    <td>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input toggle-status" id="status{{ $data->id }}" data-id="{{ $data->id }}"
                          {{ $data->status == 1 ? 'checked' : '' }}>    
                            <label class="custom-control-label" for="status{{ $data->id }}"></label>
                        </div>
                    </td>
                    <td style="text-align: center">
                      <a class="btn btn-link" id="EditBtn" rid="{{$data->id}}"><i class="fa fa-edit" style="font-size: 20px;"></i></a>
                        <a class="btn btn-link" id="deleteBtn" rid="{{$data->id}}"><i class="fas fa-trash" style="color: red; font-size: 20px;"></i></a>
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

@endsection
@section('script')

<script>
  function previewMetaImage(event) {
      var output = document.getElementById('meta_image_preview');
      output.src = URL.createObjectURL(event.target.files[0]);
      output.style.display = 'block';
  }

  $(document).ready(function() {
      $('.summernote').summernote({
          height: 200, 
      });
  });

  $(function () {
    $("#example1").DataTable();
  });
</script>

<script>
  $(document).ready(function () {
      $("#addThisFormContainer").hide();
      $("#newBtn").click(function(){
        clearform();
        $("#newBtn").hide(100);
        $("#addThisFormContainer").show(300);
      });
      $("#FormCloseBtn").click(function(){
          $("#addThisFormContainer").hide(200);
          $("#newBtn").show(100);
          clearform();
      });
      //header for csrf-token is must in laravel
      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
      var url = "{{URL::to('/admin/service')}}";
      var upurl = "{{URL::to('/admin/service-update')}}";
      // console.log(url);
      $("#addBtn").click(function(){
      //   alert("#addBtn");
          if($(this).val() == 'Create') {
              var form_data = new FormData();
              form_data.append("title_english", $("#title_english").val());
              form_data.append("title_romanian", $("#title_romanian").val());
              form_data.append("des_english", $("#des_english").summernote('code'));
              form_data.append("des_romanian", $("#des_romanian").summernote('code'));
              form_data.append("image", $("#image")[0].files[0]);
              $.ajax({
                url: url,
                method: "POST",
                contentType: false,
                processData: false,
                data:form_data,
                success: function (d) {
                    if (d.status == 303) {
                        $(".ermsg").html(d.message);
                    }else if(d.status == 300){
                      $(".ermsg").html(d.message);
                      window.setTimeout(function(){location.reload()},2000)
                      }
                },
                error: function(xhr, status, error) {
                     console.error(xhr.responseText);
                }
            });
          }
          //create  end
          //Update
          if($(this).val() == 'Update'){
              var form_data = new FormData();
              form_data.append("title_english", $("#title_english").val());
              form_data.append("title_romanian", $("#title_romanian").val());
              form_data.append("des_english", $("#des_english").summernote('code'));
              form_data.append("des_romanian", $("#des_romanian").summernote('code'));
              form_data.append("image", $("#image")[0].files[0]);
              form_data.append("codeid", $("#codeid").val());

              form_data.forEach((value, key) => {
                  console.log(key + ':', value);
              });

              
              $.ajax({
                  url:upurl,
                  type: "POST",
                  dataType: 'json',
                  contentType: false,
                  processData: false,
                  data:form_data,
                  success: function(d){
                      console.log(d);
                      if (d.status == 303) {
                        $(".ermsg").html(d.message);
                      }else if(d.status == 300){
                        $(".ermsg").html(d.message);
                      window.setTimeout(function(){location.reload()},2000)
                      }
                  },
                  error: function(xhr, status, error) {
                     console.error(xhr.responseText);
                }
              });
          }
          //Update
      });
      //Edit
      $("#contentContainer").on('click','#EditBtn', function(){
          //alert("btn work");
          codeid = $(this).attr('rid');
          //console.log($codeid);
          info_url = url + '/'+codeid+'/edit';
          //console.log($info_url);
          $.get(info_url,{},function(d){
            populateForm(d);
          });
      });
      //Edit  end
      //Delete
      $("#contentContainer").on('click','#deleteBtn', function(){
            if(!confirm('Sure?')) return;
            codeid = $(this).attr('rid');
            info_url = url + '/'+codeid;
            $.ajax({
                url:info_url,
                method: "GET",
                type: "DELETE",
                data:{
                },
                success: function(d){
                  alert('Deleted successfully!');
                    if(d.success) {
                      alert(d.message);
                      location.reload();
                    }
                },
                error:function(d){
                  console.log(d);
                }
            });
        });
      //Delete  
      function populateForm(data){
        $("#title_english").val(data.title_english);
        $("#title_romanian").val(data.title_romanian);
        $("#des_english").summernote('code', data.des_english);
        $("#des_romanian").summernote('code', data.des_romanian);
        if (data.image) {
            var imageUrl = '/images/service/' + data.image;
            console.log(imageUrl);
            $("#meta_image_preview").attr("src", imageUrl).show();
        } else {
            $("#meta_image_preview").attr("src", "").hide();
        }
        $("#codeid").val(data.id);
        $("#addBtn").val('Update');
        $("#addBtn").html('Update');
        $("#addThisFormContainer").show(300);
        $("#newBtn").hide(100);
      }
      function clearform(){
        $('#createThisForm')[0].reset();
        $("#des_english").summernote('code', '');
        $("#des_english").summernote('code', '');
        $('#meta_image_preview').attr('src', '#').hide();
        $("#addBtn").val('Create');
      }

      $('.toggle-status').change(function() {
          var status = $(this).is(':checked') ? 1 : 0;
          var id = $(this).data('id');

          $.ajax({
              type: "GET",
              url: "{{ route('admin.service.status') }}",
              data: {status: status, id: id},
              success: function(res) {
                alert(res.message);
                setTimeout(() => {
                    location.reload();
                }, 1000);
              },
              error: function(xhr) {
                  console.log('Error:', xhr.responseText);
              }
          });
      });

  });
</script>
@endsection