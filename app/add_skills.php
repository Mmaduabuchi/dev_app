<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";
//notification count
require_once __DIR__ . '/fetch_notification_count.php';
//get usertoken from session
$usertoken = $_SESSION['user']['usertoken'] ?? null;

// fetch user data from database
$stmt = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
//user details
$user_email = $user['email'];
$user_fullname = $user['fullname'];
$created_at = $user['created_at'];
$stmt->close();

//count users work_experience_records
$stmt = $conn->prepare("SELECT COUNT(*) AS record_count FROM `work_experience_records` WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$record_count = $row['record_count'];
$stmt->close();


?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title>Add More Skills | devhire - Dashboard</title>
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

        .tagDone {
            display: inline-block;
            background: #71eb6cff;
            color: white;
            padding: 5px 10px;
            margin: 3px;
            border-radius: 5px;
        }

        .tagDone .remove {
            margin-left: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .tag {
            display: inline-block;
            background: #71eb6cff;
            color: white;
            padding: 5px 10px;
            margin: 3px;
            border-radius: 5px;
        }

        .tag .remove {
            margin-left: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        #suggestions {
            border: 1px solid #ccc;
            border-radius: 5px;
            max-width: 300px;
            background: white;
            position: absolute;
            z-index: 1000;
            display: none;
        }

        #suggestions div {
            padding: 8px;
            cursor: pointer;
        }

        #suggestions div:hover {
            background: #007bff;
            color: white;
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
                            <h4 class="fs-18 fw-semibold m-0">Skills & Experience</h4>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">

                                <div class="card-header">
                                    <h5 class="card-title mb-0">Skills</h5>
                                </div><!-- end card header -->

                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="example-input-large" class="form-label">Add skills</label>
                                        <input type="text" id="example-input-large" class="form-control form-control-lg skillData" placeholder="Type a skill e.g., PHP, JavaScript" autocomplete="off">
                                        <div id="suggestions"></div>
                                    </div>
                                    <div class="mb-3">
                                        <div id="selected-skills"></div>
                                    </div>
                                    <div>
                                        <button class="btn btn-primary" id="save-btn">Save Skills</button>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="alert alert-info mt-3" role="alert">
                                                <strong>Note:</strong> You can add up to 20 skills to showcase your expertise to potential employers.
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="card bg-light-subtle">
                                                <div class="card-body">
                                                    <div class="d-flex flex-wrap gap-2">
                                                        <?php
                                                            //fetch user skills from database
                                                            $stmt = $conn->prepare("SELECT s.skill_name FROM `user_skills` us JOIN `skills` s ON us.skill_id = s.id WHERE us.user_id = ?");
                                                            $stmt->bind_param("i", $user_id);
                                                            $stmt->execute();
                                                            $result = $stmt->get_result();
                                                            if($result->num_rows > 0){
                                                                while ($skill = $result->fetch_assoc()) {
                                                        ?>         
                                                                    <span class="tagDone d-flex">
                                                                        <?php echo htmlspecialchars($skill['skill_name']); ?>
                                                                        <span class="remove">&times;</span>
                                                                    </span>
                                                        <?php
                                                                }
                                                            }else{
                                                                echo "<p class='text-center'>No skills added yet..</p>";
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
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
                                    <h5 class="card-title mb-0">Add Work Experience <?= $record_count ?>/5</h5>
                                </div><!-- end card header -->

                                <div class="card-body">
                                    <form method="post" action="" id="add_workExperience_form">
                                        <div class="mb-3">
                                            <label for="example-input-normal" class="form-label">Title*</label>
                                            <input type="text" id="example-input-normal" name="Title" class="form-control" placeholder="Lead Product Designer">
                                        </div>
                                        <div class="mb-3">
                                            <label for="simpleinput" class="form-label">Company*</label>
                                            <input type="text" name="company" id="simpleinput" class="form-control academy" placeholder="Amazon Inc">
                                        </div>
                                        <div class="mb-3">
                                            <div class="row g-2">
                                                <div class="col-12">
                                                    <label for="simpleinput" class="form-label">Year*</label>
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
                                            <label for="example-textarea" class="form-label">Description*</label>
                                            <textarea class="form-control description" id="example-textarea" placeholder="Describe work experience." rows="6" spellcheck="false"></textarea>
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-primary">Add experience</button>
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
        let selectedSkills = [];

        const input = document.querySelector('.skillData');
        const suggestions = document.getElementById('suggestions');
        const selectedContainer = document.getElementById('selected-skills');

        input.addEventListener("input", function() {
            const query = this.value.trim();
            if (query.length < 1) {
                suggestions.style.display = 'none';
                return;
            }
            fetch('<?php echo $base_url; ?>process/process_search_skills.php?q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                suggestions.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(skill => {
                        const div = document.createElement('div');
                        div.textContent = skill.name;
                        div.onclick = () => addSkill(skill.name);
                        suggestions.appendChild(div);
                    });
                    suggestions.style.display = 'block';
                } else {
                    suggestions.style.display = 'none';
                }
            });
        });
        //Add skill when selected or Enter is pressed
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const skill = input.value.trim();
                if (skill) addSkill(skill);
            }
        });

        function addSkill(skill) {
            skill = skill.charAt(0).toUpperCase() + skill.slice(1).toLowerCase();

            if (!selectedSkills.includes(skill)) {
                selectedSkills.push(skill);

                const tag = document.createElement('span');
                tag.className = 'tag';
                tag.innerHTML = skill + ' <span class="remove">&times;</span>';
                tag.querySelector('.remove').onclick = () => removeSkill(skill, tag);
                selectedContainer.appendChild(tag);
            }

            input.value = '';
            suggestions.style.display = 'none';
        }

        function removeSkill(skill, tagElement) {
            selectedSkills = selectedSkills.filter(s => s !== skill);
            tagElement.remove();
        }

        //Save all selected skills
        document.getElementById('save-btn').onclick = function() {
            if (selectedSkills.length === 0) {
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
                        title: 'Please add at least one skill.'
                    });
                } else {
                    alert('Please add at least one skill.');
                }
                return;
            }

            const formData = new FormData();
            formData.append('token', '<?php echo $usertoken; ?>');
            formData.append('skills', JSON.stringify(selectedSkills));

            fetch('<?php echo $base_url; ?>process/process_save_skills.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
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
                            title: data.message || 'Skills saved successfully.'
                        });
                        //reload page after 2 seconds
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        alert('Skills saved successfully.');
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
                            title: data.message || 'Failed to save skills.'
                        });
                    } else {
                        alert(data.message || 'Failed to save skills.');
                    }
                }
            });
        };


        document.querySelector("#add_workExperience_form").addEventListener("submit", function(e) {
            e.preventDefault();
            const jobTitle = document.querySelector("input[name='Title']").value.trim();
            const company = document.querySelector("input[name='company']").value.trim();
            const startyear = document.querySelector(".startyear").value.trim();
            const endyear = document.querySelector(".endyear").value.trim();
            const description = document.querySelector(".description").value.trim();

            if (!jobTitle || !company || !startyear || !endyear || !description) {
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
                        title: 'All fields are required.'
                    });
                } else {
                    alert('All fields are required.');
                }
                return;
            }

            const formData = new FormData();
            formData.append('jobTitle', jobTitle);
            formData.append('company', company);
            formData.append('startyear', startyear);
            formData.append('endyear', endyear);
            formData.append('description', description);
            formData.append('token', '<?php echo $usertoken; ?>');

            fetch('<?php echo $base_url; ?>process/process_add_work_experience.php', {
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
                                title: data.message || 'Work experience added successfully.'
                            });
                            //reload page after 2 seconds
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            alert('Work experience added successfully.');
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
                                title: data.message || 'Failed to add your work experience.'
                            });
                        } else {
                            alert(data.message || 'Failed to add your work experience.');
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
                            title: 'An error occurred while your adding work experience.'
                        });
                    } else {
                        alert('An error occurred while adding your work experience.');
                    }
                });

        });
    </script>

</body>

</html>