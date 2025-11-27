<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
        <!-- Toggle Button for Mobile -->
        <button id="sidebar-toggle" class="btn btn-primary d-lg-none me-2" type="button">
            <i class="bi bi-list"></i>
        </button>

        <!-- Search Bar -->
        <form class="d-none d-md-flex me-auto" role="search">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input class="form-control border-start-0" type="search" placeholder="Search talents, employers, or transactions..." aria-label="Search" style="min-width: 250px;">
            </div>
        </form>

        <!-- Right Side Icons -->
        <div class="d-flex align-items-center">
            <!-- Dark Mode Toggle -->
            <!-- <button id="dark-mode-toggle" class="btn btn-light me-3" title="Toggle Dark Mode">
                <i class="bi bi-moon-fill"></i>
            </button> -->

            <!-- Notifications Dropdown -->
            <div class="dropdown me-3">
                <button class="btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell-fill"></i> <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">4</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="width: 280px;">
                    <li class="dropdown-header">Notifications (4 New)</li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item small" href="#">New talent profile pending verification.</a></li>
                    <li><a class="dropdown-item small" href="#">Payment failed for Employer Corp.</a></li>
                    <li><a class="dropdown-item small" href="#">3 new reported accounts.</a></li>
                </ul>
            </div>

            <!-- Admin Profile Dropdown -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://placehold.co/40x40/0A66C2/ffffff?text=SA" alt="Admin" width="40" height="40" class="rounded-circle me-2">
                    <span class="d-none d-sm-inline me-1 text-dark"><?= $admin_name; ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <li><a class="dropdown-item" href="<?php echo $url . 'profile_settings' ?>">Profile Settings</a></li>
                    <li><a class="dropdown-item" href="<?php echo $url . 'open_tickets' ?>">Open Tickets</a></li>
                    <li><a class="dropdown-item" href="<?php echo $url . 'activity_log' ?>">Activity Log</a></li>
                    <li><a class="dropdown-item" href="<?php echo $url . 'notifications' ?>">Notifications</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?php echo $url . 'admin_logout' ?>">Sign Out</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>