<?php

//include auth file
include_once "auth.php";

//include route
include_once "route.php";

if(!isset($_GET["token_ref"]) || !isset($_GET["employer_id"]) || empty($_GET["token_ref"]) || empty($_GET["employer_id"])){
    header("Location: employers_management");
    exit();
}

if(strlen($_GET["token_ref"]) !== 72 || !ctype_xdigit($_GET["token_ref"]) || !is_numeric($_GET["employer_id"]) || $_GET["employer_id"] < 1){
    header("Location: employers_management");
    exit();
}

//employer id
$employer_id_num = $_GET["employer_id"];

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
    $stmt->bind_param("i", $employer_id_num);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 0){
        throw new Exception("Employer not found");
    }

    $employer = $result->fetch_assoc();
    
    $employer_joined_at = date("F j, Y", strtotime($employer["created_at"]));
    $employer_suspended_at = $employer["suspended_at"];
    $employer_email = $employer["email"];
    $employer_fullname = $employer["fullname"];
    $employer_tel = $employer["tel"] ?? "N/A";

    $stmt->close();

    //get employer profile details
    $stmt = $conn->prepare("SELECT * FROM employer_profiles WHERE user_id = ?");
    if($stmt === false){
        throw new Exception("Failed to prepare employer profile statement");
    }
    $stmt->bind_param("i", $employer["id"]);
    $stmt->execute();
    $result = $stmt->get_result();

    $employer_profile = ($result->num_rows > 0) ? $result->fetch_assoc() : null;

    $stmt->close();

    $company_name = $employer_profile["company_name"] ?? $employer_fullname;
    $company_size = $employer_profile["company_size"] ?? "N/A";
    $industry = $employer_profile["industry"] ?? "N/A";
    $website = $employer_profile["website"] ?? "N/A";
    $company_logo = $employer_profile["company_logo"] ?? "N/A";
    $bio = $employer_profile["bio"] ?? "N/A";
    $action = $employer_profile["action"] ?? "N/A";
    $location = $employer_profile["location"] ?? "N/A";
    $legal_name = $employer_profile["legal_name"] ?? "N/A";

    if ($company_logo === "N/A" || empty($company_logo)) {
        $logo_path = "../assets/gggt.avif"; // fallback image
    } else {
        $logo_path = "../" . $company_logo;
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
    <title>DevHire Admin | Employer Details</title>
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
            background-color: #fff;
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

        /* Section Titles */
        .section-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }


        /* Screen Loader */
        .screen-loader{
            position: fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(2px);
            z-index: 9999;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .loader-spinner{
            width:50px;
            height:50px;
            border:5px solid #e5e5e5;
            border-top:5px solid #0A66C2;
            border-radius:50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin{
            from{ transform: rotate(0deg); }
            to{ transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <!-- Main Wrapper -->
    <div class="container-fluid mt-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="employers_management" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="employers_management" class="text-decoration-none text-muted">Employers</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars(ucfirst($company_name)) ?></li>
            </ol>
        </nav>

        <!-- Header Summary Card -->
        <div class="card p-4 border-0">
            <div class="row align-items-center">
                <div class="col-md-auto text-center text-md-start mb-3 mb-md-0">
                    <img src="<?= htmlspecialchars($logo_path) ?>" alt="Profile" class="profile-header-img" onerror="this.src='../assets/gggt.avif'">
                </div>
                <div class="col-md">
                    <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                        <h3 class="mb-0 fw-bold"><?= htmlspecialchars(ucfirst($company_name)) ?></h3>
                        <span class="badge badge-soft-primary rounded-pill">Employer</span>
                        <span class="badge <?= ($action === 'completed') ? 'badge-soft-success' : 'badge-soft-warning' ?> rounded-pill">
                            <?= ($action === 'completed') ? 'Verified' : 'Unverified' ?>
                        </span>
                    </div>
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar3 me-1"></i> Registered <?= $employer_joined_at ?>
                        <span class="mx-2">•</span> 
                        <i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars(ucfirst($location)) ?>
                    </p>
                </div>
                <div class="col-md-auto mt-3 mt-md-0 d-flex gap-2 flex-wrap">
                    <a href="mailto:<?= $employer_email ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-envelope me-1"></i> 
                        Send Message
                    </a>
                    <?php if ($action !== 'completed'): ?>
                        <button class="btn btn-outline-primary btn-sm"><i class="bi bi-shield-check me-1"></i> Verify</button>
                    <?php endif; ?>
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm border" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if ($employer_suspended_at === null): ?>
                                <li>
                                    <a class="dropdown-item text-warning" href="#" onclick="action_fun('suspend', <?= $employer['id'] ?> )">
                                        <i class="bi bi-slash-circle me-2"></i> Suspend Account
                                    </a>
                                </li>
                            <?php else: ?>
                                <li>
                                    <a class="dropdown-item text-success" href="#" onclick="action_fun('unsuspend', <?= $employer['id'] ?> )">
                                        <i class="bi bi-check-circle me-2"></i> Unsuspend Account
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="action_fun('delete', <?= $employer['id'] ?> )"><i class="bi bi-trash me-2"></i> Delete Account</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($employer_suspended_at !== null) { ?>
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <strong>Account Suspended!</strong> This employer account has been suspended.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-4" id="employerTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button">Overview</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="jobs-tab" data-bs-toggle="tab" data-bs-target="#jobs" type="button">Job Postings</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button">Subscriptions</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button">Security Logs</button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="employerTabsContent">
            
            <!-- Overview Tab -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <div class="row">
                    <!-- Left: Profile Info -->
                    <div class="col-lg-8">
                        <div class="card p-4">
                            <h5 class="section-title"><i class="bi bi-building text-primary"></i> Company Details</h5>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small mb-1">Company Email</label>
                                    <p class="mb-0 fw-medium"><?= htmlspecialchars($employer_email) ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small mb-1">Phone Number</label>
                                    <p class="mb-0 fw-medium"><?= htmlspecialchars($employer_tel) ?></p>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="text-muted small mb-1">Company Bio / Description</label>
                                    <p class="text-muted small"><?= nl2br(htmlspecialchars($bio)) ?></p>
                                </div>
                            </div>

                            <h5 class="section-title mt-2"><i class="bi bi-info-circle text-primary"></i> Business Information</h5>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small mb-1">Industry</label>
                                    <p class="mb-0 fw-medium"><?= htmlspecialchars(ucfirst($industry)) ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small mb-1">Company Size</label>
                                    <p class="mb-0 fw-medium"><?= htmlspecialchars($company_size) ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small mb-1">Legal Name</label>
                                    <p class="mb-0 fw-medium"><?= htmlspecialchars($legal_name) ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small mb-1">Location</label>
                                    <p class="mb-0 fw-medium"><?= htmlspecialchars($location) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Right: Stats -->
                    <div class="col-lg-4">
                        <div class="card p-4">
                            <h5 class="section-title"><i class="bi bi-link-45deg text-primary"></i> Website & Links</h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <?php if($website !== 'N/A' && !empty($website)): ?>
                                        <a href="<?= (strpos($website, 'http') === 0) ? htmlspecialchars($website) : 'https://' . htmlspecialchars($website) ?>" target="_blank">
                                            <div class="p-3 border rounded d-flex align-items-center">
                                                <i class="bi bi-globe fs-3 text-primary me-3"></i>
                                                <div class="text-truncate">
                                                    <div class="stat-label">Website</div>
                                                    <span class="small fw-medium text-dark"><?= htmlspecialchars($website) ?></span>
                                                </div>
                                            </div>
                                        </a>
                                    <?php else: ?>
                                        <div class="p-3 border rounded d-flex align-items-center bg-light text-muted">
                                            <i class="bi bi-globe fs-3 me-3"></i>
                                            <div>
                                                <div class="stat-label">Website</div>
                                                <span class="small">Not provided</span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($action === 'completed'): ?>
                                <div class="mt-4 p-3 bg-light rounded text-center">
                                    <p class="small text-muted mb-0"><i class="bi bi-shield-check text-success me-1"></i> Identity Verified</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job Postings Tab -->
            <div class="tab-pane fade" id="jobs" role="tabpanel">
                <div class="card overflow-hidden">
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Job Postings</h5>
                        <a href="job_post" class="btn btn-light btn-sm border">View All Jobs</a>
                    </div>
                    <div class="table-responsive">
                        <!-- Job posts placeholder or actual integration -->
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Job Title</th>
                                    <th>Location</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Date Posted</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // Fetch paginated job posts for the employer
                                    try {
                                        $stmt = $conn->prepare("SELECT * FROM jobs WHERE company_id = ? ORDER BY created_at DESC LIMIT 5");
                                        $stmt->bind_param("i", $employer_id_num);
                                        $stmt->execute();
                                        $result_jobs = $stmt->get_result();
                                        
                                        if ($result_jobs->num_rows < 1) {
                                            echo '<tr><td colspan="5" class="text-center text-muted">No job postings available yet.</td></tr>';
                                        } else {
                                            while ($job = $result_jobs->fetch_assoc()) {
                                                $jobDate = date("M d, Y", strtotime($job['created_at']));
                                                $jobTitle = htmlspecialchars($job['job_title'] ?? 'N/A');
                                                $jobLocation = htmlspecialchars($job['location'] ?? 'N/A');
                                                $jobType = htmlspecialchars($job['job_type'] ?? 'N/A');
                                                $jobStatus = ucfirst($job['status'] ?? 'Active');
                                                
                                                $jobBadge = ($jobStatus == 'Active') ? 'bg-success' : 'bg-secondary';
                                                
                                                echo "<tr>
                                                    <td><span class='fw-medium'>{$jobTitle}</span></td>
                                                    <td>{$jobLocation}</td>
                                                    <td>{$jobType}</td>
                                                    <td><span class='badge {$jobBadge}'>{$jobStatus}</span></td>
                                                    <td>{$jobDate}</td>
                                                </tr>";
                                            }
                                        }
                                        $stmt->close();
                                    } catch (Exception $e) {
                                        echo '<tr><td colspan="5" class="text-center text-muted">No job postings available yet.</td></tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payments / Subscriptions Tab -->
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
                                    // Fetch paginated subscriptions for the employer
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
                                        $stmt->bind_param("iii", $employer_id_num, $limit, $offset);
                                        $stmt->execute();
                                        $result = $stmt->get_result();

                                        // Fetch total count for pagination
                                        $totalStmt = $conn->prepare("SELECT COUNT(*) as total FROM subscriptions WHERE user_id = ?");
                                        $totalStmt->bind_param("i", $employer_id_num);
                                        $totalStmt->execute();
                                        $totalRecords = $totalStmt->get_result()->fetch_assoc()['total'];
                                        $totalPagesSubs = ceil($totalRecords / $limit);
                                    } catch (Exception $e) {
                                        error_log($e->getMessage());
                                        echo '<tr><td colspan="6" class="text-center text-muted">Something went wrong. Please try again later.</td></tr>';
                                    }

                                    //render
                                    if (isset($result) && $result->num_rows < 1) {
                                        echo '<tr><td colspan="6" class="text-center text-muted">No subscriptions record found.</td></tr>';
                                    } else if(isset($result)) {
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
                                                <td><span class='fw-bold text-success'>₦{$amount}</span></td>
                                                <td><span class='badge {$badgeClass}'>{$status}</span></td>
                                                <td>
                                                    <a href='./../process/process_user_subscription_invoice.php?txn={$transactionId}&token_ref=" . urlencode($_GET['token_ref']) . "&user_id={$employer_id_num}' class='text-primary'>
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
                        <?php if (isset($totalPagesSubs) && $totalPagesSubs > 1): ?>
                            <nav>
                                <ul class="pagination justify-content-center mt-3">

                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                            href="?token_ref=<?= urlencode($_GET['token_ref']) ?>&employer_id=<?= urlencode($_GET['employer_id']) ?>&page=<?= $page - 1 ?>">
                                            Previous
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $totalPagesSubs; $i++): ?>
                                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                            <a class="page-link"
                                            href="?token_ref=<?= urlencode($_GET['token_ref']) ?>&employer_id=<?= urlencode($_GET['employer_id']) ?>&page=<?= $i ?>">
                                            <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $totalPagesSubs): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                            href="?token_ref=<?= urlencode($_GET['token_ref']) ?>&employer_id=<?= urlencode($_GET['employer_id']) ?>&page=<?= $page + 1 ?>">
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
                                <!-- Mock Data matching user_review.php -->
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <!-- Full Screen Loader -->
    <div id="screenLoader" class="screen-loader d-none">
        <div class="loader-spinner"></div>
    </div>

    <!-- Bootstrap 5 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

        function showLoader(){
            document.getElementById("screenLoader").classList.remove("d-none");
        }

        function hideLoader(){
            document.getElementById("screenLoader").classList.add("d-none");
        }

        function action_fun(action, user_id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Are you sure you want to ' + action + ' this employer account?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, ' + action + ' it'
            }).then((result) => {
                if (result.isConfirmed) {

                    showLoader(); // SHOW LOADER

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

                        hideLoader(); // HIDE LOADER

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

                        hideLoader(); // HIDE LOADER
                            
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