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
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">

                <p class="step-title"> <i class="bi bi-arrow-left-circle icon"></i> STEP 7</p>
                <h3 class="main-question">Success! Let's connect you with talent.</h3>

                <!-- Option 1 -->
                <div class="option-card">
                    <label for="">Enter Company Email</label>
                    <input type="email" class="form-control" placeholder="myname@company.com">
                </div>

                <!-- Option 2 -->
                <div class="option-card">
                    <label for="">Enter Company Name</label>
                    <input type="text" class="form-control" placeholder="Company Name">
                </div>

                <!-- Option 3 -->
                <div class="option-card">
                    <label for="">Enter Contact Name</label>
                    <input type="text" class="form-control" placeholder="Contact Name">
                </div>

                <!-- Option 4 -->
                <div class="option-card">
                    <label for="">Enter Phone Number</label>
                    <input type="tel" class="form-control" placeholder="Phone Number">
                </div>

                <!-- Option 5 -->
                <div class="option-card">
                    <button type="submit" class="btn btn-outlined-primary px-5 py-3">Connect Me With Talents</button>
                </div>

                <div>
                    <p>
                        By completing signup, you are agreeing to Devhire's Terms of Service, Privacy Policy, Sourced Talent Matching Agreement, and that audio or video meetings made through Toptal's systems may be recorded or monitored for quality assurance, training, and compliance purposes or for your convenience.
                    </p>
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