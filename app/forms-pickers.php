<!DOCTYPE html>
<html lang="en">
    
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>

        <meta charset="utf-8" />
        <title>Form Pickers | Hando - Dashboard</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <!-- Flatpickr Timepicker css -->
        <link href="assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />

        <!-- App css -->
        <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

        <!-- Icons -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

        <script src="assets/js/head.js"></script>


    </head>

    <!-- body start -->
    <body data-menu-color="light" data-sidebar="default">

        <!-- Begin page -->
        <div id="app-layout">                        

            <?php include "header.php" ?>

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
        
            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-18 fw-semibold m-0">Form Pickers</h4>
                            </div>
            
                            <div class="text-end">
                                <ol class="breadcrumb m-0 py-0">
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">Forms</a></li>
                                    <li class="breadcrumb-item active">Form Pickers</li>
                                </ol>
                            </div>
                        </div>

                        <!-- Advance Form -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Flatpickr Date picker</h5>
                                    </div><!-- end card header -->

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-xl-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Basic</label>
                                                    <input type="text" class="form-control" id="basic-datepicker" placeholder="Basic datepicker">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Date & Time</label>
                                                    <input type="text" class="form-control" id="datetime-datepicker" placeholder="Date and Time">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">MinDate and MaxDate</label>
                                                    <input type="text" class="form-control" id="minmax-datepicker" placeholder="Mindate - Maxdate">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">MinDate</label>
                                                    <input type="text" class="form-control" id="mindate-datepicker" placeholder="Mindate">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">MaxDate</label>
                                                    <input type="text" class="form-control" id="maxdate-datepicker" placeholder="Maxdate">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Today</label>
                                                    <input type="text" class="form-control" id="today-datepicker" placeholder="Today">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Today to maximum day</label>
                                                    <input type="text" class="form-control" id="todaymax-datepicker" placeholder="Today to maximum day">
                                                </div>

                                                <div>
                                                    <label class="form-label">Disabling dates</label>
                                                    <input type="text" class="form-control" id="disable-datepicker" placeholder="Disabling dates">
                                                </div>
                                            </div>


                                            <div class="col-xl-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Disabling dates by a functions</label>
                                                    <input type="text" class="form-control" id="disablefunction-datepicker" placeholder="Disabling by functions">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Selecting multiple dates</label>
                                                    <input type="text" class="form-control" id="multipledates-datepicker" placeholder="Selecting multiple dates">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Selecting multiple conjunction</label>
                                                    <input type="text" class="form-control" id="multipleconjunction-datepicker" placeholder="Selecting multiple conjunction">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Range Calendar</label>
                                                    <input type="text" class="form-control" id="rangecalendar-datepicker" placeholder="Range Calendar">
                                                </div>

                                                <div>
                                                    <label class="form-label">Inline</label>
                                                    <input type="text" class="form-control" id="inline-datepicker" placeholder="Inline">
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
                                        <h5 class="card-title mb-0">Flatpickr Time Picker</h5>
                                    </div><!-- end card header -->

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-xl-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Time Picker</label>
                                                    <input id="basic-timepicker" type="text" class="form-control" placeholder="Basic timepicker">
                                                </div>

                                                <div class="mb-0">
                                                    <label class="form-label">24-hour Time Picker</label>
                                                    <input id="24hours-timepicker" type="text" class="form-control" placeholder="24-hour Time Picker">
                                                </div>
                                            </div>


                                            <div class="col-xl-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Time Picker w/ Limits</label>
                                                    <input id="minmax-timepicker" type="text" class="form-control"
                                                        placeholder="Limits">
                                                </div>

                                                <div class="mb-0">
                                                    <label class="form-label">Preloading Time</label>
                                                    <input id="preloading-timepicker" type="text" class="form-control" placeholder="Preloading Time">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        
                    </div> <!-- container-fluid -->

                </div> <!-- content -->

                <!-- Footer Start -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col fs-13 text-muted text-center">
                                &copy; <script>document.write(new Date().getFullYear())</script> - Made with <span class="mdi mdi-heart text-danger"></span> by <a href="#!" class="text-reset fw-semibold">Zoyothemes</a> 
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- end Footer -->

            </div>
            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->


        </div>
        <!-- END wrapper -->

        <!-- Vendor -->
        <script src="assets/libs/jquery/jquery.min.js"></script>
        <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/node-waves/waves.min.js"></script>
        <script src="assets/libs/waypoints/lib/jquery.waypoints.min.js"></script>
        <script src="assets/libs/jquery.counterup/jquery.counterup.min.js"></script>
        <script src="assets/libs/feather-icons/feather.min.js"></script>

        <!-- Flatpickr Timepicker Plugin js -->
        <script src="assets/libs/flatpickr/flatpickr.min.js"></script>

        <script src="assets/js/pages/form-picker.js"></script>

        <!-- App js-->
        <script src="assets/js/app.js"></script>
        
    </body>
</html>