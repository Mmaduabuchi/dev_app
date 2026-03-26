<?php
//include auth file
include_once "auth.php";

//include route
include_once "route.php";

try {
    //mark all reports as read
    $stmt = $conn->prepare("UPDATE reports SET status = 'read' WHERE (status IS NULL OR status = 'unread') AND deleted_at IS NULL");
    if ($stmt === false) {
        throw new Exception('Database error: ' . $conn->error);
    }
    if (!$stmt->execute()) {
        throw new Exception('Failed to update reports.');
    }
    $stmt->close();
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
                background-color: #111827;
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

                <div class="page-content" id="messages-requests">
                    <h1 class="mb-4 fs-3">Notifications</h1>
                    <div class="card p-4">
                        <h5 class="card-title fw-bold mb-3">User Reports Submissions</h5>
                        <div class="list-group">
                            <?php
                            try {
                                $stmt = $conn->prepare("SELECT id, fullname, email, report_title, report_data, created_at FROM reports WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 20");
                                if (!$stmt) {
                                    throw new Exception("Database error: " . $conn->error);
                                }
                                $stmt->execute();
                                $result = $stmt->get_result();
                                if ($result->num_rows === 0) {
                                    echo '<div class="alert alert-info">No reports found.</div>';
                                } else {
                                    while ($report = $result->fetch_assoc()) {
                                        $report_id = $report['id'];
                                        $fullname = htmlspecialchars($report['fullname']);
                                        $email = htmlspecialchars($report['email']);
                                        $title = htmlspecialchars($report['report_title']);
                                        $data = htmlspecialchars($report['report_data']);
                                        $created_at = new DateTime($report['created_at']);
                                        $now = new DateTime();
                                        $diff = $now->diff($created_at);
                                        
                                        if ($diff->d == 0) {
                                            $time_ago = $diff->h > 0 ? $diff->h . " hours ago" : $diff->i . " mins ago";
                                            if ($diff->h == 0 && $diff->i == 0) $time_ago = "Just now";
                                        } elseif ($diff->d == 1) {
                                            $time_ago = "Yesterday";
                                        } else {
                                            $time_ago = $diff->d . " days ago";
                                        }
                            ?>
                                        <div class="list-group-item list-group-item-action border-start-0 border-end-0 border-top-0 mb-2 pb-3" id="report-item-<?= $report_id ?>">
                                            <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0 fw-bold text-primary"><?= $title ?></h6>
                                                <div class="d-flex align-items-center gap-3">
                                                    <small class="text-muted"><?= $time_ago ?></small>
                                                    <button class="btn btn-sm btn-outline-danger delete-report-btn" 
                                                            data-id="<?= $report_id ?>" 
                                                            title="Delete Report">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <span class="badge bg-light text-dark fw-normal border"><?= $fullname ?> (<?= $email ?>)</span>
                                            </div>
                                            <p class="mb-1 small text-secondary"><?= $data ?></p>
                                        </div>
                            <?php
                                    }
                                }
                                $stmt->close();
                            } catch (Exception $e) {
                                echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>

        

        <?php include_once "footer.php"; ?>


        <!-- Load Bootstrap JS Bundle (includes Popper for dropdowns/modals) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.querySelectorAll('.delete-report-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const reportId = this.getAttribute('data-id');
                    const reportItem = document.getElementById(`report-item-${reportId}`);



                    // SweetAlert Toast Config
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This report will be marked as deleted.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('./../process/process_delete_report.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ report_id: reportId })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    Toast.fire({
                                        icon: 'success',
                                        title: data.message
                                    });
                                    reportItem.remove();
                                } else {
                                    Toast.fire({
                                        icon: 'error',
                                        title: data.message
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Toast.fire({
                                    icon: 'error',
                                    title: 'An unexpected error occurred.'
                                });
                            });
                        }
                    });
                });
            });
        </script>
    </body>
</html>