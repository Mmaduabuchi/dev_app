<?php include_once "auth.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Sign In</title>
    <!-- Load Tailwind CSS from CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Load Inter font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Custom gradient for the soft background */
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(180deg, #F5F7FF 0%, #EEF2FF 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 16px; /* Ensure mobile padding on the outside */
        }
        /* Custom styles for the primary button effects */
        .btn-primary {
            transition: all 0.15s ease-out;
            transform-origin: center;
        }
        .btn-primary:hover {
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2), 0 2px 4px -2px rgba(37, 99, 235, 0.1);
            transform: translateY(-2px);
        }
        .btn-primary:active {
            transform: scale(0.98);
            box-shadow: none;
        }

        /* Input field styling (default, focus, invalid) */
        .input-group input {
            border-color: #E6E9F2; /* Default border color */
            transition: all 0.2s;
            height: 44px; /* Min tap target size */
            padding-left: 48px; /* Space for the icon */
        }
        .input-group input:focus {
            outline: none;
            border-width: 2px;
            border-color: #2563EB; /* Primary focus color */
            box-shadow: 0 0 0 1px #2563EB;
        }

        /* Initial card fade-in animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .card-animate {
            animation: fadeIn 0.3s ease-out forwards;
        }

        /* Button checkmark animation */
        .checkmark-path {
            stroke-dasharray: 1000;
            stroke-dashoffset: 1000;
            animation: dash 0.5s ease-in-out forwards;
        }
        @keyframes dash {
            to { stroke-dashoffset: 0; }
        }
        .success-bg {
            transition: background-color 0.3s ease;
        }
    </style>
