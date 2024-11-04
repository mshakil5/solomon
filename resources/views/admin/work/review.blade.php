@extends('admin.layouts.admin')

@section('content')
<div class="container py-3">
    <a href="{{ route('admin.complete') }}" class="btn btn-secondary mb-3">Go Back</a>
    <div class="card shadow-lg">
        <div class="card-header bg-secondary">
            <h2 class="card-title text-white">Review</h2>    
        </div>
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="review-section mb-4">
                @if($existingReview)
                    <div class="mb-4 p-3 border rounded bg-light shadow-sm">
                        <div class="row">
                            @if($existingReview->image)
                                <div class="col-6">
                                    <h5 class="font-weight-bold">Review Image:</h5>
                                    <img src="{{ asset('images/reviews/' . $existingReview->image) }}" alt="Review Image" class="img-fluid mb-3 rounded" style="max-width: 100%; height: auto;">
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

                        <form action="{{ route('admin.review.reply.store', $existingReview->id) }}" method="POST" class="mt-3">
                            @csrf
                            <div class="form-group">
                                <label for="reply" class="h5">Leave a Reply</label>
                                <textarea class="form-control" name="content" id="reply" rows="3" placeholder="Write a reply..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-secondary mt-2">Reply</button>
                        </form>
                    </div>
                @else
                    <p>No reviews available for this work.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection