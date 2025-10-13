<?php
session_start();
// Start session and check authentication
require_once "auth_screening_complete.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almost ready with us.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        body {
            background-color: #f8f9fa;
            padding: 0;
            margin: 0;
        }

        .custom-dropdown {
            max-width: 400px;
            margin: 60px auto;
            position: relative;
        }

        .dropdown-toggle-custom {
            width: 100%;
            background: #fff;
            border: 1px solid #dcdcdc;
            padding: 12px 15px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            font-weight: 500;
        }

        .dropdown-menu-custom {
            position: absolute;
            top: 40%;
            left: 0;
            width: 100%;
            background: #fff;
            border: 1px solid #dcdcdc;
            border-radius: 6px;
            margin-top: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            display: none;
            z-index: 1000;

            /* animation */
            opacity: 0;
            transform: scaleY(0.9);
            transform-origin: top;
            transition: all 0.2s ease;
        }

        .dropdown-menu-custom.show {
            display: block;
            opacity: 1;
            transform: scaleY(1);
        }

        .dropdown-option {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .dropdown-option:hover,
        .dropdown-option.active {
            background: #f1f5f9;
        }

        .dropdown-option i {
            font-size: 20px;
            margin-right: 12px;
            color: #3366ff;
        }

        .dropdown-option .title {
            font-weight: 500;
        }

        .dropdown-option .desc {
            font-size: 13px;
            color: #6c757d;
        }

        .apply-btn {
            margin-top: 20px;
        }

        .blue {
            color: #0818A8;
        }

        .password-toggle {
            cursor: pointer;
            padding: 0 10px;
        }


        .stepper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .stepper .step {
            text-align: center;
            position: relative;
            flex: 1;
        }

        .stepper .circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
            color: #495057;
        }

        .stepper .active .circle {
            background: #0d6efd;
            color: #fff;
        }

        .stepper .completed .circle {
            background: #198754;
            color: #fff;
        }

        .stepper .line {
            position: absolute;
            top: 20px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #dee2e6;
            z-index: -1;
        }

        .stepper .step:not(:last-child)::after {
            content: "";
            position: absolute;
            top: 20px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #dee2e6;
            z-index: -1;
        }

        .stepper .completed:not(:last-child)::after {
            background: #198754;
        }

        .upload-box {
            border: 2px dashed #ccc;
            border-radius: 8px;
            width: 180px;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            cursor: pointer;
            transition: 0.3s;
        }

        .upload-box:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }

        .upload-box i {
            font-size: 40px;
            color: #007bff;
        }

        .upload-instructions {
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>

<body>

    <section>
        <div class="container-fluid">
            <div class="row border-bottom p-3 pb-1">
                <div class="col">
                    <h6 class="mt-2">
                        Your dev<span class="blue">hire </span> Application
                    </h6>
                </div>
                <!-- <div class="col"></div> -->
                <div class="col text-end">
                    <!-- user icon -->
                    <button class="btn btn-outline-primary">
                        <i data-feather="user"></i>
                        <span><?= $_SESSION['user']['fullname'] ?></span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col">
                    <!-- Stepper -->
                    <div class="stepper mb-4">
                        <div class="step active" id="step1-indicator">
                            <div class="circle">1</div>
                            <div>Getting Started</div>
                        </div>
                        <div class="step active" id="step2-indicator">
                            <div class="circle">2</div>
                            <div>Professional Experience</div>
                        </div>
                        <div class="step active" id="step3-indicator">
                            <div class="circle">3</div>
                            <div>Profile Setup</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-5 mb-5">
                <div class="col">
                    <h4>Set up your professional profile </h4>
                </div>
                <div class="col-12 mt-4">
                    <!-- Profile Photo -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Profile Photo</label>
                        <div class="d-flex flex-column flex-md-row align-items-start gap-3">
                            <label for="profilePhoto" class="upload-box">
                                <i class="bi bi-plus-lg"></i>
                                <span class="text-muted mt-2">Upload</span>
                            </label>
                            <input type="file" id="profilePhoto" name="profilePhoto" accept="image/png, image/jpeg" hidden />

                            <div>
                                <p class="upload-instructions">
                                    Please upload a high-quality profile photo. Freelancers with
                                    professional profile photos are prioritized and see more jobs with
                                    clients.
                                </p>
                                <ul class="upload-instructions mb-1">
                                    <li>JPG / PNG file</li>
                                    <li>Minimum resolution: 380x380px</li>
                                    <li>Maximum file size: 10 MB</li>
                                </ul>
                                <!-- <a href="#" class="text-primary">View our high-quality headshot guide</a> -->
                            </div>
                        </div>
                    </div>

                    <!-- Resume Upload -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Resume</label>
                        <p class="upload-instructions">Please upload your resume</p>
                        <div class="d-flex align-items-center gap-3">
                            <label for="resumeUpload" class="btn btn-primary">Upload</label>
                            <input type="file" id="resumeUpload" name="resume" accept="application/pdf" hidden />
                            <small class="text-muted">PDF file • Maximum file size: 5 MB</small>
                        </div>
                    </div>

                    <button class="btn btn-success apply-btn w-25 p-2" onclick="completed()">Finish</button>
                </div>
            </div>
        </div>
    </section>



    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        feather.replace();

        async function completed() {
            const profilePhoto = document.getElementById('profilePhoto').files[0];
            const resume = document.getElementById('resumeUpload').files[0];

            // Allowed file types and size (5MB)
            const allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            const allowedResumeTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            const maxFileSize = 5 * 1024 * 1024; // 5MB

            // Helper for showing alerts
            const showAlert = (icon, title, timer = 3000) => {
                Swal.fire({
                    toast: true,
                    icon,
                    title,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer,
                    timerProgressBar: true
                });
            };

            //Validation
            if (!profilePhoto) return showAlert('error', 'Please upload a profile photo.');
            if (!resume) return showAlert('error', 'Please upload your resume.');

            if (!allowedImageTypes.includes(profilePhoto.type)) {
                return showAlert('error', 'Invalid profile photo. Only JPG, PNG, WEBP, GIF are allowed.');
            }
            if (profilePhoto.size > maxFileSize) {
                return showAlert('error', 'Profile photo must be under 5MB.');
            }

            if (!allowedResumeTypes.includes(resume.type)) {
                return showAlert('error', 'Invalid resume. Only PDF, DOC, DOCX are allowed.');
            }
            if (resume.size > maxFileSize) {
                return showAlert('error', 'Resume must be under 5MB.');
            }

            // Prepare form data
            const formData = new FormData();
            formData.append('action', 'completed');
            formData.append('profilePhoto', profilePhoto);
            formData.append('resume', resume);

            try {
                // Send AJAX request
                const response = await fetch('library/process_screening_wizard.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.status === 'success') {
                    showAlert('success', data.message, 2000);
                    setTimeout(() => (window.location.href = 'dashboard'), 2000);
                } else {
                    showAlert('error', data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'An error occurred. Please try again.');
            }
        }
    </script>
</body>

</html>