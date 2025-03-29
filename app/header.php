<!-- Topbar Start -->
<div class="topbar-custom">
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                <li>
                    <button class="button-toggle-menu nav-link">
                        <i data-feather="menu" class="noti-icon"></i>
                    </button>
                </li>
                <li class="d-none d-lg-block">
                    <h5 class="mb-0">Good Morning, Alex</h5>
                </li>
            </ul>

            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                <li class="d-none d-lg-block">
                    <form class="app-search d-none d-md-block me-auto">
                        <div class="position-relative topbar-search">
                            <input type="text" class="form-control ps-4" placeholder="Search..." />
                            <i class="mdi mdi-magnify fs-16 position-absolute text-muted top-50 translate-middle-y ms-2"></i>
                        </div>
                    </form>
                </li>

                <!-- Button Trigger Customizer Offcanvas -->
                <li class="d-none d-sm-flex">
                    <button type="button" class="btn nav-link" data-toggle="fullscreen">
                        <i data-feather="maximize" class="align-middle fullscreen noti-icon"></i>
                    </button>
                </li>

                <!-- Light/Dark Mode Button Themes -->
                <li class="d-none d-sm-flex">
                    <button type="button" class="btn nav-link" id="light-dark-mode">
                        <i data-feather="moon" class="align-middle dark-mode"></i>
                        <i data-feather="sun" class="align-middle light-mode"></i>
                    </button>
                </li>

                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i data-feather="bell" class="noti-icon"></i>
                        <span class="badge bg-danger rounded-circle noti-icon-badge">9</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-lg">
                        <!-- item-->
                        <div class="dropdown-item noti-title">
                            <h5 class="m-0">
                                <span class="float-end"><a href="#" class="text-dark"><small>Clear All</small></a></span>Notification
                            </h5>
                        </div>

                        <div class="noti-scroll" data-simplebar>
                            <!-- item-->
                            <a href="javascript:void(0);"
                                class="dropdown-item notify-item text-muted link-primary active">
                                <div class="notify-icon">
                                    <img src="assets/images/users/user-12.jpg" class="img-fluid rounded-circle" alt="" />
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="notify-details">Carl Steadham</p>
                                    <small class="text-muted">5 min ago</small>
                                </div>
                                <p class="mb-0 user-msg">
                                    <small class="fs-14">Completed <span class="text-reset">Improve workflow in Figma</span></small>
                                </p>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item text-muted link-primary">
                                <div class="notify-icon">
                                    <img src="assets/images/users/user-2.jpg" class="img-fluid rounded-circle" alt="" />
                                </div>
                                <div class="notify-content">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="notify-details">Olivia McGuire</p>
                                        <small class="text-muted">1 min ago</small>
                                    </div>

                                    <div class="d-flex mt-2 align-items-center">
                                        <div class="notify-sub-icon">
                                            <i class="mdi mdi-download-box text-dark"></i>
                                        </div>

                                        <div>
                                            <p class="notify-details mb-0">dark-themes.zip</p>
                                            <small class="text-muted">2.4 MB</small>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item text-muted link-primary">
                                <div class="notify-icon">
                                    <img src="assets/images/users/user-3.jpg" class="img-fluid rounded-circle" alt="" />
                                </div>
                                <div class="notify-content">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="notify-details">Travis Williams</p>
                                        <small class="text-muted">7 min ago</small>
                                    </div>
                                    <p class="noti-mentioned p-2 rounded-2 mb-0 mt-2">
                                        <span class="text-primary">@Patryk</span> Please make sure that you're....
                                    </p>
                                </div>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item text-muted link-primary">
                                <div class="notify-icon">
                                    <img src="assets/images/users/user-8.jpg" class="img-fluid rounded-circle" alt="" />
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="notify-details">Violette Lasky</p>
                                    <small class="text-muted">5 min ago</small>
                                </div>
                                <p class="mb-0 user-msg">
                                    <small class="fs-14">Completed <span class="text-reset">Create new components</span></small>
                                </p>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item text-muted link-primary">
                                <div class="notify-icon">
                                    <img src="assets/images/users/user-5.jpg" class="img-fluid rounded-circle" alt="" />
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="notify-details">Ralph Edwards</p>
                                    <small class="text-muted">5 min ago</small>
                                </div>
                                <p class="mb-0 user-msg">
                                    <small class="fs-14">Completed<span class="text-reset">Improve workflow in React</span></small>
                                </p>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item text-muted link-primary">
                                <div class="notify-icon">
                                    <img src="assets/images/users/user-6.jpg" class="img-fluid rounded-circle" alt="" />
                                </div>
                                <div class="notify-content">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="notify-details">Jocab jones</p>
                                        <small class="text-muted">7 min ago</small>
                                    </div>
                                    <p class="noti-mentioned p-2 rounded-2 mb-0 mt-2">
                                        <span class="text-reset">@Patryk</span> Please make sure that you're....
                                    </p>
                                </div>
                            </a>
                        </div>

                        <!-- All-->
                        <a href="javascript:void(0);" class="dropdown-item text-center text-primary notify-item notify-all">View all
                            <i class="fe-arrow-right"></i>
                        </a>
                    </div>
                </li>

                <!-- User Dropdown -->
                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle nav-user me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <img src="assets/images/users/user-13.jpg" alt="user-image" class="rounded-circle" />
                        <span class="pro-user-name ms-1">Creator Profile <i class="mdi mdi-chevron-down"></i></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                        <!-- item-->
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome !</h6>
                        </div>

                        <!-- item-->
                        <a class='dropdown-item notify-item' href='pages-profile.html'>
                            <i class="mdi mdi-account-circle-outline fs-16 align-middle"></i>
                            <span>Creator Profile</span>
                        </a>

                        <!-- item-->
                        <a class='dropdown-item notify-item' href='auth-lock-screen.html'>
                            <i class="mdi mdi-lock-outline fs-16 align-middle"></i>
                            <span>Switch to Affiliate Profile</span>
                        </a>

                        <!-- item-->
                        <a class='dropdown-item notify-item' href='auth-lock-screen.html'>
                            <i class="mdi mdi-lock-outline fs-16 align-middle"></i>
                            <span>Switch to Customer Profile</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <!-- item-->
                        <a class='dropdown-item notify-item' href='auth-logout.html'>
                            <i class="mdi mdi-location-exit fs-16 align-middle"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- end Topbar -->

