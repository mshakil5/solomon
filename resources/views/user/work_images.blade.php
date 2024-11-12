@extends('layouts.user')

@section('content')
<div class="row mt-3">
    <div class="col-10 mx-auto">
        <a href="{{ route('user.works') }}" class="btn btn-primary mb-3">Go Back</a>
        
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title text-white">Uploaded Image</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="uploadedFilesTable">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Note</th>
                                <th>Image</th>
                                <th>Video</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($uploads as $upload)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{!! $upload->note !!}</td>
                                <td class="text-center">
                                    @if($upload->image)
                                    <img src="{{ asset($upload->image) }}" alt="Image" width="200">
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if($upload->video)
                                    <video width="320" height="240" controls>
                                        <source src="{{ asset($upload->video) }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    @endif
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

@endsection