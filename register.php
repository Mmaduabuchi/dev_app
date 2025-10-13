<?php
require 'google_config.php';
// Start session and check authentication
require_once "auth.php";

$login_url = $client->createAuthUrl();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create an Account with us.</title>
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
      margin: 60px auto;
      position: relative;
    }
    .dropdown-toggle-custom {
      width: 100%;
      background: #fff;
      border: 1px solid #dcdcdc;
      padding: 12px 15px;
      border-radius: 6px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: pointer;
      font-weight: 500;
    }
    .dropdown-menu-custom {
      position: absolute;
      top: 15%;
      left: 0;
      width: 100%;
      background: #fff;
      border: 1px solid #dcdcdc;
      border-radius: 6px;
      margin-top: 4px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
      display: none;
      z-index: 1000;

      /* animation */
      opacity: 0;
      transform: scaleY(0.9);
      transform-origin: top;
      transition: all 0.2s ease;
    }
    .dropdown-menu-custom.show {
      display: block;
      opacity: 1;
      transform: scaleY(1);
    }
    .dropdown-option {
      display: flex;
      align-items: center;
      padding: 12px 15px;
      cursor: pointer;
      transition: background 0.2s ease;
    }
    .dropdown-option:hover,
    .dropdown-option.active {
      background: #f1f5f9;
    }
    .dropdown-option i {
      font-size: 20px;
      margin-right: 12px;
      color: #3366ff;
    }
    .dropdown-option .title {
      font-weight: 500;
    }
    .dropdown-option .desc {
      font-size: 13px;
      color: #6c757d;
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
                    <h2>Apply to Join the World’s <br> <span class="blue">Top Talent Network</span> </h2>
                    <p>
                        DevHire is a platform that bridges the gap between talented developers and businesses seeking top tech expertise.
                        <br>
                        Whether you’re a developer looking for opportunities or a business searching for the right talent,
                        <br>
                        DevHire provides a simple, transparent, and effective way to connect.
                    </p>
                </div>
            </div>
        </div>

        <div class="custom-dropdown">
            <div class="row mb-1">
                <div class="col-12">
                    <a href="<?php echo htmlspecialchars($login_url); ?>" class="btn btn-outline-danger w-100 p-3">
                        <img src="https://www.svgrepo.com/show/355037/google.svg" alt="Google logo" width="20" class="me-2">
                        Sign up with Google
                    </a>
                </div>
                <div class="col-12 text-center mt-3">
                    <p>or continue with email</p>
                </div>
            </div>
            <!-- Dropdown toggle -->
            <div class="dropdown-toggle-custom" id="dropdownToggle">
                <span id="selectedOption">I'm a Developer</span>
                <i data-feather="chevron-down"></i>
            </div>

            <!-- Dropdown menu -->
            <div class="dropdown-menu-custom" id="dropdownMenu">
                <div class="dropdown-option" data-value="Developer" tabindex="0">
                    <i data-feather="code"></i>
                    <div>
                        <div class="title">Developer</div>
                        <div class="desc">Front-end, Back-end, Full-stack, QA, etc.</div>
                    </div>
                </div>
                <div class="dropdown-option" data-value="Designer" tabindex="0">
                    <i data-feather="pen-tool"></i>
                    <div>
                        <div class="title">Designer</div>
                        <div class="desc">Digital Product, UI, UX, Interaction, etc.</div>
                    </div>
                </div>
                <div class="dropdown-option" data-value="Management Consultant" tabindex="0">
                    <i data-feather="bar-chart-2"></i>
                    <div>
                        <div class="title">Management Consultant</div>
                        <div class="desc">FP&A, M&A, Pricing, Strategy, etc.</div>
                    </div>
                </div>
                <div class="dropdown-option" data-value="Project Manager" tabindex="0">
                    <i data-feather="clipboard"></i>
                    <div>
                        <div class="title">Project Manager</div>
                        <div class="desc">Agile PM, Scrum Master, Technical PM, Digital PM, etc.</div>
                    </div>
                </div>
                <div class="dropdown-option" data-value="Product Manager" tabindex="0">
                    <i data-feather="box"></i>
                    <div>
                        <div class="title">Product Manager</div>
                        <div class="desc">Product Owner, Digital Products, Roadmap & Strategy, etc.</div>
                    </div>
                </div>
                <div class="dropdown-option" data-value="Marketing Expert" tabindex="0">
                    <i data-feather="trending-up"></i>
                    <div>
                        <div class="title">Marketing Expert</div>
                        <div class="desc">SEO/SEM, Email Marketing, Social Media Marketing, etc.</div>
                    </div>
                </div>
            </div>

            <!-- Hidden input for storing value -->
            <input type="hidden" value="Developer" name="selectedRole" id="dropdownHiddenInput">

            <div class="row mt-3">
                <div class="col-12">
                    <input type="text" id="fullname" class="form-control p-3" placeholder="Fullname">
                </div>
                <div class="col-12 mt-3">
                    <input type="email" id="email" class="form-control p-3" placeholder="Email">
                </div>
                <div class="col-12 mt-3">
                    <div class="input-group">
                        <input type="password" id="password" class="form-control p-3" placeholder="Password">
                        <span class="input-group-text password-toggle" onclick="togglePassword('password','icon1')">
                        <i data-feather="eye" id="icon1"></i>
                        </span>
                    </div>
                </div>
                <div class="col-12 mt-3">
                    <div class="input-group">
                        <input type="password" id="confrimpassword" class="form-control p-3" placeholder="Confirm password">
                        <span class="input-group-text password-toggle" onclick="togglePassword('confrimpassword','icon2')">
                        <i data-feather="eye" id="icon2"></i>
                        </span>
                    </div>
                </div>
                <div class="col-12 mt-3 text-muted fs-6 fst-italic">
                    <span>By submitting, you acknowledge and agree to devhire's Terms and Conditions and Privacy Policy.</span>
                </div>
            </div>
            <!-- Apply button -->
            <div class="apply-btn">
                <button class="btn btn-success w-100 p-3" onclick="apply()">Apply to Join devhire</button>
            </div>

            <hr>
            <p>
                Already has an account? <a href="login">login</a>
            </p>
        </div>
    </section>





    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        feather.replace();

        const dropdownToggle = document.getElementById('dropdownToggle');
        const dropdownMenu = document.getElementById('dropdownMenu');
        const selectedOption = document.getElementById('selectedOption');
        const hiddenInput = document.getElementById('dropdownHiddenInput');
        const options = document.querySelectorAll('.dropdown-option');

        let activeIndex = -1;

        // Toggle dropdown
        dropdownToggle.addEventListener('click', () => {
            dropdownMenu.classList.toggle('show');
        });

        // Handle option select
        function selectOption(option, index) {
            selectedOption.textContent = option.getAttribute('data-value');
            hiddenInput.value = option.getAttribute('data-value');
            dropdownMenu.classList.remove('show');
            options.forEach(opt => opt.classList.remove('active'));
            option.classList.add('active');
            activeIndex = index;
        }

        options.forEach((option, index) => {
            option.addEventListener('click', () => selectOption(option, index));
        });

        // Close if clicked outside
        window.addEventListener('click', (e) => {
            if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (!dropdownMenu.classList.contains('show')) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = (activeIndex + 1) % options.length;
                options[activeIndex].focus();
            }
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = (activeIndex - 1 + options.length) % options.length;
                options[activeIndex].focus();
            }
            if (e.key === 'Enter' && activeIndex >= 0) {
                e.preventDefault();
                selectOption(options[activeIndex], activeIndex);
            }
            if (e.key === 'Escape') {
                dropdownMenu.classList.remove('show');
            }
        });


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

        function apply(){
            let data = hiddenInput.value;
            // alert(data);

            const fullname = document.getElementById("fullname").value.trim();
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value;
            const confirmpassword = document.getElementById("confrimpassword").value;

            //validate user input
            if(fullname === "" || email === "" || password === "" || confirmpassword === ""){
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

            //validate email format
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if(!emailPattern.test(email)){
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

            //validate password match
            if(password !== confirmpassword){
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

            //validate password length
            if(password.length < 6){
                Swal.fire({
                    toast: true,
                    icon: 'error',
                    title: 'Password must be at least 6 characters',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }

            //prepare form data
            const formData = new FormData();
            formData.append('role', data);
            formData.append('fullname', fullname);
            formData.append('email', email);
            formData.append('password', password);
            formData.append('confirmpassword', confirmpassword);

            //send ajax request
            fetch('library/process_signup.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success'){
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
                    return
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
                return
            });
        }
    </script>
</body>
</html>
