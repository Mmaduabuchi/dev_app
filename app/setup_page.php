<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";
//notification count
require_once __DIR__ . '/fetch_notification_count.php';
//get usertoken from session
$usertoken = $_SESSION['user']['usertoken'] ?? null;

try{

    // Check skills count
    $stmt = $conn->prepare("SELECT COUNT(*) FROM `user_skills` WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($skill_count);
    $stmt->fetch();
    $skill_count = (int) $skill_count;
    $stmt->close();

    // Check work experience count
    $stmt = $conn->prepare("SELECT COUNT(*) FROM `work_experience_records` WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($experience_count);
    $stmt->fetch();
    $experience_count = (int) $experience_count;
    $stmt->close();

    // Check social media and website
    $stmt = $conn->prepare("SELECT github, linkedin, website FROM `developers_profiles` WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($github, $linkedin, $website);
    $stmt->fetch();
    $social_complete = (!empty($github) && $github !== 'Not Specified') && (!empty($linkedin) && $linkedin !== 'Not Specified');
    $website_complete = (!empty($website) && $website !== 'Not Specified');
    $stmt->close();

} catch (exception $e) {
    $conn->close();
    //session log
    $_SESSION['error'] = $e->getMessage();
    error_log($e->getMessage());
    // echo "Something went wrong. Please try again later.";
    header("Location: /devhire/dashboard/error");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title>Setup Page | Devhire - Dashboard</title>
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
        /* Stepper Styles */
        .setup-stepper {
            position: relative;
            padding: 20px 0;
        }

        .stepper-item {
            position: relative;
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
        }

        .stepper-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 25px;
            top: 50px;
            bottom: -30px;
            width: 2px;
            background: #e9ecef;
            z-index: 1;
        }

        .stepper-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .stepper-item.completed .stepper-icon {
            background: linear-gradient(135deg, #28a745, #34ce57);
            border-color: #28a745;
            color: #fff;
            box-shadow: 0 4px 10px rgba(40, 167, 69, 0.2);
        }

        .stepper-content {
            flex-grow: 1;
            padding-top: 5px;
        }

        .stepper-title {
            font-size: 16px;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stepper-desc {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 0;
        }

        .stepper-action {
            margin-left: 20px;
            flex-shrink: 0;
        }

        .status-badge {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
        }

        @media (max-width: 576px) {
            .stepper-item {
                flex-direction: column;
            }
            .stepper-action {
                margin-left: 70px;
                margin-top: 10px;
            }
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

                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Setup Page</h4>
                    </div>
                </div>

                <section class="user-setup-section">
                    <div class="row">
                        <div class="col">
                            <div class="card modern-card overflow-hidden">
                                <div class="card-header bg-light-subtle  px-4 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box bg-gradient-primary me-3">
                                            <i class="bi bi-person-check-fill fs-4 text-white"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-1">Complete Your Profile</h4>
                                            <p class="text-muted mb-0 small">Follow these steps to finish setting up your account and boost your visibility.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="setup-stepper">
                                        <!-- Skills -->
                                        <div class="stepper-item <?= ($skill_count > 0) ? 'completed' : '' ?>">
                                            <div class="stepper-icon">
                                                <?php if ($skill_count > 0): ?>
                                                    <i class="bi bi-check-lg fs-4 text-white"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-lightning-fill fs-5 text-primary"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="stepper-content">
                                                <h5 class="stepper-title">
                                                    Technical Skills
                                                    <?php if ($skill_count > 0): ?>
                                                        <span class="badge bg-success-subtle text-success status-badge">Completed</span>
                                                    <?php endif; ?>
                                                </h5>
                                                <p class="stepper-desc text-muted">Add your professional skills to showcase your expertise.</p>
                                            </div>
                                            <div class="stepper-action">
                                                <a href="/devhire/dashboard/skills#skills" class="btn btn-sm <?= ($skill_count > 0) ? 'btn-soft-success' : 'btn-primary' ?> rounded-pill px-3">
                                                    <?= ($skill_count > 0) ? 'Edit Skills' : 'Set Up' ?>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Education -->
                                        <div class="stepper-item <?= ($education_count > 0) ? 'completed' : '' ?>">
                                            <div class="stepper-icon">
                                                <?php if ($education_count > 0): ?>
                                                    <i class="bi bi-check-lg fs-4 text-white"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-mortarboard-fill fs-5 text-primary"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="stepper-content">
                                                <h5 class="stepper-title">
                                                    Education
                                                    <?php if ($education_count > 0): ?>
                                                        <span class="badge bg-success-subtle text-success status-badge">Completed</span>
                                                    <?php endif; ?>
                                                </h5>
                                                <p class="stepper-desc">Provide your academic background to strengthen your profile.</p>
                                            </div>
                                            <div class="stepper-action">
                                                <a href="/devhire/dashboard/resume#education" class="btn btn-sm <?= ($education_count > 0) ? 'btn-soft-success' : 'btn-success' ?> rounded-pill px-3">
                                                    <?= ($education_count > 0) ? 'Edit Education' : 'Add Info' ?>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Social Media -->
                                        <div class="stepper-item <?= ($social_complete) ? 'completed' : '' ?>">
                                            <div class="stepper-icon">
                                                <?php if ($social_complete): ?>
                                                    <i class="bi bi-check-lg fs-4 text-white"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-share-fill fs-5 text-primary"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="stepper-content">
                                                <h5 class="stepper-title">
                                                    Social Media Handles
                                                    <?php if ($social_complete): ?>
                                                        <span class="badge bg-success-subtle text-success status-badge">Completed</span>
                                                    <?php endif; ?>
                                                </h5>
                                                <p class="stepper-desc">Link your GitHub and LinkedIn profiles for networking.</p>
                                            </div>
                                            <div class="stepper-action">
                                                <a href="/devhire/dashboard/profile#social-media" class="btn btn-sm <?= ($social_complete) ? 'btn-soft-success' : 'btn-warning text-white' ?> rounded-pill px-3">
                                                    <?= ($social_complete) ? 'Edit Handles' : 'Add Handles' ?>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Work Experience -->
                                        <div class="stepper-item <?= ($experience_count > 0) ? 'completed' : '' ?>">
                                            <div class="stepper-icon">
                                                <?php if ($experience_count > 0): ?>
                                                    <i class="bi bi-check-lg fs-4 text-white"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-briefcase-fill fs-5 text-primary"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="stepper-content">
                                                <h5 class="stepper-title">
                                                    Work Experience
                                                    <?php if ($experience_count > 0): ?>
                                                        <span class="badge bg-success-subtle text-success status-badge">Completed</span>
                                                    <?php endif; ?>
                                                </h5>
                                                <p class="stepper-desc">Showcase your professional journey and past roles.</p>
                                            </div>
                                            <div class="stepper-action">
                                                <a href="/devhire/dashboard/skills#works" class="btn btn-sm <?= ($experience_count > 0) ? 'btn-soft-success' : 'btn-danger' ?> rounded-pill px-3">
                                                    <?= ($experience_count > 0) ? 'Edit Roles' : 'Add Experience' ?>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Personal Website -->
                                        <div class="stepper-item <?= ($website_complete) ? 'completed' : '' ?>">
                                            <div class="stepper-icon">
                                                <?php if ($website_complete): ?>
                                                    <i class="bi bi-check-lg fs-4 text-white"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-globe2 fs-5 text-primary"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="stepper-content">
                                                <h5 class="stepper-title">
                                                    Personal Website
                                                    <?php if ($website_complete): ?>
                                                        <span class="badge bg-success-subtle text-success status-badge">Completed</span>
                                                    <?php endif; ?>
                                                </h5>
                                                <p class="stepper-desc">Link your portfolio or personal website for credibility.</p>
                                            </div>
                                            <div class="stepper-action">
                                                <a href="/devhire/dashboard/profile#website" class="btn btn-sm <?= ($website_complete) ? 'btn-soft-success' : 'btn-primary' ?> rounded-pill px-3">
                                                    <?= ($website_complete) ? 'Edit Website' : 'Add Website' ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-light-subtle text-center py-3">
                                    <span class="text-muted small">Need help? <a href="support-ticket">Contact support</a></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
            <!-- content -->

            <?php include "footer.php" ?>

        </div>
        <!-- content-page -->

    </div>
    <!-- end app-layout -->

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