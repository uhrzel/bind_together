@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            @if ($status == 0)
            <h4>{{ request()->query('type') && request()->query('type') == '3' ? 'Registered Participants' : 'List of Auditionees' }}</h4>
            @else
            <h4>Official Performers</h4>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Year Level</th>
                            <th>Campus</th>
                            <th>Email</th>
                            <th>Height</th>
                            <th>Weight</th>
                            <th>Person in Contact</th>
                            <th>Emergency Contact</th>
                            <th>Relationship</th>
                            <th>COR</th>
                            <th>Photocopy</th>
                            @if ($status == 0)
                            <th>Other File</th>
                            @else
                            <th>Parent Consent</th>
                            @endif
                            <th>Date Registered</th>
                            <th>Status</th>
                            @if ($status == 0)
                            <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($auditions as $audition)
                        <tr>
                            <td>{{ $audition->user->firstname }} {{ $audition->user->lastname }}</td>
                            @php
                            $yearLevel = $audition->user->year_level; // Assuming year_level is an integer
                            $suffix = '';

                            if ($yearLevel == 1) {
                            $suffix = 'st';
                            } elseif ($yearLevel == 2) {
                            $suffix = 'nd';
                            } elseif ($yearLevel == 3) {
                            $suffix = 'rd';
                            } else {
                            $suffix = 'th';
                            }

                            $formattedYearLevel = $yearLevel . $suffix;
                            @endphp

                            <td>{{ $formattedYearLevel }} Year</td>
                            <td>{{ $audition->user->campus->name }}</td>
                            <td>{{ $audition->user->email }}</td>
                            <td>{{ $audition->height }}</td>
                            <td>{{ $audition->weight }}</td>
                            <td>{{ $audition->person_to_contact ? $audition->person_to_contact : 'N/A' }}</td>
                            <td>{{ $audition->emergency_contact }}</td>
                            <td>{{ $audition->relationship }}</td>
                            <td><img src="{{ asset('storage/' . $audition->certificate_of_registration) }}"
                                    alt=""></td>
                            <td><img src="{{ asset('storage/' . $audition->photo_copy_id) }}" alt=""></td>
                            @if ($status == 0)
                            <td><img src="{{ asset('storage/' . $audition->other_file) }}" alt="">
                            </td>
                            @else
                            <td><img src="{{ asset('storage/' . $audition->parent_consent) }}" alt="">
                            </td>
                            @endif
                            {{-- <td>{{ $audition->type ?? '' }}</td> --}}
                            <td>{{ $audition->created_at }}</td>
                            <td>
                                @if ($audition->status == 1)
                                <span class="badge bg-success">Approved</span>
                                @elseif ($audition->status == 0)
                                <span class="badge text-black" style="background: yellow">Pending</span>
                                @elseif ($audition->status == 2)
                                <span class="badge bg-danger">Declined</span>
                                @endif
                            </td>
                            @if ($status == 0)
                            <td>
                                @if($audition->status == 0)
                                <button class="btn btn-primary approveBtn" type="button" data-bs-toggle="modal"
                                    data-bs-target="#approveModal"
                                    data-id="{{ $audition->id }}">Approve</button>
                                <button class="btn btn-secondary declineBtn" type="button"
                                    data-bs-toggle="modal" data-bs-target="#declineModal"
                                    data-id="{{ $audition->id }}">Decline</button>
                                @endif
                                @if($audition->status != 0)
                                <button class="btn btn-primary " type="button">Approve</button>
                                <button class="btn btn-secondary " type="button">Decline</button>
                                @endif
                                <button type="button" class="btn btn-info viewBtn" data-bs-toggle="modal"
                                    data-bs-target="#viewAuditionModal" data-id="{{ $audition->id }}">
                                    View
                                </button>
                                <button class="btn btn-secondary deleteBtn" type="button"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-id="{{ $audition->id }}">Delete</button>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" action="" method="POST">
                @csrf
                @method('GET')
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to proceed with this deletion?</p>
                    <input type="hidden" id="deleteUserId" name="id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Approve -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" id="approveForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    Are you sure you want to approve this user?
                    <input type="hidden" name="status" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewAuditionModal" tabindex="-1" aria-labelledby="viewAuditionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewAuditionModalLabel">View Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Display Audition Data as Form Inputs -->
                <form>
                    <div class="row">
                        <!-- Full Name -->
                        <div class="col-md-6 mb-3">
                            <label for="user-fullname" class="form-label">Full Name</label>
                            <input type="text" id="user-fullname" class="form-control" readonly>
                        </div>

                        <!-- Year Level -->
                        <div class="col-md-6 mb-3">
                            <label for="user-year-level" class="form-label">Year Level</label>
                            <input type="text" id="user-year-level" class="form-control" readonly>
                        </div>

                        <!-- Campus -->
                        <div class="col-md-6 mb-3">
                            <label for="user-campus" class="form-label">Campus</label>
                            <input type="text" id="user-campus" class="form-control" readonly>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label for="user-email" class="form-label">Email</label>
                            <input type="email" id="user-email" class="form-control" readonly>
                        </div>

                        <!-- Height -->
                        <div class="col-md-6 mb-3">
                            <label for="audition-height" class="form-label">Height</label>
                            <input type="text" id="audition-height" class="form-control" readonly>
                        </div>

                        <!-- Weight -->
                        <div class="col-md-6 mb-3">
                            <label for="audition-weight" class="form-label">Weight</label>
                            <input type="text" id="audition-weight" class="form-control" readonly>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="col-md-6 mb-3">
                            <label for="audition-emergency-contact" class="form-label">Emergency Contact</label>
                            <input type="text" id="audition-emergency-contact" class="form-control" readonly>
                        </div>

                        <!-- Relationship -->
                        <div class="col-md-6 mb-3">
                            <label for="audition-relationship" class="form-label">Relationship</label>
                            <input type="text" id="audition-relationship" class="form-control" readonly>
                        </div>

                        <!-- Certificate of Registration -->
                        <div class="col-md-6 mb-3">
                            <label for="certificate-of-registration" class="form-label">Certificate of
                                Registration</label>
                            <img id="certificate-of-registration" class="img-fluid" src=""
                                alt="Certificate of Registration">
                        </div>

                        <!-- Photo Copy of ID -->
                        <div class="col-md-6 mb-3">
                            <label for="photo-copy-id" class="form-label">Photo Copy of ID</label>
                            <img id="photo-copy-id" class="img-fluid" src="" alt="Photo Copy ID">
                        </div>

                        <!-- Conditional Files: Other File or Parent Consent -->
                        <div class="col-md-6 mb-3" id="other-file-row">
                            <label for="other-file" class="form-label">Other File</label>
                            <img id="other-file" class="img-fluid" src="" alt="Other File">
                        </div>

                        <div class="col-md-6 mb-3" id="parent-consent-row">
                            <label for="parent-consent" class="form-label">Parent Consent</label>
                            <img id="parent-consent" class="img-fluid" src="" alt="Parent Consent">
                        </div>

                        <!-- Type -->
                        {{-- <div class="col-md-6 mb-3">
                            <label for="audition-type" class="form-label">Type</label>
                            <input type="text" id="audition-type" class="form-control" readonly>
                        </div> --}}

                        <!-- Status -->
                        {{-- <div class="col-md-6 mb-3">
                            <label for="audition-status" class="form-label">Status</label>
                            <input type="text" id="audition-status" class="form-control" readonly>
                        </div> --}}
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Decline -->
<div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="declineModalLabel">Decline</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" id="declineForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    Are you sure you want to decline this user?
                    <input type="hidden" name="status" value="2">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(() => {
        $('.deleteBtn').click(function() {
            $('#deleteForm').attr('action', '/activity-registration-delete/' + $(this).data('id'))
        })

        $('#datatable').DataTable();

        $('.approveBtn').click(function() {
            $('#approveForm').attr('action', '/activity-registration/' + $(this).data('id'))
        })

        $('.declineBtn').click(function() {
            $('#declineForm').attr('action', '/activity-registration/' + $(this).data('id'))
        })

        $('.viewBtn').click(function() {
            fetch('fetch-activity/' + $(this).data('id'))
                .then(response => response.json())
                .then(audition => {
                    console.log(audition);
                    $('#user-fullname').val(audition.user.firstname + ' ' + audition.user
                        .lastname);
                    $('#user-year-level').val(audition.user.year_level + ' Year');
                    $('#user-campus').val(audition.user.campus.name);
                    $('#user-email').val(audition.user.email);
                    $('#audition-height').val(audition.height);
                    $('#audition-weight').val(audition.weight);
                    $('#audition-emergency-contact').val(audition.emergency_contact);
                    $('#audition-relationship').val(audition.relationship);

                    // Image sources
                    $('#certificate-of-registration').attr('src', '/storage/' + audition
                        .certificate_of_registration);
                    $('#photo-copy-id').attr('src', '/storage/' + audition.photo_copy_id);

                    // Conditional rendering based on status
                    if (audition.status == 0) {
                        $('#other-file-row').show();
                        $('#parent-consent-row').hide();
                        $('#other-file').attr('src', '/storage/' + audition.other_file);
                    } else {
                        $('#other-file-row').hide();
                        $('#parent-consent-row').show();
                        $('#parent-consent').attr('src', '/storage/' + audition.parent_consent);
                    }

                    $('#audition-type').text(audition.type ? audition.type : '');
                    $('#audition-status').text(audition.status == 0 ? 'Pending' : 'Declined');
                })
        })
    })
</script>
@endpush