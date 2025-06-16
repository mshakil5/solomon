@extends('layouts.user')

@section('content')

@php
    $lang = session('app_locale', 'en') == 'ro';
@endphp

<div class="row justify-content-center mt-3">
    <div class="col-10">
        <div class="card">
            <div class="card-header bg-primary">
                <h4 class="card-title text-white">{{ $lang ? 'Adresele mele' : 'My Addresses' }}</h4>
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
                            {{ $lang ? 'Adaugă adresă nouă' : 'Add New Address' }}
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h5>{{ $lang ? 'Adrese de livrare' : 'Delivery Addresses' }}</h5>
                        <div class="list-group">
                            @forelse($shipping_addresses as $address)
                                <div class="list-group-item mb-3 {{ $address->primary_shipping == 1 ? 'bg-success text-white' : '' }}">
                                    <div class="d-flex justify-content-between">
                                        <h6>{{ $address->name }}</h6>
                                        <div>
                                            @if(!$address->primary_shipping == 1)
                                              <form action="{{ route('user.addresses.primary.shipping') }}" method="POST" style="display:inline;">
                                                  @csrf
                                                  <input type="hidden" name="additional_address_id" value="{{ $address->id }}">
                                                  <button type="submit" class="btn btn-sm btn-success">
                                                      {{ $lang ? 'Setează principală' : 'Set as primary' }}
                                                  </button>
                                              </form>
                                          @endif

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
                                                {{ $lang ? 'Editează' : 'Edit' }}
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-address" data-id="{{ $address->id }}">
                                                {{ $lang ? 'Șterge' : 'Delete' }}
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
                                        <p class="mb-1">{{ $lang ? 'Etaj:' : 'Floor:' }} {{ $address->floor }}</p>
                                    @endif
                                    @if($address->apartment)
                                        <p class="mb-1">{{ $lang ? 'Apartament:' : 'Apartment:' }} {{ $address->apartment }}</p>
                                    @endif
                                </div>
                            @empty
                                <div class="list-group-item">
                                    {{ $lang ? 'Nicio adresă de livrare găsită.' : 'No Delivery addresses found.' }}
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5>{{ $lang ? 'Adrese de facturare' : 'Billing Addresses' }}</h5>
                        <div class="list-group">
                            @forelse($billing_addresses as $address)
                                <div class="list-group-item mb-3 {{ $address->primary_billing == 1 ? 'bg-success text-white' : '' }}">
                                    <div class="d-flex justify-content-between">
                                        <h6>{{ $address->name }}</h6>
                                        <div>
                                            @if(!$address->primary_billing == 1)
                                                <form action="{{ route('user.addresses.primary.billing') }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <input type="hidden" name="additional_address_id" value="{{ $address->id }}">
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        {{ $lang ? 'Setează principală' : 'Set as primary' }}
                                                    </button>
                                                </form>
                                            @endif

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
                                                {{ $lang ? 'Editează' : 'Edit' }}
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-address" data-id="{{ $address->id }}">
                                                {{ $lang ? 'Șterge' : 'Delete' }}
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
                                        <p class="mb-1">{{ $lang ? 'Etaj:' : 'Floor:' }} {{ $address->floor }}</p>
                                    @endif
                                    @if($address->apartment)
                                        <p class="mb-1">{{ $lang ? 'Apartament:' : 'Apartment:' }} {{ $address->apartment }}</p>
                                    @endif
                                </div>
                            @empty
                                <div class="list-group-item">
                                    {{ $lang ? 'Nicio adresă de facturare găsită.' : 'No billing addresses found.' }}
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
                <h5 class="modal-title text-white" id="addAddressModalLabel">{{ $lang ? 'Adaugă adresă nouă' : 'Add New Address' }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ $lang ? 'Închide' : 'Close' }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addressForm" method="POST" action="{{ route('user.addresses.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="address_id">

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name">{{ $lang ? 'Numele adresei' : 'Address Name' }} <span class="text-danger">*</span></label>
                                <input id="name" type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="first_name">{{ $lang ? 'Prenume' : 'First Name' }} <span class="text-danger">*</span></label>
                                <input id="first_name" type="text" class="form-control" name="first_name" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="phone">{{ $lang ? 'Telefon' : 'Phone' }} <span class="text-danger">*</span></label>
                                <input id="phone" type="text" class="form-control" name="phone" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="district">{{ $lang ? 'District' : 'District' }} <span class="text-danger">*</span></label>
                                <input id="district" type="text" class="form-control" name="district" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="first_line">{{ $lang ? 'Linia Adresei 1' : 'Address Line 1' }} <span class="text-danger">*</span></label>
                                <input id="first_line" type="text" class="form-control" name="first_line" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="second_line">{{ $lang ? 'Linia Adresei 2' : 'Address Line 2' }}</label>
                                <input id="second_line" type="text" class="form-control" name="second_line">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="third_line">{{ $lang ? 'Linia Adresei 3' : 'Address Line 3' }}</label>
                                <input id="third_line" type="text" class="form-control" name="third_line">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="town">{{ $lang ? 'Oraș/Sat' : 'Town/City' }} <span class="text-danger">*</span></label>
                                <input id="town" type="text" class="form-control" name="town" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="post_code">{{ $lang ? 'Cod poștal' : 'Postal Code' }} <span class="text-danger">*</span></label>
                                <input id="post_code" type="text" class="form-control" name="post_code" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="floor">{{ $lang ? 'Etaj' : 'Floor' }}</label>
                                <input id="floor" type="text" class="form-control" name="floor">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="apartment">{{ $lang ? 'Apartament' : 'Apartment' }}</label>
                                <input id="apartment" type="text" class="form-control" name="apartment">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="type">{{ $lang ? 'Tip adresă' : 'Address Type' }} <span class="text-danger">*</span></label>
                                <select id="type" class="form-control" name="type" required>
                                    <option value="1">{{ $lang ? 'Adresă de livrare' : 'Delivery Address' }}</option>
                                    <option value="2">{{ $lang ? 'Adresă de facturare' : 'Billing Address' }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $lang ? 'Închide' : 'Close' }}</button>
                    <button type="submit" class="btn btn-primary">{{ $lang ? 'Salvează adresa' : 'Save Address' }}</button>
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
                <h5 class="modal-title text-white">{{ $lang ? 'Confirmă ștergerea' : 'Confirm Deletion' }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ $lang ? 'Închide' : 'Close' }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ $lang ? 'Sigur doriți să ștergeți această adresă?' : 'Are you sure you want to delete this address?' }}
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $lang ? 'Anulează' : 'Cancel' }}</button>
                    <button type="submit" class="btn btn-danger">{{ $lang ? 'Șterge' : 'Delete' }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>

    var isRo = @json($lang);

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
            
            $('#addAddressModalLabel').text(isRo ? 'Editare adresă' : 'Edit Address');
            $('#addAddressModal').modal('show');
        });
        
        // Handle add new address button click
        $('[data-target="#addAddressModal"]').click(function() {
            var url = "{{ route('user.addresses.store') }}";
            $('#addressForm').attr('action', url);
            $('#addressForm input[name="_method"]').remove();
            $('#addressForm')[0].reset();
            $('#addAddressModalLabel').text(isRo ? 'Adaugă adresă nouă' : 'Add New Address');
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