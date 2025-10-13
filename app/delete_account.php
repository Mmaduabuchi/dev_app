<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";

//get usertoken from session
$usertoken = $_SESSION['user']['usertoken'] ?? null;
if ($usertoken === null) {
    //if null redirect to login page
    header("Location: /devhire/login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <head>
        <meta charset="utf-8" />
        <title>Delete Account | devhire - Dashboard</title>
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
                                <h4 class="fs-18 fw-semibold m-0">Account Deletion & Deactivation</h4>
                            </div>
                        </div>

                        <!-- General Form -->
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card">

                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Delete my account</h5>
                                    </div><!-- end card header -->

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <p class="mt-3">
                                                    Deleting your account is a <strong>permanent action</strong>. Once you delete your account:
                                                </p>
                                                 <ul>
                                                    <li>All your personal information and profile data will be permanently removed.</li>
                                                    <li>You will lose access to your projects, files, and any saved preferences.</li>
                                                    <li>This action <strong>cannot be undone</strong>.</li>
                                                </ul>
                                                <p class="mt-3">
                                                    If you are sure you want to proceed, please click the "Delete Account" button below.
                                                </p>
                                                <button type="button" class="btn btn-danger" value="<?= $usertoken; ?>" onclick="deleteAccount(this.value)">
                                                    <i class="mdi mdi-delete-outline align-middle"></i> Delete Account
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card">

                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Deactivate my account</h5>
                                    </div>
                                    
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                            <p class="mt-3">
                                                Deactivating your account will <strong>temporarily disable</strong> your profile and restrict access until you reactivate it.
                                            </p>
                                            <ul>
                                                <li>Your profile and personal information will be hidden from other users.</li>
                                                <li>You will not be able to access your projects, files, or saved preferences while deactivated.</li>
                                                <li>You can reactivate your account anytime by contacting the support team.</li>
                                            </ul>
                                            <p class="mt-3">
                                                If you are sure you want to proceed, please click the "Deactivate Account" button below.
                                            </p>
                                            <button type="button" class="btn btn-danger" value="<?= $usertoken; ?>" onclick="Deactivate(this.value)">
                                                <i class="mdi mdi-account-off-outline align-middle"></i> Deactivate Account
                                            </button>
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

        <!-- App js-->
        <script src="<?php echo $base_url; ?>assets/js/app.js"></script>

        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function deleteAccount(dataToken) {
                // Show confirmation dialog
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you really want to delete your account? This action cannot be undone.',
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

                    fetch('<?php echo $base_url; ?>process/process_delete_account.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('Deleted!', data.message, 'success').then(() => {
                                window.location.href = '/devhire/login';
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'An error occurred while processing your request. Please try again later.', 'error');
                    });
                });
            }

            function Deactivate(dataToken) {
                // Show confirmation dialog
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you really want to deactivate your account? You can reactivate it later by contacting support.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, deactivate it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return; // User cancelled the action
                    }
                    // Continue with deactivation below
                    const formData = new FormData();
                    formData.append('usertoken', dataToken);

                    fetch('<?php echo $base_url; ?>process/process_deactivate_account.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('Deactivated!', data.message, 'success').then(() => {
                                window.location.href = '/devhire/login';
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
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