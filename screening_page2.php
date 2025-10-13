<?php
session_start();
// Start session and check authentication
require_once "auth_screening2.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Stack with us.</title>
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
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
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
    .blue{
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
                    <div class="step" id="step3-indicator">
                        <div class="circle">3</div>
                        <div>Profile Setup</div>
                    </div>
                    </div>
                </div>
            </div>
            <div class="row mt-5 mb-5">
                <div class="col">
                    <h4>Tell us about your professional experience </h4>
                </div>
                <div class="col-12 mt-4">
                    <label for="professional experience">How many years of professional experience do you have in your field overall?</label>
                    <select name="experience" class="form-select w-50 p-2" id="experience">
                        <option value="">-- Select experience --</option>
                        <option value="1">1 year</option>
                        <option value="1-5">1–5 years</option>
                        <option value="5-8">5–8 years</option>
                        <option value="8+">8 years and above</option>
                    </select>

                    <label for="job_interest" class="form-label mt-3">Primary Job Interest</label>
                    <select name="job_interest" id="job_interest" class="form-select w-50 p-2">
                        <option value="">-- Select your primary interest --</option>
                        <option value="web_developer">Web Developer</option>
                        <option value="frontend_developer">Frontend Developer</option>
                        <option value="backend_developer">Backend Developer</option>
                        <option value="fullstack_developer">Full-Stack Developer</option>
                        <option value="mobile_app_developer">Mobile App Developer</option>
                        <option value="software_engineer">Software Engineer</option>
                        <option value="devops_engineer">DevOps Engineer</option>
                        <option value="data_scientist">Data Scientist</option>
                        <option value="ml_engineer">Machine Learning Engineer</option>
                        <option value="cloud_engineer">Cloud Engineer</option>
                        <option value="cybersecurity_specialist">Cybersecurity Specialist</option>
                        <option value="ui_ux_designer">UI/UX Designer</option>
                        <option value="graphic_designer">Graphic Designer</option>
                        <option value="product_designer">Product Designer</option>
                        <option value="project_manager">Project Manager</option>
                        <option value="product_manager">Product Manager</option>
                        <option value="business_analyst">Business Analyst</option>
                        <option value="management_consultant">Management Consultant</option>
                        <option value="digital_marketing">Digital Marketing Specialist</option>
                        <option value="seo_specialist">SEO Specialist</option>
                        <option value="content_creator">Content Creator</option>
                        <option value="social_media_manager">Social Media Manager</option>
                        <option value="qa_engineer">Quality Assurance Engineer</option>
                        <option value="tech_writer">Technical Writer</option>
                        <option value="it_support">IT Support Specialist</option>
                        <option value="sys_admin">System Administrator</option>
                    </select>

                    <label for="industry" class="form-label mt-3">Which industry do you have experience in?</label>
                    <select name="industry" id="industry" class="form-select w-50 p-2">
                        <option value="">-- Select your industry --</option>
                        <option value="it">Information Technology (IT)</option>
                        <option value="software">Software Development</option>
                        <option value="web_dev">Web Development</option>
                        <option value="mobile_dev">Mobile App Development</option>
                        <option value="cloud">Cloud Computing</option>
                        <option value="ai_ml">Artificial Intelligence & Machine Learning</option>
                        <option value="cybersecurity">Cybersecurity</option>
                        <option value="data_science">Data Science & Analytics</option>
                        <option value="fintech">Fintech (Financial Technology)</option>
                        <option value="banking">Banking & Financial Services</option>
                        <option value="ecommerce">E-commerce & Retail</option>
                        <option value="education">Education / EdTech</option>
                        <option value="healthcare">Healthcare / HealthTech</option>
                        <option value="biotech">Biotechnology</option>
                        <option value="telecom">Telecommunications</option>
                        <option value="media">Media & Entertainment</option>
                        <option value="gaming">Gaming Industry</option>
                        <option value="marketing">Marketing & Advertising</option>
                        <option value="consulting">Consulting Services</option>
                        <option value="manufacturing">Manufacturing</option>
                        <option value="logistics">Transportation & Logistics</option>
                        <option value="real_estate">Real Estate & Construction</option>
                        <option value="energy">Energy & Utilities</option>
                        <option value="agriculture">Agriculture & AgriTech</option>
                        <option value="government">Government & Public Sector</option>
                        <option value="ngo">Non-Profit / NGOs</option>
                        <option value="aerospace">Aerospace & Defense</option>
                    </select>

                    <label for="certification" class="form-label mt-3">Certifications (optional) </label>
                    <select name="certification" id="certification" class="form-select w-50 p-2">
                        <option value="">-- Select a certification --</option>
                        <option value="aws_architect">AWS Certified Solutions Architect</option>
                        <option value="azure_fundamentals">Microsoft Certified: Azure Fundamentals</option>
                        <option value="gcp_architect">Google Cloud Professional Cloud Architect</option>
                        <option value="comptia_a+">CompTIA A+</option>
                        <option value="comptia_network+">CompTIA Network+</option>
                        <option value="comptia_security+">CompTIA Security+</option>
                        <option value="ceh">Certified Ethical Hacker (CEH)</option>
                        <option value="ccna">Cisco Certified Network Associate (CCNA)</option>
                        <option value="ccnp">Cisco Certified Network Professional (CCNP)</option>
                        <option value="cissp">Certified Information Systems Security Professional (CISSP)</option>
                        <option value="cka">Kubernetes Certified Administrator (CKA)</option>
                        <option value="pmp">Project Management Professional (PMP)</option>
                        <option value="csm">Certified Scrum Master (CSM)</option>
                        <option value="prince2">PRINCE2 Certification</option>
                        <option value="six_sigma">Six Sigma Green/Black Belt</option>
                        <option value="cbap">Certified Business Analyst Professional (CBAP)</option>
                        <option value="cfa">Chartered Financial Analyst (CFA)</option>
                        <option value="google_digital">Google Digital Marketing Certification</option>
                        <option value="hubspot_marketing">HubSpot Content Marketing Certification</option>
                        <option value="adobe_certified">Adobe Certified Professional</option>
                        <option value="ux_design">UX Design Professional Certificate</option>
                    </select>
                    <p>
                        <i>
                            Note that you may be ask for the Certification IDs later in the screening process. 
                            <br> So please have them ready available (either in digital or print format).
                        </i>
                    </p>

                    <label for="job_commitment" class="mt-3">Which type of job commitment do you prefer?</label>
                    <select name="job_commitment" id="job_commitment" class="form-select w-50 p-2">
                      <option value="">-- Select a job commitment --</option>
                      <option value="full_time">Full-time (≈ 40 hrs/week)</option>
                      <option value="part_time">Part-time (≈ 20 hrs/week)</option>
                      <option value="hourly">Hourly / Freelance (≤ 10 hrs/week)</option>
                      <option value="contract">Contract (fixed duration)</option>
                      <option value="internship">Internship (learning & training)</option>
                    </select>

                    <label for="rate" class="mt-3">What's your preferred hourly rate in U.S Dollars?</label>
                    <input type="number" name="rate" id="rate" class="form-control w-50 p-2" placeholder="e.g., 25" min="0" step="0.01">

                    <label for="website" class="mt-3">Website (optional) </label>
                    <input type="text" name="website" id="website" class="form-control w-50 p-2" placeholder="e.g., https://yourwebsite.com">

                    <label for="linkedln" class="mt-3">Linkedln (optional)</label>
                    <input type="text" name="linkedln" id="linkedln" class="form-control w-50 p-2" placeholder="e.g., https://linkedin.com/in/yourprofile">

                    <label for="github" class="mt-3">Github (optional)</label>
                    <input type="text" name="github" id="github" class="form-control w-50 p-2" placeholder="e.g., https://github.com/username">

                    <button class="btn btn-primary apply-btn w-25 p-2" onclick="Next()">Next</button>
                </div>
            </div>
        </div>
    </section>



    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        feather.replace();

        function Next(){
          const experience = document.getElementById('experience').value;
          const job_interest = document.getElementById('job_interest').value;
          const industry = document.getElementById('industry').value;
          const certification = document.getElementById('certification').value;
          const job_commitment = document.getElementById('job_commitment').value;
          const rate = document.getElementById('rate').value;
          const website = document.getElementById('website').value;
          const linkedln = document.getElementById('linkedln').value;
          const github = document.getElementById('github').value;

          //validate
          if (!experience || !job_interest || !industry || !job_commitment || rate === '') {
              Swal.fire({
                  toast: true,
                  icon: 'error',
                  title: 'Please fill in all required fields.',
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 3000,
                  timerProgressBar: true
              });
              return;
          }

          if (rate < 0) {
              Swal.fire({
                  toast: true,
                  icon: 'error',
                  title: 'Rate cannot be negative.',
                  position: 'top-end',
                  showConfirmButton: false,
                  timerProgressBar: true
              });
              return;
          }


          //prepare form data
          const formData = new FormData();
          formData.append('action', 'step2');
          formData.append('experience', experience);
          formData.append('job_interest', job_interest);
          formData.append('industry', industry);
          formData.append('certification', certification);
          formData.append('job_commitment', job_commitment);
          formData.append('rate', rate);
          formData.append('website', website);
          formData.append('linkedln', linkedln);
          formData.append('github', github);

          //send ajax request
          fetch('library/process_screening_wizard.php', {
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
                      timer: 2000,
                      timerProgressBar: true
                  }).then(() => { 
                      // Redirect to screening3
                      window.location.href = 'screening3';
                  });
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
                  return
              }
          })
          .catch(error => {
              console.error('Error:', error);
              Swal.fire({
                  toast: true,
                  icon: 'error',
                  title: 'An error occurred. Please try again.',
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 3000,
                  timerProgressBar: true
              });
              return
          });
        }
    </script>
</body>
</html>
