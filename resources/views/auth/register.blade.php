@extends('layouts.guest')

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

    <div class="container mb-5">
        <div class="card shadow">
            <div class="card-header bg-white">
                <h3 class="text-center">Registration</h3>
                <p class="text-center">Fill in the required fields below</p>
            </div>
            <div class="card-body">
                <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <h5 class="mb-3 text-muted">Basic Information</h5>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="student_number" class="form-label">Student number</label>
                            <input type="text" class="form-control" id="student_number" name="student_number"
                                placeholder="ex: 21-00000" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="firstname" class="form-label">First name</label>
                            <input type="text" class="form-control" id="firstname" name="firstname"
                                placeholder="First name" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="middlename" class="form-label">Middle name</label>
                            <input type="text" class="form-control" id="middlename" name="middlename"
                                placeholder="Middle name">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="lastname" class="form-label">Last name</label>
                            <input type="text" class="form-control" id="lastname" name="lastname"
                                placeholder="Last name" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label for="suffix" class="form-label">Suffix</label>
                            <input type="text" class="form-control" id="suffix" name="suffix"
                                placeholder="Enter suffix (e.g., Jr, Sr)">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="" disabled selected>Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <h5 class="mb-3 text-muted">Contact Information</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contact_number" class="form-label">Contact number</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="contact_number" name="contact"
                                    placeholder="09123456789" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="University email address" required>
                        </div>
                    </div>

                    <h5 class="mb-3 text-muted">Account Password</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Password" required>
                                <span class="input-group-text">
                                    <i class="far fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label">Confirm password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirm_password"
                                    name="password_confirmation" placeholder="Retype password" required>
                                <span class="input-group-text">
                                    <i class="far fa-eye" id="toggleConfirmPassword" style="cursor: pointer;"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3 text-muted">Profile Picture</h5>
                    <div class="mb-3">
                        <label for="profile" class="form-label">Profile</label>
                        <input class="form-control" type="file" id="profile" name="profile" accept="image/*">
                        <div class="form-text">Max. 5MB</div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">I agree to the Terms and Conditions</label>
                    </div>

                    <div class="text-center">
                        <button type="submit" style="background: #8B0000"
                            class="btn btn-transparent text-white w-25">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordField = document.getElementById('password');
        const icon = this;
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const passwordField = document.getElementById('confirm_password');
        const icon = this;
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>