<!-- Left Sidebar Start -->
<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <div class="logo-box">
                <a class='logo logo-light' href='index.html'>
                    <span class="logo-sm">
                        <img src="assets/images/logo-sm.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="assets/images/logo-light.png" alt="" height="24">
                    </span>
                </a>
                <a class='logo logo-dark' href='index.html'>
                    <span class="logo-sm">
                        <img src="assets/images/logo-sm.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="assets/images/logo-dark.png" alt="" height="24">
                    </span>
                </a>
            </div>

            <ul id="side-menu">

                <li class="menu-title">Menu</li>

                <li>
                    <a href="#sidebarDashboards" data-bs-toggle="collapse">
                        <i data-feather="home"></i>
                        <span> Dashboard </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarDashboards">
                        <ul class="nav-second-level">
                            <li>
                                <a class='tp-link' href='analytics.html'>Analytics</a>
                            </li>
                            <li>
                                <a class='tp-link' href='ecommerce.html'>eCommerce</a>
                            </li>
                            <li>
                                <a class='tp-link' href='projects.html'>Projects</a>
                            </li>
                            <li>
                                <a class='tp-link' href='hrm.html'>HRM</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="menu-title">Pages</li>

                <li>
                    <a href="#sidebarAuth" data-bs-toggle="collapse">
                        <i data-feather="users"></i>
                        <span> Sales </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarAuth">
                        <ul class="nav-second-level">
                            <li>
                                <a class='tp-link' href='auth-login.html'>Sales</a>
                            </li>
                            <li>
                                <a class='tp-link' href='auth-register.html'>Abandoned Transactions</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#sidebarError" data-bs-toggle="collapse">
                        <i data-feather="alert-octagon"></i>
                        <span> Product(s) </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarError">
                        <ul class="nav-second-level">
                            <li>
                                <a class='tp-link' href='error-404.html'>All Products</a>
                            </li>
                            <li>
                                <a class='tp-link' href='error-500.html'>Add Product</a>
                            </li>
                            <li>
                                <a class='tp-link' href='error-503.html'>Featured Products</a>
                            </li>
                            <li>
                                <a class='tp-link' href='error-429.html'>Subscribers</a>
                            </li>
                            <li>
                                <a class='tp-link' href='offline-page.html'>Product Categories</a>
                            </li>
                            <li>
                                <a class='tp-link' href='offline-page.html'>Variation Assets</a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <li class="menu-title mt-2">General</li>

                <li>
                    <a class='tp-link' href='apps-todolist.html'>
                        <i data-feather="columns"></i>
                        <span> Customers </span>
                    </a>
                </li>

                <li>
                    <a class='tp-link' href='apps-contacts.html'>
                        <i data-feather="map-pin"></i>
                        <span> Affiliates </span>
                    </a>
                </li>

                <li>
                    <a class='tp-link' href='apps-calendar.html'>
                        <i data-feather="calendar"></i>
                        <span> Coupons/Discounts </span>
                    </a>
                </li>               

                <li>
                    <a class='tp-link' href='widgets.html'>
                        <i data-feather="aperture"></i>
                        <span> Sales pages </span>
                    </a>
                </li>

                <li>
                    <a href="#sidebarAdvancedUI" data-bs-toggle="collapse">
                        <i data-feather="cpu"></i>
                        <span> Wallet & Payout </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarAdvancedUI">
                        <ul class="nav-second-level">
                            <li>
                                <a class='tp-link' href='extended-carousel.html'>Carousel</a>
                            </li>
                            <li>
                                <a class='tp-link' href='extended-notifications.html'>Notifications</a>
                            </li>
                            <li>
                                <a class='tp-link' href='extended-offcanvas.html'>Offcanvas</a>
                            </li>
                            <li>
                                <a class='tp-link' href='extended-range-slider.html'>Range Slider</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#sidebarIcons" data-bs-toggle="collapse">
                        <i data-feather="award"></i>
                        <span> Integrations </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarIcons">
                        <ul class="nav-second-level">
                            <li>
                                <a class='tp-link' href='icons-feather.html'>Feather Icons</a>
                            </li>
                            <li>
                                <a class='tp-link' href='icons-mdi.html'>Material Design Icons</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#sidebarForms" data-bs-toggle="collapse">
                        <i data-feather="briefcase"></i>
                        <span> Custom mail </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarForms">
                        <ul class="nav-second-level">
                            <li>
                                <a class='tp-link' href='forms-elements.html'>General Elements</a>
                            </li>
                            <li>
                                <a class='tp-link' href='forms-validation.html'>Validation</a>
                            </li>
                            <li>
                                <a class='tp-link' href='forms-quilljs.html'>Quilljs Editor</a>
                            </li>
                            <li>
                                <a class='tp-link' href='forms-pickers.html'>Picker</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#sidebarTables" data-bs-toggle="collapse">
                        <i data-feather="table"></i>
                        <span> Settings </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarTables">
                        <ul class="nav-second-level">
                            <li>
                                <a class='tp-link' href='tables-basic.html'>Basic Tables</a>
                            </li>
                            <li>
                                <a class='tp-link' href='tables-datatables.html'>Data Tables</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a class='tp-link' href='apps-todolist.html'>
                        <i data-feather="columns"></i>
                        <span> Billings </span>
                    </a>
                </li>

                <li>
                    <a class='tp-link' href='pages-pricing.html'>
                        <i data-feather="columns"></i>
                        <span> Get pro plans </span>
                    </a>
                </li>

            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
</div>
<!-- Left Sidebar End -->