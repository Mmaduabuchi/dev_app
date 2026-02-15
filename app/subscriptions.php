<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";
//notification count
require_once __DIR__ . '/fetch_notification_count.php';


try{
    $sub_one = 1;
    $sub_two = 2;
    $sub_three = 3;

    // fetch user data from database
    $stmt = $conn->prepare("SELECT * FROM subscription_plans WHERE id = ?");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("i", $sub_one);
    $stmt->execute();
    $result = $stmt->get_result();
    $sub_one_data = $result->fetch_assoc();

    $sub_one_data_name = $sub_one_data["name"];
    $sub_one_data_id = $sub_one_data["id"];
    $sub_one_data_description = $sub_one_data["description"];
    $sub_one_data_price = number_format($sub_one_data["price"], 2);
    $sub_one_data_duration_days = $sub_one_data["duration_days"];

    //close stmt
    $stmt->close();

    // fetch user data from database
    $stmt = $conn->prepare("SELECT * FROM subscription_plans WHERE id = ?");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("i", $sub_two);
    $stmt->execute();
    $result = $stmt->get_result();
    $sub_two_data = $result->fetch_assoc();

    $sub_two_data_name = $sub_two_data["name"];
    $sub_two_data_id = $sub_two_data["id"];
    $sub_two_data_description = $sub_two_data["description"];
    $sub_two_data_price = number_format($sub_two_data["price"], 2);
    $sub_two_data_duration_days = $sub_two_data["duration_days"];

    //close stmt
    $stmt->close();

    // fetch user data from database
    $stmt = $conn->prepare("SELECT * FROM subscription_plans WHERE id = ?");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("i", $sub_three);
    $stmt->execute();
    $result = $stmt->get_result();
    $sub_three_data = $result->fetch_assoc();

    $sub_three_data_name = $sub_three_data["name"];
    $sub_three_data_id = $sub_three_data["id"];
    $sub_three_data_description = $sub_three_data["description"];
    $sub_three_data_price = number_format($sub_three_data["price"], 2);
    $sub_three_data_duration_days = $sub_three_data["duration_days"];

    //close stmt
    $stmt->close();

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

<!-- body start -->

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

                        <div class="col-md-6 col-xl-4">
                            <div class="card overflow-hidden">
                                <div class="card-body">
                                    <div class="widget-first">

                                        <div class="d-flex align-items-center mb-3">
                                            <div>
                                                <p class="mb-0 text-dark fs-16"><?= ucfirst($sub_one_data_name) ?></p>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-3">
                                            <h3 class="mb-0 fs-26 text-dark me-2 me-3">$<?= $sub_one_data_price ?> <sub> / Month</sub> </h3>
                                        </div>

                                        <p class="text-muted fs-14 mb-2">
                                            <?= htmlspecialchars($sub_one_data_description) ?>
                                        </p>

                                        <ul class="text-muted fs-13 mb-3 list-unstyled planList">
                                            <li><i data-feather="check-circle" class="text-success me-1"></i> Access to basic dashboard</li>
                                            <li><i data-feather="check-circle" class="text-success me-1"></i> Connect with up to 10 users</li>
                                            <li><i data-feather="check-circle" class="text-success me-1"></i> Profile visible only to limited users</li>
                                            <li><i data-feather="x-circle" class="text-danger me-1"></i> No analytics, priority support, or featured listing</li>
                                        </ul>



                                        <div class="row align-items-center">
                                            <div class="col">
                                                <button class="btn btn-primary subBtn0 rounded-4 text-light p-2 w-100">Active</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="widget-first">

                                        <div class="d-flex align-items-center mb-3">
                                            <div>
                                                <p class="mb-0 text-dark fs-16"><?= ucfirst($sub_two_data_name) ?></p>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-3">
                                            <h3 class="mb-0 fs-26 text-dark me-2 me-3">$<?= $sub_two_data_price ?> <sub> / Quarterly</sub> </h3>
                                        </div>

                                        <p class="text-muted fs-14 mb-2">
                                            <?= htmlspecialchars($sub_two_data_description) ?>
                                        </p>

                                        <ul class="text-muted fs-13 mb-3 list-unstyled planList">
                                            <li><i data-feather="check-circle" class="text-success me-1"></i> Unlimited connections with talent or employers</li>
                                            <li><i data-feather="check-circle" class="text-success me-1"></i> Profile featured at the top of search results</li>
                                            <li><i data-feather="check-circle" class="text-success me-1"></i> Access to basic analytics (views, invites, responses)</li>
                                            <li><i data-feather="x-circle" class="text-danger me-1"></i> No API or premium integrations</li>
                                        </ul>



                                        <div class="row align-items-center">
                                            <div class="col">
                                                <button class="btn btn-primary subBtn rounded-4 text-light p-2 w-100">Choose Plan</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="widget-first">

                                        <div class="d-flex align-items-center mb-3">
                                            <div>
                                                <p class="mb-0 text-dark fs-16"><?= ucfirst($sub_three_data_name) ?></p>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-3">
                                            <h3 class="mb-0 fs-26 text-dark me-2 me-3">$<?= $sub_three_data_price ?> <sub> / Yearly</sub> </h3>
                                        </div>

                                        <p class="text-muted fs-14 mb-2">
                                            <?= htmlspecialchars($sub_three_data_description) ?>
                                        </p>

                                        <ul class="text-muted fs-13 mb-3 list-unstyled planList">
                                            <li><i data-feather="check-circle" class="text-success me-1"></i> Top featured profile placement across the platform</li>
                                            <li><i data-feather="check-circle" class="text-success me-1"></i> Full analytics and reporting dashboard</li>
                                            <li><i data-feather="check-circle" class="text-success me-1"></i> API access and third-party integrations</li>
                                            <li><i data-feather="check-circle" class="text-success me-1"></i> 24/7 priority support and collaboration tools</li>
                                        </ul>



                                        <div class="row align-items-center">
                                            <div class="col">
                                                <button class="btn btn-primary subBtn rounded-4 text-light p-2 w-100">Choose Plan</button>
                                            </div>
                                        </div>

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