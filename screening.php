<?php
session_start();
// Start session and check authentication
require_once "auth_screening.php";
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
                    <div class="step" id="step2-indicator">
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
                    <h4>Welcome to devhire. Let's get started! </h4>
                </div>
                <div class="col-12 mt-4">
                    <label for="name">Full legal name</label>
                    <input type="text" id="name" class="form-control w-50 p-2" placeholder="Enter your full legal name">
                    <label for="location" class="mt-3">Location</label>
                    <input type="text" id="location" class="form-control w-50 p-2" placeholder="Enter your location">
                    
                    <label for="citizenship" class="mt-3">Citizenship</label>
                    <select class="form-select w-50 p-2" id="country" name="country" required>
                      <option value="">-- Select Country --</option>
                      <option value="Afghanistan">🇦🇫 Afghanistan</option>
                      <option value="Algeria">🇩🇿 Algeria</option>
                      <option value="Angola">🇦🇴 Angola</option>
                      <option value="Argentina">🇦🇷 Argentina</option>
                      <option value="Australia">🇦🇺 Australia</option>
                      <option value="Austria">🇦🇹 Austria</option>
                      <option value="Belgium">🇧🇪 Belgium</option>
                      <option value="Botswana">🇧🇼 Botswana</option>
                      <option value="Brazil">🇧🇷 Brazil</option>
                      <option value="Cameroon">🇨🇲 Cameroon</option>
                      <option value="Canada">🇨🇦 Canada</option>
                      <option value="China">🇨🇳 China</option>
                      <option value="Côte d'Ivoire">🇨🇮 Côte d'Ivoire</option>
                      <option value="Ethiopia">🇪🇹 Ethiopia</option>
                      <option value="France">🇫🇷 France</option>
                      <option value="Germany">🇩🇪 Germany</option>
                      <option value="Ghana">🇬🇭 Ghana</option>
                      <option value="Greece">🇬🇷 Greece</option>
                      <option value="Hungary">🇭🇺 Hungary</option>
                      <option value="India">🇮🇳 India</option>
                      <option value="Indonesia">🇮🇩 Indonesia</option>
                      <option value="Ireland">🇮🇪 Ireland</option>
                      <option value="Israel">🇮🇱 Israel</option>
                      <option value="Italy">🇮🇹 Italy</option>
                      <option value="Jamaica">🇯🇲 Jamaica</option>
                      <option value="Japan">🇯🇵 Japan</option>
                      <option value="Kenya">🇰🇪 Kenya</option>
                      <option value="Madagascar">🇲🇬 Madagascar</option>
                      <option value="Malawi">🇲🇼 Malawi</option>
                      <option value="Malaysia">🇲🇾 Malaysia</option>
                      <option value="Mexico">🇲🇽 Mexico</option>
                      <option value="Morocco">🇲🇦 Morocco</option>
                      <option value="Mozambique">🇲🇿 Mozambique</option>
                      <option value="Nepal">🇳🇵 Nepal</option>
                      <option value="Netherlands">🇳🇱 Netherlands</option>
                      <option value="New Zealand">🇳🇿 New Zealand</option>
                      <option value="Nigeria">🇳🇬 Nigeria</option>
                      <option value="Norway">🇳🇴 Norway</option>
                      <option value="Pakistan">🇵🇰 Pakistan</option>
                      <option value="Peru">🇵🇪 Peru</option>
                      <option value="Philippines">🇵🇭 Philippines</option>
                      <option value="Poland">🇵🇱 Poland</option>
                      <option value="Portugal">🇵🇹 Portugal</option>
                      <option value="Qatar">🇶🇦 Qatar</option>
                      <option value="Russia">🇷🇺 Russia</option>
                      <option value="Saudi Arabia">🇸🇦 Saudi Arabia</option>
                      <option value="Senegal">🇸🇳 Senegal</option>
                      <option value="Singapore">🇸🇬 Singapore</option>
                      <option value="South Africa">🇿🇦 South Africa</option>
                      <option value="South Korea">🇰🇷 South Korea</option>
                      <option value="Spain">🇪🇸 Spain</option>
                      <option value="Sri Lanka">🇱🇰 Sri Lanka</option>
                      <option value="Sweden">🇸🇪 Sweden</option>
                      <option value="Switzerland">🇨🇭 Switzerland</option>
                      <option value="Tanzania">🇹🇿 Tanzania</option>
                      <option value="Thailand">🇹🇭 Thailand</option>
                      <option value="Turkey">🇹🇷 Turkey</option>
                      <option value="Uganda">🇺🇬 Uganda</option>
                      <option value="Ukraine">🇺🇦 Ukraine</option>
                      <option value="United Arab Emirates">🇦🇪 United Arab Emirates</option>
                      <option value="United Kingdom">🇬🇧 United Kingdom</option>
                      <option value="United States">🇺🇸 United States</option>
                      <option value="Vietnam">🇻🇳 Vietnam</option>
                      <option value="Zimbabwe">🇿🇼 Zimbabwe</option>
                    </select>
                    
                    <label for="phone" class="mt-3">Phone number</label>
                    <input type="text" id="phone" class="form-control w-50 p-2" placeholder="Enter your phone number">
                    
                    <label for="language" class="mt-3">English Proficiency</label>
                    <select name="" id="english_proficiency" class="form-select w-50 p-2">
                      <option value="">Select proficiency</option>
                      <option value="basic">Basic</option>
                      <option value="conversational">Conversational</option>
                      <option value="fluent">Fluent</option>
                      <option value="native">Native or Bilingual</option>
                    </select>

                    <label for="education_level" class="mt-3">Education Level</label>
                    <select name="education_level" id="education_level" class="form-select w-50 p-2">
                      <option value="">-- Select your education level --</option>
                      <option value="high_school">High School / Secondary School</option>
                      <option value="diploma">Diploma / Certificate</option>
                      <option value="associate">Associate Degree</option>
                      <option value="bachelor">Bachelor’s Degree</option>
                      <option value="master">Master’s Degree</option>
                      <option value="phd">Doctorate (PhD)</option>
                      <option value="vocational">Vocational Training</option>
                      <option value="bootcamp">Bootcamp / Professional Program</option>
                      <option value="self_taught">Self-taught / Non-formal Education</option>
                      <option value="other">Other</option>
                    </select>

                    <label for="bio" class="mt-3">Bio</label>
                    <textarea name="bio" id="bio" cols="30" rows="6" class="form-control w-50" placeholder="Write a short bio about yourself..."></textarea>
                    
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
          const name = document.getElementById('name').value;
          const location = document.getElementById('location').value;
          const citizenship = document.getElementById('country').value;
          const phone = document.getElementById('phone').value;
          const english_proficiency = document.getElementById('english_proficiency').value;
          const education_level = document.getElementById('education_level').value;
          const bio = document.getElementById('bio').value; 

          //validate
          if(!name || !location || !citizenship || !phone || !english_proficiency || !education_level || !bio){
              Swal.fire({
                  toast: true,
                  icon: 'error',
                  title: 'All fields are required.',
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 3000,
                  timerProgressBar: true
              });
              return
          }
          //validate phone number
          const phoneRegex = /^\+?[0-9]{7,15}$/;
          if(!phoneRegex.test(phone)){
              Swal.fire({
                  toast: true,
                  icon: 'error',
                  title: 'Invalid phone number format.',
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 3000,
                  timerProgressBar: true
              });
              return
          }
          //validate name (only letters and spaces)
          const nameRegex = /^[a-zA-Z\s]+$/;
          if(!nameRegex.test(name)){
              Swal.fire({
                  toast: true,
                  icon: 'error',
                  title: 'Name can only contain letters and spaces.',
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 3000,
                  timerProgressBar: true
              });
              return
          }
          //validate english_proficiency
          const validProficiencies = ['basic', 'conversational', 'fluent', 'native'];
          if(!validProficiencies.includes(english_proficiency)){
              Swal.fire({
                  toast: true,
                  icon: 'error',
                  title: 'Invalid English proficiency level.',
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 3000,
                  timerProgressBar: true
              });
              return
          }

          //prepare form data
          const formData = new FormData();
          formData.append('action', 'step1');
          formData.append('legalname', name);
          formData.append('location', location);
          formData.append('citizenship', citizenship);
          formData.append('phone', phone);
          formData.append('english_proficiency', english_proficiency);
          formData.append('education_level', education_level);
          formData.append('bio', bio);

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
                      // Redirect to screening2
                      window.location.href = 'screening2';
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
