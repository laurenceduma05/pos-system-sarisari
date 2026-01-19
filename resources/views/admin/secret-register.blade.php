<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/adminlte.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-container {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }

        .form-section {
            margin-bottom: 25px;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .form-section-title {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 15px !important;
        }

        .form-section-title i {
            margin-right: 8px;
            color: #007bff;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .input-group {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
        }

        .input-group .form-control {
            border: none;
            padding: 10px 12px;
            font-size: 14px;
        }

        .input-group .form-control:focus {
            box-shadow: none;
            border: none;
        }

        .input-group-text {
            background: #f8f9fa;
            border: none;
            color: #6c757d;
            padding: 10px 12px;
        }

        .input-group:focus-within .input-group-text {
            background: #007bff;
            color: white;
        }

        .form-section-title {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="#"><b>Admin</b> Registration</a>
        </div>

        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Create a new administrator account</p>

                <div class="alert alert-success" id="successMessage" style="display: none;">
                    <i class="fas fa-check"></i> <span id="successText"></span>
                </div>

                <form id="registrationForm" method="POST" action="{{ route('admin.secret.register') }}">
                    @csrf

                    <!-- Form Fields Container -->
                    <div class="form-container">
                        <!-- Personal Information Section -->
                        <div class="form-section">
                            <h6 class="form-section-title mb-3">
                                <i class="fas fa-user-circle"></i> Personal Information
                            </h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="firstName" name="firstName" placeholder="First Name" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-user"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-danger d-block mt-1" id="firstNameError"></small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Last Name" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-user"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-danger d-block mt-1" id="lastNameError"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="input-group">
                                    <select class="form-control" id="sex" name="sex" required>
                                        <option value="">-- Select Sex --</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-venus-mars"></span>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-danger d-block mt-1" id="sexError"></small>
                            </div>
                        </div>

                        <!-- Account Information Section -->
                        <div class="form-section">
                            <h6 class="form-section-title mb-3">
                                <i class="fas fa-lock"></i> Account Information
                            </h6>

                            <div class="form-group">
                                <div class="input-group">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-envelope"></span>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-danger d-block mt-1" id="emailError"></small>
                            </div>

                            <div class="form-group">
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-lock"></span>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-danger d-block mt-1" id="passwordError"></small>
                            </div>

                            <div class="form-group">
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-lock"></span>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-danger d-block mt-1" id="confirmPasswordError"></small>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-user-plus"></i> Register Admin
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-3 text-center">
                    <a href="{{ route('login') }}" class="text-muted">Back to Login</a>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/adminlte.min.js"></script>

    <script>
        const form = document.getElementById('registrationForm');
        const successMessage = document.getElementById('successMessage');
        const successText = document.getElementById('successText');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Clear previous errors
            document.querySelectorAll('small.text-danger').forEach(el => el.textContent = '');

            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify(data)
                });

                if (!response.ok) {
                    const errors = await response.json();

                    if (errors.errors) {
                        Object.keys(errors.errors).forEach(field => {
                            const errorElement = document.getElementById(field + 'Error');
                            if (errorElement) {
                                errorElement.textContent = errors.errors[field][0];
                            }
                        });
                    }
                    return;
                }

                const result = await response.json();
                successText.textContent = result.message;
                successMessage.style.display = 'block';
                form.reset();

                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            }
        });
    </script>
</body>
</html>
