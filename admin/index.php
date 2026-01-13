<?php
//include auth file
include_once "auth.php";

//include route
include_once "route.php";

$totalUsers = $totalEmployers = 0;

try{
    // Get total number of users
    $stmt = $conn->prepare("SELECT COUNT(*) as total_users FROM `users` WHERE auth = 'user' AND user_type = 'talent' AND deleted_at IS NULL");
    if($stmt === false){
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $totalUsers = (int)$row['total_users'];
    }
    $stmt->close();

    // Get total number of Employers
    $stmt = $conn->prepare("SELECT COUNT(*) as total_employers FROM `users` WHERE auth = 'user' AND user_type = 'employer' AND deleted_at IS NULL");
    if($stmt === false){
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $totalEmployers = (int)$row['total_employers'];
    }
    $stmt->close();

} catch (Exception $e){
    $_SESSION['error'] = $e->getMessage();
    header('Location: /devhire/admin/dashboard/errorpage/error');
    exit();
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

            <!-- --------------------------------- -->
            <!-- 1. Dashboard Homepage -->
            <!-- --------------------------------- -->
            <div class="page-content" id="dashboard-home">
                <h1 class="mb-4 fs-3">Dashboard Overview</h1>

                <!-- Summary Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card p-3 h-100">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-badge-fill fs-2 text-primary me-3"></i>
                                <div>
                                    <p class="text-muted mb-0 small">Total Talents</p>
                                    <h4 class="fw-bold mb-0"><?= $totalUsers ?></h4>
                                </div>
                                <span class="badge bg-success-subtle text-success ms-auto">+1.2%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card p-3 h-100">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-buildings-fill fs-2 text-info me-3"></i>
                                <div>
                                    <p class="text-muted mb-0 small">Total Employers</p>
                                    <h4 class="fw-bold mb-0"><?= $totalEmployers ?></h4>
                                </div>
                                <span class="badge bg-danger-subtle text-danger ms-auto">-0.5%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card p-3 h-100">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-star-fill fs-2 text-warning me-3"></i>
                                <div>
                                    <p class="text-muted mb-0 small">Active Subscriptions</p>
                                    <h4 class="fw-bold mb-0">5,120</h4>
                                </div>
                                <!-- <span class="badge bg-success-subtle text-success ms-auto">+3.1%</span> -->
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card p-3 h-100">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-wallet-fill fs-2 text-success me-3"></i>
                                <div>
                                    <p class="text-muted mb-0 small">Monthly Revenue</p>
                                    <h4 class="fw-bold mb-0">$85,340</h4>
                                </div>
                                <span class="badge bg-success-subtle text-success ms-auto">+8.9%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Recent Signups Table -->
                    <div class="col-lg-7">
                        <div class="card p-4">
                            <h5 class="card-title fw-bold mb-3">Recent User Signups</h5>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Type</th>
                                            <th>Registered</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Jane Smith</td>
                                            <td><span class="badge bg-primary">Talent</span></td>
                                            <td>2 mins ago</td>
                                            <td><span class="badge bg-warning-subtle text-warning">Pending</span></td>
                                        </tr>
                                        <tr>
                                            <td>Innovate Solutions</td>
                                            <td><span class="badge bg-info">Employer</span></td>
                                            <td>1 hr ago</td>
                                            <td><span class="badge bg-success">Verified</span></td>
                                        </tr>
                                        <tr>
                                            <td>Max D.</td>
                                            <td><span class="badge bg-primary">Talent</span></td>
                                            <td>5 hrs ago</td>
                                            <td><span class="badge bg-success">Verified</span></td>
                                        </tr>
                                        <tr>
                                            <td>Global Tech Inc.</td>
                                            <td><span class="badge bg-info">Employer</span></td>
                                            <td>1 day ago</td>
                                            <td><span class="badge bg-warning-subtle text-warning">Pending</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <a href="<?php echo $url . 'users_management' ?>" class="btn btn-sm btn-outline-primary mt-3 w-100">View All Users</a>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="col-lg-5">
                        <div class="card p-4 h-100">
                            <h5 class="card-title fw-bold mb-3">Quick Actions</h5>
                            <div class="list-group list-group-flush">
                                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center rounded-3 mb-2">
                                    <i class="bi bi-file-earmark-check-fill text-success fs-5 me-3"></i>
                                    Verify Pending Talents
                                </a>
                                <a href="<?php echo $url . 'reports' ?>" class="list-group-item list-group-item-action d-flex align-items-center rounded-3 mb-2">
                                    <i class="bi bi-trash-fill text-danger fs-5 me-3"></i>
                                    Review Reported Users (14)
                                </a>
                                <a href="<?php echo $url . 'subscriptions' ?>" class="list-group-item list-group-item-action d-flex align-items-center rounded-3 mb-2">
                                    <i class="bi bi-gear-fill text-secondary fs-5 me-3"></i>
                                    Update Platform Pricing
                                </a>
                                <a href="<?php echo $url . 'open_tickets' ?>" class="list-group-item list-group-item-action d-flex align-items-center rounded-3 mb-2">
                                    <i class="bi bi-envelope-fill text-primary fs-5 me-3"></i>
                                    Respond to Open Tickets
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- End Dashboard Homepage -->

        </div>
        <!-- End Page Content Container -->
    </div>
    <!-- End Main Content Wrapper -->

    <!-- Modal for Reported Accounts Evidence (reused for simulation) -->
    <div class="modal fade" id="evidenceModal" tabindex="-1" aria-labelledby="evidenceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="evidenceModalLabel">Report Evidence for #RPT-0014</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="fw-bold">Report Reason:</p>
                    <p>Talent profile claims experience with 'Quantum Computing' but their GitHub is empty.</p>
                    <p class="fw-bold">Screenshot Evidence:</p>
                    <img src="https://placehold.co/400x200/ced4da/000000?text=Mock+Screenshot+of+Empty+GitHub" class="img-fluid rounded" alt="Evidence Screenshot">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger">Suspend Account</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Load Bootstrap JS Bundle (includes Popper for dropdowns/modals) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Initialize charts on page load
        document.addEventListener('DOMContentLoaded', initializeCharts);

        // 5. Ranking Weight Calculator Logic (Simulation)
        const weightInputs = document.querySelectorAll('#search-rankings input[type="range"]');
        const totalWeightOutput = document.getElementById('totalWeight');

        // function updateWeightTotal() {
        //     let total = 0;
        //     weightInputs.forEach(input => {
        //         total += parseInt(input.value);
        //     });
        //     totalWeightOutput.textContent = total;
        //     totalWeightOutput.classList.toggle('text-danger', total !== 100);
        //     totalWeightOutput.classList.toggle('text-primary', total === 100);
        // }

        weightInputs.forEach(input => {
            input.addEventListener('input', updateWeightTotal);
        });

        // Initialize the total weight on load
        updateWeightTotal();

    </script>
</body>
</html>