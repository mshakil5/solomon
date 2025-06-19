@extends('layouts.user')

@section('content')
<div class="container mt-3">
    <a href="{{ route('user.works') }}" class="btn btn-primary mb-3">Go Back</a>
    <div class="card shadow-lg">
        <div class="card-header bg-primary">
            <h2 class="card-title text-white">Review</h2>    
        </div>
        <div class="card-body">

            <div class="review-section mb-4">
                @if($existingReview)
                    <div class="mb-4 p-3 border rounded bg-light shadow-sm">
                        <div class="row">
                            @if($existingReview->image)
                                <div class="col-6">
                                    <h5 class="font-weight-bold">Review Image:</h5>
                                    <img src="{{ asset('images/reviews/' . $existingReview->image) }}" alt="Review Image" class="img-fluid mb-3 rounded" style="max-width: 100%; height: auto; ">
                                </div>
                            @endif

                            <div class="col-6">
                                <h5 class="font-weight-bold">Comment:</h5>
                                <p>{!! nl2br(e($existingReview->note)) !!}</p>

                                <h5 class="font-weight-bold mt-3">Answers:</h5>
                                @foreach($existingReview->answers as $answer)
                                    <p><strong>{{ $answer->question->question }}:</strong> {{ ucfirst($answer->answer) }}</p>
                                @endforeach
                            </div>
                        </div>

                        <h5 class="font-weight-bold mt-3">Replies:</h5>
                        @if($existingReview->replies->count() > 0)
                            @foreach($existingReview->replies as $reply)
                                <div class="mb-3 p-2 border rounded bg-light shadow-sm">
                                    <p><strong>{{ $reply->user->name }}:</strong> {{ $reply->content }}</p>
                                    <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                </div>
                            @endforeach
                        @else
                            <p>No replies yet.</p>
                        @endif

                        <form action="{{ route('work.review.reply.store', $existingReview->id) }}" method="POST" class="mt-3">
                            @csrf
                            <div class="form-group">
                                <label for="reply" class="h5">Leave a Reply <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="content" id="reply" rows="3" placeholder="Write a reply..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Reply</button>
                        </form>
                    </div>
                @else
                    @if($questions)
                        <form action="{{ route('work.review.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="work_id" value="{{ $work->id }}">

                            @foreach($questions as $question)
                                <div class="mb-4" style="border-bottom: 1px solid #dee2e6; padding-bottom: 10px;">
                                    <div class="form-group">
                                        <label class="h5 mb-2">{{ $question->question }} <span class="text-danger">*</span></label>
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
                                <label for="note" class="h5">Additional Comment</label>
                                <textarea class="form-control" id="note" name="note" rows="5" placeholder="Add any additional comment here..."></textarea>
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