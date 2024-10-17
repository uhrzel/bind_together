@extends('layouts.app')

@section('content')
    <style>
        /* To style the pagination buttons */
        .dataTables_paginate .paginate_button {
            padding: 0;
            margin: 0;
        }

        /* To style individual page items */
        .dataTables_paginate .paginate_button.page-item {
            padding: 0px;
            margin: 0px;
        }

        /* To style the 'Next' and 'Previous' buttons */
        .dataTables_paginate .paginate_button.previous,
        .dataTables_paginate .paginate_button.next {
            padding: 0;
            margin: 0;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        /* To style the active page button */
        .dataTables_paginate .paginate_button.current {
            background-color: #343a40;
            color: white !important;
            border-radius: 4px;
        }
    </style>

    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4>Reported Comment</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Comment</th>
                                <th>Owner</th>
                                <th>Reported By</th>
                                <th>Report Reason</th>
                                <th>Status</th>
                                <th>Date Reported</th>
                                {{-- <th>Report Counts</th> --}}
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reportedComments as $reportedComment)
                                <tr>
                                    <td>{{ $reportedComment->comments->description }}</td>
                                    <td>{{ $reportedComment->comments->user->firstname }}
                                        {{ $reportedComment->comments->user->lastname }}</td>
                                    <td>{{ $reportedComment->user->firstname }} {{ $reportedComment->user->lastname }} </td>
                                    <td>{{ $reportedComment->reason }}</td>
                                    <td>
                                        @if ($reportedComment->status == 1)
                                            <span class="badge text-black" style="background: yellow">Pending</span>
                                        @elseif ($reportedComment->status == 0)
                                            <span class="badge bg-danger">Declined</span>
                                        @elseif ($reportedComment->status == 2)
                                            <span class="badge bg-success">Approved</span>
                                        @endif
                                    </td>
                                    <td>{{ $reportedComment->created_at }}</td>
                                    {{-- <td>{{ $reportedComment->created_at }}</td> --}}
                                    @php
                                        $isDisabled = $reportedComment->status != 1;
                                    @endphp
                                    <td>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#actionModal"
                                            onclick="setStatus(2, {{ $reportedComment->id }})"  {{ $isDisabled ? 'disabled' : '' }}>Approve</button>
                                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#actionModal"
                                            onclick="setStatus(0, {{ $reportedComment->id }})" {{ $isDisabled ? 'disabled' : '' }}>Decline</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Structure -->
    <div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="statusForm" method="POST" action="">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title" id="actionModalLabel">Confirm Action</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p>Are you sure you want to perform this action?</p>
                        <input type="hidden" name="status" id="statusInput">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(() => {
            $('#datatable').DataTable();
        })

        function setStatus(status, commentId) {
            document.getElementById('statusInput').value = status;
            document.getElementById('statusForm').action = `/reported-comment/${commentId}`;
        }
    </script>
@endpush
