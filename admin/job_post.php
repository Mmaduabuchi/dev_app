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

                <div class="page-content" id="job-posts">
                    <h1 class="mb-4 fs-3">Job Posts Management</h1>
                    <div class="card p-4">
                        <h5 class="card-title fw-bold mb-3">All Active Job Posts</h5>
                        <!-- Filters -->
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <input type="search" class="form-control me-2" style="max-width: 250px;" placeholder="Search by title or employer...">
                            <select class="form-select me-2" style="max-width: 150px;"><option selected>All Roles</option></select>
                            <select class="form-select me-2" style="max-width: 150px;"><option selected>All Industries</option></select>
                            <button class="btn btn-outline-secondary"><i class="bi bi-funnel"></i> Filter</button>
                            <button class="btn btn-success ms-auto"><i class="bi bi-plus-circle"></i> Add New Job</button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle small">
                                <thead>
                                    <tr>
                                        <th>Job Title</th>
                                        <th>Employer</th>
                                        <th>Salary</th>
                                        <th>Posted Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Senior React Developer</td>
                                        <td>Tech Global Corp</td>
                                        <td>$120k - $140k</td>
                                        <td>2 days ago</td>
                                        <td><span class="badge bg-primary">Approved</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-secondary me-1" title="Feature Job"><i class="bi bi-lightning"></i></button>
                                            <button class="btn btn-sm btn-outline-danger" title="Remove"><i class="bi bi-x-circle"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>UX Designer - Mobile</td>
                                        <td>Design Hub LLC</td>
                                        <td>N/A</td>
                                        <td>5 days ago</td>
                                        <td><span class="badge bg-warning">Pending Review</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-success me-1" title="Approve"><i class="bi bi-check"></i></button>
                                            <button class="btn btn-sm btn-outline-danger" title="Remove"><i class="bi bi-x-circle"></i></button>
                                        </td>
                                    </tr>
                                    <!-- More Job Rows... -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- End Job Posts Management -->
            
            </div>
        </div>

        

        <?php include_once "footer.php"; ?>


        <!-- Load Bootstrap JS Bundle (includes Popper for dropdowns/modals) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>

            
        </script>
    </body>
</html>