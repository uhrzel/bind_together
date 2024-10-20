@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4>Deleted Comment</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Content</th>
                            <th>Owner</th>
                            <th>Reason for Deletion</th>
                            <th>Date Posted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($deletedComments as $deletedComment)
                            <tr>
                                <td>{{ $deletedComment->reason }}</td>
                                <td>{{ $deletedComment->newsfeed->user->firstname }} {{ $deletedComment->newsfeed->user->lastname }} </td>
                                <td>{{ $deletedComment->other_reason }} </td>
                                <td>{{ $deletedComment->comments->created_at }} </td>
                                <td>
                                    <button type="button" class="btn btn-danger">Restore</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        $('#datatable').DataTable();
    </script>
@endpush