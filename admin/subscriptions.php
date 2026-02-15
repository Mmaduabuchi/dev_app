<?php
//include auth file
include_once "auth.php";

//include route
include_once "route.php";

try{
    $planIds = [1,2,3];
    
    $placeholders = implode(',', array_fill(0, count($planIds), '?'));

    $stmt = $conn->prepare("SELECT * FROM subscription_plans WHERE id IN ($placeholders)");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $types = str_repeat('i', count($planIds));
    $stmt->bind_param($types, ...$planIds);

    $stmt->execute();
    $result = $stmt->get_result();

    $plans = [];
    while ($row = $result->fetch_assoc()) {
        $plans[$row['id']] = $row;
    }

    $stmt->close();

    // Assign values
    $sub_one_data = $plans[1];
    $sub_two_data = $plans[2];
    $sub_three_data = $plans[3];

    $sub_one_data_name = $sub_one_data["name"];
    $sub_one_data_price = number_format($sub_one_data["price"], 2);
    $sub_one_data_duration_days = $sub_one_data["duration_days"];

    $sub_two_data_name = $sub_two_data["name"];
    $sub_two_data_price = number_format($sub_two_data["price"], 2);
    $sub_two_data_duration_days = $sub_two_data["duration_days"];

    $sub_three_data_name = $sub_three_data["name"];
    $sub_three_data_price = number_format($sub_three_data["price"], 2);
    $sub_three_data_duration_days = $sub_three_data["duration_days"];

} catch (Exception $e) {
    $conn->close();
    error_log($e->getMessage());
    echo "Something went wrong. Please try again later.";
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
                <div class="page-content" id="subscription-management">
                    <h1 class="mb-4 fs-3">Subscription Management</h1>

                    <div class="row g-4 mb-4">
                        <!-- Pricing Configuration -->
                        <div class="col-lg-12">
                            <div class="card p-4">
                                <h5 class="card-title fw-bold mb-3">Platform Subscription Plans</h5>
                                <div class="row g-4">
                                    <!-- Plan 1: Basic -->
                                    <div class="col-md-4">
                                        <div class="card border-secondary h-100 text-center p-3">
                                            <h6 class="text-secondary fw-bold"><?= ucfirst($sub_one_data_name) ?></h6>
                                            <h2 class="display-6 fw-bold mb-3">$<?= $sub_one_data_price ?><span class="fs-6 fw-normal text-muted">/mo</span></h2>
                                            <p class="small text-muted mb-4">Limited visibility & features</p>
                                            <ul class="list-unstyled text-start small mb-4">
                                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> 5 Job Slots (Employer)</li>
                                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Standard Profile (Talent)</li>
                                                <li><i class="bi bi-x-circle-fill text-danger me-2"></i> No Search Boost</li>
                                            </ul>
                                            <button data-bs-toggle="modal" data-bs-target="#editPlanModal" data-plan="<?= ucfirst($sub_one_data_name) ?>" class="btn btn-outline-primary btn-sm mt-auto"><i class="bi bi-pencil"></i> Edit Plan</button>
                                        </div>
                                    </div>
                                    <!-- Plan 2: Standard -->
                                    <div class="col-md-4">
                                        <div class="card border-primary border-3 h-100 text-center p-3">
                                            <h6 class="text-primary fw-bold"><?= ucfirst($sub_two_data_name) ?></h6>
                                            <h2 class="display-6 fw-bold mb-3">$<?= $sub_two_data_price ?><span class="fs-6 fw-normal text-muted">/mo</span></h2>
                                            <p class="small text-muted mb-4">Balanced feature set</p>
                                            <ul class="list-unstyled text-start small mb-4">
                                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Unlimited Job Slots</li>
                                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Featured Profile Option</li>
                                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> 15% Search Boost</li>
                                            </ul>
                                            <button data-bs-toggle="modal" data-bs-target="#editPlanModal" data-plan="<?= ucfirst($sub_two_data_name) ?>" class="btn btn-primary btn-sm mt-auto"><i class="bi bi-pencil"></i> Edit Plan</button>
                                        </div>
                                    </div>
                                    <!-- Plan 3: Premium -->
                                    <div class="col-md-4">
                                        <div class="card border-warning h-100 text-center p-3">
                                            <h6 class="text-warning fw-bold"><?= ucfirst($sub_three_data_name) ?></h6>
                                            <h2 class="display-6 fw-bold mb-3">$<?= $sub_three_data_price ?><span class="fs-6 fw-normal text-muted">/mo</span></h2>
                                            <p class="small text-muted mb-4">Maximum visibility and tools</p>
                                            <ul class="list-unstyled text-start small mb-4">
                                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Dedicated Account Manager</li>
                                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> Premium Profile Badge</li>
                                                <li><i class="bi bi-check-circle-fill text-success me-2"></i> 50% Search Boost</li>
                                            </ul>
                                            <button data-bs-toggle="modal" data-bs-target="#editPlanModal" data-plan="<?= ucfirst($sub_three_data_name) ?>" data-name="<?= $sub_three_data_name ?>" class="btn btn-outline-primary btn-sm mt-auto"><i class="bi bi-pencil"></i> Edit Plan</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subscription Analytics -->
                    <div class="card p-4">
                        <h5 class="card-title fw-bold mb-3">Subscription Distribution</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <canvas id="subscriptionPieChart" height="200"></canvas>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <ul class="list-group list-group-flush w-100">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= ucfirst($sub_three_data_name) ?> <span class="badge bg-warning rounded-pill">1,024</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= ucfirst($sub_two_data_name) ?> <span class="badge bg-primary rounded-pill">2,500</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= ucfirst($sub_one_data_name) ?> <span class="badge bg-secondary rounded-pill">1,596</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End Subscription Management -->
            
            </div>
        </div>


        <!-- Edit Plan Modal -->
        <div class="modal fade" id="editPlanModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="editPlanForm" method="POST" action="update-plan.php">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Subscription Plan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <input type="hidden" name="plan_type" id="planType">

                            <div class="mb-3">
                                <label class="form-label">Plan Name</label>
                                <input type="text" name="plan_name" id="planName" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Monthly Price ($)</label>
                                <input type="number" name="plan_price" id="planPrice" class="form-control" required>
                            </div>

                            <!-- Features Section -->
                            <div class="mb-3">
                                <label class="form-label">Plan Features</label>
                                <div id="featureList"></div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addFeature">
                                    + Add Feature
                                </button>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <!-- Close Button -->
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        

        <?php include_once "footer.php"; ?>


        <!-- Load Bootstrap JS Bundle (includes Popper for dropdowns/modals) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {

                const editButtons = document.querySelectorAll(".edit-plan-btn");
                const featureList = document.getElementById("featureList");
                const addFeatureBtn = document.getElementById("addFeature");

                function createFeatureInput(value = "") {
                    const div = document.createElement("div");
                    div.className = "input-group mb-2";

                    div.innerHTML = `
                        <input type="text" name="features[]" class="form-control" value="${value}" required>
                        <button type="button" class="btn btn-danger remove-feature"><i class="bi bi-trash"></i></button>
                    `;

                    div.querySelector(".remove-feature").addEventListener("click", () => {
                        div.remove();
                    });

                    return div;
                }

                editButtons.forEach(button => {
                    button.addEventListener("click", function () {

                        const plan = this.getAttribute("data-plan");
                        const name = this.getAttribute("data-name");
                        const price = this.getAttribute("data-price");
                        const features = this.getAttribute("data-features");

                        document.getElementById("planType").value = plan;
                        document.getElementById("planName").value = name;
                        document.getElementById("planPrice").value = price;

                        // Clear old features
                        featureList.innerHTML = "";

                        // Load features
                        if (features) {
                            features.split("|").forEach(f => {
                                featureList.appendChild(createFeatureInput(f));
                            });
                        }
                    });
                });

                // Add new feature
                addFeatureBtn.addEventListener("click", () => {
                    featureList.appendChild(createFeatureInput());
                });

            });

            document.addEventListener("DOMContentLoaded", function () {

                const modal = document.getElementById("editPlanModal");
                const form = document.getElementById("editPlanForm");
                const featureList = document.getElementById("featureList");

                // Reset modal when closed
                modal.addEventListener("hidden.bs.modal", function () {
                    form.reset();
                    featureList.innerHTML = "";
                });

            });
        </script>

    </body>
</html>