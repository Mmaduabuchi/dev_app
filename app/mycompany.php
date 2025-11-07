<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";
//notification count
require_once __DIR__ . '/fetch_notification_count.php';
//get usertoken from session
$usertoken = $_SESSION['user']['usertoken'] ?? null;

// Fetch empolyer profile from the database
$stmt = $conn->prepare("SELECT ep.*, u.picture, u.fullname, u.email, u.role FROM employer_profiles ep JOIN users u ON ep.user_id = u.id WHERE u.usertoken = ? AND ep.action = 'completed'");
$stmt->bind_param("s", $usertoken);
$stmt->execute();
$result = $stmt->get_result();
$empolyer = $result->fetch_assoc();

if (!$empolyer) {
    // empolyer not found, redirect to error page
    header("Location: /devhire/dashboard/error");
    exit();
}

$profile_pic = $empolyer['company_logo'] ? '/devhire/' . $empolyer['company_logo'] :  $empolyer['picture'];
$email = htmlspecialchars($empolyer['email']);
$bio = htmlspecialchars($empolyer['bio']);
$company_name = $empolyer['company_name'];
$company_size = $empolyer['company_size'];
$industry = $empolyer['industry'];
$company_logo = htmlspecialchars($empolyer['company_logo']);
$website = htmlspecialchars($empolyer['website']);


?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title>My Company | devhire - Dashboard</title>
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

        .profile-container {
            margin-top: 50px;
        }

        .profile-card {
            background: #f9fdfd;
            border-radius: 12px;
            border: 1px solid #e5e5e5;
            text-align: center;
            padding: 30px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
        }

        #img_empolyer {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
        }

        #img_empolyer_h5 {
            font-weight: 600;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .profile-card a {
            color: #0077b6;
            text-decoration: none;
            font-weight: 500;
        }

        .profile-card a:hover {
            text-decoration: underline;
        }

        .profile-info {
            text-align: left;
            margin-top: 25px;
        }

        .profile-info p {
            margin-bottom: 5px;
            color: #555;
        }

        .profile-info strong {
            color: #000;
        }

        .fs-big {
            font-size: 1.1rem;
        }

        .tagDone {
            display: inline-block;
            background: #71eb6cff;
            color: white;
            padding: 5px 10px;
            margin: 3px;
            border-radius: 5px;
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
                            <h4 class="fs-18 fw-semibold m-0">My Company</h4>
                        </div>
                    </div>

                    <!-- Start Row -->
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-7">
                            <div class="row">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Company Overview</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <?php
                                                $paragraphs = preg_split("/\r\n|\n|\r/", trim($bio));
                                                foreach ($paragraphs as $para) {
                                                    $para = trim($para);
                                                    if (!empty($para)) {
                                                        echo '<p class="text-muted fs-big mb-2">' . htmlspecialchars($para) . '</p>';
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>  
                            
                            
                            <div class="row mt-4 mb-4">
                                <?php
                                if (!empty($website)):
                                ?>
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Portfolio</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col">
                                                    <p>Website URL::</p>
                                                    <span class="text-danger">
                                                        <i>
                                                            <a href="<?= htmlspecialchars($website) ?>" target="_blank">
                                                                <?= htmlspecialchars($website) ?>
                                                            </a>
                                                        </i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                endif;
                                ?>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-5 mb-4">
                            <div class="card">
                                <div class="card-header text-center">
                                    <img id="img_empolyer" src="<?= $profile_pic; ?>" alt="User Image">
                                    <h5 id="img_empolyer_h5"><?= $fullname; ?></h5>
                                    <a href="#"><?= $role ?></a>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <div class="profile-info">
                                                <p><strong>Company Name:</strong> <?= ucfirst($company_name) ?> </p>
                                                <hr>
                                                <p><strong>Email:</strong> <?= $email; ?> </p>
                                                <hr>
                                                <p><strong>Company Size:</strong> <?= ucfirst($company_size) ?> </p>
                                                <hr>
                                                <p>
                                                    <strong>Company Website:</strong>                                             
                                                    <a href="<?= htmlspecialchars($website) ?>" target="_blank">
                                                        <?= htmlspecialchars($website) ?>
                                                    </a>    
                                                </p>
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