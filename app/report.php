<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";
//get usertoken from session
$usertoken = $_SESSION['user']['usertoken'] ?? null;

// fetch user data from database
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

//user details
$user_email = $user['email'];
$user_fullname = $user['fullname'];
?>
<!DOCTYPE html>
<html lang="en">
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <head>
        <meta charset="utf-8" />
        <title>Report an issue to us | devhire - Dashboard</title>
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

        <!-- Begin page -->
        <div id="app-layout">                        

            <?php include "header.php" ?>
        
            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-18 fw-semibold m-0">Complaint page</h4>
                            </div>
                        </div>

                        <!-- General Form -->
                        <div class="row">
                            <div class="col">
                                <div class="card">

                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Report details</h5>
                                    </div>

                                    <div class="card-body">
                                        <form>
                                            <div class="mb-3">
                                                <label for="example-input-large" class="form-label">Fullname</label>
                                                <input type="text" id="example-input-large" name="fullname" class="form-control name form-control-lg" value="<?php echo htmlspecialchars($user_fullname); ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label for="example-input-large" class="form-label">Email</label>
                                                <input type="email" id="example-input-large" name="email" class="form-control email form-control-lg" value="<?php echo htmlspecialchars($user_email); ?>" readonly>
                                            </div>
                                            <!-- Report Title Dropdown -->
                                            <div class="mb-3">
                                                <label for="report-title" class="form-label">Report Title</label>
                                                <select id="report-title" name="reportTitle" class="form-control form-control-lg reportTitle">
                                                    <option value="">-- Select a Report Title --</option>
                                                    <option value="System Bug">System Bug</option>
                                                    <option value="Payment Issue">Payment Issue</option>
                                                    <option value="Login Problem">Login Problem</option>
                                                    <option value="Feature Request">Feature Request</option>
                                                    <option value="Account Suspended">Account Suspended</option>
                                                    <option value="UI/UX Feedback">UI/UX Feedback</option>
                                                    <option value="Performance Issue">Performance Issue</option>
                                                    <option value="Security Concern">Security Concern</option>
                                                    <option value="Data Error">Data Error</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="example-textarea" class="form-label">Report Data</label>
                                                <textarea class="form-control reportData" id="example-textarea" placeholder="Write your report here...." rows="6" spellcheck="false"></textarea>
                                                <!-- <span class="text-secondary">Brief your report for your profile.</span> -->
                                            </div> 
                                            <div>
                                                <span class="btn btn-primary" id="sendreportBtn">Send Report</span>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="card">

                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Your Reports Records</h5>
                                    </div>

                                    <div class="card-body">
                                        <?php
                                            function reportrecords(){
                                                global $conn;
                                                global $user_id;
                                                try{
                                                    $stmt = $conn->prepare("SELECT id, report_title, created_at FROM `reports` WHERE user_id = ? AND deleted_at IS NULL ");
                                                    $stmt->bind_param("i", $user_id);
                                                    if($stmt->execute()){
                                                        $result = $stmt->get_result();
                                                        while($reportData = $result->fetch_assoc()){
                                        ?>
                                                            <!-- File Item -->
                                                            <div class="d-flex justify-content-between align-items-center p-3 mb-2 bg-light rounded">
                                                                <?php
                                                                    $rawDate = $reportData['created_at'];
                                                                    // Create DateTime object
                                                                    $date = new DateTime($rawDate);
                                                                ?>
                                                                <span> <b class="text-primary"> <?= $reportData['report_title']; ?> </b> - Reported_at:: <?= $date->format("l, F jS Y g:i:s A"); ?></span>
                                                                <button type="button" class="btn btn-sm btn-link text-danger p-0" value="<?= $reportData['id']; ?>"  onclick="deleteReport(this.value)" title="Delete Report" aria-label="Delete Report">
                                                                    <i class="mdi mdi-close fs-5 align-middle"></i>
                                                                </button>
                                                            </div>
                                        <?php
                                                        }
                                                    }else{
                                                        echo "Error getting data.";
                                                    }
                                                    $stmt->close();
                                                } catch (Exception $e){
                                                    echo "Server error: " . $e->getMessage();
                                                } finally{
                                                    $conn->close();
                                                }
                                            }
                                            reportrecords();
                                        ?>
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

        <!-- App js-->
        <script src="<?php echo $base_url; ?>assets/js/app.js"></script>

        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.querySelector("#sendreportBtn").addEventListener("click", (evt) => {
                evt.preventDefault();

                //get users input
                const name = document.querySelector(".name").value;
                const email = document.querySelector(".email").value;
                const reportData = document.querySelector(".reportData").value.trim();
                const reportTitle = document.querySelector(".reportTitle").value;

                //validate user inputs
                if(!name || !email || !reportData || !reportTitle){
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'All fields are required.',
                        showConfirmButton: false,
                        timer: 2500
                    });
                    return;
                }

                if(reportData.length > 1000){
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Report cannot exceed 1000 characters.',
                        showConfirmButton: false,
                        timer: 2500
                    });
                    return;
                }

                //create data form obj
                const formData = new FormData();
                formData.append('usertoken', <?php echo json_encode($usertoken); ?>);
                formData.append("name", name);
                formData.append("email", email);
                formData.append("reportTitle", reportTitle);
                formData.append("reportData", reportData);

                //send data to server
                fetch("<?php echo $base_url; ?>process/process_report_issues.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status === "success"){
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 2500
                        });
                        setTimeout(() => location.reload(), 2600);
                    }else{
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 2500
                        });
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'An error occurred while processing your request. Please try again later.',
                        showConfirmButton: false,
                        timer: 2500
                    });
                    return;
                })
            })

            //delete user report record
            function deleteReport(data){
                // alert(data);
                // Show confirmation dialog
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to delete this report record? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return; // User cancelled the action
                    }
                    // Continue with deletion below
                    const formData = new FormData();
                    formData.append('usertoken', <?php echo json_encode($usertoken); ?>);
                    formData.append('recordID', data);

                    fetch('<?php echo $base_url; ?>process/process_delete_user_report.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 2500
                            });
                            setTimeout(() => location.reload(), 2600);
                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 2500
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'An error occurred while processing your request. Please try again later.', 'error');
                    });
                });
            }
        </script>
        
    </body>

</html>