<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";
//notification count
require_once __DIR__ . '/fetch_notification_count.php';



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
?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title>Manage Subscription | devhire - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo $base_url; ?>assets/images/favicon.ico">

    <!-- plugin css -->
    <link href="<?php echo $base_url; ?>assets/libs/jsvectormap/css/jsvectormap.min.css" rel="stylesheet" type="text/css" />

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
                            <h4 class="fs-18 fw-semibold m-0">Manage Subscription</h4>
                        </div>
                    </div>

                    <!-- Start Row -->
                    <div class="row">

                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <h5 class="text-dark">Plan & Billing</h5>
                                            <p>Manage your plan and payments</p>
                                        </div>
                                        <div class="col-12 col-md-6 text-end pt-2">
                                            <button class="btn btn-outline-dark" id="Cancelsub">Cancel subscription</button>
                                            <button class="btn btn-outline-dark">Manage payments</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body bg-light-subtle">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <h5 class="text-dark">Current Plan</h5>
                                        </div>
                                        <div class="col-12 col-md-6 text-end">
                                            <button class="btn btn-outline-dark">Change Plan</button>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="widget-first">

                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <p class="mb-0 text-dark fs-15">Monthly Plan</p>
                                                            <div>
                                                                <span class="badge text-success badge-custom-second bg-success-subtle fw-medium rounded-4 fs-14 me-2 contact-badge">
                                                                    Active
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <h3 class="mb-0 fs-22 text-dark me-3">$50/Monthly</h3>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="widget-first">

                                                        <div class="d-flex align-items-center mb-2">
                                                            <p class="mb-0 text-dark fs-15">Renew at</p>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <h3 class="mb-0 fs-22 text-dark me-3">Oct 26, 2025</h3>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- End Start -->


                    <div class="row">
                        <div class="col-lg-6 col-xl-12">
                            <div class="card overflow-hidden">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h5 class="card-title mb-0">Subscription History / Logs</h5>
                                        <div class="ms-auto">
                                            <button class="btn btn-sm bg-light border dropdown-toggle fw-medium" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                View All <i class="mdi mdi-chevron-down ms-1 fs-14"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#">Today</a>
                                                <a class="dropdown-item" href="#">This Week</a>
                                                <a class="dropdown-item" href="#">Last Week</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

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
                                                        switch(strtolower($data['status'])){
                                                            case 'active': $badgeClass = 'bg-success-subtle text-success'; break;
                                                            case 'expired': $badgeClass = 'bg-danger-subtle text-danger'; break;
                                                            case 'cancelled': $badgeClass = 'bg-secondary-subtle text-secondary'; break;
                                                            default: $badgeClass = 'bg-primary-subtle text-primary';
                                                        }

                                                        echo "<tr>
                                                            <td class='text-center'><p class='mb-0 fs-14'>{$date}</p></td>
                                                            <td><span class='badge {$badgeClass} fw-semibold'>{$planName}</span></td>
                                                            <td><p class='mb-0 fw-medium'>\${$amount}</p></td>
                                                            <td><p class='mb-0 fw-medium'>{$status}</p></td>
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
                                                    <?php if($page > 1): ?>
                                                        <li class="page-item">
                                                            <a class="page-link" href="?page=<?= $page-1 ?>">Previous</a>
                                                        </li>
                                                    <?php endif; ?>

                                                    <?php for($i=1; $i<=$totalPages; $i++): ?>
                                                        <li class="page-item <?= $i==$page?'active':'' ?>">
                                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                                        </li>
                                                    <?php endfor; ?>

                                                    <?php if($page < $totalPages): ?>
                                                        <li class="page-item">
                                                            <a class="page-link" href="?page=<?= $page+1 ?>">Next</a>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </nav>
                                        <?php endif; ?>

                                    </div>
                                </div>
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

    <!-- for basic area chart -->
    <script src="../../../apexcharts.com/samples/assets/stock-prices.js"></script>

    <!-- Vector map-->
    <script src="<?php echo $base_url; ?>assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="<?php echo $base_url; ?>assets/libs/jsvectormap/maps/world-merc.js"></script>

    <!-- Widgets Init Js -->
    <script src="<?php echo $base_url; ?>assets/js/pages/ecommerce-dashboard.init.js"></script>

    <!-- App js-->
    <script src="<?php echo $base_url; ?>assets/js/app.js"></script>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const cancelBtn = document.querySelector("#Cancelsub");
            if (!cancelBtn) return; 

            cancelBtn.addEventListener("click", () => {
                // Confirm action
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to cancel this subscription?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, cancel it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    //Disable button to prevent double-click
                    cancelBtn.disabled = true;

                    fetch('<?php echo $base_url; ?>process/process_cancel_user_subscription.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': '<?php echo $_SESSION["csrf_token"]; ?>'
                        },
                        body: JSON.stringify({
                            action: 'cancel_subscription'
                        })
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok.');
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message || 'Subscription cancelled successfully.',
                                showConfirmButton: false,
                                timer: 2500
                            });
                            setTimeout(() => location.reload(), 2600);
                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: data.message || 'Failed to cancel subscription.',
                                showConfirmButton: false,
                                timer: 2500
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'An unexpected error occurred. Please try again later.',
                            showConfirmButton: false,
                            timer: 2500
                        });
                    })
                    .finally(() => {
                        cancelBtn.disabled = false; // Re-enable the button
                    });
                });
            });
        });
    </script>

</body>

</html>