@extends('layouts.app')

@section('content')
    <div class="main py-4">
        <div class="row">
            <div class="col-12 col-xl-12">
                <div class="card card-body border-0 shadow mb-4">
                    <h2 class="h5 mb-4">{{ __('My profile') }}</h2>
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <h5 class="mb-3 text-primary">Basic Information</h5>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="firstname">{{ 'First Name' }}</label>
                                <input type="text" name="firstname" id="firstname" class="form-control"
                                    value="{{ old('firstname', auth()->user()->firstname) }}" placeholder="First Name"
                                    required>
                                @error('firstname')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="middlename">{{ 'Middle Name' }}</label>
                                <input type="text" name="middlename" id="middlename" class="form-control"
                                    value="{{ old('middlename', auth()->user()->middlename) }}" placeholder="Middle Name">
                                @error('middlename')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="lastname">{{ 'Last Name' }}</label>
                                <input type="text" name="lastname" id="lastname" class="form-control"
                                    value="{{ old('lastname', auth()->user()->lastname) }}" placeholder="Last Name"
                                    required>
                                @error('lastname')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="suffix">{{ 'Suffix' }}</label>
                                <input type="text" name="suffix" id="suffix" class="form-control"
                                    value="{{ old('suffix', auth()->user()->suffix) }}" placeholder="Suffix (optional)"
                                    nullable>
                                @error('suffix')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="birthdate">{{ 'Date of Birth' }}</label>
                                <input type="date" name="birthdate" id="birthdate" class="form-control"
                                    value="{{ old('birthdate', auth()->user()->birthdate) }}" placeholder="Date of Birth"
                                    required>
                                @error('birthdate')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="gender">{{ 'Gender' }}</label>
                                <select name="gender" id="gender" class="form-select" required>
                                    <option value="male" {{ auth()->user()->gender == 'male' ? 'selected' : '' }}>Male
                                    </option>
                                    <option value="female" {{ auth()->user()->gender == 'female' ? 'selected' : '' }}>
                                        Female</option>
                                    <option value="other" {{ auth()->user()->gender == 'other' ? 'selected' : '' }}>Other
                                    </option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="address">{{ 'Address' }}</label>
                                <input type="text" name="address" id="address" class="form-control"
                                    value="{{ old('address', auth()->user()->address) }}" placeholder="Address" nullable>
                                @error('address')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Avatar Update -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="avatar">{{ 'Profile Picture (optional)' }}</label>
                                <input type="file" name="avatar" id="avatar" class="form-control">
                                @error('avatar')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="current_avatar">{{ 'Current Avatar' }}</label><br>
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Current Avatar"
                                    class="img-thumbnail" width="120">
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <h5 class="mb-3 text-primary">Contact Information</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contact">{{ 'Contact Number' }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">+63</span>
                                    <input type="text" name="contact" id="contact" class="form-control"
                                        value="{{ old('contact', auth()->user()->contact) }}" placeholder="9359434344"
                                        required>
                                    @error('contact')
                                        <div class="invalid-feedback"> {{ $message }} </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email">{{ 'Email' }}</label>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="{{ old('email', auth()->user()->email) }}" placeholder="Email Address"
                                    required readonly>
                                @error('email')
                                    <div class="invalid-feedback"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>

                        <h5 class="my-3 text-primary">Password Update</h5>

                        <div class="row">
                            <!-- Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password">{{ 'Password' }}</label>
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="New Password">
                                <div id="passwordError" class="invalid-feedback" style="display: block;"></div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation">{{ 'Confirm Password' }}</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" placeholder="Confirm Password" required>
                                <div id="confirmPasswordError" class="invalid-feedback" style="display: block;"></div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-3">
                            <button type="submit" class="btn btn-danger">{{ __('Submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @if ($message = Session::get('success'))
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'success',
                text: '{{ $message }}',
            })
        </script>
    @endif
@endsection

@push('scripts')
    <script>
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

        $('#password').on('input', function() {
            const password = $(this).val();
            const passwordError = $('#passwordError');
            if (!passwordRegex.test(password)) {
                passwordError.text(
                    "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character."
                );
                $(this).addClass('is-invalid');
            } else {
                passwordError.text("");
                $(this).removeClass('is-invalid');
            }
        });

        $('#password_confirmation').on('input', function() {
            const password = $('#password').val();
            const confirmPassword = $(this).val();
            const confirmPasswordError = $('#confirmPasswordError');
            if (password !== confirmPassword) {
                confirmPasswordError.text("Passwords do not match.");
                $(this).addClass('is-invalid');
            } else {
                confirmPasswordError.text("");
                $(this).removeClass('is-invalid');
            }
        });
    </script>
@endpush
