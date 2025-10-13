<?php
// Start session and check authentication
require_once "auth.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgotten passowrd</title>
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
    .blue{
        color: #0818A8;
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
                    <h2>Reset Your Password!</h2>
                    <p>
                        Don’t stress, it happens. We’ll help you get back into your account in no time.
                    </p>
                </div>
            </div>
        </div>

        <div class="custom-dropdown">
            <div class="row mt-3">
                <div class="col-12 mt-3">
                    <input type="email" id="email" class="form-control p-3" placeholder="Email">
                </div>
            </div>
            <!-- Apply button -->
            <div class="apply-btn">
                <button class="btn btn-success w-100 p-3" onclick="forgotten()">Submit to devhire</button>
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
        function forgotten(){
            const email = document.getElementById("email").value.trim();

            // Email validation regex (basic)
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (email === "") {
                Swal.fire({
                    toast: true,
                    icon: 'warning',
                    title: 'Please enter your email address.',
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
            // Send data to server
            fetch('library/process_forgotten.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email: email })
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
                    title: 'An error occurred. Please try again later.',
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
