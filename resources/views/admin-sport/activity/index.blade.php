@extends('layouts.app')

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container-fluid">
        <div class="card">
            <div class="card-header row">
                <div class="col">
                    <h4>Activity</h4>
                </div>
                <div class="col text-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newCompetitionModal">Add
                        New</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Venue</th>
                                <th>Activity Duration</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activities as $activity)
                                <tr>
                                    <td>{{ $activity->title }}</td>
                                    <td>{{ $activity->type }}</td>
                                    <td>{{ $activity->venue }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($activity->start_date)->format('F d, Y h:i A') }} -
                                        {{ \Carbon\Carbon::parse($activity->end_date)->format('F d, Y h:i A') }}
                                    </td>
                                    <td>
                                        @if ($activity->status == 1)
                                            <span class="badge bg-success">Approved</span>
                                        @elseif ($activity->status == 0)
                                            <span class="badge text-black" style="background: yellow">Pending</span>
                                        @elseif ($activity->status == 2)
                                            <span class="badge bg-danger">Declined</span>
                                        @endif
                                    </td>

                                    <td>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#editCompetitionModal"
                                            onclick="loadActivityData({{ $activity->id }})">Edit</button>
                                        <button type="button" class="btn btn-danger">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="newCompetitionModal" tabindex="-1" aria-labelledby="newCompetitionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newCompetitionModalLabel">New Competition</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form inside modal -->
                    <form action="{{ route('activity.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <!-- Title -->
                            <div class="col-md-6">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" placeholder="Title" name="title" required>
                            </div>
                            <!-- Target Players -->
                            <div class="col-md-6">
                                <label for="target_players" class="form-label">Target players</label>
                                <select class="form-select" name="target_player" required>
                                    <option value="" disabled selected>Select target</option>
                                    <option value="0">All Student</option>
                                    <option value="1">Official Player</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control" name="content" placeholder="Content" rows="3" required></textarea>
                        </div>

                        <div class="row">
                            <div class="form-group col">
                                <label for="activity_type" class="form-label">Activity Type</label>
                                <select class="form-select" name="type" required>
                                    @if (auth()->user()->hasRole('adviser'))
                                        <option value="0">Audition</option>
                                        <option value="2">Practice</option>
                                    @endif
                                    @if (auth()->user()->hasRole('coach'))
                                        <option value="1">Tryout</option>
                                        <option value="2">Practice</option>
                                    @endif
                                    @if (auth()->user()->hasRole(['admin_sport', 'admin_org']))
                                        <option value="3" selected>Competition</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col">
                                <label for="organization">Organization</label>
                                <input type="text" value="{{ $user->organization->name }}" class="form-control" placeholder="Organization" readonly>
                            </div>
                        </div>
                        <div class="row mb-3 mt-3">
                            <!-- Activity Start Date -->
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Activity Start Date</label>
                                <input type="datetime-local" class="form-control" name="start_date" required>
                            </div>

                            <!-- Activity End Date -->
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Activity End Date</label>
                                <input type="datetime-local" class="form-control" name="end_date" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Venue -->
                            <div class="col-md-12">
                                <label for="venue" class="form-label">Venue</label>
                                <input type="text" class="form-control" placeholder="Venue" name="venue" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <!-- Address -->
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" name="address" placeholder="Address" required>
                        </div>

                        <div class="mb-3">
                            <!-- Attachment -->
                            <label for="attachment" class="form-label">Attachment (Image)</label>
                            <input class="form-control" type="file" name="attachment[]" accept="image/*" multiple>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editCompetitionModal" tabindex="-1" aria-labelledby="editCompetitionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCompetitionModalLabel">Edit Competition</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form inside modal -->
                    <form action="" id="editActivityForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <!-- Title -->
                            <div class="col-md-6">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" placeholder="Title" id="title"
                                    name="title" required>
                            </div>
                            <!-- Target Players -->
                            <div class="col-md-6">
                                <label for="target_players" class="form-label">Target players</label>
                                <select class="form-select" id="target_players" name="target_player" required>
                                    <option value="" disabled selected>Select target</option>
                                    <option value="0">All Student</option>
                                    <option value="1">Official Player</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control" id="content" name="content" placeholder="Content" rows="3" required></textarea>
                        </div>

                        <div class="">
                            <label for="activity_type" class="form-label">Activity Type</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="3" selected>Competition</option>
                            </select>
                        </div>
                        <div class="row mb-3 mt-3">
                            <!-- Activity Start Date -->
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Activity Start Date</label>
                                <input type="datetime-local" class="form-control" id="start_date" name="start_date"
                                    required>
                            </div>

                            <!-- Activity End Date -->
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Activity End Date</label>
                                <input type="datetime-local" class="form-control" id="end_date" name="end_date"
                                    required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Venue -->
                            <div class="col-md-12">
                                <label for="venue" class="form-label">Venue</label>
                                <input type="text" class="form-control" id="venue" placeholder="Venue"
                                    name="venue" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <!-- Address -->
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address"
                                placeholder="Address" required>
                        </div>

                        <div class="mb-3">
                            <!-- Attachment -->
                            <label for="attachment" class="form-label">Attachment (Image)</label>
                            <input class="form-control" type="file" id="attachment" name="attachment[]"
                                accept="image/*" multiple>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $('#datatable').DataTable();

        function loadActivityData(activityId) {
            // Call the show route using AJAX
            $.ajax({
                url: '/activity/' + activityId, // Assuming your route is like /activity/{id}
                method: 'GET',
                success: function(data) {
                    // Populate the modal fields with data
                    $('#editActivityForm #title').val(data.title);
                    $('#editActivityForm #target_players').val(data.target_player);
                    $('#editActivityForm #content').val(data.content);
                    $('#editActivityForm #type').val(data.type);
                    $('#editActivityForm #start_date').val(data.start_date);
                    $('#editActivityForm #end_date').val(data.end_date);
                    $('#editActivityForm #venue').val(data.venue);
                    $('#editActivityForm #address').val(data.address);

                    // Set the form action dynamically for updating the activity
                    $('#editActivityForm').attr('action', '/activity/' + activityId);
                },
                error: function(xhr) {
                    console.error('Error fetching activity data', xhr);
                    alert('Failed to load activity data.');
                }
            });
        }
    </script>
@endpush
