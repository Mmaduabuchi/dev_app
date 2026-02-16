<?php
//include auth file
include_once "auth.php";

//include route
include_once "route.php";


try{
    // Get total number of users
    $stmt = $conn->prepare("SELECT COUNT(*) as total_users FROM `users` WHERE auth = 'user' AND user_type = 'talent' AND deleted_at IS NULL");
    if($stmt === false){
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $totalUsers = (int)$row['total_users'];
    }
    $stmt->close();

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
                    <h1 class="mb-4 fs-3">Users Management</h1>

                    <ul class="nav nav-tabs mb-4" id="userTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="talent-tab" data-bs-toggle="tab" data-bs-target="#talents-pane" type="button" role="tab" aria-controls="talents-pane" aria-selected="true">Talents</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="employer-tab" data-bs-toggle="tab" data-bs-target="#employers-pane" type="button" role="tab" aria-controls="employers-pane" aria-selected="false">Employers / CEOs</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- 2A. Talents Page -->
                        <div class="tab-pane fade show active" id="talents-pane" role="tabpanel" aria-labelledby="talent-tab">
                            <div class="card p-4">
                                <h5 class="card-title fw-bold mb-3">Talent Roster (<?= $totalUsers ?> Total)</h5>

                                <!-- Filters & Search -->
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <input type="search" class="form-control me-2" style="max-width: 200px;" placeholder="Search by name or skill...">
                                    <select class="form-select me-2" style="max-width: 150px;">
                                        <option selected>All Skills</option>
                                        <option>Frontend</option>
                                        <option>Backend</option>
                                        <option>Design</option>
                                    </select>
                                    <select class="form-select me-2" style="max-width: 150px;">
                                        <option selected>All Subs</option>
                                        <option>Basic</option>
                                        <option>Standard</option>
                                        <option>Premium</option>
                                    </select>
                                    <button class="btn btn-outline-secondary"><i class="bi bi-funnel"></i> Apply Filters</button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle small">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <!-- <th>Skills</th> -->
                                                <th>Nationality</th>
                                                <th>Location</th>
                                                <th>Subscription</th>
                                                <th>Status</th>
                                                <th>Last Seen</th>
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
                                                    $totalUsersStmt = $conn->prepare("SELECT COUNT(*) as total FROM users u WHERE u.user_type = 'talent' AND u.deleted_at IS NULL");
                                                    $totalUsersStmt->execute();
                                                    $totalUsersResult = $totalUsersStmt->get_result()->fetch_assoc();
                                                    $totalUsers = $totalUsersResult['total'];
                                                    $totalPages = ceil($totalUsers / $limit);

                                                    $stmt = $conn->prepare("SELECT u.id, u.fullname, u.email, u.user_type, u.is_profile_complete, u.suspended_at, dp.*
                                                    FROM users u LEFT JOIN developers_profiles dp ON u.id = dp.user_id WHERE u.user_type = 'talent' AND u.deleted_at IS NULL  ORDER BY u.created_at DESC LIMIT ? OFFSET ?");
                                                    if($stmt === false){
                                                        throw new Exception("Failed to prepare statement.");
                                                    }
                                                    $stmt->bind_param("ii", $limit, $offset);
                                                    $stmt->execute();
                                                    $result = $stmt->get_result();
                                                    if($result->num_rows > 0){
                                                        while ($user = $result->fetch_assoc()) {
                                                            $status = ((int)$user["is_profile_complete"] === 1) ? "Verified" : "Not Verified";
                                            ?>
                                                            <tr>
                                                                <td><?= htmlspecialchars($user['fullname']) ?></td>
                                                                <!-- <td><span class="badge bg-primary">Frontend</span> <span class="badge bg-secondary">React</span></td> -->
                                                                <td><?= htmlspecialchars(ucfirst($user["citizenship"])) ?></td>
                                                                <td><?= htmlspecialchars(ucfirst($user["location"])) ?></td>
                                                                <td><span class="badge bg-warning">Premium</span></td>
                                                                <td><span class="badge bg-<?= ($user["is_profile_complete"] == 1) ? "success" : "secondary" ?>"><?= $status ?></span></td>
                                                                <td>Just now</td>
                                                                <td>
                                                                    <button class="btn btn-sm btn-outline-info me-1 view-profile-btn" 
                                                                        data-fullname="<?= htmlspecialchars($user['fullname']) ?>" 
                                                                        data-legalname="<?= htmlspecialchars($user['legal_name']) ?>" 
                                                                        data-citizenship="<?= htmlspecialchars($user['citizenship']) ?>" 
                                                                        data-location="<?= htmlspecialchars($user['location']) ?>" 
                                                                        data-status="<?= htmlspecialchars($status) ?>" 
                                                                        data-phonenumber="<?= htmlspecialchars($user["phone_number"]) ?>" 
                                                                        data-yearsofexperience="<?= htmlspecialchars($user["years_of_experience"]) ?>" 
                                                                        data-educationlevel="<?= htmlspecialchars($user["education_level"]) ?>" 
                                                                        data-industryexperience="<?= htmlspecialchars($user["industry_experience"]) ?>" 
                                                                        data-jobcommitment="<?= htmlspecialchars($user["job_commitment"]) ?>" 
                                                                        data-preferredhourlyrate="<?= htmlspecialchars($user["preferred_hourly_rate"]) ?>" 
                                                                        data-englishproficiency="<?= htmlspecialchars($user["english_proficiency"]) ?>" 
                                                                        data-bio="<?= htmlspecialchars($user["bio"]) ?>"
                                                                    title="View Profile">
                                                                            <i class="bi bi-eye"></i>
                                                                    </button>
                                                                    <?php
                                                                        if($user["suspended_at"] !== null):
                                                                    ?>
                                                                        <button class="btn btn-sm btn-outline-danger" title="Suspend"><i class="bi bi-slash-circle"></i></button>
                                                                    <?php
                                                                        else:
                                                                    ?>
                                                                        <button class="btn btn-sm btn-success" title="Verify"><i class="bi bi-check"></i></button>
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

                                    <!-- Single modal -->
                                    <div class="modal fade" id="viewUserModal" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content text-dark">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">User Profile Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <section class="overflow-auto" style="height: 200px;">
                                                        <p><strong>Full Name:</strong> <span id="modalFullName"></span></p>
                                                        <p><strong>Legal Name:</strong> <span id="modalLegalName"></span></p>
                                                        <p><strong>Citizenship:</strong> <span id="modalCitizenship"></span></p>
                                                        <p><strong>Location:</strong> <span id="modalLocation"></span></p>
                                                        <p><strong>Status:</strong> <span class="badge bg-success" id="modalStatus"></span></p>
                                                        <p><strong>Gender:</strong> <span id="modalGender"></span></p>
                                                        <p><strong>Phone Number:</strong> <span id="modalPhoneNumber"></span></p>
                                                        <p><strong>Email:</strong> <span id="modalEmail"></span></p>
                                                        <p><strong>Education Level:</strong> <span id="modalEducationLevel"></span></p>
                                                        <p><strong>Industry Experience:</strong> <span id="modalIndustryExperience"></span></p>
                                                        <p><strong>Job Commitment:</strong> <span id="modalJobCommitment"></span></p>
                                                        <p><strong>Preferred Hourly Rate:</strong> <span id="modalPreferredHourlyRate"></span></p>
                                                        <p><strong>English Proficiency:</strong> <span id="modalEnglishProficiency"></span></p>
                                                    </section>
                                                    <div class="mt-3">
                                                        <label for="modalBio" class="form-label"><strong>Bio:</strong></label>
                                                        <textarea id="modalBio" class="form-control" rows="6" readonly></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" id="btnDeleteUser">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                    <button type="button" class="btn btn-warning" id="btnSuspendUser">
                                                        <i class="bi bi-person-dash"></i> Suspend
                                                    </button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

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

                        <!-- 2B. Employers / CEOs Page -->
                        <div class="tab-pane fade" id="employers-pane" role="tabpanel" aria-labelledby="employer-tab">
                            <div class="card p-4">
                                <h5 class="card-title fw-bold mb-3">Employer Roster (<?= $totalEmployers ?> Total)</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle small">
                                        <thead>
                                            <tr>
                                                <th>Company Name</th>
                                                <th>Subscription</th>
                                                <th>Active Jobs</th>
                                                <th>Verification</th>
                                                <th>Registered Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Tech Global Corp</td>
                                                <td><span class="badge bg-success">Standard</span></td>
                                                <td>5</td>
                                                <td><span class="badge bg-success">Verified</span></td>
                                                <td>2021-08-15</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info me-1" title="View Company"><i class="bi bi-building"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Startup XYZ</td>
                                                <td><span class="badge bg-secondary">Basic</span></td>
                                                <td>1</td>
                                                <td><span class="badge bg-warning">Pending</span></td>
                                                <td>2023-11-01</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info me-1"><i class="bi bi-building"></i></button>
                                                    <button class="btn btn-sm btn-success" title="Verify"><i class="bi bi-check"></i></button>
                                                </td>
                                            </tr>
                                            <!-- More Employer Rows... -->
                                        </tbody>
                                    </table>
                                </div>
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
            document.querySelectorAll('.view-profile-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const fullname = button.dataset.fullname;
                    const legalname = button.dataset.legalname;
                    const citizenship = button.dataset.citizenship;
                    const location = button.dataset.location;
                    const status = button.dataset.status;
                    const Bio = button.dataset.bio;
                    const gender = button.dataset.gender;
                    const phonenumber = button.dataset.phonenumber;
                    const email = button.dataset.email;
                    const educationlevel = button.dataset.educationlevel;
                    const industryexperience = button.dataset.industryexperience;
                    const jobcommitment = button.dataset.jobcommitment;
                    const preferredhourlyrate = button.dataset.preferredhourlyrate;
                    const englishproficiency = button.dataset.englishproficiency;

                    document.getElementById('modalFullName').textContent = fullname;
                    document.getElementById('modalLegalName').textContent = legalname;
                    document.getElementById('modalCitizenship').textContent = citizenship;
                    document.getElementById('modalLocation').textContent = location;
                    document.getElementById('modalStatus').textContent = status;
                    document.getElementById('modalBio').textContent = Bio;
                    document.getElementById('modalGender').textContent = gender;
                    document.getElementById('modalPhoneNumber').textContent = phonenumber;
                    document.getElementById('modalEmail').textContent = email;
                    document.getElementById('modalEducationLevel').textContent = educationlevel;
                    document.getElementById('modalIndustryExperience').textContent = industryexperience;
                    document.getElementById('modalJobCommitment').textContent = jobcommitment;
                    document.getElementById('modalPreferredHourlyRate').textContent = preferredhourlyrate;
                    document.getElementById('modalEnglishProficiency').textContent = englishproficiency;

                    const modal = new bootstrap.Modal(document.getElementById('viewUserModal'));
                    modal.show();
                });
            });
            
        </script>
    </body>
</html>