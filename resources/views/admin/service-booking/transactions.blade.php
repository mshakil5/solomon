@extends('admin.layouts.admin')

@section('content')


<section class="content" id="newBtnSection">
    <div class="container-fluid">
      <div class="row">
        <div class="col-2">
            <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
            <div class="page-header"><a href="{{ route('admin.service.bookings.new') }}" class="btn btn-secondary">Back</a></div>
        </div>
      </div>
    </div>
</section>

<section class="content mt-3" id="addThisFormContainer">
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
                    <label>Date<span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}">
                    <input type="hidden" id="service_booking_id" value="{{ $id }}">
                  </div> 
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>Type<span class="text-danger">*</span></label>
                    <select name="type" id="type" class="form-control">
                        <option value="Budget">Initial Budget</option>
                        <option value="Receive">Receive</option>
                        {{-- <option value="Expense">Expense</option> --}}
                    </select>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="form-group">
                        <label>Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount">
                    </div>
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

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                                <h4>Transactions</h4>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        
                        <div class="text-center mb-4 company-name-container">
                                <h4>Transactions</h4>

                                <h5>Service: {{ $serviceDetails->service->title_english ?? 'N/A' }} | {{ $serviceDetails->service->title_romanian ?? 'N/A' }} </h5>
                                <h5>Customer: {{ $serviceDetails->user->name ?? 'N/A' }}</h5>
                                <h5>Email: {{ $serviceDetails->user->email ?? 'N/A' }}</h5>
                        </div>

                        <div class="table-responsive">
                            <table id="dataTransactionsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">ID</th>
                                        <th style="width: 10%">Date</th>
                                        <th  class="text-center"  style="width: 15%">Transaction Inputter</th>
                                        <th  class="text-center">Description</th>
                                        <th  class="text-center" style="width: 10%">Receive</th>                                
                                        <th  class="text-center" style="width: 10%">Budget</th>                                
                                        <th  class="text-center" style="width: 8%">Action</th>                                
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalReceive = 0;
                                        $totalBudget = 0;
                                    @endphp
        
                                    @foreach($transactions as $index => $data)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                                            <td>{{ \App\Models\User::find($data->created_by)->name ?? 'Unknown' }}</td>
                                            <td>{{ $data->transaction_type }}</td> 
                                            @if(in_array($data->transaction_type, ['Receive', 'Expense']))
                                            <td class="text-right">{{ $data->amount }}</td>
                                            <td></td>
                                            @php
                                                $totalReceive += $data->amount;
                                            @endphp
                                            @elseif($data->transaction_type == 'Budget')

                                            <td></td>
                                            <td class="text-right">{{ $data->amount }}</td>

                                            @php
                                                $totalBudget += $data->amount;
                                            @endphp
                                            @endif
                                            
                                            <td>
                                                <a class="" id="EditBtn" rid="{{$data->id}}"><i class="fa fa-edit" style="font-size: 20px;"></i></a>
                                                <a class="" id="deleteBtn" rid="{{$data->id}}"><i class="fas fa-trash" style="color: red; font-size: 20px;"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                  <tr>
                                    <td  class="text-right" colspan="4"> Total: </td>
                                    <td class="text-right">{{ number_format($totalReceive, 2) }}</td>
                                    <td class="text-right">{{ number_format($totalBudget, 2) }}</td>
                                    <td></td>
                                  </tr>
                                  <tr>
                                    <td  class="text-right" colspan="4"> Due: </td>
                                    <td class="text-right text-danger">{{ number_format($totalBudget- $totalReceive, 2) }}</td>
                                    <td class="text-right"></td>
                                    <td></td>
                                  </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



@endsection

@section('script')

<script>
    $(function () {
        $("#example1").DataTable();
    });

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
      var url = "{{URL::to('/admin/service-transactions')}}";
      var upurl = "{{URL::to('/admin/service-transactions-update')}}";
      // console.log(url);
      $("#addBtn").click(function(){
      //   alert("#addBtn");
          if($(this).val() == 'Create') {
              var form_data = new FormData();
              form_data.append("type", $("#type").val());
              form_data.append("amount", $("#amount").val());
              form_data.append("date", $("#date").val());
              form_data.append("service_booking_id", $("#service_booking_id").val());



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
              form_data.append("type", $("#type").val());
              form_data.append("amount", $("#amount").val());
              form_data.append("date", $("#date").val());
              form_data.append("service_booking_id", $("#service_booking_id").val());
              form_data.append("codeid", $("#codeid").val());

            //   form_data.forEach((value, key) => {
            //       console.log(key + ':', value);
            //   });

              
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
                    // if(d.success) {
                      // alert(d.message);
                      location.reload();
                    // }
                },
                error:function(d){
                  console.log(d);
                }
            });
        });
      //Delete  
      function populateForm(data){
        $("#amount").val(data.amount);
        $("#type").val(data.transaction_type);
        $("#date").val(data.date);
        $("#codeid").val(data.id);
        $("#addBtn").val('Update');
        $("#addBtn").html('Update');
        $("#addThisFormContainer").show(300);
        $("#newBtn").hide(100);
      }
      function clearform(){
        $('#createThisForm')[0].reset();
        $("#addBtn").val('Create');
      }

      $('.toggle-status').change(function() {
          var status = $(this).is(':checked') ? 1 : 0;
          var id = $(this).data('id');

          $.ajax({
              type: "GET",
              url: "{{ route('admin.type.status') }}",
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
