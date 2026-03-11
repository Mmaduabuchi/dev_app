<?php
//include auth file
include_once "auth.php";

//include route
include_once "route.php";

$totalOpen = $highPriority = $newToday = 0;
try{
    // Total open tickets
    $totalOpenStmt = $conn->prepare("SELECT COUNT(*) AS total FROM support_ticket WHERE deleted_at IS NULL");
    if($totalOpenStmt === false){
        throw new Exception("Failed to prepare statement.");
    }
    $totalOpenStmt->execute();
    $totalOpen = $totalOpenStmt->get_result()->fetch_assoc()['total'];
    $totalOpenStmt->close();

    // High priority open tickets
    $highPriorityStmt = $conn->prepare("SELECT COUNT(*) AS total FROM support_ticket WHERE deleted_at IS NULL AND priority = 'High'");
    if($highPriorityStmt === false){
        throw new Exception("Failed to prepare statement.");
    }
    $highPriorityStmt->execute();
    $highPriority = $highPriorityStmt->get_result()->fetch_assoc()['total'];
    $highPriorityStmt->close();

    // New tickets today
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM support_ticket WHERE deleted_at IS NULL AND DATE(created_at) = CURDATE()");
    if($stmt === false){
        throw new Exception("Failed to prepare statement.");
    }
    $stmt->execute();
    $newToday = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

} catch (Exception $e) {
    $totalOpen = $highPriority = $newToday = 0;
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

            /* Screen Loader */
            .screen-loader{
                position: fixed;
                top:0;
                left:0;
                width:100%;
                height:100%;
                background: rgba(255,255,255,0.8);
                backdrop-filter: blur(2px);
                z-index: 9999;
                display:flex;
                align-items:center;
                justify-content:center;
            }

            .loader-spinner{
                width:50px;
                height:50px;
                border:5px solid #e5e5e5;
                border-top:5px solid #0A66C2;
                border-radius:50%;
                animation: spin 0.8s linear infinite;
            }

            @keyframes spin{
                from{ transform: rotate(0deg); }
                to{ transform: rotate(360deg); }
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
                <h1>Open Tickets</h1>

                <section>
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card p-3">
                                <h5 class="card-title text-muted">Total Open Tickets</h5>
                                <p class="card-text fs-3 fw-bold"><?= $totalOpen ?></p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card p-3">
                                <h5 class="card-title text-muted">High Priority</h5>
                                <p class="card-text fs-3 fw-bold text-danger"><?= $highPriority ?></p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card p-3">
                                <h5 class="card-title text-muted">New Today</h5>
                                <p class="card-text fs-3 fw-bold"><?= $newToday ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="mb-2 mb-md-0">All Open Tickets</h5>
                            <form action="" method="GET" class="input-group w-auto">
                                <input type="text" name="search" class="form-control" placeholder="Search tickets..." aria-label="Search tickets" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Ticket Reference</th>
                                            <th>Subject</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            try{
                                                $limit = 15; // tickets per page
                                                $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                                                $page = max($page, 1);
                                                $offset = ($page - 1) * $limit;

                                                /* Get total tickets with search */
                                                $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                                                $searchTerm = "%$search%";

                                                if ($search !== '') {
                                                    $countQuery = "SELECT COUNT(*) AS total FROM support_ticket WHERE deleted_at IS NULL AND (ticket_reference LIKE ? OR category LIKE ? OR message LIKE ?)";
                                                    $countStmt = $conn->prepare($countQuery);
                                                    $countStmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
                                                } else {
                                                    $countQuery = "SELECT COUNT(*) AS total FROM support_ticket WHERE deleted_at IS NULL";
                                                    $countStmt = $conn->prepare($countQuery);
                                                }
                                                
                                                if($countStmt === false){
                                                    throw new Exception("Failed to prepare count statement.");
                                                }
                                                $countStmt->execute();
                                                $totalResult = $countStmt->get_result()->fetch_assoc();
                                                $totalTickets = $totalResult['total'];

                                                $totalPages = ceil($totalTickets / $limit);

                                                //get support_ticket with search
                                                if ($search !== '') {
                                                    $query = "SELECT * FROM support_ticket WHERE deleted_at IS NULL AND (ticket_reference LIKE ? OR category LIKE ? OR message LIKE ?) ORDER BY created_at DESC LIMIT ? OFFSET ?";
                                                    $stmt = $conn->prepare($query);
                                                    if($stmt === false){
                                                        throw new Exception("Failed to prepare statement.");
                                                    }
                                                    $stmt->bind_param("sssii", $searchTerm, $searchTerm, $searchTerm, $limit, $offset);
                                                } else {
                                                    $query = "SELECT * FROM support_ticket WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT ? OFFSET ?";
                                                    $stmt = $conn->prepare($query);
                                                    if($stmt === false){
                                                        throw new Exception("Failed to prepare statement.");
                                                    }
                                                    $stmt->bind_param("ii", $limit, $offset);
                                                }

                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                if ($result->num_rows < 1) {
                                                    echo "<tr><td colspan='6'>No open tickets found.</td></tr>";
                                                } else{
                                                    while ($row = $result->fetch_assoc()) {
                                                        $priority = $row['priority'];
                                                        $priority_status = "";
                                                        if ($priority == "High") {
                                                            $priority_status = "danger";
                                                        } else if ($priority == "Medium") {
                                                            $priority_status = "info";
                                                        } else {
                                                        $priority_status = "success";
                                                        }
                                                        
                                                        $status = $row['status'] ?? 'pending';
                                                        $status_badge = "bg-warning text-dark";
                                                        if ($status == "Resolved") {
                                                            $status_badge = "bg-success";
                                                        } else if ($status == "Closed") {
                                                            $status_badge = "bg-secondary";
                                                        }

                                        ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($row['ticket_reference']) ?></td>
                                                            <td><?= htmlspecialchars($row['category']) ?></td>
                                                            <td><span class="badge <?= $status_badge ?>"><?= ucfirst(htmlspecialchars($status)) ?></span></td>
                                                            <td><span class="badge bg-<?= $priority_status ?>"><?= ucfirst($priority) ?></span></td>
                                                            <td>
                                                                <?php 
                                                                    $date = new DateTime($row['created_at']);
                                                                    echo $date->format('M d, Y h:i A');
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-sm btn-primary view-ticket" 
                                                                    data-ticket-id="<?= $row['id'] ?>"
                                                                    data-ticket-ref="<?= htmlspecialchars($row['ticket_reference']) ?>"
                                                                    data-message="<?= htmlspecialchars($row['message']) ?>"
                                                                    data-status="<?= htmlspecialchars($status) ?>">
                                                                    <i class="bi bi-eye"></i> View
                                                                </button>
                                                            </td>
                                                        </tr>                                       
                                        
                                        <?php
                                                    }
                                                }
                                            } catch (Exception $e) {
                                                echo "<tr><td colspan='6'>" . $e->getMessage() . "</td></tr>";
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>


                            <nav aria-label="Tickets pagination">
                                <ul class="pagination justify-content-end mt-3 flex-wrap">
                                    <!-- Previous -->
                                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page - 1 ?><?= $search !== '' ? '&search=' . urlencode($search) : '' ?>">Previous</a>
                                    </li>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?><?= $search !== '' ? '&search=' . urlencode($search) : '' ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <!-- Next -->
                                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page + 1 ?><?= $search !== '' ? '&search=' . urlencode($search) : '' ?>">Next</a>
                                    </li>
                                </ul>
                            </nav>

                        </div>
                    </div>

                </section>
            
            </div>
        </div>

        

        <!-- Ticket Details Modal -->
        <div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ticketModalLabel">Ticket Details: <span id="modalTicketRef" class="fw-bold"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="fw-bold">Message:</p>
                        <div id="modalTicketMessage" class="p-3 bg-light rounded" style="white-space: pre-wrap;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger me-auto" id="deleteTicketBtn">Delete</button>
                        <button type="button" class="btn btn-success" id="resolveTicketBtn">Resolve</button>
                        <button type="button" class="btn btn-primary" id="openTicketBtn">Open</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Full Screen Loader -->
        <div id="screenLoader" class="screen-loader d-none">
            <div class="loader-spinner"></div>
        </div>

        <?php include_once "footer.php"; ?>

        <!-- Load Bootstrap JS Bundle (includes Popper for dropdowns/modals) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>

            function showLoader(){
                document.getElementById("screenLoader").classList.remove("d-none");
            }

            function hideLoader(){
                document.getElementById("screenLoader").classList.add("d-none");
            }


            let activeTicketId = null;

            // SweetAlert Toast Config
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            document.addEventListener("click", function(e) {
                if (e.target.closest(".view-ticket")) {
                    const btn = e.target.closest(".view-ticket");
                    activeTicketId = btn.dataset.ticketId;
                    const ticketRef = btn.dataset.ticketRef;
                    const message = btn.dataset.message;
                    const status = btn.dataset.status;

                    document.getElementById("modalTicketRef").innerText = ticketRef;
                    document.getElementById("modalTicketMessage").innerText = message;

                    // Enable/disable resolve button based on status
                    const resolveBtn = document.getElementById("resolveTicketBtn");
                    if (status === 'Resolved') {
                        resolveBtn.disabled = true;
                        resolveBtn.innerText = 'Resolved';
                    } else {
                        resolveBtn.disabled = false;
                        resolveBtn.innerText = 'Resolve';
                    }

                    new bootstrap.Modal(document.getElementById('ticketModal')).show();
                }
            });

            function performTicketAction(action) {
                if (!activeTicketId) return;

                let confirmText = `Are you sure you want to ${action} this ticket?`;
                if (action === 'delete') confirmText = "This action is irreversible. Are you sure you want to delete this ticket?";

                Swal.fire({
                    title: 'Confirm Action',
                    text: confirmText,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: action === 'delete' ? '#d33' : '#3085d6',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, proceed!'
                }).then((result) => {
                    if (result.isConfirmed) {

                        showLoader(); // SHOW LOADER

                        fetch("./../process/process_ticket_action.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/x-www-form-urlencoded" },
                            body: `action=${action}&ticket_id=${activeTicketId}`
                        })
                        .then(res => res.json())
                        .then(data => {

                            hideLoader(); // HIDE LOADER

                            if (data.status === 'success') {
                                Toast.fire({
                                    icon: 'success',
                                    title: data.message
                                });
                                // Close modal and reload page after a short delay
                                bootstrap.Modal.getInstance(document.getElementById('ticketModal')).hide();
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    title: data.message || 'Action failed'
                                });
                            }
                        })
                        .catch(err => {

                            hideLoader(); // HIDE LOADER
                            
                            console.error(err);
                            Toast.fire({
                                icon: 'error',
                                title: 'An error occurred during processing'
                            });
                        });
                    }
                });
            }

            document.getElementById("resolveTicketBtn").addEventListener("click", () => performTicketAction('resolve'));
            document.getElementById("deleteTicketBtn").addEventListener("click", () => performTicketAction('delete'));
            document.getElementById("openTicketBtn").addEventListener("click", () => performTicketAction('open'));

        </script>
    </body>
</html>