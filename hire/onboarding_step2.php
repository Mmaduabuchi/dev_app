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

                <p class="step-title"> <i class="bi bi-arrow-left-circle icon"></i> STEP 2</p>
                <h3 class="main-question">How many people are employed at your company?</h3>

                <!-- Option 1 -->
                <div class="option-card">
                    <div class="d-flex align-items-center">
                        <div class="option-text">
                            <h6>Less than 10</h6>
                        </div>
                    </div>
                    <div class="arrow-icon">→</div>
                </div>

                <!-- Option 2 -->
                <div class="option-card">
                    <div class="d-flex align-items-center">
                        <div class="option-text">
                            <h6>11 - 50</h6>
                        </div>
                    </div>
                    <div class="arrow-icon">→</div>
                </div>

                <!-- Option 3 -->
                <div class="option-card">
                    <div class="d-flex align-items-center">
                        <div class="option-text">
                            <h6>51 - 200</h6>
                        </div>
                    </div>
                    <div class="arrow-icon">→</div>
                </div>

                <!-- Option 4 -->
                <div class="option-card">
                    <div class="d-flex align-items-center">
                        <div class="option-text">
                            <h6>201 - 1000</h6>
                        </div>
                    </div>
                    <div class="arrow-icon">→</div>
                </div>

                <!-- Option 4 -->
                <div class="option-card">
                    <div class="d-flex align-items-center">
                        <div class="option-text">
                            <h6>1001 - 5000</h6>
                        </div>
                    </div>
                    <div class="arrow-icon">→</div>
                </div>

                <!-- Option 4 -->
                <div class="option-card">
                    <div class="d-flex align-items-center">
                        <div class="option-text">
                            <h6>More than 5000</h6>
                        </div>
                    </div>
                    <div class="arrow-icon">→</div>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const backBtn = document.querySelector('.icon');
            if (!backBtn) return;
            backBtn.addEventListener('click', () => history.back());
        });
    </script>
</body>
</html>