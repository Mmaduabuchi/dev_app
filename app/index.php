<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";
//notification count
require_once __DIR__ . '/fetch_notification_count.php';
?>
<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>

    <meta charset="utf-8" />
    <title> Creator Dashboard | Hando -  Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo $base_url; ?>assets/images/favicon.ico">

    <!-- App css -->
    <link href="<?php echo $base_url; ?>assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons -->
    <link href="<?php echo $base_url; ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <script src="<?php echo $base_url; ?>assets/js/head.js"></script>


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
                            <h4 class="fs-18 fw-semibold m-0">Dashboard</h4>
                        </div>
                    </div>

                    <!-- Start Main Widgets -->
                    <div class="row">
                        <div class="col-md-6 col-lg-4 col-xl">
                            <div class="card">
                                <div class="card-body">
                                    <div class="widget-first">

                                        <div class="d-flex align-items-center mb-2">
                                            <div class="p-2 border border-danger border-opacity-10 bg-danger-subtle rounded-2 me-2">
                                                <div class="bg-danger rounded-circle widget-size text-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                                        <path fill="#ffffff" d="M12 4a4 4 0 0 1 4 4a4 4 0 0 1-4 4a4 4 0 0 1-4-4a4 4 0 0 1 4-4m0 10c4.42 0 8 1.79 8 4v2H4v-2c0-2.21 3.58-4 8-4" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <p class="mb-0 text-dark fs-15">Total Visitor</p>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <h3 class="mb-0 fs-22 text-dark me-3">3,456</h3>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl">
                            <div class="card">
                                <div class="card-body">
                                    <div class="widget-first">

                                        <div class="d-flex align-items-center mb-2">
                                            <div
                                                class="p-2 border border-secondary border-opacity-10 bg-secondary-subtle rounded-2 me-2">
                                                <div class="bg-secondary rounded-circle widget-size text-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="#ffffff" width="20" height="20" viewBox="0 0 24 24" stroke-width="" stroke="" class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <p class="mb-0 text-dark fs-15">Shortlisted</p>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <h3 class="mb-0 fs-22 text-dark me-3">09</h3>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl">
                            <div class="card">
                                <div class="card-body">
                                    <div class="widget-first">

                                        <div class="d-flex align-items-center mb-2">
                                            <div
                                                class="p-2 border border-secondary border-opacity-10 bg-secondary-subtle rounded-2 me-2">
                                                <div class="bg-secondary rounded-circle widget-size text-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="#ffffff"   width="20" height="20" viewBox="0 0 24 24" stroke-width="1.0" stroke="currentColor" class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <p class="mb-0 text-dark fs-15">Views</p>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <h3 class="mb-0 fs-22 text-dark me-3">839</h3>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xl">
                            <div class="card">
                                <div class="card-body">
                                    <div class="widget-first">

                                        <div class="d-flex align-items-center mb-2">
                                            <div class="p-2 border border-danger border-opacity-10 bg-danger-subtle rounded-2 me-2">
                                                <div class="bg-danger rounded-circle widget-size text-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="#ffffff"  width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="" class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <p class="mb-0 text-dark fs-15">Applied Job</p>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <h3 class="mb-0 fs-22 text-dark me-3">254</h3>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                    <!-- End Main Widgets -->

                    <!-- start row -->
                    <div class="row">

                        <div class="col-md-12 col-xl-8">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h5 class="card-title mb-0">Profile Views</h5>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div id="sales-overview" class="apex-charts"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 col-xl-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h5 class="card-title mb-0">Sales Pipeline</h5>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div id="top-session" class="apex-charts"></div>

                                    <div class="row mt-2">
                                        <div class="col">
                                            <div class="d-flex justify-content-between align-items-center p-1">
                                                <div>
                                                    <i class="mdi mdi-circle fs-12 align-middle me-1 text-success"></i>
                                                    <span class="align-middle fw-semibold">Won</span>
                                                </div>
                                                <span class="fw-medium text-muted float-end"><i
                                                        class="mdi mdi-arrow-up text-success align-middle fs-14 me-1"></i>12.48%</span>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center p-1">
                                                <div>
                                                    <i class="mdi mdi-circle fs-12 align-middle me-1"
                                                        style="color: #522c8f;"></i>
                                                    <span class="align-middle fw-semibold">Discovery</span>
                                                </div>
                                                <span class="fw-medium text-muted float-end"><i
                                                        class="mdi mdi-arrow-up text-success align-middle fs-14 me-1"></i>5.23%</span>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center p-1">
                                                <div>
                                                    <i class="mdi mdi-circle fs-12 align-middle me-1 text-warning"></i>
                                                    <span class="align-middle fw-semibold">Undiscovery</span>
                                                </div>
                                                <span class="fw-medium text-muted float-end"><i
                                                        class="mdi mdi-arrow-up text-success align-middle fs-14 me-1"></i>15.58%</span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                    </div>
                    <!-- end start -->

                    <!-- Start Monthly Sales -->
                    <div class="row">

                        <div class="col-md-6 col-xl-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h5 class="card-title mb-0">Tasks List</h5>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <ul class="list-unstyled task-list-tab mb-0">

                                        <li>
                                            <div class="d-flex mb-2 pb-1">
                                                <div class="form-check  me-2">
                                                    <input type="checkbox" class="form-check-input">
                                                </div>
                                                <div class="flex-fill w-100">
                                                    <div class="d-flex align-items-start justify-content-between gap-1">
                                                        <div>
                                                            <h6 class="d-block fw-medium mb-1 text-dark fs-15">Plan Product Launch Event</h6>
                                                            <p class="text-muted mb-0 fs-13">Coordinate with the event planning with team</p>
                                                        </div>
                                                        <div class="d-flex gap-1">
                                                            <a aria-label="anchor" class="btn btn-icon btn-sm bg-primary-subtle me-1" data-bs-toggle="tooltip" data-bs-original-title="Edit">
                                                                <i class="mdi mdi-pencil-outline fs-14 text-primary"></i>
                                                            </a>
                                                            <a aria-label="anchor" class="btn btn-icon btn-sm bg-danger-subtle" data-bs-toggle="tooltip" data-bs-original-title="Delete">
                                                                <i class="mdi mdi-delete fs-14 text-danger"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                        <li>
                                            <div class="d-flex mb-2 pb-1">
                                                <div class="form-check me-2">
                                                    <input type="checkbox" class="form-check-input" checked="">
                                                </div>
                                                <div class="flex-fill w-100">
                                                    <div class="d-flex align-items-start justify-content-between gap-1">
                                                        <div>
                                                            <h6 class="d-block fw-medium mb-1 text-dark fs-15">Prepare Monthly Sales Report</h6>
                                                            <p class="text-muted mb-0 fs-13">Analyze sales trends and compile data</p>
                                                        </div>
                                                        <div class="d-flex gap-1">
                                                            <a aria-label="Edit" class="btn btn-icon btn-sm bg-primary-subtle me-1" data-bs-toggle="tooltip" data-bs-original-title="Edit">
                                                                <i class="mdi mdi-pencil-outline fs-14 text-primary"></i>
                                                            </a>
                                                            <a aria-label="Delete" class="btn btn-icon btn-sm bg-danger-subtle" data-bs-toggle="tooltip" data-bs-original-title="Delete">
                                                                <i class="mdi mdi-delete fs-14 text-danger"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                        <li>
                                            <div class="d-flex mb-2 pb-1">
                                                <div class="form-check me-2">
                                                    <input type="checkbox" class="form-check-input">
                                                </div>
                                                <div class="flex-fill w-100">
                                                    <div class="d-flex align-items-start justify-content-between gap-1">
                                                        <div>
                                                            <h6 class="d-block fw-medium mb-1 text-dark fs-15">Organize Team Meeting</h6>
                                                            <p class="text-muted mb-0 fs-13">Set up a recurring weekly meeting</p>
                                                        </div>
                                                        <div class="d-flex gap-1">
                                                            <a aria-label="Edit" class="btn btn-icon btn-sm bg-primary-subtle me-1" data-bs-toggle="tooltip" data-bs-original-title="Edit">
                                                                <i class="mdi mdi-pencil-outline fs-14 text-primary"></i>
                                                            </a>
                                                            <a aria-label="Delete" class="btn btn-icon btn-sm bg-danger-subtle" data-bs-toggle="tooltip" data-bs-original-title="Delete">
                                                                <i class="mdi mdi-delete fs-14 text-danger"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                        <li>
                                            <div class="d-flex mb-2 pb-1">
                                                <div class="mb-3 form-check  me-2">
                                                    <input type="checkbox" class="form-check-input" checked="">
                                                </div>
                                                <div class="flex-fill w-100">
                                                    <div class="d-flex align-items-start justify-content-between gap-1">
                                                        <div>
                                                            <h6 class="d-block fw-medium mb-1 text-dark fs-15">Sales Accounting</h6>
                                                            <p class="text-muted mb-0 fs-13">Meeting for the sales team regarding growth</p>
                                                        </div>
                                                        <div class="d-flex gap-1">
                                                            <a aria-label="anchor" class="btn btn-icon btn-sm bg-primary-subtle me-1" data-bs-toggle="tooltip" data-bs-original-title="Edit">
                                                                <i class="mdi mdi-pencil-outline fs-14 text-primary"></i>
                                                            </a>
                                                            <a aria-label="anchor" class="btn btn-icon btn-sm bg-danger-subtle" data-bs-toggle="tooltip" data-bs-original-title="Delete">
                                                                <i class="mdi mdi-delete fs-14 text-danger"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                        <li>
                                            <div class="d-flex mb-2 pb-1">
                                                <div class="mb-3 form-check  me-2">
                                                    <input type="checkbox" class="form-check-input">
                                                </div>
                                                <div class="flex-fill w-100">
                                                    <div class="d-flex align-items-start justify-content-between gap-1">
                                                        <div>
                                                            <h6 class="d-block fw-medium mb-1 text-dark fs-15">Update User Database</h6>
                                                            <p class="text-muted mb-0 fs-13">Hando project s2 to amazon cloud database setup</p>
                                                        </div>
                                                        <div class="d-flex gap-1">
                                                            <a aria-label="anchor" class="btn btn-icon btn-sm bg-primary-subtle me-1" data-bs-toggle="tooltip" data-bs-original-title="Edit">
                                                                <i class="mdi mdi-pencil-outline fs-14 text-primary"></i>
                                                            </a>
                                                            <a aria-label="anchor" class="btn btn-icon btn-sm bg-danger-subtle" data-bs-toggle="tooltip" data-bs-original-title="Delete">
                                                                <i class="mdi mdi-delete fs-14 text-danger"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                        <li>
                                            <div class="d-flex mb-0 pb-0">
                                                <div class="mb-3 form-check me-2">
                                                    <input type="checkbox" class="form-check-input" checked="">
                                                </div>
                                                <div class="flex-fill w-100">
                                                    <div class="d-flex align-items-start justify-content-between gap-1">
                                                        <div>
                                                            <h6 class="d-block fw-medium mb-1 text-dark fs-15">Update Front-end project UI</h6>
                                                            <p class="text-muted mb-0 fs-13">For the handover of the project, update the new UI design</p>
                                                        </div>
                                                        <div class="d-flex gap-1">
                                                            <a aria-label="anchor" class="btn btn-icon btn-sm bg-primary-subtle me-1" data-bs-toggle="tooltip" data-bs-original-title="Edit">
                                                                <i class="mdi mdi-pencil-outline fs-14 text-primary"></i>
                                                            </a>
                                                            <a aria-label="anchor" class="btn btn-icon btn-sm bg-danger-subtle" data-bs-toggle="tooltip" data-bs-original-title="Delete">
                                                                <i class="mdi mdi-delete fs-14 text-danger"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                    </ul>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card overflow-hidden">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h5 class="card-title mb-0">Leads Overview</h5>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div id="deals-statistics" class="apex-charts"></div>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-12 col-xl-5">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h5 class="card-title mb-0">Latest transactions</h5>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <ul class="list-group list-group-flush list-group-no-gutters">

                                        <!-- List Item -->
                                        <li class="list-group-item">
                                            <div class="d-flex">

                                                <div class="flex-shrink-0 align-self-center">
                                                    <!-- Avatar -->
                                                    <div
                                                        class="align-content-center text-center border border-dashed rounded-circle p-1">
                                                        <img src="assets/images/users/user-12.jpg"
                                                            class="avatar avatar-sm rounded-circle">
                                                    </div>
                                                    <!-- End Avatar -->
                                                </div>

                                                <div class="flex-grow-1 ms-3 align-content-center">
                                                    <div class="row">
                                                        <div class="col-7 col-md-5 order-md-1">
                                                            <h6 class="mb-1 text-dark fs-15">Bob Dean</h6>
                                                            <span class="fs-14 text-muted">Transfer to bank
                                                                account</span>
                                                        </div>

                                                        <div class="col-5 col-md-4 order-md-3 text-end mt-2 mt-md-0">
                                                            <h6 class="mb-1 text-dark fs-14">$158.00 USD</h6>
                                                            <span class="fs-13 text-muted">24 Jan, 2024</span>
                                                        </div>

                                                        <div class="col-auto col-md-3 order-md-2 align-self-center">
                                                            <span
                                                                class="badge bg-warning-subtle text-warning fw-semibold rounded-pill">Pending</span>
                                                        </div>
                                                    </div>
                                                    <!-- End Row -->
                                                </div>

                                            </div>
                                        </li>
                                        <!-- End List Item -->

                                        <!-- List Item -->
                                        <li class="list-group-item">
                                            <div class="d-flex">

                                                <div class="flex-shrink-0 align-self-center">
                                                    <!-- Avatar -->
                                                    <div
                                                        class="avatar border border-dashed rounded-circle align-content-center text-center p-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                            viewBox="0 0 24 24">
                                                            <path fill="#2786f1"
                                                                d="M15.194 7.57c.487-.163 1.047-.307 1.534-.451c-1.408-.596-3.176-1.227-4.764-1.625c-.253.073-1.01.271-1.534.434c.541.162 2.328.577 4.764 1.642m-8.896 6.785c.577.343 1.19.812 1.786 1.209c3.952-3.068 7.85-5.432 12.127-6.767c-.596-.307-1.119-.578-1.787-.902c-2.562.65-6.947 2.4-12.126 6.46m-.758-6.46c-2.112.974-4.331 2.31-5.54 3.085c.433.199.866.361 1.461.65c2.671-1.805 4.764-2.905 5.594-3.266c-.595-.217-1.154-.361-1.515-.47zm8.066.234c-.686-.379-3.068-1.263-4.71-1.642c-.487.18-1.173.451-1.642.65c.595.162 2.815.758 4.71 1.714c.487-.235 1.173-.523 1.642-.722m-3.374 1.552c-.56-.27-1.173-.523-1.643-.74c-1.425.704-3.284 1.769-5.63 3.447c.505.27 1.047.595 1.624.92c1.805-1.335 3.627-2.598 5.649-3.627m1.732 8.825c3.79-3.249 9.113-6.407 12.036-7.544a48 48 0 0 0-1.949-1.155c-3.771 1.246-8.174 4.007-12.108 7.129c.667.505 1.371 1.028 2.02 1.57zm2.851-.235h-.108l-.18-.27h-.109v.27h-.072v-.596h.27c.055 0 .109 0 .145.036c.054.019.072.073.072.127c0 .108-.09.162-.198.162zm-.289-.343c.09 0 .199.018.199-.09c0-.072-.072-.09-.144-.09h-.163v.18zm-.523.036c0-.289.235-.523.541-.523s.542.234.542.523a.543.543 0 0 1-.542.542a.53.53 0 0 1-.54-.542m.107 0c0 .235.199.433.451.433a.424.424 0 1 0 0-.848c-.27 0-.45.199-.45.415" />
                                                        </svg>
                                                    </div>
                                                    <!-- End Avatar -->
                                                </div>

                                                <div class="flex-grow-1 ms-3 align-content-center">
                                                    <div class="row">
                                                        <div class="col-7 col-md-5 order-md-1">
                                                            <h6 class="mb-1 text-dark fs-15">Bank of America</h6>
                                                            <span class="fs-14 text-muted">Withdrawal to account</span>
                                                        </div>

                                                        <div class="col-5 col-md-4 order-md-3 text-end mt-2 mt-md-0">
                                                            <h6 class="mb-1 text-success fs-14">$258.00 USD</h6>
                                                            <span class="fs-13 text-muted">26 June, 2024</span>
                                                        </div>

                                                        <div class="col-auto col-md-3 order-md-2 align-self-center">
                                                            <span
                                                                class="badge bg-success-subtle text-success fw-semibold rounded-pill">Completed</span>
                                                        </div>
                                                    </div>
                                                    <!-- End Row -->
                                                </div>

                                            </div>
                                        </li>
                                        <!-- End List Item -->

                                        <!-- List Item -->
                                        <li class="list-group-item">
                                            <div class="d-flex">

                                                <div class="flex-shrink-0 align-self-center">
                                                    <!-- Avatar -->
                                                    <div
                                                        class="avatar border border-dashed rounded-circle align-content-center text-center p-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                                            viewBox="0 0 256 256">
                                                            <path fill="#e01e5a"
                                                                d="M53.841 161.32c0 14.832-11.987 26.82-26.819 26.82S.203 176.152.203 161.32c0-14.831 11.987-26.818 26.82-26.818H53.84zm13.41 0c0-14.831 11.987-26.818 26.819-26.818s26.819 11.987 26.819 26.819v67.047c0 14.832-11.987 26.82-26.82 26.82c-14.83 0-26.818-11.988-26.818-26.82z" />
                                                            <path fill="#36c5f0"
                                                                d="M94.07 53.638c-14.832 0-26.82-11.987-26.82-26.819S79.239 0 94.07 0s26.819 11.987 26.819 26.819v26.82zm0 13.613c14.832 0 26.819 11.987 26.819 26.819s-11.987 26.819-26.82 26.819H26.82C11.987 120.889 0 108.902 0 94.069c0-14.83 11.987-26.818 26.819-26.818z" />
                                                            <path fill="#2eb67d"
                                                                d="M201.55 94.07c0-14.832 11.987-26.82 26.818-26.82s26.82 11.988 26.82 26.82s-11.988 26.819-26.82 26.819H201.55zm-13.41 0c0 14.832-11.988 26.819-26.82 26.819c-14.831 0-26.818-11.987-26.818-26.82V26.82C134.502 11.987 146.489 0 161.32 0s26.819 11.987 26.819 26.819z" />
                                                            <path fill="#ecb22e"
                                                                d="M161.32 201.55c14.832 0 26.82 11.987 26.82 26.818s-11.988 26.82-26.82 26.82c-14.831 0-26.818-11.988-26.818-26.82V201.55zm0-13.41c-14.831 0-26.818-11.988-26.818-26.82c0-14.831 11.987-26.818 26.819-26.818h67.25c14.832 0 26.82 11.987 26.82 26.819s-11.988 26.819-26.82 26.819z" />
                                                        </svg>
                                                    </div>
                                                    <!-- End Avatar -->
                                                </div>

                                                <div class="flex-grow-1 ms-3 align-content-center">
                                                    <div class="row">
                                                        <div class="col-7 col-md-5 order-md-1">
                                                            <h6 class="mb-1 text-dark fs-15">Slack</h6>
                                                            <span class="fs-14 text-muted">Subscription to plan</span>
                                                        </div>

                                                        <div class="col-5 col-md-4 order-md-3 text-end mt-2 mt-md-0">
                                                            <h6 class="mb-1 text-dark fs-14">-$154.00 USD</h6>
                                                            <span class="fs-13 text-muted">12 May, 2024</span>
                                                        </div>

                                                        <div class="col-auto col-md-3 order-md-2 align-self-center">
                                                            <span
                                                                class="badge bg-danger-subtle text-danger fw-semibold rounded-pill">Failed</span>
                                                        </div>
                                                    </div>
                                                    <!-- End Row -->
                                                </div>

                                            </div>
                                        </li>
                                        <!-- End List Item -->

                                        <!-- List Item -->
                                        <li class="list-group-item">
                                            <div class="d-flex">

                                                <div class="flex-shrink-0 align-self-center">
                                                    <!-- Avatar -->
                                                    <div
                                                        class="avatar border border-dashed rounded-circle align-content-center text-center p-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                                                            viewBox="0 0 24 24">
                                                            <path fill="#f06a6a"
                                                                d="M18.78 12.653a5.22 5.22 0 1 0 0 10.44a5.22 5.22 0 0 0 0-10.44m-13.56 0a5.22 5.22 0 1 0 .001 10.439a5.22 5.22 0 0 0-.001-10.439m12-6.525a5.22 5.22 0 1 1-10.44 0a5.22 5.22 0 0 1 10.44 0" />
                                                        </svg>
                                                    </div>
                                                    <!-- End Avatar -->
                                                </div>

                                                <div class="flex-grow-1 ms-3 align-content-center">
                                                    <div class="row">
                                                        <div class="col-7 col-md-5 order-md-1">
                                                            <h6 class="mb-1 text-dark fs-15">Asana</h6>
                                                            <span class="fs-14 text-muted">Subscription payment</span>
                                                        </div>

                                                        <div class="col-5 col-md-4 order-md-3 text-end mt-2 mt-md-0">
                                                            <h6 class="mb-1 text-success fs-14">$258.00 USD</h6>
                                                            <span class="fs-13 text-muted">15 Fab, 2024</span>
                                                        </div>

                                                        <div class="col-auto col-md-3 order-md-2 align-self-center">
                                                            <span
                                                                class="badge bg-success-subtle text-success fw-semibold rounded-pill">Completed</span>
                                                        </div>
                                                    </div>
                                                    <!-- End Row -->
                                                </div>

                                            </div>
                                        </li>
                                        <!-- End List Item -->

                                        <!-- List Item -->
                                        <li class="list-group-item">
                                            <div class="d-flex">

                                                <div class="flex-shrink-0 align-self-center">
                                                    <!-- Avatar -->
                                                    <div
                                                        class="avatar border border-dashed rounded-circle align-content-center text-center p-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                                                            viewBox="0 0 256 208">
                                                            <path
                                                                d="M205.28 31.36c14.096 14.88 20.016 35.2 22.512 63.68c6.626 0 12.805 1.47 16.976 7.152l7.792 10.56A17.55 17.55 0 0 1 256 123.2v28.688c-.008 3.704-1.843 7.315-4.832 9.504C215.885 187.222 172.35 208 128 208c-49.066 0-98.19-28.273-123.168-46.608c-2.989-2.189-4.825-5.8-4.832-9.504V123.2c0-3.776 1.2-7.424 3.424-10.464l7.792-10.544c4.173-5.657 10.38-7.152 16.992-7.152c2.496-28.48 8.4-48.8 22.512-63.68C77.331 3.165 112.567.06 127.552 0H128c14.72 0 50.4 2.88 77.28 31.36m-77.264 47.376c-3.04 0-6.544.176-10.272.544c-1.312 4.896-3.248 9.312-6.08 12.128c-11.2 11.2-24.704 12.928-31.936 12.928c-6.802 0-13.927-1.42-19.744-5.088c-5.502 1.808-10.786 4.415-11.136 10.912c-.586 12.28-.637 24.55-.688 36.824c-.026 6.16-.05 12.322-.144 18.488c.024 3.579 2.182 6.903 5.44 8.384C79.936 185.92 104.976 192 128.016 192c23.008 0 48.048-6.08 74.512-18.144c3.258-1.48 5.415-4.805 5.44-8.384c.317-18.418.062-36.912-.816-55.312h.016c-.342-6.534-5.648-9.098-11.168-10.912c-5.82 3.652-12.927 5.088-19.728 5.088c-7.232 0-20.72-1.728-31.936-12.928c-2.832-2.816-4.768-7.232-6.08-12.128a106 106 0 0 0-10.24-.544m-26.941 43.93c5.748 0 10.408 4.66 10.408 10.409v19.183c0 5.749-4.66 10.409-10.408 10.409s-10.408-4.66-10.408-10.409v-19.183c0-5.748 4.66-10.408 10.408-10.408m53.333 0c5.749 0 10.409 4.66 10.409 10.409v19.183c0 5.749-4.66 10.409-10.409 10.409c-5.748 0-10.408-4.66-10.408-10.409v-19.183c0-5.748 4.66-10.408 10.408-10.408M81.44 28.32c-11.2 1.12-20.64 4.8-25.44 9.92c-10.4 11.36-8.16 40.16-2.24 46.24c4.32 4.32 12.48 7.2 21.28 7.2c6.72 0 19.52-1.44 30.08-12.16c4.64-4.48 7.52-15.68 7.2-27.04c-.32-9.12-2.88-16.64-6.72-19.84c-4.16-3.68-13.6-5.28-24.16-4.32m68.96 4.32c-3.84 3.2-6.4 10.72-6.72 19.84c-.32 11.36 2.56 22.56 7.2 27.04c10.56 10.72 23.36 12.16 30.08 12.16c8.8 0 16.96-2.88 21.28-7.2c5.92-6.08 8.16-34.88-2.24-46.24c-4.8-5.12-14.24-8.8-25.44-9.92c-10.56-.96-20 .64-24.16 4.32M128 56c-2.56 0-5.6.16-8.96.48c.32 1.76.48 3.68.64 5.76c0 1.44 0 2.88-.16 4.48c3.2-.32 5.92-.32 8.48-.32s5.28 0 8.48.32c-.16-1.6-.16-3.04-.16-4.48c.16-2.08.32-4 .64-5.76c-3.36-.32-6.4-.48-8.96-.48" />
                                                        </svg>
                                                    </div>
                                                    <!-- End Avatar -->
                                                </div>

                                                <div class="flex-grow-1 ms-3 align-content-center">
                                                    <div class="row">
                                                        <div class="col-7 col-md-5 order-md-1">
                                                            <h6 class="mb-1 text-dark fs-15">Github Copilot</h6>
                                                            <span class="fs-14 text-muted">Renew A Plan</span>
                                                        </div>

                                                        <div class="col-5 col-md-4 order-md-3 text-end mt-2 mt-md-0">
                                                            <h6 class="mb-1 text-dark fs-14">$89.00 USD</h6>
                                                            <span class="fs-13 text-muted">25 April, 2024</span>
                                                        </div>

                                                        <div class="col-auto col-md-3 order-md-2 align-self-center">
                                                            <span
                                                                class="badge bg-primary-subtle text-primary fw-semibold rounded-pill">Completed</span>
                                                        </div>
                                                    </div>
                                                    <!-- End Row -->
                                                </div>

                                            </div>
                                        </li>
                                        <!-- End List Item -->

                                    </ul>
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

    <!-- Apexcharts JS -->
    <script src="<?php echo $base_url; ?>assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- Widgets Init Js -->
    <script src="<?php echo $base_url; ?>assets/js/pages/crm-dashboard.init.js"></script>

    <!-- App js-->
    <script src="<?php echo $base_url; ?>assets/js/app.js"></script>

</body>

</html>