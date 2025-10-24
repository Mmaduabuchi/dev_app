<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";
//notification count
require_once __DIR__ . '/fetch_notification_count.php';
//get usertoken from session
$usertoken = $_SESSION['user']['usertoken'] ?? null;

// Fetch candidate profile from the database
$stmt = $conn->prepare("SELECT dp.*, u.picture, u.fullname, u.email, u.role FROM developers_profiles dp JOIN users u ON dp.user_id = u.id WHERE u.usertoken = ? AND dp.action = 'completed'");
$stmt->bind_param("s", $usertoken);
$stmt->execute();
$result = $stmt->get_result();
$candidate = $result->fetch_assoc();

if (!$candidate) {
    // Candidate not found, redirect to error page
    header("Location: /devhire/dashboard/error/");
    exit();
}

$profile_pic = $candidate['profile_picture'] ? '/devhire/' . $candidate['profile_picture'] :  $candidate['picture'];
$fullname = htmlspecialchars($candidate['legal_name']);
$email = htmlspecialchars($candidate['email']);
$role = htmlspecialchars($candidate['role']);
$bio = htmlspecialchars($candidate['bio']);
$years_of_experience = $candidate['years_of_experience'];
$english_proficiency = $candidate['english_proficiency'];
$preferred_hourly_rate = $candidate['preferred_hourly_rate'];
$primary_job_interest = $candidate['primary_job_interest'];
$job_commitment = $candidate['job_commitment'];
$location = htmlspecialchars($candidate['location']);
$age = htmlspecialchars($candidate['age']);
$citizenship = htmlspecialchars($candidate['citizenship']);
$website = htmlspecialchars($candidate['website']);
$github = htmlspecialchars($candidate['github']);
$linkedin = htmlspecialchars($candidate['linkedin']);


