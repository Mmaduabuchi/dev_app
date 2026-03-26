<?php
//include auth file
include_once "auth.php";

//include route
include_once "route.php";

// Pagination settings
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

try {

    // Fetch total count for pagination
    $total_stmt = $conn->prepare("SELECT COUNT(*) FROM admin_audit_logs");
    $total_stmt->execute();
    $total_stmt->bind_result($total_rows);
    $total_stmt->fetch();
    $total_stmt->close();
    $total_pages = ceil($total_rows / $limit);

    // Fetch logs with admin names
    $query = "SELECT l.*, u.fullname as admin_name FROM admin_audit_logs l LEFT JOIN users u ON l.admin_id = u.id ORDER BY l.created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("ii", $limit, $offset);
    if (!$stmt->execute()) {
        throw new Exception('Database execute failed.');
    }
    $logs_result = $stmt->get_result();
    $stmt->close();

    // Helper function for time formatting
    function time_elapsed_string($datetime, $full = false) {
        if (!$datetime) return "some time ago";
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        
        $weeks = floor($diff->days / 7);
        
        $units = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        
        $values = array(
            'y' => $diff->y,
            'm' => $diff->m,
            'w' => $weeks,
            'd' => $diff->d - ($weeks * 7),
            'h' => $diff->h,
            'i' => $diff->i,
            's' => $diff->s,
        );

        $result = [];

        foreach ($values as $key => $val) {
            if ($val > 0) {
                $result[] = $val . ' ' . $units[$key] . ($val > 1 ? 's' : '');
            }
        }

        if (!$full) {
            $result = array_slice($result, 0, 1);
        }
        return $result ? implode(', ', $result) . ' ago' : 'just now';
    }

    // Action icon mapping
    $action_map = [
        'create_job' => ['icon' => 'bi-person-plus-fill', 'color' => 'text-primary'],
        'update_status' => ['icon' => 'bi-pencil-square', 'color' => 'text-warning'],
        'delete_user' => ['icon' => 'bi-trash-fill', 'color' => 'text-danger'],
        'login' => ['icon' => 'bi-box-arrow-in-right', 'color' => 'text-success'],
        'update_settings' => ['icon' => 'bi-gear-fill', 'color' => 'text-info'],
    ];
} catch (Exception $e) {
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

                <section>
                    <div class="row">
                        <div class="col-12">
                            <h2 class="mb-4">Activity Log</h2>
                            <div class="card">
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <?php if ($logs_result->num_rows > 0): ?>
                                            <?php while ($log = $logs_result->fetch_assoc()): ?>
                                                <?php 
                                                    $action = $log['action'];
                                                    $display_action = $action_map[$action] ?? ['icon' => 'bi-info-circle-fill', 'color' => 'text-secondary'];
                                                ?>
                                                <li class="list-group-item d-flex align-items-start py-3">
                                                    <i class="bi <?= $display_action['icon'] ?> fs-4 <?= $display_action['color'] ?> me-3"></i>
                                                    <div>
                                                        <p class="mb-1"><strong><?= htmlspecialchars($log['admin_name'] ?? 'System') ?></strong> <?= htmlspecialchars($log['action_description']) ?></p>
                                                        <small class="text-muted"><i class="bi bi-clock me-1"></i> <?= time_elapsed_string($log['created_at']) ?></small>
                                                    </div>
                                                </li>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <li class="list-group-item py-4 text-center text-muted">
                                                No activity logs found.
                                            </li>
                                        <?php endif; ?>
                                    </ul>

                                    <!-- Pagination -->
                                    <?php if ($total_pages > 1): ?>
                                        <nav aria-label="Page navigation" class="mt-4">
                                            <ul class="pagination justify-content-center">
                                                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                                    <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                                        <span aria-hidden="true">&laquo;</span>
                                                    </a>
                                                </li>
                                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                                    </li>
                                                <?php endfor; ?>
                                                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                                    <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                                        <span aria-hidden="true">&raquo;</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </nav>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </section>
            
            </div>
        </div>

        

        <?php include_once "footer.php"; ?>


        <!-- Load Bootstrap JS Bundle (includes Popper for dropdowns/modals) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>

            
        </script>
    </body>
</html>