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
            .subBtn{
                background-color: #0077b6 !important;
                font-weight: bold !important;
                border: none !important;
                transition: background-color 0.3s ease, color 0.3s ease !important;
            }
            .subBtn:hover {
                background-color: #023e8a !important;
                color: #fff !important;
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
                                <h4 class="fs-18 fw-semibold m-0">Subscriptions</h4>
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
                                                    <p class="mb-0 text-dark fs-16">Free</p>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center mb-3">
                                                <h3 class="mb-0 fs-26 text-dark me-2 me-3">$0.00 <sub> / month</sub> </h3>
                                            </div>

                                            <p class="text-muted fs-14 mb-2">
                                                Perfect for individuals or small startups taking their first step into the DevHire ecosystem.
                                            </p>

                                            <ul class="text-muted fs-13 mb-3 list-unstyled">
                                                <li><i data-feather="check-circle" class="text-success me-1"></i> Access to basic hiring dashboard</li>
                                                <li><i data-feather="check-circle" class="text-success me-1"></i> Post up to 3 projects per month</li>
                                                <li><i data-feather="check-circle" class="text-success me-1"></i> View and invite up to 10 developers</li>
                                                <li><i data-feather="check-circle" class="text-success me-1"></i> Basic support (Email only)</li>
                                                <li><i data-feather="x-circle" class="text-danger me-1"></i> No analytics or team collaboration</li>
                                            </ul>

                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <button class="btn btn-primary subBtn rounded-4 text-light p-2 w-100">Buy Now</button>
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
                                                    <p class="mb-0 text-dark fs-16">Standard</p>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center mb-3">
                                                <h3 class="mb-0 fs-26 text-dark me-2 me-3">$9.00 <sub> / month</sub> </h3>
                                            </div>

                                            <p class="text-muted fs-14 mb-2">
                                                Ideal for small teams and growing businesses looking for more control and visibility.
                                            </p>

                                            <ul class="text-muted fs-13 mb-3 list-unstyled">
                                                <li><i data-feather="check-circle" class="text-success me-1"></i> Post up to 10 projects per month</li>
                                                <li><i data-feather="check-circle" class="text-success me-1"></i> View and invite unlimited developers</li>
                                                <li><i data-feather="check-circle" class="text-success me-1"></i> Team collaboration tools (up to 3 members)</li>
                                                <li><i data-feather="check-circle" class="text-success me-1"></i> Basic analytics dashboard</li>
                                                <li><i data-feather="check-circle" class="text-success me-1"></i> Priority email & chat support</li>
                                                <li><i data-feather="x-circle" class="text-danger me-1"></i> No custom branding or premium integrations</li>
                                            </ul>
                                            
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <button class="btn btn-primary subBtn rounded-4 text-light p-2 w-100">Buy Now</button>
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
                                                    <p class="mb-0 text-dark fs-16">Premium</p>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center mb-3">
                                                <h3 class="mb-0 fs-26 text-dark me-2 me-3">$15.00 <sub> / month</sub> </h3>
                                            </div>

                                            <p class="text-muted fs-14 mb-2">
                                                Designed for established businesses seeking advanced features, analytics, and priority support.
                                            </p>

                                            <ul class="text-muted fs-13 mb-3 list-unstyled">
                                                <li><i data-feather="check-circle" class="text-success me-1"></i> Unlimited job postings & project invites</li>
                                                <li><i data-feather="check-circle" class="text-success me-1"></i> Team collaboration (up to 10 members)</li>
                                                <li><i data-feather="check-circle" class="text-success me-1"></i> Full analytics and reporting dashboard</li>
                                                <li><i data-feather="check-circle" class="text-success me-1"></i> API access and third-party integrations</li>
                                                <li><i data-feather="check-circle" class="text-success me-1"></i> Priority support (24/7)</li>
                                                <li><i data-feather="x-circle" class="text-danger me-1"></i> Limited to one workspace</li>
                                            </ul>
                                            
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <button class="btn btn-primary subBtn rounded-4 text-light p-2 w-100">Buy Now</button>
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