?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title>My Profile | devhire - Dashboard</title>
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

        #img_candidate {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
        }

        #img_candidate_h5 {
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


        /* .education-card {
            border-radius: 12px;
            border: 1px solid #e5e5e5;
            padding: 2rem;
            background: #fff;
        } */

        .edu-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2rem;
            position: relative;
        }

        .edu-number {
            width: 40px;
            height: 40px;
            border: 2px solid #198754;
            color: #198754;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .edu-line {
            position: absolute;
            left: 19px;
            top: 45px;
            width: 2px;
            height: calc(100% - 45px);
            background-color: #e5e5e5;
        }

        .edu-content h6 {
            color: #198754;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .edu-content h5 {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .edu-content p {
            color: #6c757d;
            margin-bottom: 0;
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
                            <h4 class="fs-18 fw-semibold m-0">My Profile</h4>
                        </div>
                    </div>

                    <!-- Start Row -->
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-7">
                            <div class="row">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Overview</h5>
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

                            <div class="row mt-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Education</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <?php
                                                //fetch user work experience records
                                                $stmt = $conn->prepare("SELECT * FROM `education_records` WHERE user_id = ? ORDER BY created_at DESC");
                                                $stmt->bind_param("i", $user_id);
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                if ($result->num_rows > 0):
                                                    $count = 1;
                                                    while ($row = $result->fetch_assoc()):
                                                ?>
                                                        <!-- Item 1 -->
                                                        <div class="edu-item">
                                                            <div class="edu-number"><?= $count ?></div>
                                                            <div class="edu-content">
                                                                <h6><?= htmlspecialchars($row['academy']) ?></h6>
                                                                <?php
                                                                $courseLabel = !empty($row['course']) ? htmlspecialchars(ucwords(str_replace('_', ' ', $row['course']))) : '';
                                                                $degreeTitle = !empty($row['degree']) ? htmlspecialchars($row['degree']) : '';
                                                                $startYear = htmlspecialchars($row['start_year'] ?? '');
                                                                $endYearRaw = $row['end_year'] ?? '';
                                                                $endYear = (empty($endYearRaw) || in_array(strtolower($endYearRaw), ['present', 'ongoing'], true)) ? 'Present' : htmlspecialchars($endYearRaw);
                                                                ?>
                                                                <h5 class="d-flex justify-content-between align-items-center mb-1">
                                                                    <span>
                                                                        <?php if ($degreeTitle): ?>
                                                                            <strong><?= $degreeTitle ?></strong><?php if ($courseLabel): ?> — <?= $courseLabel ?><?php endif; ?>
                                                                            <?php else: ?>
                                                                                <?= $courseLabel ?>
                                                                            <?php endif; ?>
                                                                    </span>
                                                                    <small class="text-muted"><?= $startYear ?> &ndash; <?= $endYear ?></small>
                                                                </h5>
                                                                <p><?= htmlspecialchars($row['description']) ?></p>
                                                            </div>
                                                            <?php if ($count < $result->num_rows): ?>
                                                                <div class="edu-line"></div>
                                                            <?php endif; ?>
                                                        </div>
                                                <?php
                                                        $count++;
                                                    endwhile;
                                                else:
                                                ?>
                                                    <p class="text-center">No educational record added yet..</p>
                                                <?php
                                                endif;
                                                $stmt->close();
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Skills</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <div class="d-flex flex-wrap gap-2">
                                                    <?php
                                                    //fetch user skills from database
                                                    $stmt = $conn->prepare("SELECT us.id AS user_skill_id, s.skill_name FROM `user_skills` us JOIN `skills` s ON us.skill_id = s.id WHERE us.user_id = ?");
                                                    $stmt->bind_param("i", $user_id);
                                                    $stmt->execute();
                                                    $result = $stmt->get_result();
                                                    if ($result->num_rows > 0) {
                                                        while ($skill = $result->fetch_assoc()) {
                                                    ?>
                                                            <span class="tagDone d-flex align-items-center" data-id="<?= $skill['user_skill_id'] ?>">
                                                                <?= htmlspecialchars($skill['skill_name']) ?>
                                                                <!-- <span class="removeAdded">&times;</span> -->
                                                            </span>
                                                    <?php
                                                        }
                                                    } else {
                                                        echo "<p class='text-center'>No skills added yet..</p>";
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Work Experience</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <?php
                                                //fetch user work experience records
                                                $stmt = $conn->prepare("SELECT * FROM `work_experience_records` WHERE user_id = ? ORDER BY created_at DESC");
                                                $stmt->bind_param("i", $user_id);
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                if ($result->num_rows > 0):
                                                    $count = 1;
                                                    while ($row = $result->fetch_assoc()):
                                                ?>
                                                        <!-- Item 1 -->
                                                        <div class="edu-item">
                                                            <div class="edu-number"><?= $count ?></div>
                                                            <div class="edu-content">
                                                                <h6><?= htmlspecialchars($row['company']) ?></h6>
                                                                <h5><?= htmlspecialchars($row['job_title']) ?></h5>
                                                                <p><?= htmlspecialchars($row['job_description']) ?></p>
                                                            </div>
                                                            <?php if ($count < $result->num_rows): ?>
                                                                <div class="edu-line"></div>
                                                            <?php endif; ?>
                                                        </div>
                                                <?php
                                                        $count++;
                                                    endwhile;
                                                else:
                                                ?>
                                                    <p class="text-center">No work experience added yet..</p>
                                                <?php
                                                endif;
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
                                    <img id="img_candidate" src="<?= $profile_pic; ?>" alt="User Image">
                                    <h5 id="img_candidate_h5"><?= $fullname; ?></h5>
                                    <a href="#"><?= $role ?></a>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <div class="profile-info">
                                                <p><strong>Location:</strong> <?= $location . ', ' . $citizenship; ?></p>
                                                <hr>
                                                <p><strong>Age:</strong> 28</p>
                                                <hr>
                                                <p><strong>Email:</strong> <?= $email; ?> </p>
                                                <hr>
                                                <p><strong>Primary job interest:</strong> <?= ucfirst($primary_job_interest) ?> </p>
                                                <hr>
                                                <p><strong>English Proficiency:</strong> <?= ucfirst($english_proficiency) ?> </p>
                                                <hr>
                                                <p>
                                                    <strong>Job Commitment:</strong>
                                                    <?php 
                                                      echo ($job_commitment === "part_time") ? 'Part time' : ucfirst($job_commitment);
                                                    ?>
                                                </p>
                                                <hr>
                                                <p><strong>Preferred hourly rate in USD:</strong> <?= $preferred_hourly_rate ?> </p>
                                                <hr>
                                                <p><strong>Years of Experience:</strong> <?= $years_of_experience . ' years'; ?> </p>
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