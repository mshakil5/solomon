@extends('admin.layouts.admin')

@section('content')
<div class="container">
    
    <div class="row">
        <div class="col-2">
            <a href="{{ url()->previous() }}">
                <button type="button" class="btn btn-secondary my-3"><i class="fas fa-arrow-left"></i> Go back</button>
            </a>
        </div>
            <h1 class="mt-2">Work Details</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <label for="name">Job ID:</label>
            <input type="text" class="form-control" id="date" name="date" value="{{ $work->orderid }}" disabled>
        </div>

        <div class="col-md-6">
            <label for="name">Date:</label>
            <input type="text" class="form-control" id="date" name="date" value="{{ \Carbon\Carbon::parse($work->date)->format('d/m/y') }}" disabled>
        </div>

        <div class="col-md-6">
            <label for="name">Category:</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $work->category->name }}" disabled>
        </div>

        @if($work->subCategory)
        <div class="col-md-6">
            <label for="name">Sub Category:</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $work->subCategory->name }}" disabled>
        </div>
        @endif

        <div class="col-md-6">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $work->name }}" disabled>
        </div>

        <div class="col-md-6">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $work->email }}" disabled>
        </div>

        <div class="col-md-6">
            <label for="phone">Phone:</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ $work->phone }}" disabled>
        </div>

        <div class="col-md-6">
            <label for="address_first_line">Address First Line:</label>
            <input type="text" class="form-control" id="address_first_line" name="address_first_line" value="{{ $work->address_first_line }}" disabled>
        </div>

        <div class="col-md-6">
            <label for="address_second_line">Address Second Line:</label>
            <input type="text" class="form-control" id="address_second_line" name="address_second_line" value="{{ $work->address_second_line }}" disabled>
        </div>

        <div class="col-md-6">
            <label for="address_third_line">Address Third Line:</label>
            <input type="text" class="form-control" id="address_third_line" name="address_third_line" value="{{ $work->address_third_line }}" disabled>
        </div>

        <div class="col-md-6">
            <label for="town">Town:</label>
            <input type="text" class="form-control" id="town" name="town" value="{{ $work->town }}" disabled>
        </div>

        <div class="col-md-6">
            <label for="post_code">Post Code:</label>
            <input type="text" class="form-control" id="post_code" name="post_code" value="{{ $work->post_code }}" disabled>
        </div>

        @if($work->use_different_address == 1)

            <div class="col-md-12 mt-3">
                <h4>Different Address Details</h4>
            </div>

          <div class="col-md-6">
              <label for="different_address_first_line">Address First Line:</label>
              <input type="text" class="form-control" id="different_address_first_line" name="different_address_first_line" value="{{ $work->different_address_first_line }}" disabled>
          </div>

          <div class="col-md-6">
              <label for="different_address_second_line">Address Second Line:</label>
              <input type="text" class="form-control" id="different_address_second_line" name="different_address_second_line" value="{{ $work->different_address_second_line }}" disabled>
          </div>

          <div class="col-md-6">
              <label for="different_address_third_line">Address Third Line:</label>
              <input type="text" class="form-control" id="different_address_third_line" name="different_address_third_line" value="{{ $work->different_address_third_line }}" disabled>
          </div>

          <div class="col-md-6">
              <label for="different_town">Town:</label>
              <input type="text" class="form-control" id="different_town" name="different_town" value="{{ $work->different_town }}" disabled>
          </div>

          <div class="col-md-6">
              <label for="different_post_code">Post Code:</label>
              <input type="text" class="form-control" id="different_post_code" name="different_post_code" value="{{ $work->different_post_code }}" disabled>
          </div>
        @endif

        @if ($work->workAssign)
        <div class="col-md-6">
            <label for="phone">Staff:</label>
            <input type="text" class="form-control" id="staff" name="staff" value="@if ($work->workAssign) {{ $work->workAssign->staff->name }} {{ $work->workAssign->staff->surname }} @endif" disabled>
        </div>
        @endif
    </div>

    @if ($work->workAssign)
    <div class="row">

        <div class="col-md-6">
            <label for="start_date">Start Date:</label>
            <input type="text" class="form-control" id="start_date" name="start_date" value="{{ \Carbon\Carbon::parse($work->workAssign->start_date)->format('d/m/y') }}" disabled>
        </div>

        <div class="col-md-6">
            <label for="end_date">End Date:</label>
            <input type="text" class="form-control" id="end_date" name="end_date" value="{{ \Carbon\Carbon::parse($work->workAssign->end_date)->format('d/m/y') }}" disabled>
        </div>

        <div class="col-md-6">
            <label for="start_time">Start Time:</label>
            <input type="text" class="form-control" id="start_time" name="start_time" 
                value="{{ $work->workAssign && $work->workAssign->start_time ? \Carbon\Carbon::parse($work->workAssign->start_time)->format('h:i A') : '' }}" disabled>
        </div>

        <div class="col-md-6">
            <label for="end_time">End Time:</label>
            <input type="text" class="form-control" id="end_time" name="end_time" 
                value="{{ $work->workAssign && $work->workAssign->end_time ? \Carbon\Carbon::parse($work->workAssign->end_time)->format('h:i A') : '' }}" disabled>
        </div>

        <div class="col-md-12">
            <label for="note">Note:</label>
            <input type="text" class="form-control" id="note" name="note" value="{!! $work->workAssign->note !!}" disabled>
        </div>

    </div>
    @endif

    @if($work->workimage)
        @foreach($work->workimage as $index => $image)
            <div class="row pb-5">
                <div class="col-md-12 mt-3">
                    <div class="row align-items-center mt-3">
                        <div class="col-md-6">
                                <a href="{{ asset('/' . $image->name) }}" download class="btn btn-secondary">
                                    <i class="fas fa-download"></i>
                                </a>
                            @if(in_array(pathinfo($image->name, PATHINFO_EXTENSION), ['jpeg', 'jpg', 'png', 'gif', 'svg']))
                                <a href="{{ asset('/' . $image->name) }}" data-lightbox="image-{{ $index }}">
                                    <div class="d-flex justify-content-center align-items-center" style="height: 150px;">
                                        <img src="{{ asset('/' . $image->name) }}" alt="Image" class="img-fluid rounded" style="max-height: 100%; max-width: 100%;">
                                    </div>
                                </a>
                            @elseif(in_array(pathinfo($image->name, PATHINFO_EXTENSION), ['mp4', 'avi', 'mov', 'wmv']))
                                <div class="d-flex justify-content-center align-items-center" style="height: 150px;">
                                    <video controls class="img-fluid rounded" style="max-height: 100%; max-width: 100%;">
                                        <source src="{{ asset('/' . $image->name) }}" type="video/{{ pathinfo($image->name, PATHINFO_EXTENSION) }}">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                                
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="description{{ $index }}">Description:</label>
                                <textarea class="form-control" id="description{{ $index }}" name="descriptions[{{ $index }}]" disabled>{{ $image->description }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
    
    @if($work->review)
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <label for="image" class="form-label">Image:</label>
                            @if($work->review->image)
                                <img src="{{ asset('images/reviews/' . $work->review->image) }}" alt="Review Image" class="img-fluid mb-2">
                            @else
                                <p>No image available</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Note:</h5>
                            <input type="text" class="form-control" id="note" name="note" value="{!! $work->review->note !!}" disabled>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5>Answers:</h5>
                            @foreach($work->review->answers as $answer)
                                <p>
                                    <strong>{{ $answer->question->question }}:</strong> 
                                    {{ ucfirst($answer->answer) }}
                                </p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection
