@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-5">
        <div class="row">
            <div class="col-md-12">
                <h2>Feedback</h2>
            </div>
        </div>

        @foreach ($feedbacks as $feedback)
        <div class="card mb-2">
            <div class="card-header d-flex">
                <div class="me-3">
                    <img src="{{ asset('storage/' . $feedback->user->avatar) }}" class="rounded-circle" alt="User Avatar">
                </div>
                <div>
                    <h5 class="mb-0">{{ $feedback->user->firstname }} {{ $feedback->user->lastname }}</h5>
                    <small>{{ $feedback->user->email }}</small><br>
                    <small>{{ $feedback->created_at }}</small>
                </div>
            </div>

            <div class="card-body">
                <div class="mb-2">
                    <label for="subject" class="form-label"><strong>Subject</strong></label>
                    <textarea type="text" class="form-control" rows="1">{{ $feedback->subject }}</textarea>
                </div>
                <div class="mb-2">
                    <label for="subject" class="form-label"><strong>Message</strong></label>
                    <textarea type="text" class="form-control" rows="1">{{ $feedback->message }}</textarea>
                </div>
                <form action="{{ route('feedback.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="response" class="form-label"><strong>Response</strong></label>
                        <textarea class="form-control" id="response" rows="2" placeholder="Respond here..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-envelope"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach

    </div>
@endsection