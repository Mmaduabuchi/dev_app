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

                <div class="page-content" id="users-management">
                    <h1 class="mb-4 fs-3">Users Management</h1>

                    <ul class="nav nav-tabs mb-4" id="userTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="talent-tab" data-bs-toggle="tab" data-bs-target="#talents-pane" type="button" role="tab" aria-controls="talents-pane" aria-selected="true">Talents</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="employer-tab" data-bs-toggle="tab" data-bs-target="#employers-pane" type="button" role="tab" aria-controls="employers-pane" aria-selected="false">Employers / CEOs</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- 2A. Talents Page -->
                        <div class="tab-pane fade show active" id="talents-pane" role="tabpanel" aria-labelledby="talent-tab">
                            <div class="card p-4">
                                <h5 class="card-title fw-bold mb-3">Talent Roster (12,450 Total)</h5>

                                <!-- Filters & Search -->
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <input type="search" class="form-control me-2" style="max-width: 200px;" placeholder="Search by name or skill...">
                                    <select class="form-select me-2" style="max-width: 150px;">
                                        <option selected>All Skills</option>
                                        <option>Frontend</option>
                                        <option>Backend</option>
                                        <option>Design</option>
                                    </select>
                                    <select class="form-select me-2" style="max-width: 150px;">
                                        <option selected>All Subs</option>
                                        <option>Basic</option>
                                        <option>Standard</option>
                                        <option>Premium</option>
                                    </select>
                                    <button class="btn btn-outline-secondary"><i class="bi bi-funnel"></i> Apply Filters</button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle small">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Skills</th>
                                                <th>Subscription</th>
                                                <th>Ranking Score</th>
                                                <th>Status</th>
                                                <th>Last Seen</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Alex Johnson</td>
                                                <td><span class="badge bg-primary">Frontend</span> <span class="badge bg-secondary">React</span></td>
                                                <td><span class="badge bg-warning">Premium</span></td>
                                                <td class="fw-bold">8.9/10</td>
                                                <td><span class="badge bg-success">Verified</span></td>
                                                <td>Just now</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info me-1" title="View Profile"><i class="bi bi-eye"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger" title="Suspend"><i class="bi bi-slash-circle"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Maria Garcia</td>
                                                <td><span class="badge bg-dark">Designer</span> <span class="badge bg-secondary">Figma</span></td>
                                                <td><span class="badge bg-secondary">Basic</span></td>
                                                <td>4.2/10</td>
                                                <td><span class="badge bg-secondary">Pending</span></td>
                                                <td>1 week ago</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info me-1"><i class="bi bi-eye"></i></button>
                                                    <button class="btn btn-sm btn-success" title="Verify"><i class="bi bi-check"></i></button>
                                                </td>
                                            </tr>
                                            <!-- More Talent Rows... -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <nav>
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                                        <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>

                        <!-- 2B. Employers / CEOs Page -->
                        <div class="tab-pane fade" id="employers-pane" role="tabpanel" aria-labelledby="employer-tab">
                            <div class="card p-4">
                                <h5 class="card-title fw-bold mb-3">Employer Roster (2,810 Total)</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle small">
                                        <thead>
                                            <tr>
                                                <th>Company Name</th>
                                                <th>Subscription</th>
                                                <th>Active Jobs</th>
                                                <th>Verification</th>
                                                <th>Registered Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Tech Global Corp</td>
                                                <td><span class="badge bg-success">Standard</span></td>
                                                <td>5</td>
                                                <td><span class="badge bg-success">Verified</span></td>
                                                <td>2021-08-15</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info me-1" title="View Company"><i class="bi bi-building"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Startup XYZ</td>
                                                <td><span class="badge bg-secondary">Basic</span></td>
                                                <td>1</td>
                                                <td><span class="badge bg-warning">Pending</span></td>
                                                <td>2023-11-01</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info me-1"><i class="bi bi-building"></i></button>
                                                    <button class="btn btn-sm btn-success" title="Verify"><i class="bi bi-check"></i></button>
                                                </td>
                                            </tr>
                                            <!-- More Employer Rows... -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End Users Management -->
            
            </div>
        </div>

        

        <?php include_once "footer.php"; ?>


        <!-- Load Bootstrap JS Bundle (includes Popper for dropdowns/modals) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>

            
        </script>
    </body>
</html>