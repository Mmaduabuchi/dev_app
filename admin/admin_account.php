<?php
//include auth file
include_once "auth.php";

//include route
include_once "route.php";

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

                <div class="page-content" id="admin-accounts">
                    <h1 class="mb-4 fs-3">Admin Accounts & Roles</h1>
                    <div class="card p-4">
                        <h5 class="card-title fw-bold mb-3">Admin Roster</h5>
                        <button class="btn btn-primary mb-3 w-auto" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                            <i class="bi bi-plus-circle"></i> Add New Administrator
                        </button>

                        
                        <div class="table-responsive">
                            <table class="table table-hover align-middle small">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Last Login</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        try{
                                            $administrator_arr = ["admin", "subadmin", "moderator"];
                                            $stmt = $conn->prepare("SELECT * FROM users WHERE role = 'administrator' AND auth IN (?, ?, ?) AND suspended_at IS NULL AND deleted_at IS NULL ORDER BY created_at DESC");
                                            if($stmt === false){
                                                throw new Exception("Failed to prepare statement.");
                                            }
                                            $stmt->bind_param("sss", $administrator_arr[0], $administrator_arr[1], $administrator_arr[2]);
                                            $stmt->execute();
                                            $result = $stmt->get_result();
                                            if($result->num_rows < 1){
                                                echo "<tr><td colspan='5'>No record found.</td></tr>";
                                            }else{                                                
                                                while($row = $result->fetch_assoc()){
                                    ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($row["fullname"]); ?></td>
                                                        <td><?php echo htmlspecialchars($row["email"]); ?></td>
                                                        <td><span class="badge bg-<?= ($row["auth"] == "admin") ? 'danger' : 'secondary' ?>"><?= ($row["auth"] == "admin") ? 'Super Admin' : ucfirst($row["user_type"]) ?></span></td>
                                                        <td><?php echo $row["last_login"] ?? "N/A"; ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-info me-1" title="Edit Permissions"><i class="bi bi-pencil-square"></i></button>
                                                            <button class="btn btn-sm btn-outline-danger" value="<?= $row["id"] ?>" onclick="removeAdmin(this.value)" title="Remove"><i class="bi bi-trash"></i></button>
                                                        </td>
                                                    </tr>
                                    <?php
                                                }
                                            }
                                        } catch (Exception $e) {
                                            echo "ERROR: " . $e->getMessage();
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- End Admin Accounts -->
            
            </div>

            <!-- Modal for Adding New Administrator -->
            <div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold" id="addAdminModalLabel">Add New Administrator</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="#" id="formAddAdmin" method="POST">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="adminName" class="form-label small fw-semibold">Full Name</label>
                                    <input type="text" class="form-control" id="adminName" name="name" placeholder="Enter full name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="adminEmail" class="form-label small fw-semibold">Email Address</label>
                                    <input type="email" class="form-control" id="adminEmail" name="email" placeholder="name@devhire.com" required>
                                </div>
                                <div class="mb-3">
                                    <label for="adminRole" class="form-label small fw-semibold">Role</label>
                                    <select class="form-select" id="adminRole" name="role" required>
                                        <option value="" selected disabled>Select a role</option>
                                        <option value="subadmin">Sub-Admin</option>
                                        <option value="moderator">Moderator</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="adminPassword" class="form-label small fw-semibold">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="adminPassword" name="password" required>
                                        <span class="input-group-text bg-white cursor-pointer" id="togglePassword">
                                            <i class="bi bi-eye" id="toggleIcon"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Create Account</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal for Confirming Administrator Deletion -->
            <div class="modal fade" id="deleteAdminModal" tabindex="-1" aria-labelledby="deleteAdminModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold" id="deleteAdminModalLabel">Confirm Deletion</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <p class="mb-0">Are you sure you want to remove this administrator? <br> This action cannot be undone.</p>
                            <input type="hidden" value="" id="delete_admin_id">
                        </div>
                        <div class="modal-footer border-0 d-flex">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete Account</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <?php include_once "footer.php"; ?>


        
        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Load Bootstrap JS Bundle (includes Popper for dropdowns/modals) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('adminPassword');
            const toggleIcon = document.getElementById('toggleIcon');

            togglePassword.addEventListener('click', function () {
                if (password.type === 'password') {
                    password.type = 'text';
                    toggleIcon.classList.remove('bi-eye');
                    toggleIcon.classList.add('bi-eye-slash');
                } else {
                    password.type = 'password';
                    toggleIcon.classList.remove('bi-eye-slash');
                    toggleIcon.classList.add('bi-eye');
                }
            });

            document.getElementById("formAddAdmin").addEventListener("submit", function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch("./../process/process_add_new_administrator.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        Swal.fire({
                            toast: true,
                            icon: 'success',
                            title: data.message,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        setTimeout(() => {
                            window.location.reload();
                        }, 2500);
                    } else {
                        Swal.fire({
                            toast: true,
                            icon: 'error',
                            title: data.message,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        toast: true,
                        icon: 'error',
                        title: error,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    console.error("Error:", error);
                });                
            });

            function removeAdmin(administratorID){
                document.getElementById("delete_admin_id").value = administratorID;
                
                // show bootstrap modal properly
                const modal = new bootstrap.Modal(
                    document.getElementById('deleteAdminModal')
                );
                modal.show();
            }

            document.getElementById("confirmDeleteBtn").addEventListener("click", function () {
                const administratorID = document.getElementById("delete_admin_id").value;
                fetch("./../process/process_delete_administrator.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ 
                        administrator_id: administratorID 
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        Swal.fire({
                            toast: true,
                            icon: 'success',
                            title: data.message,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        setTimeout(() => {
                            window.location.reload();
                        }, 2500);
                    } else {
                        Swal.fire({
                            toast: true,
                            icon: 'error',
                            title: data.message,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        setTimeout(() => {
                            window.location.reload();
                        }, 2500);
                    }
                })
                .catch(error => {
                    Swal.fire({
                        toast: true,
                        icon: 'error',
                        title: error,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    console.error("Error:", error);
                });
            });
        </script>
    </body>
</html>