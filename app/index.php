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
    }
} catch (exception $e) {
    $conn->close();
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

                    <!-- Start Main Widgets -->
                    <div class="row">
                        <div class="col-md-6 col-lg-4 col-xl">
                            <div class="card">
                                <div class="card-body">
                                    <div class="widget-first">

                                        <div class="d-flex align-items-center mb-2">
                                            <div class="p-2 border border-danger border-opacity-10 bg-danger-subtle rounded-2 me-2">
                                                <div class="bg-danger rounded-circle widget-size text-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                                        <path fill="#ffffff" d="M12 4a4 4 0 0 1 4 4a4 4 0 0 1-4 4a4 4 0 0 1-4-4a4 4 0 0 1 4-4m0 10c4.42 0 8 1.79 8 4v2H4v-2c0-2.21 3.58-4 8-4" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <p class="mb-0 text-dark fs-15">Total Visitor</p>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <h3 class="mb-0 fs-22 text-dark me-3">3,456</h3>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl">
                            <div class="card">
                                <div class="card-body">
                                    <div class="widget-first">

                                        <div class="d-flex align-items-center mb-2">
                                            <div
                                                class="p-2 border border-secondary border-opacity-10 bg-secondary-subtle rounded-2 me-2">
                                                <div class="bg-secondary rounded-circle widget-size text-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="#ffffff" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.0" stroke="currentColor" class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <p class="mb-0 text-dark fs-15">Views</p>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <h3 class="mb-0 fs-22 text-dark me-3">839</h3>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl">
                            <div class="card">
                                <div class="card-body">
                                    <div class="widget-first">

                                        <div class="d-flex align-items-center mb-2">
                                            <div class="p-2 border border-danger border-opacity-10 bg-danger-subtle rounded-2 me-2">
                                                <div class="bg-danger rounded-circle widget-size text-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="#ffffff" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="" class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <?php
                                            if ($user_global_variable !== false):
                                            ?>
                                                <p class="mb-0 text-dark fs-15">Sent Requests</p>
                                            <?php
                                            else:
                                            ?>
                                                <p class="mb-0 text-dark fs-15">Requests</p>
                                            <?php
                                            endif;
                                            ?>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <?php
                                            if ($user_global_variable !== false):
                                            ?>
                                                <h3 class="mb-0 fs-22 text-dark me-3"><?php echo $sent_request_count; ?></h3>
                                            <?php
                                            else:
                                            ?>
                                                <h3 class="mb-0 fs-22 text-dark me-3"><?php echo $recevied_request_count; ?></h3>
                                            <?php
                                            endif;
                                            ?>
                                        </div>

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
                                    <div class="d-flex align-items-center">
                                        <h5 class="card-title mb-0">Profile Views</h5>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div id="sales-overview" class="apex-charts"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 col-xl-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h5 class="card-title mb-0">Sales Pipeline</h5>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div id="top-session" class="apex-charts"></div>

                                    <div class="row mt-2">
                                        <div class="col">
                                            <div class="d-flex justify-content-between align-items-center p-1">
                                                <div>
                                                    <i class="mdi mdi-circle fs-12 align-middle me-1 text-success"></i>
                                                    <span class="align-middle fw-semibold">Won</span>
                                                </div>
                                                <span class="fw-medium text-muted float-end"><i
                                                        class="mdi mdi-arrow-up text-success align-middle fs-14 me-1"></i>12.48%</span>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center p-1">
                                                <div>
                                                    <i class="mdi mdi-circle fs-12 align-middle me-1"
                                                        style="color: #522c8f;"></i>
                                                    <span class="align-middle fw-semibold">Discovery</span>
                                                </div>
                                                <span class="fw-medium text-muted float-end"><i
                                                        class="mdi mdi-arrow-up text-success align-middle fs-14 me-1"></i>5.23%</span>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center p-1">
                                                <div>
                                                    <i class="mdi mdi-circle fs-12 align-middle me-1 text-warning"></i>
                                                    <span class="align-middle fw-semibold">Undiscovery</span>
                                                </div>
                                                <span class="fw-medium text-muted float-end"><i
                                                        class="mdi mdi-arrow-up text-success align-middle fs-14 me-1"></i>15.58%</span>
                                            </div>

                                        </div>
                                    </div>
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
                                                            <td class='text-center'><p class='mb-0 fs-14'>{$date}</p></td>
                                                            <td><p class='mb-0 fw-semibold'>{$planName}</p></td>
                                                            <td><p class='mb-0 fw-medium'>\${$amount}</p></td>
                                                            <td><span class='badge {$badgeClass} fw-semibold'>{$status}</span></td>
                                                            <td><a href='/invoice.php?txn={$transactionId}' class='mb-0 fw-medium'>[Download]</a></td>
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
    <script src="<?php echo $base_url; ?>assets/js/pages/crm-dashboard.init.js"></script>

    <!-- App js-->
    <script src="<?php echo $base_url; ?>assets/js/app.js"></script>

</body>

</html>