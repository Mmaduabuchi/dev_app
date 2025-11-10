<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";
//notification count
require_once __DIR__ . '/fetch_notification_count.php';
//get usertoken from session
$usertoken = $_SESSION['user']['usertoken'] ?? null;

// fetch user data from database
try {
    //Fetch user data
    $stmt = $conn->prepare("SELECT email, fullname FROM users WHERE id = ?");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        throw new Exception("User not found.");
    }

    //User details
    $user_email = $user['email'];
    $user_fullname = $user['fullname'];

    //mark all user notifications as read
    $stmt = $conn->prepare("UPDATE `notifications` SET status = 'read' WHERE user_id = ? AND deleted_at IS NULL");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

} catch (Exception $e) {
    // handle error gracefully
    die("Error: " . $e->getMessage());
} finally {
    //End connection
    if (isset($stmt)) $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title>Myrequest | devhire - Dashboard</title>
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
        /* .education-card {
            border-radius: 12px;
            border: 1px solid #e5e5e5;
            padding: 2rem;
            background: #fff;
        }

        .edu-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2rem;
            position: relative;
        }

        .edu-number {
            width: 40px;
            height: 40px;
            border: 2px solid #198754;
            color: #198754;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .edu-line {
            position: absolute;
            left: 19px;
            top: 45px;
            width: 2px;
            height: calc(100% - 45px);
            background-color: #e5e5e5;
        }

        .edu-content h6 {
            color: #198754;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .edu-content h5 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .edu-content p {
            color: #6c757d;
            margin-bottom: 0;
        } */
    </style>
</head>

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
                            <h4 class="fs-18 fw-semibold m-0">My Request</h4>
                        </div>
                    </div>

                    <!-- General Form -->
                    <div class="row">
                        <div class="col">
                            <div class="card">

                                <div class="card-header">
                                    <h5 class="card-title mb-0">Request notification</h5>
                                </div>

                                <div class="card-body">
                                    <div class="mb-3">
                                        <?php
                                        // Pagination setup
                                        $limit = 5; // Number of notifications per page
                                        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
                                        $offset = ($page - 1) * $limit;

                                        // Count total notifications
                                        $countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM notifications WHERE user_id = ? AND deleted_at IS NULL");
                                        $countStmt->bind_param("i", $user_id);
                                        $countStmt->execute();
                                        $countResult = $countStmt->get_result();
                                        $total = $countResult->fetch_assoc()['total'] ?? 0;
                                        $countStmt->close();

                                        $totalPages = ceil($total / $limit);

                                        // Fetch paginated notifications
                                        $stmt = $conn->prepare("SELECT id, title, created_at  FROM notifications WHERE user_id = ? AND deleted_at IS NULL ORDER BY created_at DESC LIMIT ?, ?");
                                        $stmt->bind_param("iii", $user_id, $offset, $limit);
                                        $stmt->execute();
                                        $result = $stmt->get_result();

                                        if ($result && $result->num_rows > 0) {
                                            while ($notify = $result->fetch_assoc()):
                                                $title = htmlspecialchars($notify['title'] ?? 'No title', ENT_QUOTES, 'UTF-8');
                                                $nid = (int)$notify['id'];
                                                $time = isset($notify['created_at']) ? date('M j, Y H:i', strtotime($notify['created_at'])) : '';
                                        ?>
                                                <div class="d-flex justify-content-between align-items-center p-3 mb-2 bg-light rounded">
                                                    <div>
                                                        <div class="fw-semibold"><?= $title ?></div>
                                                        <?php if ($time): ?><small class="text-muted"><?= $time ?></small><?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2" onclick="deleteNotification(<?= $nid ?>)"  title="Delete Notification" aria-label="Delete Notification">
                                                            <i class="mdi mdi-close fs-5 align-middle"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                        <?php
                                            endwhile;
                                        } else {
                                            echo "<p class='bg-light rounded p-3 mb-2 mb-0'>No request sent yet.</p>";
                                        }

                                        if (isset($stmt)) $stmt->close();
                                        ?>

                                        <!-- Pagination Links -->
                                        <?php if ($totalPages > 1): ?>
                                            <nav>
                                                <ul class="pagination justify-content-center mt-3">
                                                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                                        <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                                                    </li>

                                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                                        </li>
                                                    <?php endfor; ?>

                                                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                                        <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                                                    </li>
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

    <!-- App js-->
    <script src="<?php echo $base_url; ?>assets/js/app.js"></script>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        //delete resume file
        function deleteNotification(data) {
            // Show confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to delete this request?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (!result.isConfirmed) {
                    return; // User cancelled the action
                }
                // Continue with deletion below
                const formData = new FormData();
                formData.append('nid', data);
                formData.append('token', <?= json_encode($usertoken) ?>);

                fetch('<?php echo $base_url; ?>process/process_delete_request.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 2500
                            });
                            setTimeout(() => location.reload(), 2600);
                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: data.message,
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
                            title: 'An error occurred while processing your request. Please try again later.',
                            showConfirmButton: false,
                            timer: 2500
                        });
                    });
            });
        }
    </script>

</body>

</html>