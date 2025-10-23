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
        <title>Meet Developers | devhire - Dashboard</title>
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
            .contact-badge {
                cursor: pointer;
                transition: background-color 0.3s ease, color 0.3s ease !important;
            }

            .contact-badge:hover {
                background-color: #198754 !important;
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
                                <h4 class="fs-18 fw-semibold m-0">Meet Developers</h4>
                            </div>
                        </div>

                        <!-- Start Row -->
                        <div class="row">

                            <?php 
                            // Fetch developer profiles from the database
                            $stmt = $conn->prepare("SELECT dp.*, u.picture, u.usertoken, u.fullname, u.email, u.role FROM developers_profiles dp JOIN users u ON dp.user_id = u.id WHERE dp.action = 'completed'");
                            $stmt->execute();
                            $result = $stmt->get_result();

                            while ($developer = $result->fetch_assoc()) {
                                // Display each developer profile in a card
                            ?>
                                <div class="col-md-6 col-xl-3">
                                    <div class="card overflow-hidden">
                                        <div class="card-body">
                                            <div class="widget-first">

                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="rounded-2 bg-white p-1 me-3 shadow-sm border">
                                                        <?php
                                                            // $profile_pic = $developer['picture'] ? $developer['picture'] : '/devhire/' . $developer['profile_picture'];
                                                            $profile_pic = $developer['profile_picture'] ? '/devhire/' . $developer['profile_picture'] :  $developer['picture'];                                                        ?>
                                                        <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" width="40" height="40" class="rounded-circle">
                                                    </div>

                                                    <div>
                                                        <p class="mb-0 text-dark fs-16"><?php echo htmlspecialchars($developer['fullname']); ?></p>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-2">
                                                    <h3 class="mb-0 fs-6 text-dark me-2 me-3"><?php echo htmlspecialchars($developer['role']); ?></h3>
                                                </div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <h3 class="fs-6"><?php echo $developer['years_of_experience'] . ' years  of experience.'; ?></h3>
                                                </div>
                                                <p class="text-muted fs-14 mb-2">
                                                    <?php 
                                                        $bio = htmlspecialchars($developer['bio']); 
                                                        echo strlen($bio) > 50 ? substr($bio, 0, 50) . '...' : $bio;
                                                    ?>
                                                </p>
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <a href="/devhire/dashboard/candidate-profile?token=<?php echo uniqid();?>&ref=<?php echo $developer['usertoken']; ?>">
                                                            <span class="badge text-success badge-custom-second bg-success-subtle fw-medium rounded-4 fs-14 me-2 contact-badge">
                                                                + Contact me
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>    
                            
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