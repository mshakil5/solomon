@extends('layouts.master')

@section('content')

@include('frontend.inc.hero')

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header text-center">
                    <h2>
                        Complete Your Job Request for {{ $category->name }}
                    </h2>
                </div>
                <div class="card-body">

                @if ($success = Session::get('success'))
                        <div class="alert alert-primary alert-dismissible fade show" role="alert">
                            <strong>{{ $success }}</strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="categoryForm" action="{{route('work.store')}}" method="post" role="form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="category_id" value="{{ $category->id }}">
                        <input type="hidden" name="sub_category_id" value="{{ $subcategory->id ?? '' }}">

                        @auth
                        <div class="row d-none">
                            <div class="col-lg-6 col-12" >
                                 <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="useRegisteredAddress">
                                    <label class="form-check-label" for="useRegisteredAddress">
                                        Use registered address
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12" >
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="useRegisteredPhone">
                                    <label class="form-check-label" for="useRegisteredPhone">
                                        Use registered phone number
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endauth

                        <div id="imageContainer">
                            <div class="row image-row mt-3">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="image-upload">Upload Image/Video  </label>
                                        <div class="input-group mb-3">
                                            <input type="file" class="form-control image-upload" id="image-upload" name="images[]" accept="image/*,video/*">
                                        </div>
                                        <span style="color: red">*<small>Picture will help us to know the job.</small></span>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label for="description">Description </label>
                                        <div class="input-group mb-3">
                                            <textarea class="form-control description resizable" id="description" placeholder="Description" rows="3" name="descriptions[]"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-1 text-end">
                                    <label for="add-row" class="form-label" style="visibility: hidden;">Action</label>
                                    <button id="add-row" class="btn btn-primary add-row" type="button">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                          <div class="col-lg-4 col-12">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Your Name" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                          </div>
                        
                          <div class="col-lg-4 col-12">
                              <label for="email">Email <span class="text-danger">*</span></label>
                              <input type="email" name="email" class="form-control" placeholder="you@example.com" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                          </div>
                        
                          <div class="col-lg-4 col-12">
                              <label for="phone">Phone <span class="text-danger">*</span></label>
                              <input type="number" name="phone" id="phone" class="form-control" value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                          </div>    
                        </div>

                        <div class="row mt-3">
                          <div class="col-lg-4 col-12">
                            <label for="address_first_line">Address First Line <span class="text-danger">*</span></label>
                            <input type="text" name="address_first_line" id="address_first_line" class="form-control" value="{{ old('address_first_line', auth()->user()->address_first_line ?? '') }}" required>
                          </div>
                        
                          <div class="col-lg-4 col-12">
                              <label for="address_second_line">Address Second Line</label>
                              <input type="text" name="address_second_line" id="address_second_line" class="form-control" value="{{ old('address_second_line', auth()->user()->address_second_line ?? '') }}">
                          </div>
                        
                          <div class="col-lg-4 col-12">
                              <label for="address_third_line">Address Third Line</label>
                              <input type="text" name="address_third_line" id="address_third_line" class="form-control" value="{{ old('address_third_line', auth()->user()->address_third_line ?? '') }}">
                          </div>
                        
                          <div class="col-lg-6 col-12 mt-3">
                              <label for="town">Town <span class="text-danger">*</span></label>
                              <input type="text" name="town" id="town" class="form-control" value="{{ old('town', auth()->user()->town ?? '') }}">
                          </div>
                          
                          <div class="col-lg-6 col-12 mt-3">
                              <label for="post_code">Post Code <span class="text-danger">*</span></label>
                              <input type="text" name="post_code" id="post_code" class="form-control" value="{{ old('post_code', auth()->user()->postcode ?? '') }}">
                          </div>
                        
                        </div>

                        <div class="form-group mt-3">
                            <label for="use_different_address">
                                <input type="checkbox" id="use_different_address" name="use_different_address" value="0" onchange="this.value = this.checked ? 1 : 0;"> Use Different Address
                            </label>
                        </div>

                        <div id="different_address_section" style="display: none;">
                            <h5>Different Address</h5>
                            <div class="row">
                                <div class="col-lg-4 col-12">
                                    <label for="different_address_first_line">Address First Line <span class="text-danger">*</span></label>
                                    <input type="text" name="different_address_first_line" id="different_address_first_line" class="form-control" value="{{ old('different_address_first_line') }}">
                                </div>
                        
                                <div class="col-lg-4 col-12">
                                    <label for="different_address_second_line">Address Second Line</label>
                                    <input type="text" name="different_address_second_line" id="different_address_second_line" class="form-control" value="{{ old('different_address_second_line') }}">
                                </div>
                        
                                <div class="col-lg-4 col-12">
                                    <label for="different_address_third_line">Address Third Line</label>
                                    <input type="text" name="different_address_third_line" id="different_address_third_line" class="form-control" value="{{ old('different_address_third_line') }}">
                                </div>
                        
                                <div class="col-lg-6 col-12 mt-3">
                                    <label for="different_town">Town <span class="text-danger">*</span></label>
                                    <input type="text" name="different_town" id="different_town" class="form-control" value="{{ old('different_town') }}">
                                </div>
                        
                                <div class="col-lg-6 col-12 mt-3">
                                    <label for="different_post_code">Post Code <span class="text-danger">*</span></label>
                                    <input type="text" name="different_post_code" id="different_post_code" class="form-control" value="{{ old('different_post_code') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="form-control btn btn-primary mt-3">Submit</button>
                            </div>
                        </div>

                        <div id='loading' style='display:none ;'>
                            <img src="{{ asset('loader.gif') }}" id="loading-image" alt="Loading..." />
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
      #loading {
        position: fixed;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0.7;
        background-color: #fff;
        z-index: 99;
    }

    #loading-image {
        z-index: 100;
    }
