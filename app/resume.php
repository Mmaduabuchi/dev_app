<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";
//get usertoken from session
$usertoken = $_SESSION['user']['usertoken'] ?? null;

// fetch user data from database
try {
    //Fetch user data
    $stmt = $conn->prepare("SELECT email, fullname FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        throw new Exception("User not found.");
    }

    //Fetch user resume
    $stmt = $conn->prepare("SELECT resume FROM developers_profiles WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $resume = $result->fetch_assoc();

    $user_resume = $resume['resume'] ?? null;

    //User details
    $user_email = $user['email'];
    $user_fullname = $user['fullname'];

} catch (Exception $e) {
    // handle error gracefully
    die("Error: " . $e->getMessage());
} finally {
    //End connection
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <head>
        <meta charset="utf-8" />
        <title>Resume | devhire - Dashboard</title>
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
                                <h4 class="fs-18 fw-semibold m-0">My Resume</h4>
                            </div>
                        </div>

                        <!-- General Form -->
                        <div class="row">
                            <div class="col">
                                <div class="card">

                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Resume Attachment</h5>
                                    </div>

                                    <div class="card-body">
                                        <form>
                                            <div class="mb-3">
                                                <?php
                                                    if($user_resume) {
                                                ?>
                                                    <!-- File Item -->
                                                    <div class="d-flex justify-content-between align-items-center p-3 mb-2 bg-light rounded">
                                                        <span><?= $user_resume; ?></span>
                                                        <button type="button" class="btn btn-sm btn-link text-danger p-0" value="<?php echo $usertoken; ?>"  onclick="deleteResume(this.value)" title="Delete Resume" aria-label="Delete Resume">
                                                            <i class="mdi mdi-close fs-5 align-middle"></i>
                                                        </button>
                                                    </div>
                                                <?php
                                                    }else{
                                                        echo "<p class='bg-light rounded p-3 mb-2'>No resume uploaded yet.</p>";
                                                    }
                                                ?>
                                            </div>
                                            <div class="mb-3">
                                                <label for="formFile" class="form-label">Resume</label>
                                                <input class="form-control resume" type="file" id="formFile">
                                            </div>
                                            <div>
                                                <span class="btn btn-primary" id="uploadResumeBtn">Upload Resume</span>
                                                <span>Upload file .pdf, .doc, .docx</span>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card">

                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Education</h5>
                                    </div>

                                    <div class="card-body">
                                        <form>
                                            <div class="mb-3">
                                                <label for="courseSelect" class="form-label">Course</label>
                                                <select class="form-select" id="courseSelect" name="course">
                                                    <option value="">-- Select a Course --</option>
                                                    <option value="computer_science">Computer Science</option>
                                                    <option value="computer_science_education">Computer Science Education</option>
                                                    <option value="Computer_engineering">Computer Engineering</option>
                                                    <option value="software_engineering">Software Engineering</option>
                                                    <option value="information_technology">Information Technology</option>
                                                    <option value="cybersecurity">Cybersecurity</option>
                                                    <option value="data_science">Data Science</option>
                                                    <option value="electrical_engineering">Electrical Engineering</option>
                                                    <option value="mechanical_engineering">Mechanical Engineering</option>
                                                    <option value="civil_engineering">Civil Engineering</option>
                                                    <option value="business_admin">Business Administration</option>
                                                    <option value="economics">Economics</option>
                                                    <option value="accounting">Accounting</option>
                                                    <option value="law">Law</option>
                                                    <option value="medicine">Medicine</option>
                                                    <option value="nursing">Nursing</option>
                                                    <option value="pharmacy">Pharmacy</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="simpleinput" class="form-label">Title</label>
                                                <!-- <input type="text" id="simpleinput" class="form-control" placeholder="Bachelor of Science (B.Sc.) in Computer Science"> -->
                                                <select id="degreeTitle" class="form-select">
                                                <option value="">-- Select Degree/Certificate --</option>
                                                <option value="bsc">Bachelor of Science (B.Sc.)</option>
                                                <option value="ba">Bachelor of Arts (B.A.)</option>
                                                <option value="beng">Bachelor of Engineering (B.Eng.)</option>
                                                <option value="llb">Bachelor of Laws (LL.B)</option>
                                                <option value="mbbs">Bachelor of Medicine, Bachelor of Surgery (MBBS)</option>
                                                <option value="msc">Master of Science (M.Sc.)</option>
                                                <option value="ma">Master of Arts (M.A.)</option>
                                                <option value="mba">Master of Business Administration (MBA)</option>
                                                <option value="mphil">Master of Philosophy (M.Phil.)</option>
                                                <option value="phd">Doctor of Philosophy (PhD)</option>
                                                <option value="diploma">Diploma</option>
                                                <option value="certificate">Certificate</option>
                                            </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="simpleinput" class="form-label">Academy</label>
                                                <input type="text" id="simpleinput" class="form-control academy" placeholder="Google Arts College & University">
                                            </div>
                                            <div class="mb-3">
                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <label for="simpleinput" class="form-label">Year</label>
                                                    </div>
                                                    <div class="col-md">
                                                        <div class="form-floating mb-3">
                                                            <select class="form-select startyear" id="floatingSelectGrid">
                                                                <?php
                                                                    $currentYear = date("Y");
                                                                    $startYear = $currentYear - 50;
                                                                    for ($year = $startYear; $year <= $currentYear; $year++) {
                                                                        echo "<option value='{$year}'>{$year}</option>";
                                                                    }
                                                                ?>
                                                            </select>
                                                            <label for="floatingInputGrid">Start year</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md">
                                                        <div class="form-floating mb-3">
                                                            <select class="form-select endyear" id="floatingSelectGrid">
                                                                <?php
                                                                    $currentYear = date("Y");
                                                                    $startYear = $currentYear - 50;
                                                                    for ($year = $startYear; $year <= $currentYear; $year++) {
                                                                        $selected = ($year == $currentYear) ? "selected" : "";
                                                                        echo "<option value='{$year}' {$selected}>{$year}</option>";
                                                                    }
                                                                ?>
                                                            </select>
                                                            <label for="floatingSelectGrid">End year</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="example-textarea" class="form-label">Description</label>
                                                <textarea class="form-control description" id="example-textarea" placeholder="Brief your education for your profile." rows="6" spellcheck="false"></textarea>
                                            </div>
                                            <div>
                                                <span class="btn btn-primary" id="updateEducationalDataBtn">Save Data</span>
                                            </div>
                                        </form>
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
            // Upload Resume
            document.getElementById('uploadResumeBtn').addEventListener('click', function() {
                const fileInput = document.querySelector('.resume');
                const file = fileInput.files[0];
                if (!file) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Please select a file to upload.',
                        showConfirmButton: false,
                        timer: 2500
                    });
                    return;
                }
                const allowedTypes = ['application/pdf', 'application/x-pdf' , 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!allowedTypes.includes(file.type)) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Invalid file type. Only PDF, DOC, and DOCX are allowed.',
                        showConfirmButton: false,
                        timer: 2500
                    });
                    return;
                }
                if (file.size > 5 * 1024 * 1024) { // 5MB limit
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'File size exceeds 2MB limit.',
                        showConfirmButton: false,
                        timer: 2500
                    });
                    return;
                }
                const formData = new FormData();
                formData.append('resume', file);
                formData.append('usertoken', '<?php echo $usertoken; ?>');
                fetch('<?php echo $base_url; ?>process/process_upload_resume.php', {
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
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'An error occurred while processing your request. Please try again later.',
                        showConfirmButton: false,
                        timer: 2500
                    });
                });
            });

            //delete resume file
            function deleteResume(dataToken) {
                // Show confirmation dialog
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to delete resume document?',
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
                    formData.append('usertoken', dataToken);

                    fetch('<?php echo $base_url; ?>process/process_delete_resume.php', {
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
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'An error occurred while processing your request. Please try again later.',
                            showConfirmButton: false,
                            timer: 2500
                        });
                    });
                });
            }


            //add educational data
            document.querySelector("#updateEducationalDataBtn").addEventListener("click", (event)=>{
                event.preventDefault();
                //get users inputs
                const courseSelect = document.querySelector("#courseSelect").value.trim();
                const degreeTitle = document.querySelector("#degreeTitle").value.trim();
                const academy = document.querySelector(".academy").value.trim();
                const startyear = document.querySelector(".startyear").value.trim();
                const endyear = document.querySelector(".endyear").value.trim();
                const description = document.querySelector(".description").value.trim();

                //validate inputs
                if(!courseSelect || !degreeTitle || !academy || !startyear || !endyear || !description){
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

                //validate description word length (max 800 characters)
                if(description.length > 800){
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Description cannot exceed 800 characters.',
                        showConfirmButton: false,
                        timer: 2500
                    });
                    return;
                }
                //create form object data
                const formData = new FormData();
                formData.append('usertoken', '<?php echo $usertoken; ?>');
                formData.append("course", courseSelect);
                formData.append("degree", degreeTitle);
                formData.append("academy", academy);
                formData.append("startyear", startyear);
                formData.append("endyear", endyear);
                formData.append("description", description);

                fetch("<?php echo $base_url; ?>process/process_education_data.php", {
                    method: 'POST',
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
                .catch(err => {
                    console.error('Error:', error);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'An error occurred while processing your request. Please try again later.',
                        showConfirmButton: false,
                        timer: 2500
                    });
                });
            })
        </script>
        
    </body>

</html>