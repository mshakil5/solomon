@extends('layouts.user')

@section('content')

<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary">
                <h4 class="card-title text-white">My Addresses</h4>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="row mb-4">
                    <div class="col-12 text-right">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addAddressModal">
                            Add New Address
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h5>Shipping Addresses</h5>
                        <div class="list-group">
                            @forelse($shipping_addresses as $address)
                                <div class="list-group-item mb-3">
                                    <div class="d-flex justify-content-between">
                                        <h6>{{ $address->name }}</h6>
                                        <div>
                                            <button class="btn btn-sm btn-info edit-address" 
                                                    data-id="{{ $address->id }}"
                                                    data-type="{{ $address->type }}"
                                                    data-name="{{ $address->name }}"
                                                    data-first_name="{{ $address->first_name }}"
                                                    data-phone="{{ $address->phone }}"
                                                    data-district="{{ $address->district }}"
                                                    data-first_line="{{ $address->first_line }}"
                                                    data-second_line="{{ $address->second_line }}"
                                                    data-third_line="{{ $address->third_line }}"
                                                    data-town="{{ $address->town }}"
                                                    data-post_code="{{ $address->post_code }}"
                                                    data-floor="{{ $address->floor }}"
                                                    data-apartment="{{ $address->apartment }}">
                                                Edit
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-address" data-id="{{ $address->id }}">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                    <p class="mb-1">{{ $address->first_name }}</p>
                                    <p class="mb-1">{{ $address->phone }}</p>
                                    <p class="mb-1">{{ $address->first_line }}</p>
                                    @if($address->second_line)
                                        <p class="mb-1">{{ $address->second_line }}</p>
                                    @endif
                                    @if($address->third_line)
                                        <p class="mb-1">{{ $address->third_line }}</p>
                                    @endif
                                    <p class="mb-1">{{ $address->town }}, {{ $address->district }}</p>
                                    <p class="mb-1">{{ $address->post_code }}</p>
                                    @if($address->floor)
                                        <p class="mb-1">Floor: {{ $address->floor }}</p>
                                    @endif
                                    @if($address->apartment)
                                        <p class="mb-1">Apartment: {{ $address->apartment }}</p>
                                    @endif
                                </div>
                            @empty
                                <div class="list-group-item">
                                    No shipping addresses found.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5>Billing Addresses</h5>
                        <div class="list-group">
                            @forelse($billing_addresses as $address)
                                <div class="list-group-item mb-3">
                                    <div class="d-flex justify-content-between">
                                        <h6>{{ $address->name }}</h6>
                                        <div>
                                            <button class="btn btn-sm btn-info edit-address" 
                                                    data-id="{{ $address->id }}"
                                                    data-type="{{ $address->type }}"
                                                    data-name="{{ $address->name }}"
                                                    data-first_name="{{ $address->first_name }}"
                                                    data-phone="{{ $address->phone }}"
                                                    data-district="{{ $address->district }}"
                                                    data-first_line="{{ $address->first_line }}"
                                                    data-second_line="{{ $address->second_line }}"
                                                    data-third_line="{{ $address->third_line }}"
                                                    data-town="{{ $address->town }}"
                                                    data-post_code="{{ $address->post_code }}"
                                                    data-floor="{{ $address->floor }}"
                                                    data-apartment="{{ $address->apartment }}">
                                                Edit
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-address" data-id="{{ $address->id }}">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                    <p class="mb-1">{{ $address->first_name }}</p>
                                    <p class="mb-1">{{ $address->phone }}</p>
                                    <p class="mb-1">{{ $address->first_line }}</p>
                                    @if($address->second_line)
                                        <p class="mb-1">{{ $address->second_line }}</p>
                                    @endif
                                    @if($address->third_line)
                                        <p class="mb-1">{{ $address->third_line }}</p>
                                    @endif
                                    <p class="mb-1">{{ $address->town }}, {{ $address->district }}</p>
                                    <p class="mb-1">{{ $address->post_code }}</p>
                                    @if($address->floor)
                                        <p class="mb-1">Floor: {{ $address->floor }}</p>
                                    @endif
                                    @if($address->apartment)
                                        <p class="mb-1">Apartment: {{ $address->apartment }}</p>
                                    @endif
                                </div>
                            @empty
                                <div class="list-group-item">
                                    No billing addresses found.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1" role="dialog" aria-labelledby="addAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="addAddressModalLabel">Add New Address</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addressForm" method="POST" action="{{ route('user.addresses.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="address_id">
                    
                    <div class="form-group">
                        <label for="name" class="form-label">Address Name <span class="text-danger">*</span></label>
                        <input id="name" type="text" class="form-control" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                        <input id="first_name" type="text" class="form-control" name="first_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                        <input id="phone" type="text" class="form-control" name="phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="district" class="form-label">District <span class="text-danger">*</span></label>
                        <input id="district" type="text" class="form-control" name="district" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="first_line" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                        <input id="first_line" type="text" class="form-control" name="first_line" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="second_line" class="form-label">Address Line 2</label>
                        <input id="second_line" type="text" class="form-control" name="second_line">
                    </div>
                    
                    <div class="form-group">
                        <label for="third_line" class="form-label">Address Line 3</label>
                        <input id="third_line" type="text" class="form-control" name="third_line">
                    </div>
                    
                    <div class="form-group">
                        <label for="town" class="form-label">Town/City <span class="text-danger">*</span></label>
                        <input id="town" type="text" class="form-control" name="town" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="post_code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                        <input id="post_code" type="text" class="form-control" name="post_code" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="floor" class="form-label">Floor</label>
                        <input id="floor" type="text" class="form-control" name="floor">
                    </div>
                    
                    <div class="form-group">
                        <label for="apartment" class="form-label">Apartment</label>
                        <input id="apartment" type="text" class="form-control" name="apartment">
                    </div>
                    
                    <div class="form-group">
                        <label for="type" class="form-label">Address Type <span class="text-danger">*</span></label>
                        <select id="type" class="form-control" name="type" required>
                            <option value="1">Shipping Address</option>
                            <option value="2">Billing Address</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteAddressModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this address?
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Handle edit button click
        $('.edit-address').click(function() {
            var addressId = $(this).data('id');
            var url = "{{ route('user.addresses.update', ':id') }}";
            url = url.replace(':id', addressId);
            
            $('#addressForm').attr('action', url);
            $('#addressForm').append('<input type="hidden" name="_method" value="PUT">');
            
            // Fill the form with address data
            $('#address_id').val(addressId);
            $('#name').val($(this).data('name'));
            $('#first_name').val($(this).data('first_name'));
            $('#phone').val($(this).data('phone'));
            $('#district').val($(this).data('district'));
            $('#first_line').val($(this).data('first_line'));
            $('#second_line').val($(this).data('second_line'));
            $('#third_line').val($(this).data('third_line'));
            $('#town').val($(this).data('town'));
            $('#post_code').val($(this).data('post_code'));
            $('#floor').val($(this).data('floor'));
            $('#apartment').val($(this).data('apartment'));
            $('#type').val($(this).data('type'));
            
            $('#addAddressModalLabel').text('Edit Address');
            $('#addAddressModal').modal('show');
        });
        
        // Handle add new address button click
        $('[data-target="#addAddressModal"]').click(function() {
            var url = "{{ route('user.addresses.store') }}";
            $('#addressForm').attr('action', url);
            $('#addressForm input[name="_method"]').remove();
            $('#addressForm')[0].reset();
            $('#addAddressModalLabel').text('Add New Address');
        });
        
        // Handle delete button click
        $('.delete-address').click(function() {
            var addressId = $(this).data('id');
            var url = "{{ route('user.addresses.destroy', ':id') }}";
            url = url.replace(':id', addressId);
            
            $('#deleteForm').attr('action', url);
            $('#deleteAddressModal').modal('show');
        });
    });
</script>
@endsection