</style>


@endsection

@section('script')

<script>
  $(document).ready(function() {
      $('#use_different_address').change(function() {
          if ($(this).is(':checked')) {
              $('#different_address_section').show();
          } else {
              $('#different_address_section').hide();
          }
      });
  });
</script>

<script>
    $(document).ready(function() {
        function addNewRow() {
            var newRow = `
                <div class="row image-row" style="margin-top: 10px;">
                    <div class="col-lg-6 col-12">
                        <div class="input-group mb-3">
                            <input type="file" class="form-control image-upload" name="images[]" accept="image/*,video/*" required>
                        </div>
                        <span style="color: red">*<small>Picture will help us to know the job.</small></span>
                    </div>
                    <div class="col-lg-5 col-12">
                        <div class="input-group mb-3">
                            <textarea class="form-control description resizable" placeholder="Description" rows="3" name="descriptions[]" required></textarea>
                        </div>
                    </div>
                    <div class="col-lg-1 col-12 text-end">
                        <button class="btn btn-danger remove-row" type="button">-</button>
                    </div>
                </div>
            `;
            $('#imageContainer').append(newRow);
        }

        $(document).on('click', '.add-row', function() {
            addNewRow();
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('.row').remove();
        });

    });
</script>

<script>
    $(document).ready(function() {
        // Use registered address
        $('#useRegisteredAddress').on('change', function() {
            if ($(this).is(':checked')) {
                $('#address_first_line').val('{{ auth()->check() ? auth()->user()->address_first_line : '' }}');
                $('#address_second_line').val('{{ auth()->check() ? auth()->user()->address_second_line : '' }}');
                $('#address_third_line').val('{{ auth()->check() ? auth()->user()->address_third_line : '' }}');
                $('#town').val('{{ auth()->check() ? auth()->user()->town : '' }}');
                $('#post_code').val('{{ auth()->check() ? auth()->user()->postcode : '' }}');
            } else {
                $('#address_first_line, #address_second_line, #address_third_line, #town, #post_code').val('');
            }
        });

        // Use registered phone number
        $('#useRegisteredPhone').on('change', function() {
            if ($(this).is(':checked')) {
                $('#phone').val('{{ auth()->check() ? auth()->user()->phone : '' }}');
            } else {
                $('#phone').val('');
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('categoryForm');
        const loadingDiv = document.getElementById('loading');

        form.addEventListener('submit', function() {
            loadingDiv.style.display = 'flex';
        });
    });
</script>

@endsection