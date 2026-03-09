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
                <?php
                    // Get user full name
                    $fullname = $_SESSION['user']['fullname'] ?? 'User';

                    // Determine greeting based on time
                    $hour = date('H'); // 24-hour format

                    if ($hour >= 5 && $hour < 12) {
                        $greeting = "Good Morning";
                    } elseif ($hour >= 12 && $hour < 17) {
                        $greeting = "Good Afternoon";
                    } elseif ($hour >= 17 && $hour < 21) {
                        $greeting = "Good Evening";
                    } else {
                        $greeting = "Good Night";
                    }
                ?>
                <li class="d-none d-lg-block">
                    <h5 class="mb-0"><?= $greeting; ?>, <?= htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8'); ?></h5>
                </li>
            </ul>

            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                <li class="d-none d-lg-block">
                    <form class="app-search d-none d-md-block me-auto">
                        <a href="/devhire/dashboard/search">
                            <div class="position-relative topbar-search">
                                <input type="text" disabled class="form-control ps-4" placeholder="Search..." />
                                <i class="mdi mdi-magnify fs-16 position-absolute text-muted top-50 translate-middle-y ms-2"></i>
                            </div>
                        </a>
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
                        <?php
                        if ($notification_count > 0):
                        ?>
                            <span class="badge bg-danger rounded-circle noti-icon-badge"><?= $notification_count ?></span>
                        <?php
                        endif;
                        ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-lg">
                        <!-- item-->
                        <div class="dropdown-item noti-title">
                            <h5 class="m-0">
                                <span class="float-end">
                                    <!-- <a href="#" class="text-dark">
                                        <small>Clear All</small>
                                    </a> -->
                                </span>
                                Notification
                            </h5>
                        </div>

                        <div class="noti-scroll" data-simplebar>
                            <?php
                            if ($user_id !== null) {
                                $nav_userId = $user_id;
                                $nav_limit = 3;
                                $stmt_nav = $conn->prepare("SELECT id, title, created_at, message FROM notifications WHERE user_id = ? AND deleted_at IS NULL ORDER BY created_at DESC LIMIT ?");
                                $stmt_nav->bind_param("ii", $nav_userId, $nav_limit);
                                $stmt_nav->execute();
                                $result_nav = $stmt_nav->get_result();
                                
                                if ($result_nav && $result_nav->num_rows > 0) {
                                    while ($notify_nav = $result_nav->fetch_assoc()):
                                        $title_nav = htmlspecialchars($notify_nav['title'] ?? 'No title', ENT_QUOTES, 'UTF-8');
                                        $message_nav = htmlspecialchars($notify_nav['message'] ?? '', ENT_QUOTES, 'UTF-8');
                                        
                                        // Calculate time ago
                                        $time_ago = '';
                                        if (isset($notify_nav['created_at'])) {
                                            $diff = time() - strtotime($notify_nav['created_at']);
                                            if ($diff < 60) $time_ago = 'Just now';
                                            elseif ($diff < 3600) $time_ago = floor($diff/60) . ' min ago';
                                            elseif ($diff < 86400) $time_ago = floor($diff/3600) . ' hr ago';
                                            else $time_ago = floor($diff/86400) . ' days ago';
                                        }
                            ?>
                                        <!-- item-->
                                        <a href="/devhire/dashboard/myrequest" class="dropdown-item notify-item text-muted link-primary">
                                            <div class="notify-icon">
                                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                                                    <i class="mdi mdi-bell-outline fs-4"></i>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="notify-details"><?= $title_nav ?></p>
                                                <small class="text-muted"><?= $time_ago ?></small>
                                            </div>
                                            <p class="mb-0 user-msg">
                                                <small class="fs-14"><?= mb_strimwidth($message_nav, 0, 37, '...') ?></small>
                                            </p>
                                        </a>
                            <?php
                                    endwhile;
                                } else {
                            ?>
                                        <div class="p-3 text-center text-muted">
                                            <p class="mb-0">No new request</p>
                                        </div>
                            <?php
                                }
                                if (isset($stmt_nav)) $stmt_nav->close();
                            }
                            ?>
                        </div>

                        <!-- All-->
                        <a href="/devhire/dashboard/myrequest" class="dropdown-item text-center text-primary notify-item notify-all">View all
                            <i class="fe-arrow-right"></i>
                        </a>
                    </div>
                </li>

                <!-- User Dropdown -->
                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle nav-user me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <?php
                        if($user_global_variable === false){
                            if (!empty($user_profile_picture['uploaded_picture'])) {
                                $profile_picture = "/devhire/" . $user_profile_picture['uploaded_picture'];
                            } elseif (!empty($user_profile_picture['google_picture'])) {
                                $profile_picture = $user_profile_picture['google_picture'];
                            } else {
                                $profile_picture = $base_url . "assets/images/users/user-12.jpg";
                            }
                        }else{
                            if (!empty($company_profile_picture['company_logo'])) {
                                $profile_picture = "/devhire/" . $company_profile_picture['company_logo'];
                            } else {
                                $profile_picture = $base_url . "assets/images/users/user-12.jpg";
                            }
                        }
                        ?>
                        <img src="<?php echo $profile_picture; ?>" alt="user-image" class="rounded-circle" />
                        <span class="pro-user-name ms-1">Candidate Profile <i class="mdi mdi-chevron-down"></i></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                        <!-- item-->
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome !</h6>
                        </div>

                        <?php 
                            if($user_global_variable === false):
                        ?>
                            <a class='dropdown-item notify-item' href='/devhire/dashboard/myprofile'>
                                <i class="mdi mdi-account-circle-outline fs-16 align-middle"></i>
                                <span>Profile</span>
                            </a>
                        <?php
                        else:
                        ?>
                            <a class='dropdown-item notify-item' href='/devhire/dashboard/mycompany'>
                                <i class="mdi mdi-briefcase-outline fs-16 align-middle"></i>
                                <span>Company</span>
                            </a>                        
                        <?php
                        endif;
                        ?>

                        <!-- item-->
                        <a class='dropdown-item notify-item' href='/devhire/dashboard/setting'>
                            <i class="mdi mdi-account-cog-outline fs-16 align-middle"></i>
                            <span>Account Settings</span>
                        </a>

                        <!-- item-->
                        <!-- <a class='dropdown-item notify-item' href='/devhire/dashboard/notification'>
                            <i class="mdi mdi-bell-outline fs-16 align-middle"></i>
                            <span>Notification</span>
                        </a> -->

                        <div class="dropdown-divider"></div>

                        <!-- item-->
                        <a class='dropdown-item notify-item' href='/devhire/logout'>
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
                <a class='logo logo-light' href='/devhire/dashboard'>
                    <span class="logo-sm">
                        <img src="<?php echo $base_url; ?>assets/images/logo-sm.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="<?php echo $base_url; ?>assets/images/logo-light.png" alt="" height="24">
                    </span>
                </a>
                <a class='logo logo-dark' href='/devhire/dashboard'>
                    <span class="logo-sm">
                        <img src="<?php echo $base_url; ?>assets/images/logo-sm.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="<?php echo $base_url; ?>assets/images/logo-dark.png" alt="" height="24">
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
                                <a class='tp-link' href='/devhire/dashboard/subscriptions'>Subscriptions</a>
                            </li>
                            <li>
                                <a class='tp-link' href='/devhire/dashboard/manage'>Manage Subscription</a>
                            </li>
                            <li>
                                <a class='tp-link' href='/devhire/dashboard/developers'>Meet Developers</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="menu-title mt-2">General</li>
                
                <?php
                    if($user_global_variable === false):
                ?>

                    <li>
                        <a class='tp-link' href='/devhire/dashboard/myprofile'>
                            <i data-feather="user"></i>
                            <span> My Profile </span>
                        </a>
                    </li>

                    <li>
                        <a class='tp-link' href='/devhire/dashboard/profile'>
                            <i data-feather="settings"></i>
                            <span> Profile Settings</span>
                        </a>
                    </li>

                    <li>
                        <a class='tp-link' href='/devhire/dashboard/resume'>
                            <i data-feather="file-text"></i>
                            <span> Resume </span>
                        </a>
                    </li>

                <?php
                    else:
                ?>

                    <li>
                        <a class='tp-link' href='/devhire/dashboard/mycompany'>
                            <i data-feather="briefcase"></i>
                            <span> My Company </span>
                        </a>
                    </li>

                    <li>
                        <a class='tp-link' href='/devhire/dashboard/company'>
                            <i data-feather="sliders"></i>
                            <span> Company Settings</span>
                        </a>
                    </li>

                    <li>
                        <a class='tp-link' href='/devhire/dashboard/sentrequest'>
                            <i data-feather="send"></i>
                            <span> Sent Request </span>
                        </a>
                    </li>

                <?php
                    endif;
                ?>

                <li>
                    <a class='tp-link' href='/devhire/dashboard/search'>
                        <i data-feather="search"></i>
                        <span> Search </span>
                    </a>
                </li>                
                
                <?php
                    if($user_global_variable === false):
                ?>
                    <li>
                        <a class='tp-link' href='/devhire/dashboard/myrequest'>
                            <i data-feather="inbox"></i>
                            <span> Request </span>
                        </a>
                    </li>

                    <li>
                        <a class='tp-link' href='/devhire/dashboard/skills'>
                            <i data-feather="plus-circle"></i>
                            <span> Add Skills </span>
                        </a>
                    </li>
                <?php
                    endif;
                ?>

                <li>
                    <a class='tp-link' href='/devhire/dashboard/support-ticket'>
                        <i data-feather="help-circle"></i>
                        <span> Support Ticket </span>
                    </a>
                </li>

                <li>
                    <a class='tp-link' href='/devhire/dashboard/report'>
                        <i data-feather="alert-circle"></i>
                        <span> Report Issue </span>
                    </a>
                </li>

                <li>
                    <a href="#sidebarAdvancedUI" data-bs-toggle="collapse">
                        <i data-feather="settings"></i>
                        <span> Account Settings </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarAdvancedUI">
                        <ul class="nav-second-level">
                            <li>
                                <a class='tp-link' href='/devhire/dashboard/setting'>Account Settings</a>
                            </li>
                            <li>
                                <a class='tp-link' href='/devhire/dashboard/delete'>Delete Account</a>
                            </li>
                            <li>
                                <a class='tp-link' href='/devhire/logout'>Logout</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- <li>
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
                </li> -->

            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
</div>
<!-- Left Sidebar End -->