@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4>Deleted Post</h4>
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
                        @foreach ($deletedPosts as $deletedPost)
                            <tr>
                                <td>{{ $deletedPost->reason }}</td>
                                <td>{{ $deletedPost->newsfeed->user->firstname }} {{ $deletedPost->newsfeed->user->lastname }} </td>
                                <td>{{ $deletedPost->other_reason }} </td>
                                <td>{{ $deletedPost->newsfeed->created_at }} </td>
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