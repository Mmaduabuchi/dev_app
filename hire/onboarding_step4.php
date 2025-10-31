<?php
//database connection
require_once __DIR__ . '/../config/databaseconnection.php';
//require auth_hire
require_once 'auth_hire.php';
require_once 'config.php';

$data = 3;
//fetch user process data/stage
$stmt = $conn->prepare("SELECT id FROM `onboarding_sessions` WHERE onboarding_id = ? AND step_number = ?");
if (!$stmt) {
    throw new Exception('Database error: ' . $conn->error);
}
$stmt->bind_param("si", $onboarding_id, $data);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows < 1) {
    $stmt->close();
    $conn->close();
    header("location: {$root_url}");
    exit;
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devhire Onboarding - Step 1</title>
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
            display: flex;
            align-items: center;
            justify-content: space-between;
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

        /* .option-icon {
            font-size: 28px;
            color: #4f46e5;
            margin-right: 15px;
        } */

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

        .arrow-icon {
            font-size: 20px;
            color: #6c757d;
            transition: transform 0.2s;
        }

        .option-card:hover .arrow-icon {
            transform: translateX(4px);
            color: #4f46e5;
        }

        a.talent-link {
            display: block;
            margin-top: 20px;
            color: #5a67d8;
            text-decoration: none;
            font-weight: 500;
        }

        a.talent-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .main-question {
                font-size: 20px;
            }
        }

        .icon {
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">

                <p class="step-title"> <i class="bi bi-arrow-left-circle icon"></i> STEP 4</p>
                <h3 class="main-question">How long do you need the developer?</h3>

                <!-- Option 1 -->
                <div class="option-card">
                    <div class="d-flex align-items-center">
                        <div class="option-text">
                            <h6>Less than 1 week</h6>
                        </div>
                    </div>
                    <div class="arrow-icon">→</div>
                </div>

                <!-- Option 2 -->
                <div class="option-card">
                    <div class="d-flex align-items-center">
                        <div class="option-text">
                            <h6>1 to 4 weeks</h6>
                        </div>
                    </div>
                    <div class="arrow-icon">→</div>
                </div>

                <!-- Option 3 -->
                <div class="option-card">
                    <div class="d-flex align-items-center">
                        <div class="option-text">
                            <h6>1 to 3 months</h6>
                        </div>
                    </div>
                    <div class="arrow-icon">→</div>
                </div>

                <!-- Option 4 -->
                <div class="option-card">
                    <div class="d-flex align-items-center">
                        <div class="option-text">
                            <h6>3 to 6 months</h6>
                        </div>
                    </div>
                    <div class="arrow-icon">→</div>
                </div>

                <!-- Option 5 -->
                <div class="option-card">
                    <div class="d-flex align-items-center">
                        <div class="option-text">
                            <h6>Longer than 6 months</h6>
                        </div>
                    </div>
                    <div class="arrow-icon">→</div>
                </div>

                <!-- Option 6 -->
                <div class="option-card">
                    <div class="d-flex align-items-center">
                        <div class="option-text">
                            <h6>I'll decide later</h6>
                        </div>
                    </div>
                    <div class="arrow-icon">→</div>
                </div>
            </div>
        </div>
    </div>


    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const optionCards = document.querySelectorAll('.option-card');

            optionCards.forEach(card => {
                card.addEventListener('click', () => {
                    const title = card.querySelector('h6').innerText;

                    // Send to backend
                    fetch('<?php echo $base_url; ?>process/process_save_onboarding4.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            step_number: 4,
                            field_value: title
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Move to Step 2 page
                            window.location.href = './commitment';
                        } else {
                            // alert('Error saving data. Please try again.');
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
        });

        document.addEventListener('DOMContentLoaded', () => {
            const backBtn = document.querySelector('.icon');
            if (!backBtn) return;
            backBtn.addEventListener('click', () => history.back());
        });
    </script>

</body>

</html>