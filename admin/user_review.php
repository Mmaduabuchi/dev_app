<?php
//include auth file
include_once "auth.php";

//include route
include_once "route.php";

if(!isset($_GET["token_ref"]) || !isset($_GET["user_id"]) || empty($_GET["token_ref"]) || empty($_GET["user_id"])){
    header("Location: users_management");
    exit();
}

if(strlen($_GET["token_ref"]) !== 72 || !ctype_xdigit($_GET["token_ref"]) || !is_numeric($_GET["user_id"]) || $_GET["user_id"] < 1){
    header("Location: users_management");
    exit();
}

//user id
$user_id_num = $_GET["user_id"];


// Determine current page
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

try{

    //get user details
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1");
    if($stmt === false){
        throw new Exception("Failed to prepare user statement");
    }
    $stmt->bind_param("i", $user_id_num);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 0){
        throw new Exception("User not found");
    }

    $user = $result->fetch_assoc();
    
    $user_joined_at = date("F j, Y", strtotime($user["created_at"]));
    $user_suspended_at = $user["suspended_at"];

    $stmt->close();

    //get user developers profile details
    $stmt = $conn->prepare("SELECT * FROM developers_profiles WHERE user_id = ?");
    if($stmt === false){
        throw new Exception("Failed to prepare developer profile statement");
    }
    $stmt->bind_param("i", $user["id"]);
    $stmt->execute();
    $result = $stmt->get_result();

    $developers_profiles = ($result->num_rows > 0) ? $result->fetch_assoc() : null;

    $stmt->close();

    $user_phone_number = $developers_profiles["phone_number"] ?? "N/A";
    $user_bio = $developers_profiles["bio"] ?? "N/A";
    $user_stack = $developers_profiles["stack"] ?? "N/A";
    $user_legal_name = $developers_profiles["legal_name"] ?? "N/A";
    $user_location = $developers_profiles["location"] ?? "N/A";
    $user_citizenship = $developers_profiles["citizenship"] ?? "N/A";
    $user_english_proficiency = $developers_profiles["english_proficiency"] ?? "N/A";
    $user_years_of_experience = $developers_profiles["years_of_experience"] ?? "N/A";
    $user_education_level = $developers_profiles["education_level"] ?? "N/A";
    $user_contact_email = $developers_profiles["contact_email"] ?? "N/A";
    $user_certificate = $developers_profiles["certificate"] ?? "N/A";
    $user_primary_job_interest = $developers_profiles["primary_job_interest"] ?? "N/A";
    $user_industry_experience = $developers_profiles["industry_experience"] ?? "N/A";
    $user_profile_picture = $developers_profiles["profile_picture"] ?? "N/A";
    $user_job_commitment = $developers_profiles["job_commitment"] ?? "N/A";
    $user_preferred_hourly_rate = $developers_profiles["preferred_hourly_rate"] ?? "N/A";
    $user_resume = $developers_profiles["resume"] ?? "N/A";
    $user_website = $developers_profiles["website"] ?? "N/A";
    $user_github = $developers_profiles["github"] ?? "N/A";
    $user_linkedin = $developers_profiles["linkedin"] ?? "N/A";
    $user_action = $developers_profiles["action"] ?? "N/A";

    // Query to fetch skills
    $stmt = $conn->prepare("SELECT s.skill_name FROM user_skills us INNER JOIN skills s ON us.skill_id = s.id WHERE us.user_id = ?");
    if($stmt === false){
        throw new Exception("Failed to prepare statement");
    }
    $stmt->bind_param("i", $user["id"]);
    $stmt->execute();
    $result = $stmt->get_result();

    $skills = [];

    while ($row = $result->fetch_assoc()) {
        $skills[] = $row['skill_name'];
    }

    $stmt->close();


    $resumePath = "./../" . $user_resume; // e.g. library/documents/profile_68f6531e7d9ae4.46434995.jpg

    if ($resumePath && file_exists($resumePath)) {
        // Get file name
        $fileName = basename($resumePath);

        // Get file size in bytes
        $fileSize = filesize($resumePath);

        // Convert to readable format
        if ($fileSize >= 1048576) {
            $size = number_format($fileSize / 1048576, 2) . ' MB';
        } elseif ($fileSize >= 1024) {
            $size = number_format($fileSize / 1024, 2) . ' KB';
        } else {
            $size = $fileSize . ' bytes';
        }

    } else {
        $fileName = "No resume uploaded";
        $size = "";
    }


} catch (Exception $e){
    $_SESSION['error'] = $e->getMessage();
    header('Location: /devhire/admin/dashboard/errorpage/error');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevHire Admin | User Details</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --dh-primary: #2563eb;
            --dh-bg: #f8fafc;
            --dh-card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --dh-border-radius: 12px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--dh-bg);
            color: #1e293b;
            padding-bottom: 3rem;
        }
        
        a {
            text-decoration: none;
        }

        /* Layout & Utilities */
        .container-fluid {
            max-width: 1200px;
        }

        .card {
            border: none;
            border-radius: var(--dh-border-radius);
            box-shadow: var(--dh-card-shadow);
            margin-bottom: 1.5rem;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.2s;
        }

        /* Summary Header */
        .profile-header-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        .badge-soft-success { background: #dcfce7; color: #166534; }
        .badge-soft-primary { background: #dbeafe; color: #1e40af; }
        .badge-soft-warning { background: #fef9c3; color: #854d0e; }

        /* Tabs Styling */
        .nav-tabs {
            border-bottom: 1px solid #e2e8f0;
            gap: 1.5rem;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #64748b;
            padding: 1rem 0;
            font-weight: 500;
            position: relative;
        }

        .nav-tabs .nav-link.active {
            color: var(--dh-primary);
            background: transparent;
        }

        .nav-tabs .nav-link.active::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--dh-primary);
        }

        /* Stats Card */
        .stat-label {
            font-size: 0.875rem;
            color: #64748b;
        }
        .stat-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
        }

        /* Table Styling */
        .table thead th {
            background-color: #f8fafc;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.025em;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Portfolio Grid */
        .portfolio-item {
            transition: transform 0.2s;
            cursor: pointer;
        }
        .portfolio-item:hover {
            transform: translateY(-4px);
        }
        .portfolio-img {
            height: 160px;
            background: #e2e8f0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Section Titles */
        .section-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
</head>
<body>

    <!-- Main Wrapper -->
    <div class="container-fluid mt-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="users_management" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Talent</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars(ucfirst($user["fullname"])) ?></li>
            </ol>
        </nav>

        <!-- Header Summary Card -->
        <div class="card p-4 border-0">
            <div class="row align-items-center">
                <div class="col-md-auto text-center text-md-start mb-3 mb-md-0">
                    <img src="../assets/gggt.avif" alt="Profile" class="profile-header-img">
                </div>
                <div class="col-md">
                    <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                        <h3 class="mb-0 fw-bold"><?= htmlspecialchars(ucfirst($user["fullname"])) ?></h3>
                        <span class="badge badge-soft-primary rounded-pill"><?= htmlspecialchars(ucfirst($user["user_type"])) ?></span>
                        <span class="badge badge-soft-success rounded-pill">Active</span>
                    </div>
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar3 me-1"></i> Joined <?= $user_joined_at ?>
                        <span class="mx-2">•</span> 
                        <i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars(ucfirst($user_location . ", " . $user_citizenship)) ?>
                    </p>
                </div>
                <div class="col-md-auto mt-3 mt-md-0 d-flex gap-2 flex-wrap">
                    <a href="mailto:<?= $user['email'] ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-envelope me-1"></i> 
                        Send Message
                    </a>
                    <button class="btn btn-outline-primary btn-sm"><i class="bi bi-shield-check me-1"></i> Verify</button>
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm border" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php
                                if($user_suspended_at === null) {
                            ?>
                                <li>
                                    <a class="dropdown-item text-warning" href="#" onclick="action_fun('suspend', <?= $user['id'] ?> )">
                                        <i class="bi bi-slash-circle me-2"></i> Suspend User
                                    </a>
                                </li>
                            <?php
                                } else {
                            ?>
                                <li>
                                    <a class="dropdown-item text-success" href="#" onclick="action_fun('unsuspend', <?= $user['id'] ?> )">
                                        <i class="bi bi-check-circle me-2"></i> Unsuspend User
                                    </a>
                                </li>
                            <?php
                                }
                            ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="action_fun('delete', <?= $user['id'] ?> )"><i class="bi bi-trash me-2"></i> Delete User</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($user_suspended_at !== null) { ?>
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <strong>Account Suspended!</strong> This user has been suspended.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-4" id="userTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button">Overview</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button">Payments</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="cv-tab" data-bs-toggle="tab" data-bs-target="#cv" type="button">CV & Portfolio</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button">Subscriptions</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button">Security Logs</button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="userTabsContent">
            
            <!-- Overview Tab -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <div class="row">
                    <!-- Left: Profile Info -->
                    <div class="col-lg-8">
                        <div class="card p-4">
                            <h5 class="section-title"><i class="bi bi-person text-primary"></i> Personal Details</h5>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small mb-1">Email Address</label>
                                    <p class="mb-0 fw-medium"><?= htmlspecialchars($user["email"]) ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small mb-1">Phone Number</label>
                                    <p class="mb-0 fw-medium"><?= htmlspecialchars($user_phone_number) ?></p>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="text-muted small mb-1">Professional Bio</label>
                                    <p class="text-muted small"><?= htmlspecialchars($user_bio) ?></p>
                                </div>
                            </div>

                            <h5 class="section-title mt-2"><i class="bi bi-stars text-primary"></i> Skills & Expertise</h5>
                            <div class="mb-4">
                                <?php if (!empty($skills)): ?>
                                    <?php foreach ($skills as $skill): ?>
                                        <span class="badge bg-light text-dark border p-2 px-3 me-2 mb-2">
                                            <?= htmlspecialchars($skill) ?>
                                        </span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted">No skills added</span>
                                <?php endif; ?>
                            </div>


                            <h5 class="section-title mt-2 mb-4"><i class="bi bi-briefcase text-primary"></i> Education</h5>
                            <div class="border-start ps-4 ms-2 py-2">
                                <?php
                                    $stmt = $conn->prepare("SELECT * FROM education_records WHERE user_id = ? ORDER BY start_year DESC");
                                    $stmt->bind_param("i", $user_id_num);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    
                                    if ($result->num_rows > 0): 

                                        $total = $result->num_rows;
                                        $count = 0;
                                        while ($edu = $result->fetch_assoc()): 
                                            $count++;
                                            $isLast = ($count === $total);
                                ?>
                                            <div class="mb-<?= $isLast ? '0' : '4' ?> position-relative">
                                                
                                                <div class="position-absolute translate-middle-x"
                                                    style="left: -25px; top: 5px; width: 10px; height: 10px;
                                                    background: <?= $isLast ? '#cbd5e1' : 'var(--dh-primary)' ?>;
                                                    border-radius: 50%;">
                                                </div>

                                                <!-- Course / Degree -->
                                                <h6 class="mb-1">
                                                    <?= htmlspecialchars($edu['degree']) ?> in <?= htmlspecialchars($edu['course']) ?>
                                                </h6>

                                                <!-- Academy + Years -->
                                                <p class="small text-muted mb-1">
                                                    <?= htmlspecialchars($edu['academy']) ?> • 
                                                    <?= htmlspecialchars($edu['start_year']) ?> —
                                                    <?= $edu['end_year'] ? htmlspecialchars($edu['end_year']) : 'Present' ?>
                                                </p>

                                                <!-- Description -->
                                                <?php if (!empty($edu['description'])): ?>
                                                    <p class="small text-muted">
                                                        <?= nl2br(htmlspecialchars($edu['description'])) ?>
                                                    </p>
                                                <?php endif; ?>

                                            </div>

                                <?php
                                        endwhile; 

                                    else:
                                ?>
                                        <p class="text-muted small">No education records available.</p>
                                <?php 
                                    endif; 
                                    $stmt->close(); 
                                ?>
                            </div>

                            <h5 class="section-title mt-5"><i class="bi bi-briefcase text-primary"></i> Work Experience</h5>
                            <div class="border-start ps-4 ms-2 py-2">
                                <?php
                                    $stmt = $conn->prepare("SELECT * FROM work_experience_records WHERE user_id = ? ORDER BY start_year DESC");
                                    $stmt->bind_param("i", $user_id_num);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    
                                    if ($result->num_rows > 0): 

                                        $total = $result->num_rows;
                                        $count = 0;
                                        while ($work = $result->fetch_assoc()): 
                                            $count++;
                                            $isLast = ($count === $total);
                                ?>
                                            <div class="mb-<?= $isLast ? '0' : '4' ?> position-relative">
                                                
                                                <div class="position-absolute translate-middle-x"
                                                    style="left: -25px; top: 5px; width: 10px; height: 10px;
                                                    background: <?= $isLast ? '#cbd5e1' : 'var(--dh-primary)' ?>;
                                                    border-radius: 50%;">
                                                </div>

                                                <!-- Course / Degree -->
                                                <h6 class="mb-1">
                                                    <?= htmlspecialchars($work['job_title']) ?> in <?= htmlspecialchars($work['company']) ?>
                                                </h6>

                                                <!-- Academy + Years -->
                                                <p class="small text-muted mb-1">
                                                    <?= htmlspecialchars($work['company']) ?> • 
                                                    <?= htmlspecialchars($work['start_year']) ?> —
                                                    <?= $work['end_year'] ? htmlspecialchars($work['end_year']) : 'Present' ?>
                                                </p>

                                                <!-- Description -->
                                                <?php if (!empty($work['job_description'])): ?>
                                                    <p class="small text-muted">
                                                        <?= nl2br(htmlspecialchars($work['job_description'])) ?>
                                                    </p>
                                                <?php endif; ?>

                                            </div>

                                <?php
                                        endwhile; 

                                    else:
                                ?>
                                        <p class="text-muted small">No work experience records available.</p>
                                <?php 
                                    endif; 
                                    $stmt->close(); 
                                ?>
                            </div>
                        </div>
                    </div>
                    <!-- Right: Stats -->
                    <div class="col-lg-4">
                        <div class="card p-4">
                            <h5 class="section-title"><i class="bi bi-folder2-open text-primary"></i> Portfolio</h5>
                            <div class="row g-3">
                                <div class="col-6">
                                    <a href="<?= $user_website ?>">
                                        <div class="p-3 border rounded d-flex align-items-center">
                                            <i class="bi bi-globe fs-3 text-primary me-3"></i>
                                            <div>
                                                <div class="stat-label">Website</div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="<?= $user_github ?>">
                                        <div class="p-3 border rounded d-flex align-items-center">
                                            <i class="bi bi-github fs-3 me-3"></i>
                                            <div>
                                                <div class="stat-label">GitHub</div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="<?= $user_linkedin ?>">
                                        <div class="p-3 border rounded d-flex align-items-center">
                                            <i class="bi bi-linkedin fs-3 text-primary me-3"></i>
                                            <div>
                                                <div class="stat-label">LinkedIn</div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="mt-4 p-3 bg-light rounded text-center">
                                <p class="small text-muted mb-0">Identity Verified on Jan 05, 2024</p>
                            </div>
                        </div>


                        <div class="card p-4">
                            <h5 class="section-title">
                                <!-- <i class="bi bi-folder2-open text-primary"></i>  -->
                                More details
                            </h5>
                            <div class="row g-3">
                                <div class="col">
                                    <ul>
                                        <li>
                                            <b>Legal Name: </b> <?= ucfirst($user_legal_name) ?>
                                        </li>
                                        <br>
                                        <li>
                                            <b>primary job interest: </b> <?= ucfirst($user_primary_job_interest) ?>
                                        </li>
                                        <br>
                                        <li>
                                            <b>English Proficiency: </b> <?= ucfirst($user_english_proficiency) ?>
                                        </li>
                                        <br>
                                        <li>
                                            <b>Job Commitment: </b> <?= ucfirst($user_job_commitment) ?>
                                        </li>
                                        <br>
                                        <li>
                                            <b>Preferred hourly rate in USD: </b> <?= $user_preferred_hourly_rate ?>
                                        </li>
                                        <br>
                                        <li>
                                            <b>Contact Email: </b> <?= $user_contact_email ?>
                                        </li>
                                        <br>
                                        <li>
                                            <b>Education Level: </b> <?= ucfirst($user_education_level) ?>
                                        </li>
                                        <br>
                                        <li>
                                            <b>Years of Experience: </b> <?= $user_years_of_experience . " years" ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

            <!-- Transaction Tab -->
            <div class="tab-pane fade" id="activity" role="tabpanel">
                <div class="card overflow-hidden">
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Transaction History</h5>
                         <a href="./../process/process_download_transactions.php?user_id=<?= urlencode($_GET['user_id']) ?>&token_ref=<?= urlencode($_GET['token_ref']) ?>" 
                            class="btn btn-light btn-sm border">
                            Download Log
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Plan</th>
                                    <th>Method</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                    // Fetch paginated transaction_history
                                    try {
                                        $stmt = $conn->prepare("SELECT * FROM transaction_history WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
                                        if (!$stmt) throw new Exception('Prepare failed: ' . $conn->error);
                                        $stmt->bind_param("iii", $user_id_num, $limit, $offset);
                                        $stmt->execute();
                                        $result = $stmt->get_result();

                                        // Fetch total count for pagination
                                        $totalStmt = $conn->prepare("SELECT COUNT(*) as total FROM transaction_history WHERE user_id = ?");
                                        $totalStmt->bind_param("i", $user_id_num);
                                        $totalStmt->execute();
                                        $totalRecords = $totalStmt->get_result()->fetch_assoc()['total'];
                                        $totalPages = ceil($totalRecords / $limit);
                                    } catch (Exception $e) {
                                        $conn->close();
                                        error_log($e->getMessage());
                                        echo '<tr><td colspan="6" class="text-center text-muted">Something went wrong. Please try again later.</td></tr>';
                                    }

                                    //render
                                    if ($result->num_rows < 1) {
                                        echo '<tr><td colspan="6" class="text-center text-muted">No transaction record found.</td></tr>';
                                    } else {
                                        while ($data = $result->fetch_assoc()) {
                                            $date = date("M d, Y", strtotime($data['created_at']));
                                            $planName = $data['plan'];
                                            $amount = number_format($data['amount'], 2);
                                            $method = ucfirst($data['method']);
                                            $status = ucfirst($data['status']);
                                            $transactionId = $data['transaction_id'];

                                            // Optional: badge color based on status
                                            switch (strtolower($data['status'])) {
                                                case 'active':
                                                    $badgeClass = 'bg-success-subtle text-success';
                                                    break;
                                                case 'expired':
                                                    $badgeClass = 'bg-danger-subtle text-danger';
                                                    break;
                                                case 'cancelled':
                                                    $badgeClass = 'bg-secondary-subtle text-secondary';
                                                    break;
                                                default:
                                                    $badgeClass = 'bg-primary-subtle text-primary';
                                            }

                                            echo "<tr>
                                                <td class='text-muted small'>{$transactionId}</td>
                                                <td><span class='fw-medium'>{$planName}</span></td>
                                                <td>{$method}</td>
                                                <td><span class='fw-bold text-success'>\${$amount}</span></td>
                                                <td>{$date}</td>
                                                <td><span class='badge {$badgeClass}'>{$status}</span></td>
                                            </tr>";
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav>
                                <ul class="pagination justify-content-center mt-3">

                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                            href="?token_ref=<?= urlencode($_GET['token_ref']) ?>&user_id=<?= urlencode($_GET['user_id']) ?>&page=<?= $page - 1 ?>">
                                            Previous
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                            <a class="page-link"
                                            href="?token_ref=<?= urlencode($_GET['token_ref']) ?>&user_id=<?= urlencode($_GET['user_id']) ?>&page=<?= $i ?>">
                                            <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                            href="?token_ref=<?= urlencode($_GET['token_ref']) ?>&user_id=<?= urlencode($_GET['user_id']) ?>&page=<?= $page + 1 ?>">
                                            Next
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- CV & Portfolio Tab -->
            <div class="tab-pane fade" id="cv" role="tabpanel">
                <div class="row">
                    <div class="col-lg-4 mb-4">
                        <div class="card p-4 h-100">
                            <h5 class="section-title">Curriculum Vitae</h5>
                            <div class="bg-light rounded p-5 text-center mb-3 d-flex flex-column align-items-center justify-content-center border border-dashed border-2">
                                <i class="bi bi-file-earmark-pdf fs-1 text-danger mb-2"></i>
                                <p class="small fw-medium mb-0">
                                    <?= htmlspecialchars($fileName); ?>
                                </p>
                                <?php if ($size): ?>
                                    <span class="text-muted smaller">
                                        <?= $size; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <a href="./../process/process_resume_download.php?file=<?= urlencode($user_resume); ?>" class="btn btn-primary w-100 mb-2">
                                <i class="bi bi-download me-2"></i> Download CV
                            </a>

                            <button class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#cvModal">
                                View Fullscreen
                            </button>

                            <div class="modal fade" id="cvModal" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-body p-0">
                                            <iframe src="../../<?= htmlspecialchars($user_resume); ?>" width="100%" height="600px"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 mb-4">
                        <div class="card p-4">
                            <h5 class="section-title">Portfolio Projects</h5>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="portfolio-item card border">
                                        <div class="portfolio-img">
                                            <i class="bi bi-code-square fs-2 text-muted"></i>
                                        </div>
                                        <div class="p-3">
                                            <h6 class="mb-1">Stripe Checkout Redesign</h6>
                                            <p class="text-muted smaller mb-0">UI/UX & Frontend Development</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="portfolio-item card border">
                                        <div class="portfolio-img">
                                            <i class="bi bi-phone fs-2 text-muted"></i>
                                        </div>
                                        <div class="p-3">
                                            <h6 class="mb-1">Crypto Tracking Mobile App</h6>
                                            <p class="text-muted smaller mb-0">React Native / Node.js</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="portfolio-item card border">
                                        <div class="portfolio-img">
                                            <i class="bi bi-cloud-check fs-2 text-muted"></i>
                                        </div>
                                        <div class="p-3">
                                            <h6 class="mb-1">SaaS Infrastructure Tool</h6>
                                            <p class="text-muted smaller mb-0">DevOps & Cloud Orchestration</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="portfolio-item card border d-flex align-items-center justify-content-center bg-light border-dashed" style="min-height: 245px;">
                                        <div class="text-center">
                                            <i class="bi bi-plus-circle fs-3 text-muted"></i>
                                            <p class="text-muted small mt-2">Add New Case Study</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payments Tab -->
            <div class="tab-pane fade" id="payments" role="tabpanel">
                <div class="card overflow-hidden">
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Subscription History</h5>
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control form-control-sm" placeholder="Search subscriptions...">
                            <button class="btn btn-light btn-sm border"><i class="bi bi-funnel"></i></button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Plan Name</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Invoice</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                    // Fetch paginated subscriptions
                                    try {
                                        $stmt = $conn->prepare("
                                            SELECT us.*, sp.name AS plan_name, sp.price AS plan_price
                                            FROM subscriptions us
                                            INNER JOIN subscription_plans sp ON us.plan_id = sp.id
                                            WHERE us.user_id = ?
                                            ORDER BY us.created_at DESC
                                            LIMIT ? OFFSET ?
                                        ");
                                        if (!$stmt) throw new Exception('Prepare failed: ' . $conn->error);
                                        $stmt->bind_param("iii", $user_id_num, $limit, $offset);
                                        $stmt->execute();
                                        $result = $stmt->get_result();

                                        // Fetch total count for pagination
                                        $totalStmt = $conn->prepare("SELECT COUNT(*) as total FROM subscriptions WHERE user_id = ?");
                                        $totalStmt->bind_param("i", $user_id_num);
                                        $totalStmt->execute();
                                        $totalRecords = $totalStmt->get_result()->fetch_assoc()['total'];
                                        $totalPages = ceil($totalRecords / $limit);
                                    } catch (Exception $e) {
                                        $conn->close();
                                        error_log($e->getMessage());
                                        echo '<tr><td colspan="6" class="text-center text-muted">Something went wrong. Please try again later.</td></tr>';
                                    }

                                    //render
                                    if ($result->num_rows < 1) {
                                        echo '<tr><td colspan="6" class="text-center text-muted">No subscriptions record found.</td></tr>';
                                    } else {
                                        while ($data = $result->fetch_assoc()) {
                                            $date = date("M d, Y", strtotime($data['created_at']));
                                            $planName = $data['plan_name'];
                                            $amount = number_format($data['plan_price'], 2);
                                            $status = ucfirst($data['status']);
                                            $transactionId = $data['transaction_id'];

                                            // Optional: badge color based on status
                                            switch (strtolower($data['status'])) {
                                                case 'active':
                                                    $badgeClass = 'bg-success-subtle text-success';
                                                    break;
                                                case 'expired':
                                                    $badgeClass = 'bg-danger-subtle text-danger';
                                                    break;
                                                case 'cancelled':
                                                    $badgeClass = 'bg-secondary-subtle text-secondary';
                                                    break;
                                                default:
                                                    $badgeClass = 'bg-primary-subtle text-primary';
                                            }

                                            echo "<tr>
                                                <td class='text-muted small'>{$transactionId}</td>
                                                <td><span class='fw-medium'>{$planName}</span></td>
                                                <td>{$date}</td>
                                                <td><span class='fw-bold text-success'>\${$amount}</span></td>
                                                <td><span class='badge {$badgeClass}'>{$status}</span></td>
                                                <td>
                                                    <a href='./../process/process_user_subscription_invoice.php?txn={$transactionId}&token_ref=" . urlencode($_GET['token_ref']) . "&user_id={$user_id_num}' class='text-primary'>
                                                        <i class='bi bi-download'></i> PDF
                                                    </a>
                                                </td>
                                            </tr>";
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav>
                                <ul class="pagination justify-content-center mt-3">

                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                            href="?token_ref=<?= urlencode($_GET['token_ref']) ?>&user_id=<?= urlencode($_GET['user_id']) ?>&page=<?= $page - 1 ?>">
                                            Previous
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                            <a class="page-link"
                                            href="?token_ref=<?= urlencode($_GET['token_ref']) ?>&user_id=<?= urlencode($_GET['user_id']) ?>&page=<?= $i ?>">
                                            <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                            href="?token_ref=<?= urlencode($_GET['token_ref']) ?>&user_id=<?= urlencode($_GET['user_id']) ?>&page=<?= $page + 1 ?>">
                                            Next
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Security Logs Tab -->
            <div class="tab-pane fade" id="security" role="tabpanel">
                <div class="card overflow-hidden">
                    <div class="p-4 border-bottom">
                        <h5 class="mb-0">Login & Security Logs</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Status</th>
                                    <th>IP Address</th>
                                    <th>Location</th>
                                    <th>Device / Browser</th>
                                    <th>Date & Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="fw-medium">Account Login</span></td>
                                    <td><span class="text-success fw-medium"><i class="bi bi-check-circle-fill me-1"></i> Success</span></td>
                                    <td>192.168.1.45</td>
                                    <td>San Francisco, US</td>
                                    <td class="small text-muted">Chrome / macOS 14.1</td>
                                    <td class="small text-muted">Today, 10:24 AM</td>
                                </tr>
                                <tr>
                                    <td><span class="fw-medium">Password Reset Request</span></td>
                                    <td><span class="text-warning fw-medium"><i class="bi bi-exclamation-triangle-fill me-1"></i> Triggered</span></td>
                                    <td>192.168.1.45</td>
                                    <td>San Francisco, US</td>
                                    <td class="small text-muted">Safari / iPhone 15</td>
                                    <td class="small text-muted">Yesterday, 08:12 PM</td>
                                </tr>
                                <tr>
                                    <td><span class="fw-medium">Failed Login Attempt</span></td>
                                    <td><span class="text-danger fw-medium"><i class="bi bi-x-circle-fill me-1"></i> Blocked</span></td>
                                    <td>45.12.99.102</td>
                                    <td>London, UK</td>
                                    <td class="small text-muted">Unknown / Android 13</td>
                                    <td class="small text-muted">Oct 20, 2023, 02:44 AM</td>
                                </tr>
                                <tr>
                                    <td><span class="fw-medium">2FA Enabled</span></td>
                                    <td><span class="text-success fw-medium"><i class="bi bi-check-circle-fill me-1"></i> Success</span></td>
                                    <td>192.168.1.45</td>
                                    <td>San Francisco, US</td>
                                    <td class="small text-muted">Chrome / macOS 14.1</td>
                                    <td class="small text-muted">Sep 12, 2023, 04:30 PM</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function action_fun(action, user_id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Are you sure you want to ' + action + ' this user?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, ' + action + ' this user'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Call your backend API
                    fetch('./../process/process_suspend_or_delete_account.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ 
                            action: action,
                            user_id: user_id
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message || action + ' successfully!',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            setTimeout(() => {
                                location.reload();
                            }, 3000);
                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                        }
                    })
                    .catch(error => {
                        // console.error('Error:', error);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: action + ' failed',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    });
                }
            });
        }
    </script>

</body>
</html>