</head>
<body class="text-gray-900">

    <div id="login-card" class="card-animate w-full max-w-sm sm:max-w-md p-6 bg-white rounded-xl shadow-xl transition duration-300">

        <!-- Branding Area -->
        <div class="flex items-center mb-6">
            <!-- Logo SVG Placeholder (Requested Asset) -->
            <svg class="w-10 h-10 p-2 mr-3 bg-blue-500 rounded-lg text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
            </svg>
            <h1 class="text-xl font-semibold">Devhire</h1>
        </div>

        <!-- Headline & Subtitle -->
        <h2 class="text-2xl font-bold mb-1 text-gray-800">Super Admin Sign In</h2>
        <p class="text-sm text-gray-500 mb-8">Enter your admin credentials to access the panel.</p>

        <form id="login-form">
            <!-- Email Input Group -->
            <div class="mb-6 input-group relative">
                <label for="email" class="block text-sm font-medium mb-1 text-gray-700">Email Address</label>
                <!-- Mail Icon (Requested Asset: Icon) -->
                <svg class="absolute left-3 top-1/2 mt-1 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                <input type="email" id="email" placeholder="name@company.com" class="w-full p-2.5 pl-12 rounded-lg border focus:ring-1 focus:ring-blue-500 transition-all duration-150" aria-label="Email Address" required>
                <p id="email-error" class="text-red-500 text-xs mt-1 hidden flex items-center">
                    <svg class="h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                    <span></span>
                </p>
            </div>

            <!-- Password Input Group -->
            <div class="mb-8 input-group relative">
                <label for="password" class="block text-sm font-medium mb-1 text-gray-700">Password</label>
                <!-- Lock Icon (Requested Asset: Icon) -->
                <svg class="absolute left-3 top-1/2 mt-1 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input type="password" id="password" placeholder="••••••••" class="w-full p-2.5 pl-12 pr-12 rounded-lg border focus:ring-1 focus:ring-blue-500 transition-all duration-150" aria-label="Password" required>

                <!-- Toggle Password Visibility Button -->
                <button type="button" id="toggle-password" class="absolute right-3 top-1/2 mt-1 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1" aria-label="Toggle password visibility">
                    <!-- Eye Icon (Requested Asset: Icon) -->
                    <svg id="eye-icon" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
                
                <p id="password-error" class="text-red-500 text-xs mt-1 hidden flex items-center">
                    <svg class="h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                    <span></span>
                </p>
            </div>

            <!-- Primary CTA -->
            <button type="submit" id="sign-in-btn" class="btn-primary w-full px-6 py-3 font-semibold text-white bg-blue-600 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 flex justify-center items-center">
                <span id="btn-text">Sign In</span>
                <!-- Checkmark SVG for success state -->
                <svg id="btn-check" class="h-6 w-6 text-white hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path class="checkmark-path" d="M20 6L9 17l-5-5"/>
                </svg>
            </button>
        </form>
    </div>



    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const form = document.getElementById('login-form');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const togglePasswordBtn = document.getElementById('toggle-password');
        const eyeIcon = document.getElementById('eye-icon');
        const signInBtn = document.getElementById('sign-in-btn');
        const btnText = document.getElementById('btn-text');
        const btnCheck = document.getElementById('btn-check');

        const primaryColor = '#2563EB';
        const errorColor = '#EF4444'; // Tailwind red-500

        //Helper to show/hide error message
        function setError(input, message) {
            const errorElement = document.getElementById(`${input.id}-error`);
            const errorSpan = errorElement.querySelector('span');
            const inputField = input;

            if (message) {
                inputField.style.borderColor = errorColor;
                inputField.style.borderWidth = '2px';
                inputField.classList.remove('focus:ring-blue-500');
                inputField.classList.add('focus:ring-red-500');
                errorSpan.textContent = message;
                errorElement.classList.remove('hidden');
            } else {
                inputField.style.borderColor = '#E6E9F2'; // Reset to default
                inputField.style.borderWidth = '1px';
                inputField.classList.add('focus:ring-blue-500');
                inputField.classList.remove('focus:ring-red-500');
                errorElement.classList.add('hidden');
                errorSpan.textContent = '';
            }
        }

        //Password Visibility Toggle
        togglePasswordBtn.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            // Toggle Eye Icon SVG (Eye/Eye-Off)
            eyeIcon.innerHTML = type === 'password'
                ? '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>' // Eye
                : '<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.56 13.56 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.37-1.62"/><line x1="2" x2="22" y1="2" y2="22"/>'; // Eye-Off
        });


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

        //Form Submission and Validation
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Reset all errors
            setError(emailInput, '');
            setError(passwordInput, '');

            let isValid = true;
            const emailValue = emailInput.value.trim();
            const passwordValue = passwordInput.value.trim();

            // Simple email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailValue) {
                setError(emailInput, 'Email address is required.');
                isValid = false;
            } else if (!emailRegex.test(emailValue)) {
                setError(emailInput, 'Please enter a valid email address.');
                isValid = false;
            }

            if (!passwordValue) {
                setError(passwordInput, 'Password is required.');
                isValid = false;
            }

            if (isValid) {
                // Disable button and show loading/processing state
                signInBtn.disabled = true;
                btnText.textContent = 'Signing In...';
                signInBtn.classList.add('opacity-80', 'cursor-not-allowed');
                signInBtn.classList.remove('bg-green-600', 'hover:bg-green-700', 'focus:ring-green-300');
                signInBtn.classList.add('bg-blue-600', 'hover:bg-blue-700', 'focus:ring-blue-300');
                btnCheck.classList.add('hidden');
                btnText.classList.remove('hidden');


                const formData = new FormData();
                formData.append('email', emailValue);
                formData.append('password', passwordValue);
                
                try {
                    const response = await fetch('<?php echo $base_url; ?>../../admin_login/process/process_login_admin.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.status === 'success') {
                        showToast('success', data.message || 'Login successful.');
                        // Simulate success (Design Hint: Subtle right-side check animation)
                        btnText.textContent = 'Success';
                        signInBtn.classList.add('success-bg', 'bg-green-600', 'hover:bg-green-700', 'focus:ring-green-300');
                        signInBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'focus:ring-blue-300');

                        // Show checkmark
                        btnText.classList.add('hidden');
                        btnCheck.classList.remove('hidden');

                        //redirect here after a small delay
                        setTimeout(() => { window.location.href = '/devhire/admin/dashboard/home'; }, 1000); // Redirect after success animation
                    } else {
                        showToast('error', data.message || 'Failed to login.');
                    }
                } catch (error) {
                    console.error(error);
                    showToast('error', 'An error occurred while logging in.');
                } finally {
                    // Reset button state if an error occurred
                    if (btnText.textContent !== 'Success') { // Only reset if not in success state
                        signInBtn.disabled = false;
                        btnText.textContent = 'Sign In';
                        signInBtn.classList.remove('opacity-80', 'cursor-not-allowed');
                    }
                }
            }
        });

        // Reset error state on input change/focus
        emailInput.addEventListener('input', () => setError(emailInput, ''));
        passwordInput.addEventListener('input', () => setError(passwordInput, ''));

    </script>
</body>
</html>