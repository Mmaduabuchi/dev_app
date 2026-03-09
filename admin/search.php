<?php
//include auth file
include_once "auth.php";

//include route
include_once "route.php";

$search_query = isset($_GET['query']) ? trim($_GET['query']) : '';
$search_param = "%{$search_query}%";

$talents = [];
$employers = [];
$transactions = [];
$error_msg = null;

if (!empty($search_query)) {
    try {
        // Search Talents
        $stmt_talents = $conn->prepare("SELECT u.id, u.fullname, u.email, u.user_type, u.suspended_at, dp.profile_picture 
            FROM users u 
            LEFT JOIN developers_profiles dp ON u.id = dp.user_id 
            WHERE u.user_type = 'talent' AND u.deleted_at IS NULL AND (u.fullname LIKE ? OR u.email LIKE ?)");
        if ($stmt_talents) {
            $stmt_talents->bind_param("ss", $search_param, $search_param);
            $stmt_talents->execute();
            $result = $stmt_talents->get_result();
            while ($row = $result->fetch_assoc()) {
                $talents[] = $row;
            }
            $stmt_talents->close();
        }

        // Search Employers
        $stmt_employers = $conn->prepare("SELECT u.id, u.fullname, u.email, u.user_type, u.suspended_at, ep.company_name, ep.industry, ep.website 
            FROM users u 
            LEFT JOIN employer_profiles ep ON u.id = ep.user_id 
            WHERE u.user_type = 'employer' AND u.deleted_at IS NULL AND (u.fullname LIKE ? OR ep.company_name LIKE ? OR u.email LIKE ?)");
        if ($stmt_employers) {
            $stmt_employers->bind_param("sss", $search_param, $search_param, $search_param);
            $stmt_employers->execute();
            $result = $stmt_employers->get_result();
            while ($row = $result->fetch_assoc()) {
                $employers[] = $row;
            }
            $stmt_employers->close();
        }

        // Search Transactions
        $stmt_trans = $conn->prepare("SELECT * FROM transaction_history WHERE deleted_at IS NULL AND (transaction_id LIKE ? OR user_company LIKE ? OR plan LIKE ?)");
        if ($stmt_trans) {
            $stmt_trans->bind_param("sss", $search_param, $search_param, $search_param);
            $stmt_trans->execute();
            $result = $stmt_trans->get_result();
            while ($row = $result->fetch_assoc()) {
                $transactions[] = $row;
            }
            $stmt_trans->close();
        }
    } catch (Exception $e) {
        $error_msg = "Error during search: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>DevHire Admin | Search</title>
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
                <div class="page-content" id="search-results">
                    <h1 class="mb-4 fs-3">Search Results for "<?= htmlspecialchars($search_query) ?>"</h1>
                    
                    <?php if ($error_msg): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
                    <?php endif; ?>

                    <?php if (empty($search_query)): ?>
                        <div class="alert alert-info">Please enter a search query to find talents, employers, or transactions.</div>
                    <?php else: ?>
                        <!-- Talents Results -->
                        <div class="card p-4 mb-4">
                            <h5 class="card-title fw-bold mb-3">Talents (<?= count($talents) ?>)</h5>
                            <?php if (count($talents) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle small">
                                        <thead>
                                            <tr>
                                                <th>Full Name</th>
                                                <th>Email</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($talents as $talent): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($talent['fullname']) ?></td>
                                                    <td><?= htmlspecialchars($talent['email']) ?></td>
                                                    <td>
                                                        <?php if ($talent['suspended_at']): ?>
                                                            <span class="badge bg-danger">Suspended</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-success">Active</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="user_review?token_ref=<?= bin2hex(random_bytes(36)) ?>&user_id=<?= $talent['id'] ?>" class="btn btn-sm btn-outline-info" title="View Profile">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">No talents found matching your query.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Employers Results -->
                        <div class="card p-4 mb-4">
                            <h5 class="card-title fw-bold mb-3">Employers (<?= count($employers) ?>)</h5>
                            <?php if (count($employers) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle small">
                                        <thead>
                                            <tr>
                                                <th>Company Name</th>
                                                <th>Contact Name</th>
                                                <th>Email</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($employers as $employer): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($employer['company_name'] ?? 'N/A') ?></td>
                                                    <td><?= htmlspecialchars($employer['fullname']) ?></td>
                                                    <td><?= htmlspecialchars($employer['email']) ?></td>
                                                    <td>
                                                        <?php if ($employer['suspended_at']): ?>
                                                            <span class="badge bg-danger">Suspended</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-success">Active</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="employer_review?token_ref=<?= bin2hex(random_bytes(36)) ?>&employer_id=<?= $employer['id'] ?>" class="btn btn-sm btn-outline-info" title="View Profile">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">No employers found matching your query.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Transactions Results -->
                        <div class="card p-4 mb-4">
                            <h5 class="card-title fw-bold mb-3">Transactions (<?= count($transactions) ?>)</h5>
                            <?php if (count($transactions) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle small">
                                        <thead>
                                            <tr>
                                                <th>Transaction ID</th>
                                                <th>User/Company</th>
                                                <th>Plan</th>
                                                <th>Amount ($)</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($transactions as $transaction): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($transaction['transaction_id']) ?></td>
                                                    <td><?= htmlspecialchars($transaction['user_company']) ?></td>
                                                    <td><?= htmlspecialchars($transaction['plan']) ?></td>
                                                    <td><?= htmlspecialchars($transaction['amount']) ?></td>
                                                    <td>
                                                        <?php 
                                                            $date = new DateTime($transaction['created_at']);
                                                            echo $date->format('l, Y-m-d H:i');
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">No transactions found matching your query.</p>
                            <?php endif; ?>
                        </div>

                    <?php endif; ?>
                </div>
            </div>
        </div>
        

        <?php include_once "footer.php"; ?>


        <!-- Load Bootstrap JS Bundle (includes Popper for dropdowns/modals) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>

            
        </script>
    </body>
</html>