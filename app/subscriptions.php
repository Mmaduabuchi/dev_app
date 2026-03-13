<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";
//notification count
require_once __DIR__ . '/fetch_notification_count.php';

$sub_payment_status_flag = isset($_GET['payment']) ? htmlspecialchars($_GET['payment']) : "";

try {
    $subscription_ids = [1, 2, 3];
    $subscriptions = [];

    foreach ($subscription_ids as $sub_id) {

        // fetch subscription plan
        $stmt = $conn->prepare("SELECT * FROM subscription_plans WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $sub_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $plan = $result->fetch_assoc();
        $stmt->close();

        // fetch features
        $stmt = $conn->prepare("SELECT feature_text, icon_type FROM plan_features WHERE plan_id = ?");
        if (!$stmt) {
            throw new Exception('Database error: ' . $conn->error);
        }
        $stmt->bind_param("i", $sub_id);
        $stmt->execute();
        $features_result = $stmt->get_result();

        $features = [];
        while ($row = $features_result->fetch_assoc()) {
            $features[] = $row;
        }
        $stmt->close();

        $plan['features'] = $features;
        $subscriptions[] = $plan;
    }

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
    <title>Subscriptions | devhire - Dashboard</title>
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
    <style>
        .subBtn {
            background-color: #0077b6 !important;
            font-weight: bold !important;
            border: none !important;
            transition: background-color 0.3s ease, color 0.3s ease !important;
        }

        .subBtn:hover {
            background-color: #023e8a !important;
            color: #fff !important;
        }

        .subBtn0 {
            background-color: #00b609ff !important;
            font-weight: bold !important;
            border: none !important;
            transition: background-color 0.3s ease, color 0.3s ease !important;
        }

        .subBtn0:hover {
            background-color: #127909ff !important;
            color: #fff !important;
        }

        .planList li {
            margin-top: 5%;
        }
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
                            <h4 class="fs-18 fw-semibold m-0">Subscriptions plan</h4>
                        </div>
                    </div>

                    <div class="row">
                        <?php if ($sub_payment_status_flag == "success") { ?>
                            <div class="col-12">
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Holy <?= htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8'); ?>!</strong> Your subscription payment was successfully processed.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($sub_payment_status_flag == "failed") { ?>
                            <div class="col-12">
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Holy <?= htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8'); ?>!</strong> Your subscription payment failed.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Start Row -->
                    <div class="row">

                        <?php foreach ($subscriptions as $plan): ?>
                            <div class="col-md-6 col-xl-4">
                                <div class="card overflow-hidden">
                                    <div class="card-body">
                                        <div class="widget-first">
                                            <div class="d-flex align-items-center mb-3">
                                                <div>
                                                    <p class="mb-0 text-dark fs-16"><?= ucfirst($plan['name']) ?></p>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center mb-3">
                                                <h3 class="mb-0 fs-26 text-dark me-2 me-3">₦<?= number_format($plan['price'], 2) ?> 
                                                    <sub> / <?= $plan['duration_days'] == 30 ? 'Month' : ($plan['duration_days'] == 90 ? 'Quarterly' : 'Yearly') ?></sub> 
                                                </h3>
                                            </div>

                                            <p class="text-muted fs-14 mb-2">
                                                <?= htmlspecialchars($plan['description']) ?>
                                            </p>

                                            <ul class="text-muted fs-13 mb-3 list-unstyled planList">
                                                <?php foreach ($plan['features'] as $feature): ?>
                                                    <?php
                                                        $icon = $feature['icon_type'] === 'check' ? 'check-circle' : 'x-circle';
                                                        $icon_color = $feature['icon_type'] === 'check' ? 'text-success' : 'text-danger';
                                                    ?>
                                                    <li>
                                                        <i data-feather="<?= $icon ?>" class="<?= $icon_color ?>"></i>
                                                        <?= htmlspecialchars($feature['feature_text']) ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>

                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <?php if ($current_plan_id == $plan['id']): ?>

                                                        <!-- Current active plan -->
                                                        <button class="btn subBtn0 rounded-4 text-light p-2 w-100" disabled>
                                                            <i data-feather="check-circle" class="me-1" style="width:16px;height:16px;"></i> Current Plan
                                                        </button>

                                                    <?php elseif ($current_plan_id && $plan['id'] == 1): ?>

                                                        <!-- Free plan is unavailable once user has an active paid subscription -->
                                                        <button class="btn btn-secondary rounded-4 text-light p-2 w-100" disabled>
                                                            Not Available
                                                        </button>

                                                    <?php elseif ($current_plan_id && $plan['id'] > $current_plan_id): ?>

                                                        <!-- Upgrade to a higher plan -->
                                                        <button class="btn subBtn rounded-4 text-light p-2 w-100 choosePlanBtn"
                                                            data-plan-id="<?= $plan['id'] ?>"
                                                            data-plan-name="<?= htmlspecialchars(ucfirst($plan['name'])) ?>"
                                                            data-plan-price="<?= number_format($plan['price'], 2) ?>"
                                                            data-plan-duration="<?= $plan['duration_days'] == 30 ? 'Monthly' : ($plan['duration_days'] == 90 ? 'Quarterly' : 'Yearly') ?>"
                                                            data-type="upgrade"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#confirmPlanModal">
                                                            <i data-feather="arrow-up-circle" class="me-1" style="width:16px;height:16px;"></i> Upgrade Plan
                                                        </button>

                                                    <?php elseif ($current_plan_id && $plan['id'] < $current_plan_id): ?>

                                                        <!-- Downgrade to a lower plan -->
                                                        <button class="btn btn-warning rounded-4 text-dark p-2 w-100 choosePlanBtn"
                                                            data-plan-id="<?= $plan['id'] ?>"
                                                            data-plan-name="<?= htmlspecialchars(ucfirst($plan['name'])) ?>"
                                                            data-plan-price="<?= number_format($plan['price'], 2) ?>"
                                                            data-plan-duration="<?= $plan['duration_days'] == 30 ? 'Monthly' : ($plan['duration_days'] == 90 ? 'Quarterly' : 'Yearly') ?>"
                                                            data-type="downgrade"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#confirmPlanModal">
                                                            <i data-feather="arrow-down-circle" class="me-1" style="width:16px;height:16px;"></i> Downgrade Plan
                                                        </button>

                                                    <?php elseif (!$current_plan_id): ?>

                                                        <!-- No active plan — show Subscribe button -->
                                                        <button class="btn subBtn rounded-4 text-light p-2 w-100 choosePlanBtn"
                                                            data-plan-id="<?= $plan['id'] ?>"
                                                            data-plan-name="<?= htmlspecialchars(ucfirst($plan['name'])) ?>"
                                                            data-plan-price="<?= number_format($plan['price'], 2) ?>"
                                                            data-plan-duration="<?= $plan['duration_days'] == 30 ? 'Monthly' : ($plan['duration_days'] == 90 ? 'Quarterly' : 'Yearly') ?>"
                                                            data-type="new"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#confirmPlanModal">
                                                            Subscribe
                                                        </button>

                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>                        

                    </div>

                    <!-- Plan Confirmation Modal -->
                    <div class="modal fade" id="confirmPlanModal" tabindex="-1" aria-labelledby="confirmPlanModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold" id="confirmPlanModalLabel">
                                        <i data-feather="credit-card" class="text-primary me-2"></i> Confirm Subscription
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body pt-3">
                                    <div class="alert alert-light border rounded-3 text-center p-4 mb-3">
                                        <p class="mb-1 text-muted fs-13">You are about to subscribe to</p>
                                        <h4 class="fw-bold text-dark mb-1" id="modalPlanName">--</h4>
                                        <h3 class="fw-bold text-primary mb-0">₦<span id="modalPlanPrice">--</span></h3>
                                        <small class="text-muted" id="modalPlanDuration">--</small>
                                    </div>
                                    <p class="text-muted fs-13 text-center">
                                        You will be redirected to Paystack to securely complete your payment.
                                    </p>
                                    <div id="modalErrorAlert" class="alert alert-danger d-none" role="alert"></div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-outline-secondary rounded-4 px-4" data-bs-dismiss="modal" id="cancelPlanBtn">
                                        <i data-feather="x" class="me-1"></i> Cancel
                                    </button>
                                    <button type="button" class="btn btn-primary rounded-4 px-4" id="proceedPaymentBtn">
                                        <span id="proceedBtnText"><i data-feather="arrow-right-circle" class="me-1"></i> Proceed to Payment</span>
                                        <span id="proceedBtnSpinner" class="d-none">
                                            <span class="spinner-border spinner-border-sm me-1" role="status"></span> Processing...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Plan Confirmation Modal -->

                </div> <!-- container-fluid -->
            </div> <!-- content -->

            <!-- Footer Start -->
            <?php include_once "footer.php"; ?>
            <!-- end Footer -->

        </div>

    </div>

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

    <script>
        const CSRF_TOKEN = '<?= htmlspecialchars($csrf_token ?? '') ?>';

        // Populate modal when "Choose Plan" is clicked
        document.querySelectorAll('.choosePlanBtn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const planId       = this.getAttribute('data-plan-id');
                const planName     = this.getAttribute('data-plan-name');
                const planPrice    = this.getAttribute('data-plan-price');
                const planDuration = this.getAttribute('data-plan-duration');
                const paymentType  = this.getAttribute('data-type');

                document.getElementById('modalPlanName').textContent     = planName;
                document.getElementById('modalPlanPrice').textContent    = planPrice;
                document.getElementById('modalPlanDuration').textContent = planDuration;
                document.getElementById('proceedPaymentBtn').dataset.planId = planId;
                document.getElementById('proceedPaymentBtn').dataset.type   = paymentType;

                // Reset state
                document.getElementById('modalErrorAlert').classList.add('d-none');
                document.getElementById('modalErrorAlert').textContent = '';
                setProceedLoading(false);
                feather.replace();
            });
        });

        function setProceedLoading(loading) {
            const btnText    = document.getElementById('proceedBtnText');
            const btnSpinner = document.getElementById('proceedBtnSpinner');
            const proceedBtn = document.getElementById('proceedPaymentBtn');
            const cancelBtn  = document.getElementById('cancelPlanBtn');

            if (loading) {
                btnText.classList.add('d-none');
                btnSpinner.classList.remove('d-none');
                proceedBtn.disabled = true;
                cancelBtn.disabled  = true;
            } else {
                btnText.classList.remove('d-none');
                btnSpinner.classList.add('d-none');
                proceedBtn.disabled = false;
                cancelBtn.disabled  = false;
            }
        }

        document.getElementById('proceedPaymentBtn').addEventListener('click', function() {
            const planId    = this.dataset.planId;
            const payType   = this.dataset.type;
            const errorBox  = document.getElementById('modalErrorAlert');

            errorBox.classList.add('d-none');
            errorBox.textContent = '';
            setProceedLoading(true);

            fetch('<?php echo $base_url; ?>process/process_payment_data_plan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Csrf-Token': CSRF_TOKEN
                },
                body: JSON.stringify({
                    action: 'initiate_plan_payment',
                    plan_id: planId,
                    payment_type: payType
                })
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.status === 'success' && data.payment_url) {
                    // Redirect to Paystack
                    window.location.href = data.payment_url;
                } else {
                    setProceedLoading(false);
                    errorBox.textContent = data.message || 'An error occurred. Please try again.';
                    errorBox.classList.remove('d-none');
                }
            })
            .catch(function(err) {
                setProceedLoading(false);
                errorBox.textContent = 'Network error. Please check your connection and try again.';
                errorBox.classList.remove('d-none');
            });
        });
    </script>
</body>

</html>