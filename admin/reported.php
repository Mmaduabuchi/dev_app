<?php
//include auth file
include_once "auth.php";

//include route
include_once "route.php";

$total_account_reports = 0;
try{
    // Total account_reports
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM account_reports");
    if($stmt === false){
        throw new Exception("Failed to prepare statement.");
    }
    $stmt->execute();
    $total_account_reports = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

} catch (Exception $e) {
    $total_account_reports = 0;
    // echo "SERVER ERROR:: " . $e->getMessage();
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

                <div class="page-content" id="reported-accounts">
                    <h1 class="mb-4 fs-3">Reported Accounts</h1>
                    <div class="card p-4">
                        <h5 class="card-title fw-bold mb-3">Reports Pending Review (<?= $total_account_reports ?> total)</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle small">
                                <thead>
                                    <tr>
                                        <th>Report ID</th>
                                        <th>Reported User</th>
                                        <th>Report Reason</th>
                                        <th>Submitted By</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $limit = 15; // reports per page 
                                        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                                        $page = max($page, 1);
                                        $offset = ($page - 1) * $limit;

                                        // Get total reports
                                        $totalStmt = $conn->prepare("SELECT COUNT(*) AS total FROM account_reports");
                                        $totalStmt->execute();
                                        $totalResult = $totalStmt->get_result()->fetch_assoc();
                                        $totalReports = $totalResult['total'];
                                        $totalPages = ceil($totalReports / $limit);
                                        $totalStmt->close();

                                        // Fetch reports with pagination
                                        try{
                                            $stmt = $conn->prepare("SELECT 
                                                    ar.*,
                                                    reported.fullname AS reported_name,
                                                    reported.email AS reported_email,
                                                    reporter.fullname AS reporter_name,
                                                    reporter.email AS reporter_email
                                                FROM account_reports ar
                                                LEFT JOIN users reported ON ar.reported_user_id = reported.id
                                                LEFT JOIN users reporter ON ar.reported_by = reporter.id
                                                ORDER BY ar.created_at DESC LIMIT ? OFFSET ?
                                            ");
                                            if($stmt === false){
                                                throw new Exception("Failed to prepare statement.");
                                            }
                                            $stmt->bind_param("ii", $limit, $offset);
                                            $stmt->execute();
                                            $result = $stmt->get_result();
                                            if ($result->num_rows < 1) {
                                                echo "<tr><td colspan='6'>No open reports found.</td></tr>";
                                            } else{
                                                while($row = $result->fetch_assoc()){
                                                    $msg = htmlspecialchars($row['message']);
                                                    if($msg === null || $msg === ''){
                                                        $msg = 'No message provided';
                                                    }
                                    ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($row['report_id']) ?></td>
                                                        <td>
                                                            <?= htmlspecialchars($row['reported_name']) ?> <br>
                                                            <small class="text-muted"><?= htmlspecialchars($row['reported_email']) ?></small>
                                                        </td>
                                                        <td><?= htmlspecialchars($row['reason']) ?></td>
                                                        <td>
                                                            <?= htmlspecialchars($row['reporter_name']) ?><br>
                                                            <small class="text-muted"><?= htmlspecialchars($row['reporter_email']) ?></small>
                                                        </td>
                                                        <td><?= (htmlspecialchars($row['status']) == "") ? "Open" : "Resolved" ?></td>
                                                        <td>
                                                            <?php 
                                                                $date = new DateTime($row['created_at']);
                                                                echo $date->format('M d, Y h:i A');
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-info me-1 view-evidence" title="View Evidence" data-bs-toggle="modal" data-bs-target="#evidenceModal"
                                                                data-message="<?= $msg ?>"
                                                                data-reason="<?= htmlspecialchars($row['reason']) ?>"
                                                                data-report-id="<?= htmlspecialchars($row['report_id']) ?>">
                                                                <i class="bi bi-search"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-warning me-1 warn-user" title="Warn User"
                                                                data-user-id="<?= htmlspecialchars($row['reported_user_id']) ?>"
                                                                data-report-id="<?= htmlspecialchars($row['report_id']) ?>">
                                                                <i class="bi bi-exclamation-triangle"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-danger ban-user" title="Ban"
                                                                data-user-id="<?= htmlspecialchars($row['reported_user_id']) ?>"
                                                                data-report-id="<?= htmlspecialchars($row['report_id']) ?>">
                                                                <i class="bi bi-x-octagon"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-success ms-2 dismiss-report" title="Dismiss"
                                                                data-user-id="<?= htmlspecialchars($row['reported_user_id']) ?>"
                                                                data-report-id="<?= htmlspecialchars($row['report_id']) ?>">
                                                                <i class="bi bi-check-lg"></i>
                                                            </button>
                                                        </td>
                                                    </tr>                                    
                                    <?php
                                                }
                                            }

                                        } catch (Exception $e) {
                                            echo "<tr><td colspan='6' class='text-center text-danger'>" . $e->getMessage() . "</td></tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <nav aria-label="Reports pagination">
                            <ul class="pagination justify-content-end mt-3 flex-wrap">
                                <!-- Previous -->
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                                </li>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <!-- Next -->
                                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>            
            </div>
        </div>
        
        <!-- Modal for Reported Accounts Evidence -->
        <div class="modal fade" id="evidenceModal" tabindex="-1" aria-labelledby="evidenceModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="evidenceModalLabel">Report Evidence for <span class="fw-bold"> RPT-164508547</span> </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="evidenceModalBody">
                        <!-- <p>                            
                            <span class="fw-bold">Report Reason: </span> 
                            <span>Abusive Behavior</span> 
                        </p>                        
                        <p class="fw-bold">Report Message:</p>
                        <p>Talent profile claims experience with 'Quantum Computing' but their GitHub is empty.</p> -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Action Confirmation Modal -->
        <div class="modal fade" id="actionConfirmModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="actionModalTitle">Confirm Action</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body" id="actionModalBody">
                        Are you sure you want to proceed?
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn" id="confirmActionBtn">Confirm</button>
                    </div>

                </div>
            </div>
        </div>


        

        <?php include_once "footer.php"; ?>

        <!-- Load Bootstrap JS Bundle (includes Popper for dropdowns/modals) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>

            document.addEventListener("click", function(e) {
                if (e.target.closest(".view-evidence")) {
                    const btn = e.target.closest(".view-evidence");
                    const reportId = btn.dataset.reportId;
                    const reason = btn.dataset.reason;
                    const message = btn.dataset.message;

                    // Set modal content
                    document.getElementById("evidenceModalLabel").innerHTML = `Report Evidence for <span class="fw-bold">${reportId}</span>`;
                    document.getElementById("evidenceModalBody").innerHTML = `
                        <p><span class="fw-bold">Report Reason: </span> <span>${reason}</span></p>
                        <p class="fw-bold">Report Message:</p>
                        <p>${message}</p>
                    `;
                }
            });

            let pendingAction = {
                action: null,
                userId: null,
                reportId: null
            };

            // SweetAlert Toast Config
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true
            });

            document.addEventListener("click", function (e) {

                // WARN USER
                if (e.target.closest(".warn-user")) {
                    const btn = e.target.closest(".warn-user");
                    openModal({
                        action: "warn",
                        userId: btn.dataset.userId,
                        reportId: btn.dataset.reportId,
                        title: "Warn User",
                        body: "Are you sure you want to <strong>warn</strong> this user?",
                        btnClass: "btn-warning"
                    });
                }

                // BAN USER
                if (e.target.closest(".ban-user")) {
                    const btn = e.target.closest(".ban-user");
                    openModal({
                        action: "ban",
                        userId: btn.dataset.userId,
                        reportId: btn.dataset.reportId,
                        title: "Ban User",
                        body: "<span class='text-danger fw-bold'>This will permanently ban this user.</span><br>Are you sure?",
                        btnClass: "btn-danger"
                    });
                }

                // DISMISS REPORT
                if (e.target.closest(".dismiss-report")) {
                    const btn = e.target.closest(".dismiss-report");
                    openModal({
                        action: "dismiss",
                        userId: null,
                        reportId: btn.dataset.reportId,
                        title: "Dismiss Report",
                        body: "This report will be dismissed with no action on the user.",
                        btnClass: "btn-success"
                    });
                }
            });

            function openModal({ action, userId, reportId, title, body, btnClass }) {
                pendingAction = { action, userId, reportId };

                document.getElementById("actionModalTitle").innerText = title;
                document.getElementById("actionModalBody").innerHTML = body;

                const confirmBtn = document.getElementById("confirmActionBtn");
                confirmBtn.className = `btn ${btnClass}`;
                confirmBtn.innerText = "Yes, Continue";

                new bootstrap.Modal(
                    document.getElementById("actionConfirmModal")
                ).show();
            }

            // CONFIRM BUTTON
            document.getElementById("confirmActionBtn").addEventListener("click", function () {

                this.disabled = true;
                this.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Processing`;

                fetch("./../process/process_report_action.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(pendingAction)
                })
                .then(res => res.json())
                .then(data => {
                    bootstrap.Modal.getInstance(
                        document.getElementById("actionConfirmModal")
                    ).hide();

                    if (data.status === "success") {
                        Toast.fire({
                            icon: "success",
                            title: data.message || "Action completed successfully"
                        });

                        setTimeout(() => location.reload(), 1200);
                    } else {
                        Toast.fire({
                            icon: "error",
                            title: data.message || "Action failed"
                        });
                    }
                })
                .catch(() => {
                    Toast.fire({
                        icon: "error",
                        title: "Server error occurred"
                    });
                })
                .finally(() => {
                    const btn = document.getElementById("confirmActionBtn");
                    btn.disabled = false;
                    btn.innerText = "Confirm";
                });
            });
        </script>

    </body>
</html>