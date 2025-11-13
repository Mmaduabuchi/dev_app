<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Login | DevHire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {

            background: #AA076B;
            /* fallback for old browsers */
            background: -webkit-linear-gradient(to right, #61045F, #AA076B);
            /* Chrome 10-25, Safari 5.1-6 */
            background: linear-gradient(to right, #61045F, #AA076B);
            /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }

        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 40px 30px;
            width: 100%;
            max-width: 420px;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card h4 {
            font-weight: 600;
            color: #333;
        }

        .login-card p {
            color: #777;
            font-size: 14px;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px;
        }

        .btn-login {
            background: linear-gradient(135deg, #007bff, #6610f2);
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 10px;
            padding: 12px;
            transition: 0.3s ease;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #6610f2, #007bff);
            transform: translateY(-2px);
        }

        .logo-circle {
            background: linear-gradient(135deg, #007bff, #6610f2);
            color: #fff;
            border-radius: 50%;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: bold;
            margin: 0 auto 15px auto;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="text-center">
            <div class="logo-circle">DH</div>
            <h4>Super Admin Login</h4>
            <p>Welcome back! Please log in to continue.</p>
        </div>

        <form method="POST" id="admin_login_form" action="#" class="mt-4 position-relative">
            <div class="mb-3 position-relative">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
            </div>

            <div class="mb-3 position-relative">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                <span class="toggle-password" onclick="togglePassword()">
                    <i class="bi bi-eye-slash" id="toggleIcon"></i>
                </span>
            </div>

            <button type="submit" class="btn btn-login w-100">Login</button>
        </form>
    </div>

    <!-- Bootstrap JS + Icons -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function togglePassword() {
            const password = document.getElementById("password");
            const icon = document.getElementById("toggleIcon");
            if (password.type === "password") {
                password.type = "text";
                icon.classList.replace("bi-eye-slash", "bi-eye");
            } else {
                password.type = "password";
                icon.classList.replace("bi-eye", "bi-eye-slash");
            }
        }


        function showToast(icon, message) {
            if (typeof Swal !== 'undefined') {
                Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true
                }).fire({
                    icon,
                    title: message
                });
            } else {
                alert(message);
            }
        }

        document.querySelector("#admin_login_form").addEventListener("submit", async function(e) {
            e.preventDefault();
            const email = document.querySelector("input[name='email']").value.trim();
            const password = document.querySelector("input[name='password']").value.trim();

            if (!email || !password) return showToast('warning', 'All fields are required.');

            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);

            try {
                const response = await fetch('<?php echo $base_url; ?>../../admin_login/process/process_login_admin.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.status === 'success') {
                    showToast('success', data.message || 'Login successful.');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showToast('error', data.message || 'Failed to login.');
                }
            } catch (error) {
                console.error(error);
                showToast('error', 'An error occurred while logging in.');
            }

        });
    </script>

</body>

</html>