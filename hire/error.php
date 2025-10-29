<?php
require_once "../app/config.php";

?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <meta charset="utf-8" />
    <title>Error 404 | Devhire - Page Not Found</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo $base_url; ?>assets/images/favicon.ico">

    <!-- App css -->
    <link href="<?php echo $base_url; ?>assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons -->
    <link href="<?php echo $base_url; ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <script src="<?php echo $base_url; ?>assets/js/head.js"></script>

</head>

<!-- body start -->

<body data-menu-color="light" data-sidebar="default">

    <body class="maintenance-bg-image">

        <!-- Begin page -->
        <div class="maintenance-pages">
            <div class="container-fluid p-0">
                <div class="row">
                    <div class="col-xl-12 align-self-center">
                        <div class="row">
                            <div class="col-md-5 mx-auto">
                                <div class="text-center">
                                    <div class="mb-0">
                                        <h3 class="fw-semibold text-dark text-capitalize">Oops!, Page Not Found</h3>
                                        <p class="text-dark">This pages you are trying to access does not exits or has been moved. <br> Try going back to our homepage.</p>
                                    </div>

                                    <a class='btn btn-primary mt-3 me-1' href='/devhire/'>Back to Home</a>

                                    <div class="error-page mt-4">
                                        <img src="<?php echo $base_url; ?>assets/images/svg/404-error.svg" class="img-fluid" alt="coming-soon">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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

        <!-- App js-->
        <script src="<?php echo $base_url; ?>assets/js/app.js"></script>

    </body>
</html>