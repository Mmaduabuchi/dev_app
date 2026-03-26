<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";
//notification count
require_once __DIR__ . '/fetch_notification_count.php';
//get usertoken from session
$usertoken = $_SESSION['user']['usertoken'] ?? null;
$employer_status = true;
try {
    //fetch all users details
    $stmt = $conn->prepare("SELECT * FROM `users` WHERE usertoken = ? AND id = ?");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("si", $usertoken, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user_global_variable !== false) {
        $stmt = $conn->prepare("SELECT * FROM `employer_profiles` WHERE user_id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows < 1) {
            // No employer profile found
            header("Location: /devhire/dashboard/error");
            exit;
        } else {
            $employer = $result->fetch_assoc();
            $company_action_status = $employer['action'] ?? null;

            // Check action status
            if ($company_action_status === "pending") {
                $employer_status = false;
            }
        }
        $stmt->close();

        $stmt = $conn->prepare("SELECT COUNT(*) as sent_request_count FROM `notifications` WHERE sender_id = ? AND deleted_at IS NULL");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        //response with notification count
        $sent_request_count = isset($data['sent_request_count']) ? (int)$data['sent_request_count'] : 0;
        $stmt->close();
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) as recevied_request_count FROM `notifications` WHERE user_id = ? AND deleted_at IS NULL");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        //response with notification count
        $recevied_request_count = isset($data['recevied_request_count']) ? (int)$data['recevied_request_count'] : 0;
        $stmt->close();

        //check education status
        $stmt = $conn->prepare("SELECT COUNT(*) FROM `education_records` WHERE user_id = ? AND deleted_at IS NULL");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($education_count);
        $stmt->fetch();
        $education_count = (int) $education_count;
        $stmt->close();

    }

    //check support ticket count
    $stmt = $conn->prepare("SELECT COUNT(*) FROM `support_ticket` WHERE user_id = ? AND deleted_at IS NULL");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($support_ticket_count);
    $stmt->fetch();
    $support_ticket_count = (int) $support_ticket_count;
    $stmt->close();

    // Fetch recent activity
    $recentActivityStmt = $conn->prepare("SELECT title, message, created_at, type FROM notifications WHERE user_id = ? AND deleted_at IS NULL ORDER BY created_at DESC LIMIT 6");
    $recentActivityStmt->bind_param("i", $user_id);
    $recentActivityStmt->execute();
    $recentActivityResults = $recentActivityStmt->get_result();
    $recentActivityStmt->close();

} catch (exception $e) {
    $conn->close();
    //session log
    $_SESSION['error'] = $e->getMessage();
    error_log($e->getMessage());
    // echo "Something went wrong. Please try again later.";
    header("Location: /devhire/dashboard/error");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title> Welcome to | Devhire - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo $base_url; ?>assets/images/favicon.ico">

    <!-- App css -->
    <link href="<?php echo $base_url; ?>assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons -->
    <link href="<?php echo $base_url; ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <script src="<?php echo $base_url; ?>assets/js/head.js"></script>

    <style>
        .modern-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            background: #fff;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        }

        .icon-box {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            color: #fff;
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #ff4d4f, #ff7875);
        }

        .bg-gradient-secondary {
            background: linear-gradient(135deg, #6c757d, #adb5bd);
        }

        .card-title-small {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .card-value {
            font-size: 26px;
            font-weight: 600;
            color: #111;
        }

        .alert-info {
            background-color: #e3f2fd;
            border-left: 5px solid #0d6efd;
            font-size: 0.95rem;
        }
        .alert-info a {
            text-decoration: underline;
            font-weight: 500;
        }
    </style>
</head>

<!-- body start -->

<body data-menu-color="light" data-sidebar="default">

    <!-- Begin page -->
    <div id="app-layout">

        <?php include "header.php" ?>

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">
                    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-18 fw-semibold m-0">Dashboard</h4>
                        </div>
                    </div>

                    <div class="row">
                        <?php if (!$sub_status): ?>
                            <div class="col-12">
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <strong>Hello <?= htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8'); ?>!</strong> Your do not have any active subscription.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <div class="col">
                            <?php if ($education_count === 0): ?>
                                <div class="alert alert-info d-flex align-items-center" role="alert">
                                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:">
                                        <use xlink:href="#info-fill"/>
                                    </svg>
                                    <div>
                                        Hey <?= htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8'); ?>! It looks like your education profile is empty. 
                                        <a href="/devhire/dashboard/resume" class="alert-link">Click here to set it up now.</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Start Main Widgets -->
                    <div class="row g-4">

                        <!-- Total Visitors -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card modern-card p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="card-title-small">Total Visitors</p>
                                        <h3 class="card-value">3,456</h3>
                                    </div>
                                    <div class="icon-box bg-gradient-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#fff" viewBox="0 0 24 24">
                                            <path d="M12 4a4 4 0 1 1 0 8a4 4 0 0 1 0-8m0 10c4.42 0 8 1.79 8 4v2H4v-2c0-2.21 3.58-4 8-4"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Views -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card modern-card p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="card-title-small text-muted">Support Tickets</p>
                                        <h3 class="card-value"><?= $support_ticket_count ?></h3>
                                    </div>
                                    <div class="icon-box bg-gradient-primary rounded-circle d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
                                        <!-- Support/Help icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#fff" viewBox="0 0 24 24">
                                            <path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm1 17h-2v-2h2v2zm1.071-7.071l-.071.071V15h-2v-2.586l.293-.293a1.5 1.5 0 0 0 .439-1.06c0-.828-.672-1.5-1.5-1.5S9 10.284 9 11h-2c0-1.654 1.346-3 3-3s3 1.346 3 3c0 .397-.105.767-.293 1.071z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Requests -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card modern-card p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <?php if ($user_global_variable !== false): ?>
                                            <p class="card-title-small">Sent Requests</p>
                                            <h3 class="card-value"><?php echo $sent_request_count; ?></h3>
                                        <?php else: ?>
                                            <p class="card-title-small">Requests</p>
                                            <h3 class="card-value"><?php echo $recevied_request_count; ?></h3>
                                        <?php endif; ?>
                                    </div>
                                    <div class="icon-box bg-gradient-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#fff" viewBox="0 0 24 24">
                                            <path d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- End Main Widgets -->

                    <div class="row">
                        <div class="col">
                            <?php
                            if ($employer_status === false):
                            ?>
                                <div class="alert alert-warning" role="alert">
                                    <div class="row">
                                        <div class="col col-md-8 pt-2">
                                            <p>Hi there! Kindly complete your profile status so we can better set up your account.</p>
                                        </div>
                                        <div class="col text-end pt-1">
                                            <a href="/devhire/dashboard/setup">
                                                <button class="btn btn-primary w-50">Setup</button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            endif;
                            ?>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-12 col-xl-8">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h5 class="card-title mb-0">Recent Activity</h5>
                                        <a href="/devhire/dashboard/myrequest" class="btn btn-sm btn-soft-primary">View All</a>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-nowrap mb-0">
                                            <tbody>
                                                <?php if ($recentActivityResults->num_rows > 0): ?>
                                                    <?php while ($activity = $recentActivityResults->fetch_assoc()): ?>
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="p-2 bg-primary-subtle text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                                        <i class="mdi mdi-bell-outline fs-16"></i>
                                                                    </div>
                                                                    <div>
                                                                        <h6 class="mb-0 fw-semibold fs-14"><?= htmlspecialchars($activity['title']) ?></h6>
                                                                        <p class="text-muted mb-0 fs-12"><?= htmlspecialchars(mb_strimwidth($activity['message'], 0, 80, "...")) ?></p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="text-end">
                                                                <span class="text-muted fs-12"><?= date("M d, H:i", strtotime($activity['created_at'])) ?></span>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="2" class="text-center py-4">
                                                            <div class="text-muted">
                                                                <i class="mdi mdi-information-outline fs-24 d-block mb-2"></i>
                                                                <p class="mb-0">No recent activity to show.</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 col-xl-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h5 class="card-title mb-0">Active Subscription Plan</h5>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <?php if ($sub_status && isset($current_plan_id)): ?>
                                        <?php
                                        // Fetch current plan details
                                        try {
                                            $planStmt = $conn->prepare("SELECT name, price FROM subscription_plans WHERE id = ?");
                                            $planStmt->bind_param("i", $current_plan_id);
                                            $planStmt->execute();
                                            $planResult = $planStmt->get_result();
                                            $activePlan = $planResult->fetch_assoc();
                                            $planStmt->close();
                                            
                                            $planName = $activePlan['name'] ?? 'Unknown Plan';
                                            $planPrice = number_format($activePlan['price'] ?? 0, 2);
                                            $startDate = isset($subscription['start_date']) ? date("M d, Y", strtotime($subscription['start_date'])) : 'N/A';
                                            $endDate = isset($subscription['end_date']) ? date("M d, Y", strtotime($subscription['end_date'])) : 'N/A';
                                        } catch (Exception $e) {
                                            $planName = 'Error locating plan';
                                            $planPrice = '0.00';
                                            $startDate = 'N/A';
                                            $endDate = 'N/A';
                                        }
                                        ?>
                                        <div class="text-center mb-4 mt-2">
                                            <div class="avatar-md bg-primary-subtle text-primary rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-shield-star fs-24"></i>
                                            </div>
                                            <h4 class="mb-1"><?= htmlspecialchars($planName) ?></h4>
                                            <p class="text-muted"><span class="fs-20 fw-bold text-dark">$<?= $planPrice ?></span> / month</p>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                            <span class="text-muted fw-medium">Status</span>
                                            <span class="badge bg-success-subtle text-success fs-12">Active</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                            <span class="text-muted fw-medium">Started On</span>
                                            <span class="fw-semibold text-dark"><?= $startDate ?></span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                            <span class="text-muted fw-medium">Expires On</span>
                                            <span class="fw-semibold text-dark"><?= $endDate ?></span>
                                        </div>

                                        <div class="mt-4 text-center">
                                            <a href="/devhire/dashboard/manage" class="btn btn-primary w-100">Manage Plan</a>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <div class="avatar-md bg-warning-subtle text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-alert-circle-outline fs-24"></i>
                                            </div>
                                            <h5 class="mb-2">No Active Plan</h5>
                                            <p class="text-muted mb-4">You are currently not subscribed to any premium features.</p>
                                            <a href="/devhire/dashboard/subscriptions" class="btn btn-primary w-100">Explore Plans</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>


                    </div>
                    <!-- end start -->

                    

                    <div class="row">
                        <!-- Subscription History / Logs -->
                        <div class="col-lg-6 col-xl-12">
                            <div class="card overflow-hidden">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h5 class="card-title mb-0">Subscription History / Logs</h5>

                                        <div class="ms-auto">
                                            <!-- <button class="btn btn-sm bg-light border dropdown-toggle fw-medium" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">View All<i class="mdi mdi-chevron-down ms-1 fs-14"></i></button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#">Today</a>
                                                <a class="dropdown-item" href="#">This Week</a>
                                                <a class="dropdown-item" href="#">Last Week</a>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>

                                <!-- start card body -->
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-traffic mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Plan</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Invoice</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                                                // Determine current page
                                                $limit = 5; // records per page
                                                $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                                                $offset = ($page - 1) * $limit;

                                                // Fetch paginated subscriptions
                                                try {
                                                    $stmt = $conn->prepare("
                                                        SELECT us.*, sp.name AS plan_name, sp.price AS plan_price
                                                        FROM subscriptions us
                                                        INNER JOIN subscription_plans sp ON us.plan_id = sp.id
                                                        WHERE us.user_id = ?
                                                        ORDER BY us.created_at DESC
                                                        LIMIT ? OFFSET ?
                                                    ");
                                                    if (!$stmt) throw new Exception('Prepare failed: ' . $conn->error);
                                                    $stmt->bind_param("iii", $user_id, $limit, $offset);
                                                    $stmt->execute();
                                                    $result = $stmt->get_result();

                                                    // Fetch total count for pagination
                                                    $totalStmt = $conn->prepare("SELECT COUNT(*) as total FROM subscriptions WHERE user_id = ?");
                                                    $totalStmt->bind_param("i", $user_id);
                                                    $totalStmt->execute();
                                                    $totalRecords = $totalStmt->get_result()->fetch_assoc()['total'];
                                                    $totalPages = ceil($totalRecords / $limit);
                                                } catch (Exception $e) {
                                                    $conn->close();
                                                    error_log($e->getMessage());
                                                    echo "Something went wrong. Please try again later.";
                                                }

                                                //render
                                                if ($result->num_rows < 1) {
                                                    echo '<tr><td colspan="5" class="text-center text-muted">No subscriptions record found.</td></tr>';
                                                } else {
                                                    while ($data = $result->fetch_assoc()) {
                                                        $date = date("M d, Y", strtotime($data['created_at']));
                                                        $planName = $data['plan_name'];
                                                        $amount = number_format($data['plan_price'], 2);
                                                        $status = ucfirst($data['status']);
                                                        $transactionId = $data['transaction_id'];

                                                        // Optional: badge color based on status
                                                        switch (strtolower($data['status'])) {
                                                            case 'active':
                                                                $badgeClass = 'bg-success-subtle text-success';
                                                                break;
                                                            case 'expired':
                                                                $badgeClass = 'bg-danger-subtle text-danger';
                                                                break;
                                                            case 'cancelled':
                                                                $badgeClass = 'bg-secondary-subtle text-secondary';
                                                                break;
                                                            default:
                                                                $badgeClass = 'bg-primary-subtle text-primary';
                                                        }

                                                        echo "<tr>
                                                            <td><p class='mb-0 fs-14'>{$date}</p></td>
                                                            <td><p class='mb-0 fw-semibold'>{$planName}</p></td>
                                                            <td><p class='mb-0 fw-medium'>\${$amount}</p></td>
                                                            <td><span class='badge {$badgeClass} fw-semibold'>{$status}</span></td>
                                                            <td><a href='./process/process_invoice.php?txn={$transactionId}' class='mb-0 fw-medium'>[Download]</a></td>
                                                        </tr>";
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>

                                        <!-- Pagination -->
                                        <?php if ($totalPages > 1): ?>
                                            <nav>
                                                <ul class="pagination justify-content-center mt-3">
                                                    <?php if ($page > 1): ?>
                                                        <li class="page-item">
                                                            <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                                                        </li>
                                                    <?php endif; ?>

                                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                                        </li>
                                                    <?php endfor; ?>

                                                    <?php if ($page < $totalPages): ?>
                                                        <li class="page-item">
                                                            <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </nav>
                                        <?php endif; ?>

                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                        </div>

                    </div>

                </div> <!-- container-fluid -->
            </div> <!-- content -->

            <!-- Footer Start -->
            <?php include_once "footer.php"; ?>
            <!-- end Footer -->

        </div>

    </div>
    <!-- END wrapper -->

    <!-- Vendor -->
    <script src="<?php echo $base_url; ?>assets/libs/jquery/jquery.min.js"></script>
    <script src="<?php echo $base_url; ?>assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $base_url; ?>assets/libs/simplebar/simplebar.min.js"></script>
    <script src="<?php echo $base_url; ?>assets/libs/node-waves/waves.min.js"></script>
    <script src="<?php echo $base_url; ?>assets/libs/waypoints/lib/jquery.waypoints.min.js"></script>
    <script src="<?php echo $base_url; ?>assets/libs/jquery.counterup/jquery.counterup.min.js"></script>
    <script src="<?php echo $base_url; ?>assets/libs/feather-icons/feather.min.js"></script>

    <!-- Apexcharts JS -->
    <script src="<?php echo $base_url; ?>assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- Widgets Init Js -->
    <!-- Dashboard Init JS (Removed) -->
    <!-- <script src="<?php echo $base_url; ?>assets/js/pages/crm-dashboard.init.js"></script> -->

    <!-- App js-->
    <script src="<?php echo $base_url; ?>assets/js/app.js"></script>

</body>

</html>