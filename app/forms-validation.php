<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";

// fetch user data from database
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

//fetch user phone number from developers_profiles table
$stmt = $conn->prepare("SELECT phone_number FROM developers_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

//user details
$user_email = $user['email'];
$user_fullname = $user['fullname'];
$user_phone = $profile['phone_number'];
?>
<!DOCTYPE html>
<html lang="en">
    
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>

        <meta charset="utf-8" />
        <title>Account Settings | Devhire - Dashboard</title>
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
                                <h4 class="fs-18 fw-semibold m-0">Account Settings</h4>
                            </div>
                        </div>

                        <!-- Account Settings -->
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Edit & Update</h5>
                                    </div><!-- end card header -->
        
                                    <div class="card-body">
                                        <form class="row g-3">
                                            <div class="col-md-6">
                                                <label for="validationDefault01" class="form-label">First name</label>
                                                <input type="text" class="form-control fullname" value="<?= $user_fullname; ?>" id="validationDefault01">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="validationDefault02" class="form-label">Phone Number</label>
                                                <input type="text" class="form-control phone" value="<?= $user_phone; ?>" id="validationDefault02">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="validationDefault03" class="form-label">Email</label>
                                                <input type="email" value="<?= $user_email; ?>" disabled class="form-control" id="validationDefault03" required>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="validationDefault05" class="form-label">Password</label>
                                                <input type="password" class="form-control currentpassword" placeholder="*********" id="validationDefault05" required>
                                            </div>
                                            <div class="col-12">
                                                <button class="btn btn-primary" id="userdataform" type="submit">Save Data</button>
                                            </div>
                                        </form>
                                    </div> <!-- end card-body -->
                                </div> <!-- end card-->
                            </div> <!-- end col -->

                            <!-- Change Password -->
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Change Password</h5>
                                    </div><!-- end card header -->
        
                                    <div class="card-body">
                                        <form class="row g-3 needs-validation" novalidate>
                                            <div class="col-md-12">
                                                <label for="validationCustom01" class="form-label">Old Password <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control oldpassword" id="validationCustom01" required>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="validationCustom02" class="form-label">New Password <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control newpassword" id="validationCustom02" required>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="validationCustom03" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control confirmpassword" id="validationCustom03" required>
                                            </div>
                                            <div class="col-12">
                                                <button class="btn btn-primary" id="resetpasswordForm" type="submit">Update New Password</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div> <!-- content -->

                <!-- Footer Start -->
                <?php include_once "footer.php"; ?>
                <!-- end Footer -->

            </div>
        </div>
        <!-- END wrapper -->





        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        <script>
            document.querySelector('#resetpasswordForm').addEventListener('click', function(event){
                event.preventDefault(); // Prevent form submission

                // Get input values
                const oldpassword = document.querySelector('.oldpassword').value;
                const newpassword = document.querySelector('.newpassword').value;
                const confirmpassword = document.querySelector('.confirmpassword').value;

                // Basic validation
                if(!oldpassword || !newpassword || !confirmpassword){
                    Swal.fire({
                        toast: true,
                        icon: 'error',
                        title: 'All fields are required.',
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    return;
                }
                if(newpassword !== confirmpassword){
                    Swal.fire({
                        toast: true,
                        icon: 'error',
                        title: 'Password do not match.',
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    return;
                }
                const formData = new FormData();
                formData.append('oldpassword', oldpassword);
                formData.append('newpassword', newpassword);
                formData.append('confirmpassword', confirmpassword);

                // Send data to server
                fetch('<?php echo $base_url; ?>process/process_password_reset.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success'){
                        Swal.fire({
                            toast: true,
                            icon: 'success',
                            title: data.message,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        // Clear input fields
                        document.querySelector('.oldpassword').value = '';
                        document.querySelector('.newpassword').value = '';
                        document.querySelector('.confirmpassword').value = '';
                    } else {
                        Swal.fire({
                            toast: true,
                            icon: 'error',
                            title: data.message,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                })
                .catch(error => {
                    console.log('Error:', error);
                    Swal.fire({
                        toast: true,
                        icon: 'error',
                        title: 'An error occurred. Please try again.',
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
            });


            document.querySelector('#userdataform').addEventListener('click', function(event){
                event.preventDefault(); // Prevent form submission

                // Get input values
                const fullname = document.querySelector('.fullname').value;
                const phone = document.querySelector('.phone').value;
                const currentpassword = document.querySelector('.currentpassword').value;

                // Basic validation
                if(!fullname || !phone || !currentpassword){
                    Swal.fire({
                        toast: true,
                        icon: 'error',
                        title: 'All fields are required.',
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    return;
                }
                const formData = new FormData();
                formData.append('fullname', fullname);
                formData.append('phone', phone);
                formData.append('currentpassword', currentpassword);

                // Send data to server
                fetch('<?php echo $base_url; ?>process/process_user_data.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success'){
                        Swal.fire({
                            toast: true,
                            icon: 'success',
                            title: data.message,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        // Clear input fields
                        document.querySelector('.oldpassword').value = '';
                        document.querySelector('.newpassword').value = '';
                        document.querySelector('.confirmpassword').value = '';
                    } else {
                        Swal.fire({
                            toast: true,
                            icon: 'error',
                            title: data.message,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                })
                .catch(error => {
                    console.log('Error:', error);
                    Swal.fire({
                        toast: true,
                        icon: 'error',
                        title: 'An error occurred. Please try again.',
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
            });
            
        </script>
        
    </body>

</html>