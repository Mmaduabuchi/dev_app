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

    $sub_one_data_id = $sub_one_data["id"];
    $sub_one_data_name = $sub_one_data["name"];
    $sub_one_data_price = number_format($sub_one_data["price"], 2);
    $sub_one_data_duration_days = $sub_one_data["duration_days"];

    $sub_two_data_id = $sub_two_data["id"];
    $sub_two_data_name = $sub_two_data["name"];
    $sub_two_data_price = number_format($sub_two_data["price"], 2);
    $sub_two_data_duration_days = $sub_two_data["duration_days"];

    $sub_three_data_id = $sub_three_data["id"];
    $sub_three_data_name = $sub_three_data["name"];
    $sub_three_data_price = number_format($sub_three_data["price"], 2);
    $sub_three_data_duration_days = $sub_three_data["duration_days"];

    // Fetch features for all plans
    $planFeatures = [];

    $stmt = $conn->prepare("SELECT plan_id, feature_text, icon_type FROM plan_features WHERE plan_id IN ($placeholders) ORDER BY id ASC");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param($types, ...$planIds);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $planFeatures[$row['plan_id']][] = $row;
    }

    $stmt->close();

    $premiumCount = $standardCount = $freeCount = 0;

    // Get premium and standard users sub count
    $stmt = $conn->prepare("
        SELECT 
            SUM(CASE WHEN sp.name = 'premium' AND s.status = 'active' THEN 1 ELSE 0 END) AS premium,
            SUM(CASE WHEN sp.name = 'standard' AND s.status = 'active' THEN 1 ELSE 0 END) AS standard
        FROM users u
        LEFT JOIN subscriptions s 
            ON u.id = s.user_id AND s.status = 'active'
        LEFT JOIN subscription_plans sp 
            ON s.plan_id = sp.id
        WHERE u.user_type IN ('talent', 'employer')
        AND u.deleted_at IS NULL
    ");

    if ($stmt === false) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    $premiumCount = $data['premium'] ?? 0;
    $standardCount = $data['standard'] ?? 0;

    // Get total users for sub count
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM users WHERE user_type IN ('talent', 'employer') AND deleted_at IS NULL");
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->execute();
    $totalUsers = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

    $stmt->close();
    $freeCount = $totalUsers - ($premiumCount + $standardCount);

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

            /* Pricing Card Styling */
            .pricing-card {
                border-radius: 16px;
                overflow: visible;
                transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease;
            }
            .pricing-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 16px 32px rgba(0, 0, 0, 0.1) !important;
            }
            .pricing-card-popular {
                border: 2px solid var(--bs-devhire-blue);
                transform: scale(1.05);
                z-index: 1;
            }
            .pricing-card-popular:hover {
                transform: scale(1.05) translateY(-8px);
            }
            @media (max-width: 991.98px) {
                .pricing-card-popular {
                    transform: none;
                }
                .pricing-card-popular:hover {
                    transform: translateY(-8px);
                }
            }
            .pricing-card-premium {
                background: linear-gradient(135deg, var(--bs-devhire-navy) 0%, #1a365d 100%);
                color: #ffffff;
            }
            .pricing-icon {
                width: 32px;
                height: 32px;
                min-width: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                font-size: 1.25rem;
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
            .modal-body {
                max-height: 400px; /* adjust as needed */
                overflow-y: auto;
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
                            <div class="card border-0 shadow-sm p-4">
                                <div class="text-center mb-5 mt-2">
                                    <h2 class="fw-bold fs-3">Platform Subscription Plans</h2>
                                    <p class="text-muted">Manage the subscription tiers available to users on the platform.</p>
                                </div>
                                <div class="row g-4 justify-content-center align-items-center px-lg-4 mb-4">
                                    <!-- Plan 1: Basic -->
                                    <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                                        <div class="card pricing-card border shadow-sm h-100 p-4">
                                            <div class="text-center mb-4">
                                                <h6 class="text-uppercase fw-bold text-muted tracking-wider mb-2"><?= ucfirst($sub_one_data_name) ?></h6>
                                                <div class="d-flex justify-content-center align-items-baseline mb-2">
                                                    <span class="fs-4 fw-semibold text-muted">₦</span>
                                                    <span class="display-5 fw-bold text-dark"><?= $sub_one_data_price ?></span>
                                                    <span class="text-muted ms-1">/mo</span>
                                                </div>
                                                <p class="small text-muted mb-0">Limited visibility & features</p>
                                            </div>
                                            <hr class="text-muted opacity-25 mb-4">
                                            <ul class="list-unstyled text-start small mb-4 flex-grow-1">
                                                <?php if (!empty($planFeatures[$sub_one_data_id])): ?>
                                                    <?php foreach ($planFeatures[$sub_one_data_id] as $feature): ?>
                                                        <li class="mb-3 d-flex align-items-center">
                                                            <?php if ($feature['icon_type'] === 'check'): ?>
                                                                <div class="pricing-icon bg-success bg-opacity-10 text-success me-3">
                                                                    <i class="bi bi-check" style="font-size: 1.3rem;"></i>
                                                                </div>
                                                                <span class="text-dark fw-medium"><?= htmlspecialchars($feature['feature_text']) ?></span>
                                                            <?php else: ?>
                                                                <div class="pricing-icon bg-danger bg-opacity-10 text-danger me-3">
                                                                    <i class="bi bi-x" style="font-size: 1.3rem;"></i>
                                                                </div>
                                                                <span class="text-muted text-decoration-line-through"><?= htmlspecialchars($feature['feature_text']) ?></span>
                                                            <?php endif; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </ul>
                                            <div class="mt-auto pt-4">
                                                <button data-bs-toggle="modal" data-bs-target="#editPlanModal" class="btn btn-outline-dark w-100 rounded-pill py-2 fw-medium edit-plan-btn"
                                                    data-name="<?= ucfirst($sub_one_data_name) ?>" 
                                                    data-plan="<?= $sub_one_data_id ?>" 
                                                    data-price="<?= $sub_one_data_price ?>"
                                                    data-features="<?= implode('|', array_map(fn($f) => $f['icon_type'] . '::' . $f['feature_text'], $planFeatures[$sub_one_data_id] ?? [])) ?>">
                                                    <i class="bi bi-pencil me-2"></i> Edit Plan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Plan 2: Standard -->
                                    <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                                        <div class="card pricing-card pricing-card-popular shadow h-100 p-4 position-relative">
                                            <div class="position-absolute top-0 start-50 translate-middle text-center w-100 mt-0">
                                                <span class="badge bg-primary rounded-pill py-2 px-3 text-uppercase tracking-wider fw-bold shadow-sm" style="font-size: 0.75rem; letter-spacing: 1px;">Recommended</span>
                                            </div>
                                            <div class="text-center mb-4 mt-3">
                                                <h6 class="text-uppercase fw-bold text-primary tracking-wider mb-2"><?= ucfirst($sub_two_data_name) ?></h6>
                                                <div class="d-flex justify-content-center align-items-baseline mb-2">
                                                    <span class="fs-4 fw-semibold text-primary">₦</span>
                                                    <span class="display-5 fw-bold text-primary"><?= $sub_two_data_price ?></span>
                                                    <span class="text-muted ms-1">/mo</span>
                                                </div>
                                                <p class="small text-muted mb-0">Balanced feature set</p>
                                            </div>
                                            <hr class="text-muted opacity-25 mb-4">
                                            <ul class="list-unstyled text-start small mb-4 flex-grow-1">
                                                <?php if (!empty($planFeatures[$sub_two_data_id])): ?>
                                                    <?php foreach ($planFeatures[$sub_two_data_id] as $feature): ?>
                                                        <li class="mb-3 d-flex align-items-center">
                                                            <?php if ($feature['icon_type'] === 'check'): ?>
                                                                <div class="pricing-icon bg-primary bg-opacity-10 text-primary me-3">
                                                                    <i class="bi bi-check" style="font-size: 1.3rem;"></i>
                                                                </div>
                                                                <span class="text-dark fw-medium"><?= htmlspecialchars($feature['feature_text']) ?></span>
                                                            <?php else: ?>
                                                                <div class="pricing-icon bg-danger bg-opacity-10 text-danger me-3">
                                                                    <i class="bi bi-x" style="font-size: 1.3rem;"></i>
                                                                </div>
                                                                <span class="text-muted text-decoration-line-through"><?= htmlspecialchars($feature['feature_text']) ?></span>
                                                            <?php endif; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </ul>
                                            <div class="mt-auto pt-4">
                                                <button data-bs-toggle="modal" data-bs-target="#editPlanModal" data-name="<?= ucfirst($sub_two_data_name) ?>" data-plan="<?= $sub_two_data_id ?>" data-price="<?= $sub_two_data_price ?>" data-features="<?= implode('|', array_map(fn($f) => $f['icon_type'] . '::' . $f['feature_text'], $planFeatures[$sub_two_data_id] ?? [])) ?>" class="btn btn-primary w-100 rounded-pill py-2 fw-medium edit-plan-btn shadow-sm">
                                                    <i class="bi bi-pencil me-2"></i> Edit Plan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Plan 3: Premium -->
                                    <div class="col-lg-4 col-md-6">
                                        <div class="card pricing-card pricing-card-premium shadow-lg h-100 p-4">
                                            <div class="text-center mb-4">
                                                <h6 class="text-uppercase fw-bold text-warning tracking-wider mb-2"><?= ucfirst($sub_three_data_name) ?></h6>
                                                <div class="d-flex justify-content-center align-items-baseline mb-2">
                                                    <span class="fs-4 fw-semibold text-white-50">₦</span>
                                                    <span class="display-5 fw-bold text-white"><?= $sub_three_data_price ?></span>
                                                    <span class="text-white-50 ms-1">/mo</span>
                                                </div>
                                                <p class="small text-white-50 mb-0">Maximum visibility and tools</p>
                                            </div>
                                            <hr class="bg-light opacity-25 mb-4">
                                            <ul class="list-unstyled text-start small mb-4 flex-grow-1">
                                                <?php if (!empty($planFeatures[$sub_three_data_id])): ?>
                                                    <?php foreach ($planFeatures[$sub_three_data_id] as $feature): ?>
                                                        <li class="mb-3 d-flex align-items-center">
                                                            <?php if ($feature['icon_type'] === 'check'): ?>
                                                                <div class="pricing-icon bg-warning text-dark me-3">
                                                                    <i class="bi bi-check" style="font-size: 1.3rem;"></i>
                                                                </div>
                                                                <span class="text-white fw-medium"><?= htmlspecialchars($feature['feature_text']) ?></span>
                                                            <?php else: ?>
                                                                <div class="pricing-icon bg-danger bg-opacity-25 text-white me-3">
                                                                    <i class="bi bi-x" style="font-size: 1.3rem;"></i>
                                                                </div>
                                                                <span class="text-white-50 text-decoration-line-through"><?= htmlspecialchars($feature['feature_text']) ?></span>
                                                            <?php endif; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </ul>
                                            <div class="mt-auto pt-4">
                                                <button data-bs-toggle="modal" data-bs-target="#editPlanModal" data-name="<?= ucfirst($sub_three_data_name) ?>" data-plan="<?= $sub_three_data_id ?>" data-price="<?= $sub_three_data_price ?>" data-features="<?= implode('|', array_map(fn($f) => $f['icon_type'] . '::' . $f['feature_text'], $planFeatures[$sub_three_data_id] ?? [])) ?>" class="btn btn-warning w-100 rounded-pill py-2 fw-bold edit-plan-btn shadow-sm text-dark">
                                                    <i class="bi bi-pencil me-2"></i> Edit Plan
                                                </button>
                                            </div>
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
                            <div class="col d-flex align-items-center">
                                <ul class="list-group list-group-flush w-100">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= ucfirst($sub_three_data_name) ?> <span class="badge bg-warning rounded-pill"><?= $premiumCount ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= ucfirst($sub_two_data_name) ?> <span class="badge bg-primary rounded-pill"><?= $standardCount ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= ucfirst($sub_one_data_name) ?> <span class="badge bg-secondary rounded-pill"><?= $freeCount ?></span>
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
                    <form id="editPlanForm">
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
                                <label class="form-label">Monthly Price (₦)</label>
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {

                const editButtons = document.querySelectorAll(".edit-plan-btn");
                const featureList = document.getElementById("featureList");
                const addFeatureBtn = document.getElementById("addFeature");

                function createFeatureInput(value = "", icon = "check") {
                    const div = document.createElement("div");
                    div.className = "input-group mb-2";

                    div.innerHTML = `
                        <select name="icon_type[]" class="form-select" style="max-width: 110px;">
                            <option value="check" ${icon === 'check' ? 'selected' : ''}>✔ Check</option>
                            <option value="cross" ${icon === 'cross' ? 'selected' : ''}>✖ Cross</option>
                        </select>
                        <input type="text" name="features[]" placeholder="Enter plan feature" class="form-control" value="${value}" required>
                        <button type="button" class="btn btn-danger remove-feature"><i class="bi bi-trash"></i></button>
                    `;

                    div.querySelector(".remove-feature").addEventListener("click", () => {
                        div.remove();
                    });

                    return div;
                }

                editButtons.forEach(button => {
                    button.addEventListener("click", function () {

                        const plan = this.dataset.plan;
                        const name = this.dataset.name;
                        const price = this.dataset.price;
                        const features = this.dataset.features;

                        document.getElementById("planType").value = plan;
                        document.getElementById("planName").value = name;
                        document.getElementById("planPrice").value = price;

                        // Clear old features
                        featureList.innerHTML = "";

                        // Load features
                        if (features) {
                            features.split("|").forEach(f => {
                                const [icon, text] = f.split("::");
                                featureList.appendChild(createFeatureInput(text, icon));
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

            document.getElementById("editPlanForm").addEventListener("submit", function(e) {
                e.preventDefault();

                const form = e.target;

                const formData = new FormData(form);

                // Disable submit button while saving
                const submitBtn = form.querySelector("button[type='submit']");
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-save me-1"></i> Saving...';

                fetch("./../process/process_update_plan.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-save me-1"></i> Save Changes';

                    // Configure SweetAlert2 toast
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });

                    if (data.status === 'success') {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById("editPlanModal"));
                        modal.hide();

                        // Show success toast
                        Toast.fire({
                            icon: 'success',
                            title: 'Plan updated successfully'
                        });

                        location.reload();
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: data.message || 'Something went wrong'
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-save me-1"></i> Save Changes';

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });

                    Toast.fire({
                        icon: 'error',
                        title: 'Failed to save. Try again.'
                    });
                });
            });

        </script>

    </body>
</html>