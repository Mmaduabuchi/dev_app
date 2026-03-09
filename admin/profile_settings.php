<?php
//include auth file
include_once "auth.php";

//include route
include_once "route.php";

try{
    $stmt = $conn->prepare("SELECT * FROM system_settings WHERE id = 1 AND setting_key = 'login_otp_enabled'");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception('System settings not found.');
    }
    $admin = $result->fetch_assoc();
    $setting_value = (int)$admin['setting_value'];
    $stmt->close();

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
                --dh-blue-primary: #0d6efd;
                --dh-blue-dark: #0a58ca;
                --dh-bg-gray: #f8f9fa;
                --dh-border-color: #dee2e6;
                --dh-text-muted: #6c757d;
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
                background-color: var(--bs-devhire-navy);
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

            /* Card & UI Elements */
            .settings-card {
                border: none;
                border-radius: 12px;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                margin-bottom: 1.5rem;
            }

            .card-header {
                background-color: #fff;
                border-bottom: 1px solid var(--dh-border-color);
                padding: 1.25rem;
                border-radius: 12px 12px 0 0 !important;
            }

            .card-header h5 {
                margin-bottom: 0;
                font-weight: 600;
                font-size: 1.1rem;
                display: flex;
                align-items: center;
            }

            .card-header h5 i {
                margin-right: 12px;
                color: var(--dh-blue-primary);
            }

            .form-label {
                font-weight: 500;
                font-size: 0.9rem;
                color: var(--dh-text-muted);
            }

            .form-control:focus {
                border-color: var(--dh-blue-primary);
                box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            }

            .role-badge {
                background-color: #e7f1ff;
                color: var(--dh-blue-primary);
                padding: 0.35rem 0.75rem;
                border-radius: 50px;
                font-size: 0.85rem;
                font-weight: 600;
                display: inline-block;
            }

            /* Custom Toggle Switch Styles */
            .form-check-input:checked {
                background-color: var(--dh-blue-primary);
                border-color: var(--dh-blue-primary);
            }

            .setting-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 1rem 0;
            }

            .setting-item:not(:last-child) {
                border-bottom: 1px solid var(--dh-border-color);
            }

            .setting-info p {
                margin-bottom: 0;
                font-size: 0.95rem;
                font-weight: 500;
            }

            .setting-info small {
                color: var(--dh-text-muted);
            }

            .btn-save {
                padding: 0.6rem 2rem;
                font-weight: 600;
                border-radius: 8px;
            }
            
            .toast-container {
                z-index: 1050;
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

                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                    <h1 class="h2">Profile Settings</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-primary btn-save shadow-sm" onclick="showSaveToast()">
                            <i class="bi bi-check2-circle me-1"></i> Save All Changes
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        
                        <!-- Account Information -->
                        <div class="card settings-card">
                            <div class="card-header">
                                <h5><i class="bi bi-person-circle"></i> Account Information</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" disabled class="form-control" value="<?= $admin_name ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" disabled class="form-control" value="<?= $admin_email ?>">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label d-block">Admin Role</label>
                                        <span class="role-badge">Super Administrator</span>
                                        <div class="mt-2">
                                            <small class="text-muted">Role permissions managed by the Organization Owner.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Settings -->
                        <div class="card settings-card">
                            <div class="card-header">
                                <h5><i class="bi bi-shield-lock"></i> Security & Access</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="alert alert-info border-0 shadow-sm mb-4">
                                    <small><i class="bi bi-info-circle-fill me-2"></i> Only Admin and Sub-Admin roles can enable Multi-Factor Authentication.</small>
                                </div>
                                
                                <div class="setting-item">
                                    <div class="setting-info">
                                        <p>Login OTP (Two-Factor Authentication)</p>
                                        <small>Requires a code sent to your email to sign in.</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <?php
                                            if($setting_value === 1):
                                        ?>
                                            <input class="form-check-input" type="checkbox" id="otpToggle" checked style="width: 3em; height: 1.5em;">
                                        <?php
                                            else:
                                        ?>
                                            <input class="form-check-input" type="checkbox" id="otpToggle" style="width: 3em; height: 1.5em;">
                                        <?php
                                            endif;
                                        ?>
                                    </div>
                                </div>

                                <div class="setting-item">
                                    <div class="setting-info">
                                        <p>IP Whitelisting</p>
                                        <small>Only allow dashboard access from verified company IPs.</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="ipToggle" style="width: 3em; height: 1.5em;">
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="admin_account" class="btn btn-outline-secondary btn-sm">Change Admin Password</a>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Preferences -->
                        <div class="card settings-card">
                            <div class="card-header">
                                <h5><i class="bi bi-bell"></i> Notification Preferences</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="setting-item">
                                    <div class="setting-info">
                                        <p>New Candidate Alerts</p>
                                        <small>Receive email when a top-tier candidate applies.</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" checked style="width: 3em; height: 1.5em;">
                                    </div>
                                </div>
                                <!-- <div class="setting-item">
                                    <div class="setting-info">
                                        <p>Security Audit Reports</p>
                                        <small>Weekly summary of login attempts and role changes.</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" checked style="width: 3em; height: 1.5em;">
                                    </div>
                                </div> -->
                                <div class="setting-item">
                                    <div class="setting-info">
                                        <p>System Maintenance</p>
                                        <small>Get notified about scheduled downtime and updates.</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" style="width: 3em; height: 1.5em;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Save Button -->
                        <div class="d-grid d-md-none mb-5">
                            <button class="btn btn-primary py-3 fw-bold shadow-sm" onclick="showSaveToast()">Save All Changes</button>
                        </div>

                    </div>

                    <!-- Sidebar Info / Help -->
                    <div class="col-lg-4">
                        <div class="card settings-card bg-primary text-white">
                            <div class="card-body p-4 text-center">
                                <div class="mb-3">
                                    <i class="bi bi-patch-check-fill" style="font-size: 3rem; opacity: 0.9;"></i>
                                </div>
                                <h5 class="fw-bold">Security Verified</h5>
                                <p class="small mb-0 opacity-75">Your account is currently protected by enterprise-grade encryption and 2FA.</p>
                            </div>
                        </div>
                        
                        <div class="card settings-card">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-3">Help & Documentation</h6>
                                <ul class="list-unstyled small mb-0">
                                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted"><i class="bi bi-book me-2"></i> Role Permission Guide</a></li>
                                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted"><i class="bi bi-shield-check me-2"></i> Security Best Practices</a></li>
                                    <li><a href="#" class="text-decoration-none text-muted"><i class="bi bi-envelope me-2"></i> Contact Tech Support</a></li>
                                </ul>
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
            document.getElementById("otpToggle").addEventListener("change", function() {
                fetch("./../process/process_update_otp_setting.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        otp_enabled: this.checked ? 1 : 0
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        showSaveToast();
                    } else {
                        showErrorToast(data.message);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    showErrorToast("An error occurred while updating the OTP setting.");
                });
            });
            
        </script>
    </body>
</html>