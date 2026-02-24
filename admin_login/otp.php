<?php 

include_once "auth.php"; 

if (!isset($_SESSION['otp_user_id'])) {
    header("Location: /devhire/admin/log/login");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .input-style {
            @apply w-12 h-12 text-center text-lg font-semibold border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-400;
        }
    </style>
</head>
<body class="bg-slate-200 min-h-screen flex items-center justify-center">

    <div class="bg-white w-[350px] md:w-[400px] lg:w-[500px] max-w-sm rounded-2xl shadow-xl p-8 text-center">

        <!-- Icon -->
        <div class="flex justify-center mb-6">
            <div class="relative">
                <div class="w-16 h-16 bg-slate-100 rounded-xl flex items-center justify-center shadow">
                    📱
                </div>
                <div class="absolute -right-2 -top-2 bg-sky-500 text-white text-xs px-2 py-1 rounded-full shadow">
                    💬
                </div>
            </div>
        </div>

        <!-- Title -->
        <h2 class="text-lg font-semibold text-gray-800">
            Account Verification
        </h2>
        <p class="text-sm text-gray-500 mt-1 mb-6">
            Enter Verify Code Below
        </p>

        <form id="otpForm">

            <!-- OTP Inputs -->
            <div class="flex justify-between gap-2 mb-6" id="otpContainer">
                <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-lg font-semibold border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-400">
                <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-lg font-semibold border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-400">
                <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-lg font-semibold border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-400">
                <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-lg font-semibold border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-400">
                <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-lg font-semibold border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-400">
            </div>

            <!-- Verify Button -->
            <button id="verifyBtn"  class="w-full bg-sky-500 hover:bg-sky-600 text-white font-medium py-3 rounded-lg transition flex items-center justify-center gap-2">
                <span id="btnText">Verify Code</span>
                <svg id="spinner" class="hidden animate-spin h-5 w-5" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="white" stroke-width="4"></circle>
                    <path class="opacity-75" fill="white" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </button>

            <!-- Resend -->
            <button id="resendBtn" class="mt-4 text-sm text-gray-400 cursor-not-allowed">
                Resend Code (<span id="countdown">60</span>s)
            </button>

            <p id="errorMessage" class="text-red-500 text-sm mt-4 hidden"></p>

        </form>

    </div>

    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const inputs = document.querySelectorAll(".otp-input");
            const form = document.getElementById("otpForm");
            const verifyBtn = document.getElementById("verifyBtn");
            const spinner = document.getElementById("spinner");
            const btnText = document.getElementById("btnText");
            const errorMessage = document.getElementById("errorMessage");
            const resendBtn = document.getElementById("resendBtn");
            const countdownEl = document.getElementById("countdown");

            // Auto Move + Auto Submit
            inputs.forEach((input, index) => {

                input.addEventListener("input", (e) => {
                    const value = e.target.value;

                    // Validate input
                    if (!/^[0-9]$/.test(value)) {
                        e.target.value = "";
                        return;
                    }

                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    } else {
                        submitOTP(); // Auto-submit when last digit entered
                    }
                });

                input.addEventListener("keydown", (e) => {
                    if (e.key === "Backspace" && input.value === "" && index > 0) {
                        inputs[index - 1].focus();
                    }
                });
            });

            // Shake Inputs
            function shakeInputs() {
                inputs.forEach(input => {
                    input.classList.add("border-red-500", "animate-bounce");
                    setTimeout(() => {
                        input.classList.remove("border-red-500", "animate-bounce");
                    }, 500);
                });
            }

            // Submit OTP
            function submitOTP() {

                let otp = "";
                inputs.forEach(input => otp += input.value);

                if (otp.length !== inputs.length) return;

                // Show loading
                spinner.classList.remove("hidden");
                btnText.textContent = "Verifying...";
                verifyBtn.disabled = true;

                fetch("../../admin_login/process/process_verify_login_otp_admin.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ otp: otp })
                })
                .then(res => res.json())
                .then(data => {

                    spinner.classList.add("hidden");
                    btnText.textContent = "Verify Code";
                    verifyBtn.disabled = false;

                    if (data.status === "success") {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: "success",
                            title: "Success",
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500);
                    } else {
                        errorMessage.textContent = data.message;
                        errorMessage.classList.remove("hidden");
                        shakeInputs();
                    }

                })
                .catch(() => {
                    errorMessage.textContent = "Network error. Try again.";
                    errorMessage.classList.remove("hidden");
                });
            }

            form.addEventListener("submit", function (e) {
                e.preventDefault();
                submitOTP();
            });

            // Resend OTP
            resendBtn.addEventListener("click", function () {

                if (resendBtn.disabled) return;

                fetch("../../admin_login/process/process_resend_login_otp_admin.php", {
                    method: "POST"
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: "success",
                            title: "Success",
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        startCountdown(data.expires_in);
                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: "error",
                            title: "Error",
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });


            function startCountdown(seconds) {

                resendBtn.disabled = true;
                resendBtn.classList.add("text-gray-400", "cursor-not-allowed");

                let timeLeft = seconds;
                countdownEl.textContent = timeLeft;

                const timer = setInterval(() => {
                    timeLeft--;
                    countdownEl.textContent = timeLeft;

                    if (timeLeft <= 0) {
                        clearInterval(timer);
                        resendBtn.disabled = false;
                        resendBtn.textContent = "Resend Code";
                        resendBtn.classList.remove("text-gray-400", "cursor-not-allowed");
                        resendBtn.classList.add("text-sky-600", "hover:underline");
                    }

                }, 1000);
            }

            // Start initial 60s countdown
            startCountdown(60);

        });
    </script>
</body>
</html>