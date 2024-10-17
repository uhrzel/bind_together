@extends('layouts.app')

@section('content')
    <div class="main py-4">
        <div class="card card-body border-0 shadow table-wrapper table-responsive">
            <div class="row">
                <div class="col">
                    <h2 class="mb-4 h5" style="text-transform: uppercase">{{ ucfirst(str_replace('_', ' ', $role)) }}</h2>
                </div>
                <div class="col text-end">
                    <!-- Trigger the Create Modal -->
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">Add New</button>
                </div>
            </div>
            <table id="datatable" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="border-gray-200">{{ __('Avatar') }}</th>
                        <th class="border-gray-200">{{ __('Name') }}</th>
                        <th class="border-gray-200">{{ __('Gender') }}</th>
                        <th class="border-gray-200">{{ __('Email') }}</th>
                        <th class="border-gray-200">{{ __('Role') }}</th>
                        <th class="border-gray-200">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td class="text-center">
                                <img class="rounded-circle" src="{{ asset('images/avatar/avatar.png') }}" alt="avatar"
                                    height="30">
                            </td>
                            <td><span class="fw-normal">{{ $user->firstname }} {{ $user->lastname }}</span></td>
                            <td><span class="fw-normal">{{ $user->gender }}</span></td>
                            <td><span class="fw-normal">{{ $user->email }}</span></td>
                            <td><span
                                    class="fw-normal">{{ ucfirst(str_replace('_', ' ', $user->getRoleNames()->first())) }}</span>
                            </td>
                            <td class="row">
                                @if ($user->is_active != 0)
                                    <button class="col-4 btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#deleteUserModal"
                                        onclick="deleteUser({{ $user->id }})">Deactivate</button>
                                @else
                                    <button class="col-4 btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#activateUserModal"
                                        onclick="activateUser({{ $user->id }})">Activate</button>
                                @endif
                                <button class="col-3 mx-3 btn btn-secondary" data-bs-toggle="modal"
                                    data-bs-target="#viewUserModal" onclick="viewUser({{ $user->id }})">View</button>
                                <button class="col-3 btn btn-info" data-bs-toggle="modal" data-bs-target="#editUserModal"
                                    onclick="editUser({{ $user->id }})">Edit</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create User Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createUserModalLabel">New Superadmin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="firstname" class="form-label">First Name</label>
                                <input type="text" class="form-control" name="firstname" placeholder="Enter First Name"
                                    required>
                                @error('firstname')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="middlename" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" name="middlename" placeholder="Enter Middle Name"
                                    required>
                                @error('middlename')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="lastname" class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="lastname" placeholder="Enter Last Name"
                                    required>
                                @error('lastname')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="suffix" class="form-label">Suffix</label>
                                <input type="text" class="form-control" name="suffix"
                                    placeholder="Enter Suffix (e.g., Jr, Sr)" required>
                                @error('suffix')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="birthdate" class="form-label">Birthday</label>
                                <input type="date" class="form-control" name="birthdate" placeholder="DD/MM/YYYY"
                                    required>
                                @error('birthdate')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" name="gender" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                                @error('gender')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <input type="hidden" name="role" value="{{ $role }}">
                        </div>
                        <div class="mt-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" placeholder="Enter Email"
                                required>
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mt-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Enter Password"
                                required>
                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mt-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="password_confirmation"
                                placeholder="Retype Password" required>
                            @error('password_confirmation')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editUserForm" action="#" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Edit Superadmin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editUserId" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="editFirstname" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="editFirstname" name="firstname"
                                    placeholder="Enter First Name" required>
                                @error('firstname')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="editMiddlename" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="editMiddlename" name="middlename"
                                    placeholder="Enter Middle Name" required>
                                @error('middlename')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="editLastname" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="editLastname" name="lastname"
                                    placeholder="Enter Last Name" required>
                                @error('lastname')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="editSuffix" class="form-label">Suffix</label>
                                <input type="text" class="form-control" id="editSuffix" name="suffix"
                                    placeholder="Enter Suffix (E.G., Jr, Sr)" required>
                                @error('suffix')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="editBirthdate" class="form-label">Birthday</label>
                                <input type="date" class="form-control" id="editBirthdate" name="birthdate"
                                    placeholder="Enter Birthdate" required>
                                @error('birthdate')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="editGender" class="form-label">Gender</label>
                                <select class="form-select" id="editGender" name="gender" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                                @error('gender')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class=" mt-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email"
                                placeholder="Enter Email" required>
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class=" mt-3">
                            <label for="editPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="editPassword" name="password"
                                placeholder="Enter New Password (if changing)">
                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class=" mt-3">
                            <label for="editPasswordConfirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="editPasswordConfirmation"
                                name="password_confirmation" placeholder="Confirm Password (if changing)">
                            @error('password_confirmation')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View User Modal -->
    <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewUserModalLabel">View Superadmin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="viewUserId" name="id">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="viewFirstname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="viewFirstname" name="firstname" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="viewMiddlename" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="viewMiddlename" name="middlename" readonly>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label for="viewLastname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="viewLastname" name="lastname" readonly>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label for="viewSuffix" class="form-label">Suffix</label>
                            <input type="text" class="form-control" id="viewSuffix" name="suffix" readonly>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label for="viewBirthdate" class="form-label">Birthday</label>
                            <input type="date" class="form-control" id="viewBirthdate" name="birthdate" readonly>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label for="viewGender" class="form-label">Gender</label>
                            <input type="text" class="form-control" id="viewGender" name="gender" readonly>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="viewEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="viewEmail" name="email" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Delete User Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteUserForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteUserModalLabel">Deactivate Superadmin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to deactivate this user?</p>
                        <input type="hidden" id="deleteUserId" name="id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Deactivate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Activate User Modal -->
    <div class="modal fade" id="activateUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="activateUserForm" action="" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteUserModalLabel">Activate Superadmin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to activate this user?</p>
                        <input type="hidden" id="deleteUserId" name="id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Activate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable();
        });

        // Define the editUser function globally so it's accessible on button click
        function editUser(id) {
            $.get('/users/' + id, function(user) {
                $('#editUserId').val(user.id);
                $('#editFirstname').val(user.firstname);
                $('#editMiddlename').val(user.middlename);
                $('#editLastname').val(user.lastname);
                $('#editSuffix').val(user.suffix);
                const birthdate = user.birthdate.split(' ')[0]; // Extracts "2024-05-03"
                $('#editBirthdate').val(birthdate);
                $('#editEmail').val(user.email);
                $('#editGender').val(user.gender);

                $('#editPassword').val('');
                $('#editPasswordConfirmation').val('');

                $('#editUserForm').attr('action', '/users/' + id);
            });
        }

        function viewUser(id) {
            $.get('/users/' + id, function(user) {
                $('#viewUserId').val(user.id);
                $('#viewFirstname').val(user.firstname);
                $('#viewMiddlename').val(user.middlename);
                $('#viewLastname').val(user.lastname);
                $('#viewSuffix').val(user.suffix);
                const birthdate = user.birthdate.split(' ')[0]; // Extracts the date part
                $('#viewBirthdate').val(birthdate);
                $('#viewEmail').val(user.email);
                $('#viewGender').val(user.gender);

                // Show the modal
                $('#viewUserModal').modal('show');
            });
        }

        function deleteUser(id) {
            $('#deleteUserForm').attr('action', '/users/' + id);
        }

        function activateUser(id) {
            $('#activateUserForm').attr('action', '/users/activate/' + id);
        }
    </script>
@endpush
