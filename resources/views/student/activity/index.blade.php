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
            <div class="card-header">
                <h4>Activities</h4>
            </div>
            <div class="card-body">
                <div class="container my-4">
                    <!-- Search Section -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="search" class="form-label fw-bold">Search</label>
                            <input type="text" id="search" class="form-control"
                                placeholder="Search For Activities...">
                        </div>
                    </div>

                    <!-- Activities Section -->
                    <div class="row">
                        @foreach ($activities as $activity)
                            @php
                                $activityTypes = [
                                    \App\Enums\ActivityType::Audition => 'Audition',
                                    \App\Enums\ActivityType::Tryout => 'Tryout',
                                    \App\Enums\ActivityType::Practice => 'Practice',
                                    \App\Enums\ActivityType::Competition => 'Competition',
                                ];

                                $hasJoined = auth()
                                    ->user()
                                    ->joinedActivities->contains($activity->id);
                            @endphp

                            <div class="col-md-4 mb-3">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $activity->title }}</h5>
                                        <p class="card-text">
                                            <strong>Sport name:</strong> {{ $activity->sport->name ?? '' }} <br>
                                            <strong>Type:</strong> {{ $activityTypes[$activity->type] ?? '' }} <br>
                                            <strong>Venue:</strong> {{ $activity->venue }} <br>
                                            <strong>Duration:</strong> {{ $activity->start_date }} -
                                            {{ $activity->end_date }}
                                        </p>
                                    </div>
                                    <div class="card-footer d-flex justify-content-between">
                                        <button class="btn btn-danger join-button" data-activity-id="{{ $activity->id }}"
                                            data-activity-type="{{ $activity->type }}" {{ $hasJoined ? 'disabled' : '' }}>
                                            <i class="fas fa-check-circle"></i>
                                            {{ $hasJoined ? 'Already Joined' : 'Join' }}
                                        </button>
                                        <button class="btn btn-dark view-button" data-activity-id="{{ $activity->id }}"
                                            data-bs-toggle="modal" data-bs-target="#activityDetailsModal">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Registration Modal -->
    <div class="modal fade" id="activityRegistrationModal" tabindex="-1" aria-labelledby="activityRegistrationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="activityRegistrationModalLabel">Activity Registration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('activity-registration.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="activity_id" name="activity_id">

                        <div class="mb-3">
                            <label for="height" class="form-label">Height (cm)</label>
                            <input type="number" class="form-control" id="height" name="height"
                                placeholder="Enter Your Height" required>
                        </div>
                        <div class="mb-3">
                            <label for="weight" class="form-label">Weight (kg)</label>
                            <input type="number" class="form-control" id="weight" name="weight"
                                placeholder="Enter Your Weight" required>
                        </div>
                        <div class="mb-3">
                            <label for="contact_person" class="form-label">Person to Contact</label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person"
                                placeholder="Enter Name Of Person To Contact" required>
                        </div>
                        <div class="mb-3">
                            <label for="relationship" class="form-label">Relationship</label>
                            <input type="text" class="form-control" id="relationship" name="relationship"
                                placeholder="Enter Relationship" required>
                        </div>
                        <div class="mb-3">
                            <label for="emergency_contact_number" class="form-label">Emergency Contact Number</label>
                            <input type="text" class="form-control" id="emergency_contact" name="emergency_contact"
                                placeholder="Enter Emergency Contact Number" required>
                        </div>
                        <div class="mb-3">
                            <label for="medical_certificate" class="form-label">Certificate of Registration</label>
                            <input type="file" class="form-control" id="medical_certificate"
                                name="certificate_of_registration" required>
                        </div>
                        <div class="mb-3">
                            <label for="parent_consent" class="form-label">Parent Consent</label>
                            <input type="file" class="form-control" id="parent_consent" name="parent_consent"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Photo Copy of Student ID</label>
                            <input type="file" class="form-control" id="student_id" name="photo_copy_id" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Structure -->
    <div class="modal fade" id="activityDetailsModal" tabindex="-1" aria-labelledby="activityDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="activityDetailsModalLabel">Activity Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><strong>Sport Name:</strong> <span id="activity-sport-name"></span></li>
                                <li><strong>Title:</strong> <span id="activity-title"></span></li>
                                <li><strong>Target Players:</strong> <span id="activity-target-players"></span></li>
                                <li><strong>Content:</strong> <span id="activity-content"></span></li>
                                <li><strong>Activity Type:</strong> <span id="activity-type"></span></li>
                                <li><strong>Activity Duration:</strong> <span id="activity-duration"></span></li>
                                <li><strong>Venue:</strong> <span id="activity-venue"></span></li>
                                <li><strong>Address:</strong> <span id="activity-address"></span></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <img id="activity-image" src="" class="img-fluid" alt="Activity Image">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tryouts/Audition Modal -->
    <div class="modal fade" id="tryoutAuditionModal" tabindex="-1" aria-labelledby="tryoutAuditionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tryoutAuditionModalLabel">Tryouts/Audition Registration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('activity-registration.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="activity_id" name="activity_id">

                        <div class="mb-3">
                            <label for="tryout_skill_level" class="form-label">Skill Level</label>
                            <input type="text" class="form-control" id="tryout_skill_level" name="skill_level"
                                placeholder="Enter Skill Level" required>
                        </div>

                        <div class="mb-3">
                            <label for="tryout_video" class="form-label">Upload Audition/Performance Video</label>
                            <input type="file" class="form-control" id="tryout_video" name="audition_video"
                                accept="video/*" required>
                        </div>

                        <div class="mb-3">
                            <label for="portfolio_link" class="form-label">Portfolio Link</label>
                            <input type="url" class="form-control" id="portfolio_link" name="portfolio_link"
                                placeholder="Enter Portfolio Link (if any)">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.join-button').on('click', function() {
                var activityType = $(this).data('activity-type');
                var activityId = $(this).data('activity-id');

                if (activityType == 3) { // Competition
                    $('#activityRegistrationModal').modal('show');
                    $('#activity_id').val(activityId);
                } else if (activityType == 1 || activityType == 0) { // Tryouts or Audition
                    $('#tryoutAuditionModal').modal('show');
                    $('#activity_id').val(activityId); // Set the hidden input for the form
                } else {
                    alert("Joined successfully!");
                }
            });

            $('.view-button').on('click', function() {
                var activityId = $(this).data('activity-id');
                var baseUrl = "{{ asset('/storage/') }}";

                $.ajax({
                    url: '/activity/' + activityId,
                    method: 'GET',
                    success: function(data) {
                        $('#activity-sport-name').text(data.sport_name);
                        $('#activity-title').text(data.title);
                        $('#activity-target-players').text(data.target_player === 1 ?
                            'Official Players' : 'All Student');
                        $('#activity-content').text(data.content);
                        $('#activity-type').text(data.type);
                        $('#activity-duration').text(data.start_date + ' - ' + data.end_date);
                        $('#activity-venue').text(data.venue);
                        $('#activity-address').text(data.address);
                        $('#activity-image').attr('src', data.attachment ? baseUrl + '/' + data
                            .attachment : '/path-to-placeholder-image.jpg');
                    },
                    error: function(xhr) {
                        alert('Error fetching activity details.');
                    }
                });
            });

        });
    </script>
@endpush
