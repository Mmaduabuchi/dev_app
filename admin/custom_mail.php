<?php
//include auth file
include_once "auth.php";

//include route
include_once "route.php";

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>DevHire Admin Dashboard</title>
        <!-- Load Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Load Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <!-- Load Chart.js for interactive graphs -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
        <style>
            :root {
                --bs-devhire-blue: #0A66C2;
                --bs-devhire-navy: #152238;
                --bs-devhire-light: #F8F9FA;
                --bs-font-inter: 'Inter', sans-serif;
            }

            /* Load Inter Font (Google Fonts) */
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
            body { font-family: var(--bs-font-inter); background-color: var(--bs-devhire-light); }

            /* General Card Styling */
            .card {
                border-radius: 12px;
                border: none;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
            }

            /* Sidebar Styling */
            .sidebar {
                width: 260px;
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                z-index: 1000;
                padding-top: 56px; /* Space for fixed navbar */
                background-color: #111827;
                color: #E9ECEF;
                transition: all 0.3s;
            }
            .sidebar-link {
                display: flex;
                align-items: center;
                padding: 10px 15px;
                margin: 4px 0;
                border-radius: 8px;
                color: #E9ECEF;
                text-decoration: none;
                transition: all 0.2s;
            }
            .sidebar-link:hover, .sidebar-link.active {
                background-color: rgba(255, 255, 255, 0.1);
                color: #FFFFFF;
            }
            .sidebar-link.active {
                border-left: 4px solid var(--bs-devhire-blue);
                padding-left: 11px;
            }
            .sidebar-link i { margin-right: 12px; }

            /* Main Content Adjustments */
            .main-content {
                margin-left: 260px;
                padding-top: 72px; /* Space for fixed navbar */
                transition: all 0.3s;
            }

            /* Navbar Customization */
            .navbar {
                background-color: #FFFFFF;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                z-index: 1010;
            }

            /* Responsive adjustments */
            @media (max-width: 991.98px) {
                .sidebar {
                    transform: translateX(-100%);
                    padding-top: 0;
                }
                .sidebar.active {
                    transform: translateX(0);
                }
                .main-content {
                    margin-left: 0;
                }
            }

            /* Dark Mode (Simulation via specific class) */
            .dark-mode {
                background-color: #212529 !important;
                color: #F8F9FA !important;
            }
            .dark-mode .card, .dark-mode .navbar {
                background-color: #2D3748 !important;
                color: #F8F9FA !important;
            }
            .dark-mode .sidebar {
                background-color: #1A202C !important;
            }
            .dark-mode .table, .dark-mode .form-control {
                color: #F8F9FA !important;
                background-color: #2D3748 !important;
                border-color: #4A5568;
            }
        </style>
    </head>
    <body class="d-flex">

        <!-- Sidebar -->
        <?php
            include_once "navbar.php";
        ?>

        <!-- Main Content Wrapper -->
        <div id="main-content" class="main-content w-100">

            <!-- Top Navigation Bar -->
            <?php
                include_once "header.php";
            ?>

            <!-- Page Content Container -->
            <div class="container-fluid p-4">

                <div class="page-content" id="messages-requests">
                    <h1 class="mb-4 fs-3">Custom Mail</h1>

                    <div class="card p-4">
                        <h5 class="card-title fw-bold mb-3">Send Email to Selected User Group</h5>

                        <div class="list-group">

                            <!-- TALENTS -->
                            <div>
                                <a href="javascript:void(0);" 
                                class="list-group-item list-group-item-action"
                                onclick="toggleSection('talentsBox')">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 fw-bold text-primary">Talents</h6>
                                    </div>
                                    <p class="mb-1 small">Send a custom mail to all Talents</p>
                                    <small class="text-muted">Click to show mail box tab</small>
                                </a>

                                <!-- Hidden Mail Form -->
                                <div id="talentsBox" class="mail-box p-3 border rounded mt-2 mb-2 d-none">
                                    <div class="mb-3">
                                        <label class="form-label">Subject</label>
                                        <input type="text" class="form-control" id="talents_subject" placeholder="Enter subject">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Message</label>
                                        <textarea class="form-control" rows="4" id="talents_message" placeholder="Write your message"></textarea>
                                    </div>

                                    <button class="btn btn-primary" id="btn_talents" onclick="sendMail('talents')">
                                        <span class="spinner-border spinner-border-sm d-none" id="spinner_talents"></span>
                                        <span id="btn_text_talents"><i class="bi bi-send-fill me-2"></i> Send Mail</span>
                                    </button>
                                </div>
                            </div>


                            <!-- EMPLOYERS -->
                            <div>
                                <a href="javascript:void(0);" class="list-group-item list-group-item-action" onclick="toggleSection('employersBox')">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 fw-bold">Employers</h6>
                                    </div>
                                    <p class="mb-1 small">Send a custom mail to all Employers</p>
                                    <small class="text-muted">Click to show mail box tab</small>
                                </a>

                                <!-- Hidden Mail Form -->
                                <div id="employersBox" class="mail-box p-3 border rounded mt-2 mb-2 d-none">
                                    <div class="mb-3">
                                        <label class="form-label">Subject</label>
                                        <input type="text" class="form-control" id="employers_subject" placeholder="Enter subject">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Message</label>
                                        <textarea class="form-control" rows="4" id="employers_message" placeholder="Write your message"></textarea>
                                    </div>

                                    <button class="btn btn-primary" id="btn_employers" onclick="sendMail('employers')">
                                        <span class="spinner-border spinner-border-sm d-none" id="spinner_employers"></span>
                                        <span id="btn_text_employers"><i class="bi bi-send-fill me-2"></i> Send Mail</span>
                                    </button>
                                </div>
                            </div>


                            <!-- SINGLE USER -->
                            <div>
                                <a href="javascript:void(0);" class="list-group-item list-group-item-action" onclick="toggleSection('singleUserBox')">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 fw-bold">Custom user</h6>
                                    </div>
                                    <p class="mb-1 small">Send a custom mail to a single user</p>
                                    <small class="text-muted">Click to show mail box tab</small>
                                </a>

                                <!-- Hidden Mail Form -->
                                <div id="singleUserBox" class="mail-box p-3 border rounded mt-2 d-none">
                                    <div class="mb-3">
                                        <label class="form-label">Add user email</label>
                                        <input type="email" class="form-control" id="singleUser_email" placeholder="Enter a user email">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Subject</label>
                                        <input type="text" class="form-control" id="singleUser_subject" placeholder="Enter subject">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Message</label>
                                        <textarea class="form-control" rows="4" id="singleUser_message" placeholder="Write your message"></textarea>
                                    </div>

                                    <button class="btn btn-primary" id="btn_singleUser" onclick="sendMail('singleUser')">
                                        <span class="spinner-border spinner-border-sm d-none" id="spinner_singleUser"></span>
                                        <span id="btn_text_singleUser"><i class="bi bi-send-fill me-2"></i> Send Mail</span>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                
            </div>
        </div>

        

        <?php include_once "footer.php"; ?>


        <!-- Load Bootstrap JS Bundle (includes Popper for dropdowns/modals) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function toggleSection(id) {

                const box = document.getElementById(id);
                const talents = document.getElementById('talentsBox');
                const employers = document.getElementById('employersBox');
                const singleUser = document.getElementById('singleUserBox');

                // If the clicked box is already open → close it
                if (!box.classList.contains('d-none')) {
                    box.classList.add('d-none');
                    return;
                }

                // Otherwise close both and open the clicked one
                talents.classList.add('d-none');
                employers.classList.add('d-none');
                singleUser.classList.add('d-none');

                box.classList.remove('d-none');
            }

            function setLoading(type, isLoading) {
                const btn = document.getElementById('btn_' + type);
                const spinner = document.getElementById('spinner_' + type);
                const text = document.getElementById('btn_text_' + type);

                if (isLoading) {
                    btn.disabled = true;
                    spinner.classList.remove('d-none');
                    text.innerHTML = ' Sending...';
                } else {
                    btn.disabled = false;
                    spinner.classList.add('d-none');
                    text.innerHTML = '<i class="bi bi-send-fill me-2"></i> Send Mail';
                }
            }

            function sendMail(type) {
                const subject = document.getElementById(type + '_subject').value;
                const message = document.getElementById(type + '_message').value;
                const email = document.getElementById(type + '_email')?.value ?? null;

                if (type === 'singleUser' && !email) {
                    setLoading(type, false);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Please enter a user email',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    return;
                }

                setLoading(type, true);

                const data = {
                    type: type,
                    subject: subject,
                    message: message,
                    email: email
                };

                fetch('./../process/process_send_custom_mail.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    setLoading(type, false);
                    if (data.status === 'success') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    setLoading(type, false);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Something went wrong!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
            }
            
        </script>
    </body>
</html>