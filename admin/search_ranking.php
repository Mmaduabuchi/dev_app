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
                <div class="page-content" id="search-rankings">
                    <h1 class="mb-4 fs-3">Talent Search Rankings</h1>
                    <div class="card p-4">
                        <h5 class="card-title fw-bold mb-3">Talent Ranking Weight Controls</h5>
                        <p class="text-muted small">Adjust the percentage weight of factors contributing to a Talent's search ranking score.</p>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="skillMatch" class="form-label">Skill Match Percentage (%)</label>
                                <input type="range" class="form-range" id="skillMatch" min="0" max="100" value="40" oninput="this.nextElementSibling.value=this.value">
                                <output class="float-end fw-bold">40</output>
                            </div>
                            <div class="col-md-6">
                                <label for="experience" class="form-label">Experience/Portfolio %</label>
                                <input type="range" class="form-range" id="experience" min="0" max="100" value="30" oninput="this.nextElementSibling.value=this.value">
                                <output class="float-end fw-bold">30</output>
                            </div>
                            <div class="col-md-6">
                                <label for="subscriptionBoost" class="form-label">Subscription Boost %</label>
                                <input type="range" class="form-range" id="subscriptionBoost" min="0" max="100" value="15" oninput="this.nextElementSibling.value=this.value">
                                <output class="float-end fw-bold">15</output>
                            </div>
                            <div class="col-md-6">
                                <label for="profileComplete" class="form-label">Profile Completeness %</label>
                                <input type="range" class="form-range" id="profileComplete" min="0" max="100" value="15" oninput="this.nextElementSibling.value=this.value">
                                <output class="float-end fw-bold">15</output>
                            </div>
                            <div class="col-12 text-center mt-5">
                                <h4 class="mb-0">Total Weight: <span id="totalWeight" class="text-primary">100</span>%</h4>
                            </div>
                        </div>
                        <button class="btn btn-primary mt-4 w-100"><i class="bi bi-floppy-fill"></i> Save Ranking Weights</button>
                    </div>
                </div>
                <!-- End Search Rankings & Algorithms -->
            
            </div>
        </div>

        

        <?php include_once "footer.php"; ?>


        <!-- Load Bootstrap JS Bundle (includes Popper for dropdowns/modals) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>

            
        </script>
    </body>
</html>