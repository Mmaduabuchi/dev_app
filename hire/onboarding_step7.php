<?php
//database connection
require_once __DIR__ . '/../config/databaseconnection.php';
//require auth_hire
require_once 'auth_hire.php';
require_once 'config.php';

$data = 6;

try{
    //fetch user process data/stage
    $stmt = $conn->prepare("SELECT id FROM `onboarding_sessions` WHERE onboarding_id = ? AND step_number = ?");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("si", $onboarding_id, $data);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows < 1) {
        $stmt->close();
        $conn->close();
        header("location: {$root_url}");
        exit;
    }

} catch (Exception $e) {
    error_log($e->getMessage());
    echo "Something went wrong. Please try again later.";
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devhire Onboarding - Step 1</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #f9fafc;
            font-family: "Inter", sans-serif;
        }

        .info-box {
            background-color: #eef3ff;
            border-radius: 6px;
            padding: 15px 20px;
            color: #344767;
            font-size: 15px;
        }

        .step-title {
            font-size: 14px;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .main-question {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 25px;
        }

        .option-card {
            background: #fff;
            border: 1px solid #e4e6ef;
            border-radius: 10px;
            padding: 18px 22px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .option-card:hover {
            border-color: #4f46e5;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.1);
        }

        .option-text {
            flex: 1;
        }

        .option-text h6 {
            margin: 0;
            font-weight: 600;
        }

        .option-text small {
            color: #6c757d;
        }

        .option-card:hover .arrow-icon {
            transform: translateX(4px);
            color: #4f46e5;
        }

        .btn-outlined-primary {
            border: 2px solid #4f46e5;
            color: #4f46e5;
            background: transparent;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-outlined-primary:hover {
            background: #4f46e5;
            color: #fff;
        }


        @media (max-width: 768px) {
            .main-question {
                font-size: 20px;
            }
        }

        .icon {
            cursor: pointer;
        }

        #password-rules {
            list-style: none;
            padding-left: 0;
        }

        #password-rules li {
            margin: 3px 0;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">

                <p class="step-title"> <i class="bi bi-arrow-left-circle icon"></i> STEP 7</p>
                <h3 class="main-question">Success! Let's connect you with talent.</h3>

                <!-- Option 1 -->
                <div class="option-card">
                    <label for="">Enter Company Email</label>
                    <input type="email" class="form-control CompanyEmail" placeholder="myname@company.com">
                </div>

                <!-- Option 2 -->
                <div class="option-card">
                    <label for="">Enter Company Name</label>
                    <input type="text" class="form-control CompanyName" placeholder="Company Name">
                </div>

                <!-- Option 3 -->
                <div class="option-card">
                    <label for="">Enter Contact Name</label>
                    <input type="text" class="form-control CompanyContactName" placeholder="Contact Name">
                </div>

                <!-- Option 4 -->
                <div class="option-card">
                    <label for="">Enter Phone Number</label>
                    <input type="tel" class="form-control CompanyPhoneNumber" placeholder="Phone Number">
                </div>

                <!-- Option 5 -->
                <div class="option-card">
                    <label for="PasswordInput">Enter Password</label>
                    <div class="input-group">
                        <input type="password" id="PasswordInput" class="form-control Password" placeholder="*******" />
                        <button class="btn btn-outline-secondary toggle-password" type="button" aria-label="Toggle password visibility" data-target="PasswordInput">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <!-- Password Requirements Info -->
                    <small class="form-text text-muted mt-2">
                        Password must be at least <strong>8 characters</strong> long, and include at least:
                        <ul id="password-rules" class="mb-0 mt-1">
                            <li id="rule-length">❌ At least 8 characters</li>
                            <li id="rule-uppercase">❌ One uppercase letter (A–Z)</li>
                            <li id="rule-lowercase">❌ One lowercase letter (a–z)</li>
                            <li id="rule-number">❌ One number (0–9)</li>
                            <li id="rule-special">❌ One special character (@, #, $, %, etc.)</li>
                            <li id="rule-space">❌ No spaces</li>
                        </ul>
                    </small>
                </div>

                <!-- Option 6 -->
                <div class="option-card">
                    <label for="ConfirmPasswordInput">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" id="ConfirmPasswordInput" class="form-control ConfirmPassword" placeholder="*******" />
                        <button class="btn btn-outline-secondary toggle-password" type="button" aria-label="Toggle confirm password visibility" data-target="ConfirmPasswordInput">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Option 7 -->
                <div class="option-card">
                    <button type="submit" class="btn btn-outlined-primary px-5 py-3 submitBtn">Connect Me With Talents</button>
                </div>

                <div>
                    <p>
                        By completing signup, you are agreeing to Devhire's Terms of Service, Privacy Policy, Sourced Talent Matching Agreement, and that audio or video meetings made through Toptal's systems may be recorded or monitored for quality assurance, training, and compliance purposes or for your convenience.
                    </p>
                </div>
            </div>
        </div>
    </div>


    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const submitBtn = document.querySelector(".submitBtn");
            submitBtn.addEventListener("click", () => {
                const CompanyEmail = document.querySelector(".CompanyEmail").value.trim();
                const CompanyName = document.querySelector(".CompanyName").value.trim();
                const CompanyContactName = document.querySelector(".CompanyContactName").value.trim();
                const CompanyPhoneNumber = document.querySelector(".CompanyPhoneNumber").value.trim();
                const Password = document.querySelector(".Password").value.trim();
                const ConfirmPassword = document.querySelector(".ConfirmPassword").value.trim();
                let step_number = 7;

                //validate user inputs
                if (!CompanyEmail || !CompanyName || !CompanyContactName || !CompanyPhoneNumber || !Password || !ConfirmPassword) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'All inputs fields are required.',
                        showConfirmButton: false,
                        timer: 2500
                    });
                    return;
                }

                //validate user email format
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(CompanyEmail)) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Please enter a valid email address.',
                        showConfirmButton: false,
                        timer: 2500
                    });
                    return;
                }

                //validate password
                if (Password !== ConfirmPassword) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Password do not match.',
                        showConfirmButton: false,
                        timer: 2500
                    });
                    return;
                }

                const formData = new FormData();
                formData.append("CompanyEmail", CompanyEmail);
                formData.append("CompanyName", CompanyName);
                formData.append("CompanyContactName", CompanyContactName);
                formData.append("CompanyPhoneNumber", CompanyPhoneNumber);
                formData.append("Password", Password);
                formData.append("ConfirmPassword", ConfirmPassword);
                formData.append("step_number", step_number);

                // Send to backend
                fetch('<?php echo $base_url; ?>process/process_save_onboarding_last.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(async res => {
                        if (!res.ok) {
                            // Handle HTTP errors
                            const text = await res.text();
                            throw new Error(`Server error (${res.status}): ${text}`);
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            }).then(() => {
                                // Redirect to dashboard
                                window.location.href = '../dashboard';
                            });
                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 2500
                            });
                        }
                    })
                    .catch(err => console.error(err));
            });

        });

        document.addEventListener('DOMContentLoaded', () => {
            const backBtn = document.querySelector('.icon');
            if (!backBtn) return;
            backBtn.addEventListener('click', () => history.back());
        });


        document.addEventListener("DOMContentLoaded", () => {
            const passwordInput = document.getElementById("PasswordInput");
            const toggleButtons = document.querySelectorAll(".toggle-password");

            // Toggle password visibility
            toggleButtons.forEach((btn) => {
                btn.addEventListener("click", () => {
                    const targetId = btn.getAttribute("data-target");
                    const input = document.getElementById(targetId);
                    const icon = btn.querySelector("i");

                    if (input.type === "password") {
                        input.type = "text";
                        icon.className = "bi bi-eye-slash";
                    } else {
                        input.type = "password";
                        icon.className = "bi bi-eye";
                    }
                });
            });

            // Live password validation
            passwordInput.addEventListener("input", () => {
                const value = passwordInput.value;

                updateRule("rule-length", value.length >= 8);
                updateRule("rule-uppercase", /[A-Z]/.test(value));
                updateRule("rule-lowercase", /[a-z]/.test(value));
                updateRule("rule-number", /\d/.test(value));
                updateRule("rule-special", /[!@#$%^&*(),.?":{}|<>]/.test(value));
                updateRule("rule-space", !/\s/.test(value));
            });

            function updateRule(ruleId, isValid) {
                const rule = document.getElementById(ruleId);
                if (isValid) {
                    rule.textContent = rule.textContent.replace("❌", "✅");
                    rule.style.color = "green";
                } else {
                    if (!rule.textContent.startsWith("❌")) {
                        rule.textContent = rule.textContent.replace("✅", "❌");
                    }
                    rule.style.color = "red";
                }
            }
        });
    </script>

</body>

</html>