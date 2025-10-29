<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";
//notification count
require_once __DIR__ . '/fetch_notification_count.php';

?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title>Manage Subscription | devhire - Dashboard</title>
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
                            <h4 class="fs-18 fw-semibold m-0">Manage Subscription</h4>
                        </div>
                    </div>

                    <!-- Start Row -->
                    <div class="row">

                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <h5 class="text-dark">Plan & Billing</h5>
                                            <p>Manage your plan and payments</p>
                                        </div>
                                        <div class="col-12 col-md-6 text-end pt-2">
                                            <button class="btn btn-outline-dark">Cancel subscription</button>
                                            <button class="btn btn-outline-dark">Manage payments</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body bg-light-subtle">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <h5 class="text-dark">Current Plan</h5>
                                        </div>
                                        <div class="col-12 col-md-6 text-end">
                                            <button class="btn btn-outline-dark">Change Plan</button>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="widget-first">

                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <p class="mb-0 text-dark fs-15">Monthly Plan</p>
                                                            <div>
                                                                <span class="badge text-success badge-custom-second bg-success-subtle fw-medium rounded-4 fs-14 me-2 contact-badge">
                                                                    Active
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <h3 class="mb-0 fs-22 text-dark me-3">$50/Monthly</h3>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="widget-first">

                                                        <div class="d-flex align-items-center mb-2">
                                                            <p class="mb-0 text-dark fs-15">Renew at</p>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <h3 class="mb-0 fs-22 text-dark me-3">Oct 26, 2025</h3>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- End Start -->


                    <div class="row">
                        <!-- Subscription History / Logs -->
                        <div class="col-lg-6 col-xl-12">
                            <div class="card overflow-hidden">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h5 class="card-title mb-0">Subscription History / Logs</h5>

                                        <div class="ms-auto">
                                            <button class="btn btn-sm bg-light border dropdown-toggle fw-medium" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">View All<i class="mdi mdi-chevron-down ms-1 fs-14"></i></button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#">Today</a>
                                                <a class="dropdown-item" href="#">This Week</a>
                                                <a class="dropdown-item" href="#">Last Week</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- start card body -->
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-traffic mb-0">

                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Plan</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Invoice</th>
                                                </tr>
                                            </thead>

                                            <tr>
                                                <td class="text-center">
                                                    <div class="d-flex align-items-center">
                                                        <!-- <span class="avatar mb-0 position-relative">
                                                            <img class="avatar avatar-sm rounded-2 me-3 bg-primary-subtle rounded-circle p-1" src="<?php echo $base_url; ?>assets/images/products/bag.png" alt="product-image" />
                                                        </span> -->
                                                        <p class="mb-0 fs-14">Oct 5, 2025</p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary fw-semibold">Premium</span>
                                                </td>
                                                <td>
                                                    <p class="mb-0 fw-medium">$15.00</p>
                                                </td>
                                                <td>
                                                    <p class="mb-0 fw-medium">Success</p>
                                                </td>
                                                <td>
                                                    <p class="mb-0 fw-medium">[Download]</p>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-center">
                                                    <div class="d-flex align-items-center">
                                                        <!-- <span class="avatar mb-0 position-relative">
                                                            <img class="avatar avatar-sm rounded-2 me-3 bg-primary-subtle rounded-circle p-1" src="<?php echo $base_url; ?>assets/images/products/watch.png" alt="product-image" />
                                                        </span> -->
                                                        <p class="mb-0 fs-14">Nov 5, 2025</p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary fw-semibold">Standard</span>
                                                </td>
                                                <td>
                                                    <p class="mb-0 fw-medium">$9.00</p>
                                                </td>
                                                <td>
                                                    <p class="mb-0 fw-medium">Success</p>
                                                </td>
                                                <td>
                                                    <p class="mb-0 fw-medium">[Download]</p>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-center">
                                                    <div class="d-flex align-items-center">
                                                        <!-- <span class="avatar mb-0 position-relative">
                                                            <img class="avatar avatar-sm rounded-2 me-3 bg-primary-subtle rounded-circle p-1" src="<?php echo $base_url; ?>assets/images/products/headphone.png" alt="product-image" />
                                                        </span> -->
                                                        <p class="mb-0 fs-14">Oct 5, 2024</p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary fw-semibold">Premium</span>
                                                </td>
                                                <td>
                                                    <p class="mb-0 fw-medium">$15.00</p>
                                                </td>
                                                <td>
                                                    <p class="mb-0 fw-medium">Success</p>
                                                </td>
                                                <td>
                                                    <p class="mb-0 fw-medium">[Download]</p>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-center">
                                                    <div class="d-flex align-items-center">
                                                        <!-- <span class="avatar mb-0 position-relative">
                                                            <img class="avatar avatar-sm rounded-2 me-3 bg-primary-subtle rounded-circle p-1" src="<?php echo $base_url; ?>assets/images/products/leather-jacket.png" alt="product-image" />
                                                        </span> -->
                                                        <p class="mb-0 fs-14">Oct 5, 2023</p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary fw-semibold">Standard</span>
                                                </td>
                                                <td>
                                                    <p class="mb-0 fw-medium">$9.00</p>
                                                </td>
                                                <td>
                                                    <p class="mb-0 fw-medium">Success</p>
                                                </td>
                                                <td>
                                                    <p class="mb-0 fw-medium">[Download]</p>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-center">
                                                    <div class="d-flex align-items-center">
                                                            <!-- <span class="avatar mb-0 position-relative">
                                                                <img class="avatar avatar-sm rounded-2 me-3 bg-primary-subtle rounded-circle p-1" src="<?php echo $base_url; ?>assets/images/products/shoes.png" alt="product-image" />
                                                            </span> -->
                                                        <p class="mb-0 fs-14">Oct 5, 2022</p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary fw-semibold">Premium</span>
                                                </td>
                                                <td>
                                                    <p class="mb-0 fw-medium">$15.00</p>
                                                </td>
                                                <td>
                                                    <p class="mb-0 fw-medium">Success</p>
                                                </td>
                                                <td>
                                                    <p class="mb-0 fw-medium">[Download]</p>
                                                </td>
                                            </tr>

                                        </table>
                                    </div>
                                </div>
                                <!-- end card body -->
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