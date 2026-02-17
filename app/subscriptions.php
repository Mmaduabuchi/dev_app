<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";
//notification count
require_once __DIR__ . '/fetch_notification_count.php';


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
                                                <h3 class="mb-0 fs-26 text-dark me-2 me-3">$<?= number_format($plan['price'], 2) ?> 
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
                                                    <?php $btn_color = $plan['id'] == 1 ? 'subBtn0' : 'subBtn' ?>
                                                    <button class="btn <?= $btn_color ?> rounded-4 text-light p-2 w-100">
                                                        <?= $plan['id'] == 1 ? 'Active' : 'Choose Plan' ?>
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>                        

                    </div>

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
</body>

</html>