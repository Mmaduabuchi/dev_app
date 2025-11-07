<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";

try{
    //fetch user process data/stage
    // $stmt = $conn->prepare("SELECT id FROM `onboarding_sessions` WHERE onboarding_id = ? AND step_number = ?");
    // if (!$stmt) {
    //     throw new Exception('Database error: ' . $conn->error);
    // }
    // $stmt->bind_param("si", $onboarding_id, $data);
    // $stmt->execute();
    // $result = $stmt->get_result();
    // if ($result && $result->num_rows < 1) {
    //     $stmt->close();
    //     $conn->close();
    //     header("location: {$root_url}");
    //     exit;
    // }

} catch (Exception $e) {
    error_log($e->getMessage());
    echo "Something went wrong. Please try again later.";
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devhire status - Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #f9fafc;
            font-family: "Inter", sans-serif;
        }

        .info-box {
            background-color: #eef3ff;
            border-radius: 6px;
            padding: 15px 20px;
            color: #344767;
            font-size: 15px;
        }

        .step-title {
            font-size: 14px;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .main-question {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 25px;
        }

        .option-card {
            background: #fff;
            border: 1px solid #e4e6ef;
            border-radius: 10px;
            padding: 18px 22px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .option-card:hover {
            border-color: #4f46e5;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.1);
        }

        .option-text {
            flex: 1;
        }

        .option-text h6 {
            margin: 0;
            font-weight: 600;
        }

        .option-text small {
            color: #6c757d;
        }

        .option-card:hover .arrow-icon {
            transform: translateX(4px);
            color: #4f46e5;
        }

        .btn-outlined-primary {
            border: 2px solid #4f46e5;
            color: #4f46e5;
            background: transparent;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-outlined-primary:hover {
            background: #4f46e5;
            color: #fff;
        }


        @media (max-width: 768px) {
            .main-question {
                font-size: 20px;
            }
        }

        .icon {
            cursor: pointer;
        }

        #password-rules {
            list-style: none;
            padding-left: 0;
        }

        #password-rules li {
            margin: 3px 0;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">

                <p class="step-title"> <i class="bi bi-arrow-left-circle icon"></i> Dashboard</p>
                <h3 class="main-question">Set up your company profile — provide your company details to complete onboarding and start hiring.</h3>

                <!-- Option 2 -->
                <div class="option-card">
                    <label for=""> <span class="text-danger">*</span> Choose Company Industry</label>
                    <select class="form-control CompanyIndustry">
                        <option value="" disabled selected>Select industry</option>
                        <option value="Technology">Technology</option>
                        <option value="Software">Software</option>
                        <option value="Finance">Finance</option>
                        <option value="Healthcare">Healthcare</option>
                        <option value="Education">Education</option>
                        <option value="Manufacturing">Manufacturing</option>
                        <option value="Retail">Retail</option>
                        <option value="E-commerce">E-commerce</option>
                        <option value="Telecommunications">Telecommunications</option>
                        <option value="Energy">Energy</option>
                        <option value="Utilities">Utilities</option>
                        <option value="Real Estate">Real Estate</option>
                        <option value="Construction">Construction</option>
                        <option value="Transportation & Logistics">Transportation & Logistics</option>
                        <option value="Hospitality">Hospitality</option>
                        <option value="Food & Beverage">Food & Beverage</option>
                        <option value="Media & Entertainment">Media & Entertainment</option>
                        <option value="Marketing & Advertising">Marketing & Advertising</option>
                        <option value="Legal">Legal</option>
                        <option value="Consulting">Consulting</option>
                        <option value="Human Resources">Human Resources</option>
                        <option value="Non-profit">Non-profit</option>
                        <option value="Government">Government</option>
                        <option value="Agriculture">Agriculture</option>
                        <option value="Automotive">Automotive</option>
                        <option value="Aerospace">Aerospace</option>
                        <option value="Pharmaceuticals">Pharmaceuticals</option>
                        <option value="Biotechnology">Biotechnology</option>
                        <option value="Insurance">Insurance</option>
                        <option value="Consumer Goods">Consumer Goods</option>
                    </select>
                </div>

                <!-- Option 3 -->
                <div class="option-card">
                    <label for="">Enter Company Website (Optional)</label>
                    <input type="text" class="form-control CompanyWebsite" placeholder="www.yourcompanydomainname.com">
                </div>

                <!-- Option 4 -->
                <div class="option-card">
                    <label for=""> <span class="text-danger">*</span> Upload Your Company Logo</label>
                    <input type="file" class="form-control CompanyLogo">
                </div>

                <!-- Option 5 -->
                <div class="option-card">
                    <label for=""> <span class="text-danger">*</span> Company Bio</label>
                    <textarea name="companyBio" class="form-control CompanyBio" rows="5" cols="5" id=""></textarea>
                </div>

                <!-- Option 6 -->
                <div class="option-card">
                    <button type="submit" class="btn btn-outlined-primary px-5 py-3 submitBtn">Continue Setup</button>
                </div>
            </div>
        </div>
    </div>


    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const submitBtn = document.querySelector(".submitBtn");
            submitBtn.addEventListener("click", () => {
                const CompanyIndustry = document.querySelector(".CompanyIndustry").value.trim();
                const CompanyWebsite = document.querySelector(".CompanyWebsite").value.trim();
                const CompanyLogo = document.querySelector(".CompanyLogo").files[0];
                const CompanyBio = document.querySelector(".CompanyBio").value.trim();

                //validate user inputs
                if (!CompanyIndustry || !CompanyBio) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Company Industry or Bio are required.',
                        showConfirmButton: false,
                        timer: 2500
                    });
                    return;
                }

                //validate logo
                if (!CompanyLogo) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Please upload your company logo.',
                        showConfirmButton: false,
                        timer: 2500
                    });
                    return;
                }

                const formData = new FormData();
                formData.append("CompanyIndustry", CompanyIndustry);
                formData.append("CompanyWebsite", CompanyWebsite);
                formData.append("CompanyLogo", CompanyLogo);
                formData.append("CompanyBio", CompanyBio);

                // Send to backend
                fetch('<?php echo $base_url; ?>process/process_company_setup.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(async res => {
                        if (!res.ok) {
                            // Handle HTTP errors
                            const text = await res.text();
                            throw new Error(`Server error (${res.status}): ${text}`);
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            }).then(() => {
                                // Redirect to dashboard
                                window.location.href = '../dashboard';
                            });
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
                    .catch(err => console.error(err));
            });

        });

        document.addEventListener('DOMContentLoaded', () => {
            const backBtn = document.querySelector('.icon');
            if (!backBtn) return;
            backBtn.addEventListener('click', () => history.back());
        });

    </script>

</body>

</html>