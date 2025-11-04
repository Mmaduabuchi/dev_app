<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";
//notification count
require_once __DIR__ . '/fetch_notification_count.php';
//get usertoken from session
$usertoken = $_SESSION['user']['usertoken'] ?? null;

// fetch user data from database
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

//fetch user phone number from developers_profiles table
$stmt = $conn->prepare("SELECT * FROM developers_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

//user details
$user_email = $user['email'];
$user_fullname = $user['fullname'];
$user_phone = $user['tel'] ?? 'NAN';
$created_at = $user['created_at'];

//more user details
$profile_pic = "/devhire/" . $profile['profile_picture'];
$user_bio = $profile['bio'];
$user_legal_name = $profile['legal_name'];
$user_location = $profile['location'];
$user_github = empty($profile['github']) ? 'Not Specified' : $profile['github'];
$user_website = empty($profile['website']) ? 'Not Specified' : $profile['website'];
$user_linkedin = empty($profile['linkedin']) ? 'Not Specified' : $profile['linkedin'];

?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title>Profile | devhire - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo $base_url; ?>assets/images/favicon.ico">

    <!-- App css -->
    <link href="<?php echo $base_url; ?>assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons -->
    <link href="<?php echo $base_url; ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <script src="<?php echo $base_url; ?>assets/js/head.js"></script>

    <style>
        #img_candidate {
            width: 80px;
            height: 80px;
            border-radius: 20%;
            object-fit: cover;
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
                            <h4 class="fs-18 fw-semibold m-0">Company</h4>
                        </div>
                    </div>

                    <!-- General Form -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">

                                <div class="card-header">
                                    <h5 class="card-title mb-0">My Company Details</h5>
                                </div><!-- end card header -->

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <form>
                                                <div class="mb-3">
                                                    <label for="example-email" class="form-label">Company Email</label>
                                                    <input type="email" readonly id="example-email" name="example-email" class="form-control" value="<?= $user_email; ?>">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="example-password" class="form-label">Registeration Date</label>
                                                    <input type="text" readonly id="example-password" class="form-control" value="<?= $created_at; ?>">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="example-palaceholder" class="form-label">Company Contact Number</label>
                                                    <input type="text" id="example-palaceholder" readonly class="form-control" value="<?= $user_phone; ?>">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="example-textarea" class="form-label">Company Bio</label>
                                                    <textarea class="form-control" id="example-textarea" placeholder="Write something interesting about your company...." rows="5" spellcheck="false"><?= htmlspecialchars($user_bio); ?></textarea>
                                                    <span class="text-secondary">Brief description for your company.</span>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">

                                <div class="card-header">
                                    <h5 class="card-title mb-0">Company Website</h5>
                                </div><!-- end card header -->

                                <div class="card-body">
                                    <form method="post" action="" id="add_website_form">
                                        <div class="mb-3">
                                            <label for="example-input-normal" class="form-label">Website</label>
                                            <input type="text" id="example-input-normal" name="website" class="form-control" placeholder="e.g., https://websitename.com/">
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-primary">Add Website</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">

                                <div class="card-header">
                                    <h5 class="card-title mb-0">Update Company Logo</h5>
                                </div><!-- end card header -->

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <label for="profile_picture">Upload company logo picture (JPG, PNG, WebP allowed)</label>
                                            <input type="file" id="profile_picture" class="form-control">
                                            <br>
                                            <button class="btn btn-primary w-25" id="btn_profile_picture">Update</button>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-6 text-md-center text-start mt-3 mt-md-0">
                                            <img id="img_candidate" class="img-fluid shadow-sm" src="<?php echo $profile_pic; ?>" alt="Company logo Image">
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
        const profilePictureInput = document.getElementById('profile_picture');
        const btnProfilePicture = document.getElementById('btn_profile_picture');

        //validate and preview profile picture before upload
        profilePictureInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const validImageTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (validImageTypes.includes(file.type)) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('img_candidate').src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                } else {
                    if (typeof Swal !== 'undefined') {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3500,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'error',
                            title: 'Invalid file type. Please select a JPG, PNG, or WebP image.'
                        });
                    } else {
                        alert('Invalid file type. Please select a JPG, PNG, or WebP image.');
                    }
                    profilePictureInput.value = '';
                }
            }
        });

        btnProfilePicture.addEventListener('click', function(e) {
            e.preventDefault();
            const file = profilePictureInput.files[0];
            if (!file) {
                if (typeof Swal !== 'undefined') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'warning',
                        title: 'Please select a profile picture to upload.'
                    });
                } else {
                    alert('Please select a profile picture to upload.');
                }
                return;
            }

            // Proceed with uploading the profile picture
            console.log('Uploading profile picture:', file);
            const formData = new FormData();
            formData.append('profile_picture', file);
            formData.append('token', '<?php echo $usertoken; ?>');
            fetch('<?php echo $base_url; ?>process/process_update_profile_picture.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        if (typeof Swal !== 'undefined') {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3500,
                                timerProgressBar: true
                            });
                            Toast.fire({
                                icon: 'success',
                                title: 'Profile picture updated successfully.'
                            });
                            //reload page after 2 seconds
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            alert('Profile picture updated successfully.');
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3500,
                                timerProgressBar: true
                            });
                            Toast.fire({
                                icon: 'error',
                                title: data.message || 'Failed to update profile picture.'
                            });
                        } else {
                            alert(data.message || 'Failed to update profile picture.');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof Swal !== 'undefined') {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3500,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'error',
                            title: 'An error occurred while uploading the profile picture.'
                        });
                    } else {
                        alert('An error occurred while uploading the profile picture.');
                    }
                });
        });


        document.querySelector("#add_website_form").addEventListener("submit", function(e) {
            e.preventDefault();
            const website = document.querySelector("input[name='website']").value;

            if (!website) {
                if (typeof Swal !== 'undefined') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'warning',
                        title: 'Please enter your website URL.'
                    });
                } else {
                    alert('Please enter your website URL.');
                }
                return;
            }

            const formData = new FormData();
            formData.append('website', website);
            formData.append('token', '<?php echo $usertoken; ?>');

            fetch('<?php echo $base_url; ?>process/process_add_website.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        if (typeof Swal !== 'undefined') {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3500,
                                timerProgressBar: true
                            });
                            Toast.fire({
                                icon: 'success',
                                title: data.message || 'Website URL updated successfully.'
                            });
                            //reload page after 2 seconds
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            alert('Website URL updated successfully.');
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3500,
                                timerProgressBar: true
                            });
                            Toast.fire({
                                icon: 'error',
                                title: data.message || 'Failed to update your website URL.'
                            });
                        } else {
                            alert(data.message || 'Failed to update your website URL.');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof Swal !== 'undefined') {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3500,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'error',
                            title: 'An error occurred while updating your website URL.'
                        });
                    } else {
                        alert('An error occurred while updating your website URL.');
                    }
                });

        });
    </script>

</body>

</html>