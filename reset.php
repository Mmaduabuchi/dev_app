<?php
// Start session and check authentication
require_once "auth.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create a new password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://unpkg.com/feather-icons"></script>
  <style>
    body {
      background-color: #f8f9fa;
      padding: 0;
      margin: 0;
    }
    .custom-dropdown {
      max-width: 400px;
      margin: 30px auto;
      position: relative;
    }
    .apply-btn {
      margin-top: 20px;
    }
    .blue{
        color: #0818A8;
    }
    .password-toggle {
      cursor: pointer;
      padding: 0 10px;
    }
  </style>
</head>
<body>

    <section>
        <div class="container">
            <div class="row border-bottom p-3 pb-1">
                <div class="col">
                    <h2>dev<span class="blue">hire </span></h2>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <h2>Create a New Password!</h2>
                    <p>
                        Set a strong password to secure your account.
                    </p>
                </div>
            </div>
        </div>

        <div class="custom-dropdown">
            <div class="row mt-3">
                <div class="col-12 mt-3"> 
                    <div class="input-group">
                        <input type="password" id="password" class="form-control p-3" placeholder="New password">
                        <span class="input-group-text password-toggle" onclick="togglePassword('password','icon1')">
                            <i data-feather="eye" id="icon1"></i>
                        </span>
                    </div>
                </div>
                <div class="col-12 mt-3"> 
                    <div class="input-group">
                        <input type="password" id="confirmpassword" class="form-control p-3" placeholder="Confirm new password">
                        <span class="input-group-text password-toggle" onclick="togglePassword('confirmpassword','icon2')">
                            <i data-feather="eye" id="icon2"></i>
                        </span>
                    </div>
                </div>
            </div>
            <!-- Apply button -->
            <div class="apply-btn">
                <button class="btn btn-success w-100 p-3" onclick="resetPassword()">Reset devhire password</button>
            </div>

            <hr>
            <p>
                Are you done? <a href="login">login</a>
            </p>
        </div>
    </section>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        feather.replace();

        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === "password") {
                input.type = "text";
                icon.dataset.feather = "eye-off";
            } else {
                input.type = "password";
                icon.dataset.feather = "eye";
            }
            feather.replace(); // re-render icons
        }

        function resetPassword(){
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirmpassword").value;

            if (password === "" || confirmPassword === "") {
                Swal.fire({
                    toast: true,
                    icon: 'warning',
                    title: 'Please fill in both fields',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }

            if (password !== confirmPassword) {
                Swal.fire({
                    toast: true,
                    icon: 'error',
                    title: 'Passwords do not match',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }

            if (password.length < 6) {
                Swal.fire({
                    toast: true,
                    icon: 'error',
                    title: 'Password must be at least 6 characters long',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }

            // Extract token from URL
            const urlParams = new URLSearchParams(window.location.search);
            const token = urlParams.get('token');
            if (!token) {
                Swal.fire({
                    toast: true,
                    icon: 'error',
                    title: 'Invalid or missing token',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }
            // Send data to server
            fetch('library/process_reset.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ token: token, password: password, confirmPassword: confirmPassword })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        toast: true,
                        icon: 'success',
                        title: data.message,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => {
                        // Redirect to login
                        window.location.href = 'login';
                    });
                } else {
                    Swal.fire({
                        toast: true,
                        icon: 'error',
                        title: data.message,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
            }).catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    toast: true,
                    icon: 'error',
                    title: 'An error occurred. Please try again.',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        }
    </script>
</body>
</html>
