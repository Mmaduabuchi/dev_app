<?php
//include auth file
include_once "auth.php";

//include route
include_once "route.php";


try{
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

                <div class="page-content" id="users-management">
                    <h1 class="mb-4 fs-3">Employers Management</h1>

                    <ul class="nav nav-tabs mb-4" id="userTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="talent-tab" data-bs-toggle="tab" data-bs-target="#talents-pane" type="button" role="tab" aria-controls="talents-pane" aria-selected="true">Employers / CEOs</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="talents-pane" role="tabpanel" aria-labelledby="talent-tab">
                            <div class="card p-4">
                                <h5 class="card-title fw-bold mb-3">Employer Roster (<?= $totalEmployers ?> Total)</h5>

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle small">
                                        <thead>
                                            <tr>
                                                <th>Company Name</th>
                                                <th>Industry</th>
                                                <th>Website</th>
                                                <th>Subscription</th>
                                                <th>Verification</th>
                                                <th>Registered Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                try{
                                                    $limit = 15; // rows per page
                                                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                                    $page = max($page, 1); // ensure page is at least 1
                                                    $offset = ($page - 1) * $limit;

                                                    // get total users for pagination
                                                    $totalUsersStmt = $conn->prepare("SELECT COUNT(*) as total FROM users u WHERE u.user_type = 'employer' AND u.deleted_at IS NULL");
                                                    $totalUsersStmt->execute();
                                                    $totalUsersResult = $totalUsersStmt->get_result()->fetch_assoc();
                                                    $totalUsers = $totalUsersResult['total'];
                                                    $totalPages = ceil($totalUsers / $limit);

                                                    $stmt = $conn->prepare("SELECT 
                                                    u.id, 
                                                    u.fullname, 
                                                    u.email, 
                                                    u.user_type, 
                                                    u.is_profile_complete, 
                                                    u.suspended_at, dp.*,
                                                    sp.name AS plan_name,
                                                    s.status AS subscription_status
                                                    FROM users u LEFT JOIN employer_profiles dp ON u.id = dp.user_id 
                                                    
                                                    LEFT JOIN subscriptions s 
                                                        ON s.id = (
                                                            SELECT id FROM subscriptions 
                                                            WHERE user_id = u.id 
                                                            ORDER BY id DESC 
                                                            LIMIT 1
                                                        )

                                                    LEFT JOIN subscription_plans sp 
                                                        ON s.plan_id = sp.id 

                                                    WHERE u.user_type = 'employer' AND u.deleted_at IS NULL  ORDER BY u.created_at DESC LIMIT ? OFFSET ?");
                                                    if($stmt === false){
                                                        throw new Exception("Failed to prepare statement.");
                                                    }
                                                    $stmt->bind_param("ii", $limit, $offset);
                                                    $stmt->execute();
                                                    $result = $stmt->get_result();
                                                    if($result->num_rows > 0){
                                                        while ($user = $result->fetch_assoc()) {
                                                            $status = ($user["action"] === "completed") ? "Verified" : "Not Verified";
                                            ?>
                                                            <tr>
                                                                <td><?= htmlspecialchars($user['company_name']) ?></td>
                                                                <td><?= htmlspecialchars(ucfirst($user["industry"])) ?></td>
                                                                <td><?= (htmlspecialchars($user["website"]) == "") ? "N/A" : htmlspecialchars($user["website"]) ?></td>
                                                                <?php
                                                                    $planName = $user['plan_name'] ?? null;
                                                                    $status_sub = $user['subscription_status'] ?? null;

                                                                    if (!$planName) {
                                                                        // No subscription at all
                                                                        $badgeClass = 'secondary';
                                                                        $displayText = 'Free Plan';

                                                                    } else {
                                                                        switch (strtolower($status_sub)) {

                                                                            case 'active':
                                                                                $badgeClass = match(strtolower($planName)) {
                                                                                    'premium' => 'warning',
                                                                                    'standard' => 'primary',
                                                                                    default => 'secondary'
                                                                                };
                                                                                $displayText = ucfirst($planName);
                                                                                break;

                                                                            case 'expired':
                                                                            case 'cancelled':
                                                                                $badgeClass = 'danger';
                                                                                $displayText = 'Inactive Plan';
                                                                                break;

                                                                            default:
                                                                                $badgeClass = 'secondary';
                                                                                $displayText = 'Free Plan';
                                                                        }
                                                                    }
                                                                ?>
                                                                <td>
                                                                    <span class="badge bg-<?= $badgeClass ?>">
                                                                        <?= $displayText ?>
                                                                    </span>
                                                                </td>
                                                                <td><span class="badge bg-<?= ($user["action"] === "completed") ? "success" : "secondary" ?>"><?= $status ?></span></td>
                                                                <td>
                                                                    <?php 
                                                                        $date = new DateTime($user['created_at']);
                                                                        echo $date->format('l, Y-m-d H:i');
                                                                    ?>
                                                                </td>
                                                                <td class="text-center">
                                                                    <?php
                                                                        $token_ref = bin2hex(random_bytes(36));
                                                                    ?>
                                                                    <a href="employer_review?token_ref=<?= $token_ref ?>&employer_id=<?= htmlspecialchars($user['user_id']) ?>">
                                                                        <button class="btn btn-sm btn-outline-info me-1 view-profile-btn" title="View Profile">
                                                                            <i class="bi bi-eye"></i>
                                                                        </button>
                                                                    </a>
                                                                    <?php
                                                                        if($user["suspended_at"] !== null):
                                                                    ?>
                                                                        <button class="btn btn-sm btn-outline-danger" title="Suspended"><i class="bi bi-slash-circle"></i></button>
                                                                    <?php
                                                                        endif;
                                                                    ?>
                                                                </td>
                                                            </tr>
                                            <?php
                                                        }
                                                    } else {
                                                        echo "<tr>";
                                                        echo "<td colspan='6' class='text-center'>No Talents found.</td>";
                                                        echo "</tr>";
                                                    }
                                                } catch (Exception $e) {
                                                    echo "<tr><td colspan='6'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                                                }
                                            ?>
                                        </tbody>
                                    </table>

                                </div>

                                <!-- Pagination -->
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-end mt-3">
                                        <!-- Previous button -->
                                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                                        </li>

                                        <!-- Page numbers -->
                                        <?php for($i = 1; $i <= $totalPages; $i++): ?>
                                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <!-- Next button -->
                                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                                        </li>
                                    </ul>
                                </nav>

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