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

//fetch user phone number from developers_profiles table
$stmt = $conn->prepare("SELECT * FROM developers_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

//user details
$user_email = $user['email'];
$user_fullname = $user['fullname'];
$created_at = $user['created_at'];

//more user details
$user_phone = $profile['phone_number'];
$profile_pic = "/devhire/" . $profile['profile_picture'];
$user_bio = $profile['bio'];
$user_legal_name = $profile['legal_name'];
$user_location = $profile['location'];
$user_github = empty($profile['github']) ? 'Not Specified' : $profile['github'];
$user_website = empty($profile['website']) ? 'Not Specified' : $profile['website'];
$user_linkedin = empty($profile['linkedin']) ? 'Not Specified' : $profile['linkedin'];
$user_education_level = $profile['education_level'];
$user_citizenship = $profile['citizenship'];
$user_english_proficiency = $profile['english_proficiency'];
$user_years_of_experience = $profile['years_of_experience'];
$user_primary_job_interest = $profile['primary_job_interest'];
$user_industry_experience = $profile['industry_experience'];
$user_certifications =  empty($profile['certifications']) ? 'Not Specified' : $profile['certifications'];
$user_job_commitment = $profile['job_commitment'];
$user_preferred_hourly_rate = $profile['preferred_hourly_rate'];

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
                            <h4 class="fs-18 fw-semibold m-0">Profile</h4>
                        </div>
                    </div>

                    <!-- General Form -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">

                                <div class="card-header">
                                    <h5 class="card-title mb-0">My Profile Details</h5>
                                </div><!-- end card header -->

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <form>
                                                <div class="mb-3">
                                                    <label for="simpleinput" class="form-label">Legal Name</label>
                                                    <input type="text" id="simpleinput" class="form-control" value="<?= $user_legal_name; ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="example-email" class="form-label">Email</label>
                                                    <input type="email" readonly id="example-email" name="example-email" class="form-control" value="<?= $user_email; ?>">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="example-password" class="form-label">Registeration Date</label>
                                                    <input type="text" readonly id="example-password" class="form-control" value="<?= $created_at; ?>">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="example-palaceholder" class="form-label">Phone Number</label>
                                                    <input type="text" id="example-palaceholder" readonly class="form-control" value="<?= $user_phone; ?>">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="example-textarea" class="form-label">Bio</label>
                                                    <textarea class="form-control" id="example-textarea" placeholder="Write something interesting about you...." rows="5" spellcheck="false"><?= htmlspecialchars($user_bio); ?></textarea>
                                                    <span class="text-secondary">Brief description for your profile.</span>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="example-disable" class="form-label">Website</label>
                                                    <input class="form-control" type="text" value="<?= $user_website; ?>" readonly aria-label="readonly input example">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="example-disable" class="form-label">Linkedln</label>
                                                    <input class="form-control" type="text" value="<?= $user_linkedin; ?>" readonly aria-label="readonly input example">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="example-disable" class="form-label">Github</label>
                                                    <input class="form-control" type="text" value="<?= $user_github; ?>" readonly aria-label="readonly input example">
                                                </div>

                                            </form>
                                        </div>

                                        <div class="col-lg-6">
                                            <form>
                                                <div class="mb-3">
                                                    <label for="example-select" class="form-label">Citizenship</label>
                                                    <select class="form-select" id="example-select">
                                                        <option><?= $user_citizenship; ?></option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="simpleinput" class="form-label">English Proficiency</label>
                                                    <select id="simpleinput" class="form-select" aria-label="Default select example">
                                                        <option value="1"><?= ucfirst($user_english_proficiency); ?></option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="simpleinput" class="form-label">Education Level</label>
                                                    <select id="simpleinput" class="form-select" aria-label="Default select example">
                                                        <option value="1"><?= ucfirst($user_education_level); ?></option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="simpleinput" class="form-label">Years of Professional Experience </label>
                                                    <select id="simpleinput" class="form-select" aria-label="Default select example">
                                                        <option value="1"><?= $user_years_of_experience; ?> Years</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="simpleinput" class="form-label">Primary Job Interest</label>
                                                    <select id="simpleinput" class="form-select" aria-label="Default select example">
                                                        <option value="1"><?= ucfirst($user_primary_job_interest); ?></option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="simpleinput" class="form-label">Industry Experience</label>
                                                    <select id="simpleinput" class="form-select" aria-label="Default select example">
                                                        <option value="1"><?= ucfirst($user_industry_experience); ?></option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="simpleinput" class="form-label">Certifications</label>
                                                    <select id="simpleinput" class="form-select" aria-label="Default select example">
                                                        <option value="1"><?= ucfirst($user_certifications); ?></option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="simpleinput" class="form-label">Job Commitment</label>
                                                    <select id="simpleinput" class="form-select" aria-label="Default select example">
                                                        <option value="1"><?= ucfirst($user_job_commitment); ?></option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="example-disable" class="form-label">Preferred Hourly Rate</label>
                                                    <input class="form-control" type="text" value="<?= $user_preferred_hourly_rate . ' USD'; ?>" readonly aria-label="readonly input example">
                                                </div>

                                                <!-- <div>
                                                        <label for="exampleDataList" class="form-label">Datalist example</label>
                                                        <input class="form-control" list="datalistOptions" id="exampleDataList" placeholder="Type to search...">
                                                        <datalist id="datalistOptions">
                                                            <option value="San Francisco">
                                                            <option value="New York">
                                                            <option value="Seattle">
                                                            <option value="Los Angeles">
                                                            <option value="Chicago">
                                                        </datalist>
                                                    </div> -->

                                            </form>
                                        </div>
                                        <div class="col-12">
                                            <!-- <button> -->
                                            <span class="btn btn-primary">Export CSV</span>
                                            <!-- </button> -->
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
                                    <h5 class="card-title mb-0">Social Media</h5>
                                </div><!-- end card header -->

                                <div class="card-body">
                                    <form method="post" action="" id="social_media_form">
                                        <div class="mb-3">
                                            <label for="example-input-large" class="form-label">Github</label>
                                            <input type="text" id="example-input-large" name="Github" class="form-control form-control-lg" placeholder="e.g., https://github.com/username">
                                        </div>
                                        <div class="mb-3">
                                            <label for="example-input-normal" class="form-label">linkedin</label>
                                            <input type="text" id="example-input-normal" name="linkedin" class="form-control" placeholder="e.g., https://linkedin.com/in/yourprofile">
                                        </div>
                                        <div>
                                            <button class="btn btn-primary" type="submit">Save</button>
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
                                    <h5 class="card-title mb-0">Website</h5>
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
                                    <h5 class="card-title mb-0">Update Profile Picture</h5>
                                </div><!-- end card header -->

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-6">
                                            <label for="profile_picture">Upload your profile picture (JPG, PNG, WebP allowed)</label>
                                            <input type="file" id="profile_picture" class="form-control">
                                            <br>
                                            <button class="btn btn-primary w-25" id="btn_profile_picture">Update</button>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-6 text-md-center text-start mt-3 mt-md-0">
                                            <img id="img_candidate" class="img-fluid shadow-sm" src="<?php echo $profile_pic; ?>" alt="User Image">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Input Group</h5>
                                </div><!-- end card header -->

                                <div class="card-body">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">@</span>
                                        <input type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
                                    </div>

                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                        <span class="input-group-text" id="basic-addon2">@example.com</span>
                                    </div>

                                    <div class="mb-3">
                                        <label for="basic-url" class="form-label">Your vanity URL</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon3">https://example.com/users/</span>
                                            <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3 basic-addon4">
                                        </div>
                                        <div class="form-text" id="basic-addon4">Example help text goes outside the input group.</div>
                                    </div>

                                    <div class="input-group mb-3">
                                        <span class="input-group-text">$</span>
                                        <input type="text" class="form-control" aria-label="Amount (to the nearest dollar)">
                                        <span class="input-group-text">.00</span>
                                    </div>

                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Username" aria-label="Username">
                                        <span class="input-group-text">@</span>
                                        <input type="text" class="form-control" placeholder="Server" aria-label="Server">
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-text">With textarea</span>
                                        <textarea class="form-control" aria-label="With textarea"></textarea>
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


        document.querySelector("#social_media_form").addEventListener("submit", function(e) {
            e.preventDefault();
            const github = document.querySelector("input[name='Github']").value;
            const linkedin = document.querySelector("input[name='linkedin']").value;

            //validate inputs
            if (!github || !linkedin) {
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
                        title: 'Please fill in all fields.'
                    });
                } else {
                    alert('Please fill in all fields.');
                }
                return;
            }

            const formData = new FormData();
            formData.append('github', github);
            formData.append('linkedin', linkedin);
            formData.append('token', '<?php echo $usertoken; ?>');

            fetch('<?php echo $base_url; ?>process/process_update_social_media.php', {
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
                            title: data.message || 'Social media links updated successfully.'
                        });
                        //reload page after 2 seconds
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        alert('Social media links updated successfully.');
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
                            title: data.message || 'Failed to update social media links.'
                        });
                    } else {
                        alert(data.message || 'Failed to update social media links.');
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
                        title: 'An error occurred while updating social media links.'
                    });
                } else {
                    alert('An error occurred while updating social media links.');
                }
            });
        });


        document.querySelector("#add_website_form").addEventListener("submit", function (e) {
            e.preventDefault();
            const website = document.querySelector("input[name='website']").value;

            if(!website){
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