<?php
// Start session and check authentication
// require_once "../auth_stack.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log into Account</title>
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
            margin: 40px auto;
            position: relative;
        }

        .apply-btn {
            margin-top: 20px;
        }

        .blue {
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
                    <h2>Welcome Back to Dev<span class="blue">Hire</span> </h2>
                    <p>
                        Sign in and let’s get you closer to your next big hire or opportunity.
                    </p>
                </div>
            </div>
        </div>

        <div class="custom-dropdown">
            <div class="row mt-3">
                <div class="col-12 mt-3">
                    <input type="email" id="email" class="form-control p-3" placeholder="Email">
                </div>
                <div class="col-12 mt-3">
                    <div class="input-group">
                        <input type="password" id="password" class="form-control p-3" placeholder="Password">
                        <span class="input-group-text password-toggle" onclick="togglePassword()">
                            <i data-feather="eye" id="fea"></i>
                        </span>
                    </div>
                </div>
                <div class="col-12 text-end mt-2">
                    <p>Forget your <a href="forgotten">password?</a></p>
                </div>
            </div>
            <!-- Apply button -->
            <div class="apply-btn">
                <button class="btn btn-success w-100 p-3" onclick="login()">Login to devhire</button>
            </div>

            <hr>
            <p>
                Don't have an account? <a href="register">register</a>
            </p>
        </div>
    </section>



    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        feather.replace();

        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const icon = document.querySelector("#fea");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.dataset.feather = "eye-off";
            } else {
                passwordInput.type = "password";
                icon.dataset.feather = "eye";
            }
            feather.replace(); // re-render icon
        }

        function login() {
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value;

            // Email validation regex (basic)
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (email === "" || password === "") {
                Swal.fire({
                    toast: true,
                    icon: 'warning',
                    title: 'Please fill in all fields',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }

            if (!emailPattern.test(email)) {
                Swal.fire({
                    toast: true,
                    icon: 'error',
                    title: 'Enter a valid email address',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }

            // Prepare form data
            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);

            // Send AJAX request
            fetch('library/process_signin.php', {
                method: 'POST',
                body: formData
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
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        // Redirect to dashboard
                        window.location.href = 'dashboard';
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
            })
            .catch(error => {
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