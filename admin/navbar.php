<?php
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = rtrim($path, '/');
    $uriParts = explode('/', $path);
    $currentPath = end($uriParts);
?>
<style>
    /* Sidebar Base */
    .sidebar {
        width: 260px;
        height: 100vh;
        background-color: #111827;
        color: #fff;
    }

    /* Header */
    .sidebar-header {
        height: 56px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    /* Links */
    .sidebar-link {
        display: block;
        padding: 10px 15px;
        color: #cbd5e1;
        text-decoration: none;
        border-radius: 6px;
        margin-bottom: 4px;
        transition: 0.2s ease;
    }

    .sidebar-link:hover {
        background-color: rgba(255,255,255,0.08);
        color: #fff;
    }

    .sidebar-link.active {
        background-color: #023e8a;
        color: #fff;
    }

    /* Scrollbar Styling */
    .sidebar .overflow-auto::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar .overflow-auto::-webkit-scrollbar-thumb {
        background-color: #555;
        border-radius: 10px;
    }
</style>
<nav id="sidebar" class="sidebar d-flex flex-column">

    <!-- TOP LOGO -->
    <div class="p-3 d-flex align-items-center justify-content-center sidebar-header">
        <span class="fs-4 fw-bold text-white">DevHire</span>
    </div>

    <!-- SCROLLABLE MENU AREA -->
    <div class="flex-grow-1 overflow-auto">

        <div class="p-3">
            <ul class="nav flex-column">

                <li class="nav-item">
                    <a class="sidebar-link <?= ($currentPath == 'home') ? 'active' : '' ?>" 
                       href="<?php echo $url . 'home'?>">
                        <i class="bi bi-grid-fill"></i> Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="sidebar-link <?= ($currentPath == 'job_post') ? 'active' : '' ?>" 
                       href="<?php echo $url . 'job_post'?>">
                        <i class="bi bi-briefcase-fill"></i> Job Posts
                    </a>
                </li>

                <li class="nav-item">
                    <a class="sidebar-link <?= ($currentPath == 'subscriptions') ? 'active' : '' ?>" 
                       href="<?php echo $url . 'subscriptions'?>">
                        <i class="bi bi-currency-dollar"></i> Subscriptions
                    </a>
                </li>

                <li class="nav-item">
                    <a class="sidebar-link <?= ($currentPath == 'transactions') ? 'active' : '' ?>" 
                       href="<?php echo $url . 'transactions'?>">
                        <i class="bi bi-credit-card-2-front-fill"></i> Transactions
                    </a>
                </li>

                <li class="nav-item">
                    <a class="sidebar-link <?= ($currentPath == 'reports') ? 'active' : '' ?>" 
                       href="<?php echo $url . 'reports'?>">
                        <i class="bi bi-flag-fill"></i> Reported Accounts
                    </a>
                </li>

                <!-- USERS MANAGEMENT DROPDOWN -->
                <li class="nav-item">
                    <a class="sidebar-link d-flex justify-content-between align-items-center 
                        <?= (in_array($currentPath, ['users_management', 'employers_management'])) ? 'active' : '' ?>" 
                        data-bs-toggle="collapse" 
                        href="#usersDropdown" 
                        role="button"
                        aria-expanded="<?= (in_array($currentPath, ['users_management', 'employers_management'])) ? 'true' : 'false' ?>">

                        <span>Users Management</span>
                        <i class="bi bi-chevron-down small"></i>
                    </a>

                    <div class="collapse <?= (in_array($currentPath, ['users_management', 'employers_management'])) ? 'show' : '' ?>" 
                         id="usersDropdown">

                        <ul class="nav flex-column ms-3 mt-2">

                            <li class="nav-item">
                                <a class="sidebar-link <?= ($currentPath == 'users_management') ? 'active' : '' ?>" 
                                   href="<?php echo $url . 'users_management'?>">
                                    Talents
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="sidebar-link <?= ($currentPath == 'employers_management') ? 'active' : '' ?>" 
                                   href="<?php echo $url . 'employers_management'?>">
                                    Employers
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="sidebar-link <?= ($currentPath == 'messages') ? 'active' : '' ?>" 
                       href="<?php echo $url . 'messages'?>">
                        <i class="bi bi-chat-left-dots-fill"></i> Messages
                    </a>
                </li>

                <li class="nav-item">
                    <a class="sidebar-link <?= ($currentPath == 'admin_account') ? 'active' : '' ?>" 
                       href="<?php echo $url . 'admin_account' ?>">
                        <i class="bi bi-person-gear"></i> Admin Accounts
                    </a>
                </li>

            </ul>
        </div>

    </div>

    <!-- FIXED BOTTOM SECTION -->
    <div class="p-3 border-top border-secondary opacity-75 sidebar-footer">
        <p class="text-sm text-center mb-0 opacity-50">
            DevHire Admin Panel v1.0
        </p>
    </div>

</nav>