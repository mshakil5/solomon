@extends('layouts.user')

@section('content')
<div class="row mt-3">
    <div class="col-10 mx-auto">
        <a href="{{ route('user.works') }}" class="btn btn-primary mb-3">Go Back</a>
        <div class="card shadow">
            <div class="card-header bg-primary">
                <h2 class="card-title text-white">Review Questions</h2>    
            </div>
            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($existingReview)
                    <div class="mb-4">
                        <p><strong>Note:</strong> {!! $existingReview->note !!}</p>

                        @if($existingReview->image)
                            <p><strong>Review Image:</strong></p>
                            <img src="{{ asset('images/reviews/' . $existingReview->image) }}" alt="Review Image" class="img-fluid mb-3" style="max-width: 300px;">
                        @endif

                        <h5>Answers:</h5>
                        @foreach($existingReview->answers as $answer)
                            <p><strong>{{ $answer->question->question }}:</strong> {{ ucfirst($answer->answer) }}</p>
                        @endforeach
                    </div>
                @else
                    @if($questions)
                        <form action="{{ route('work.review.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="work_id" value="{{ $work->id }}">

                            @foreach($questions as $question)
                                <div class="mb-4" style="border-bottom: 1px solid #dee2e6; padding-bottom: 10px;">
                                    <div class="form-group">
                                        <label class="h5 mb-2">{{ $question->question }}</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="yes" required>
                                        <label class="form-check-label">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="no" required>
                                        <label class="form-check-label">No</label>
                                    </div>
                                </div>
                            @endforeach

                            <div class="form-group mb-4">
                                <label for="note" class="h5">Additional Notes</label>
                                <textarea class="form-control" id="note" name="note" rows="5" placeholder="Add any additional notes here..."></textarea>
                            </div>

                            <div class="form-group mb-4">
                                <label for="image" class="h5">Upload an Image</label>
                                <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                                <small class="form-text text-muted">Optional: Upload an image related to your review.</small>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Submit Review</button>
                            </div>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection