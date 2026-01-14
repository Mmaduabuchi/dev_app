<?php
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = rtrim($path, '/');
    $uriParts = explode('/', $path);
    $currentPath = end($uriParts);
?>
<nav id="sidebar" class="sidebar">
    <div class="p-3 d-flex align-items-center justify-content-center" style="height: 56px;">
        <span class="fs-4 fw-bold text-white">DevHire</span>
    </div>
    <div class="p-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="sidebar-link <?= ($currentPath == 'home') ? 'active' : '' ?>" href="<?php echo $url . 'home'?>" data-page="dashboard-home">
                    <i class="bi bi-grid-fill"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="sidebar-link <?= ($currentPath == 'users_management') ? 'active' : '' ?>" href="<?php echo $url . 'users_management'?>" data-page="users-management">
                    <i class="bi bi-people-fill"></i> Users Management
                </a>
            </li>
            <li class="nav-item">
                <a class="sidebar-link <?= ($currentPath == 'job_post') ? 'active' : '' ?>" href="<?php echo $url . 'job_post'?>" data-page="job-posts">
                    <i class="bi bi-briefcase-fill"></i> Job Posts
                </a>
            </li>
            <li class="nav-item">
                <a class="sidebar-link <?= ($currentPath == 'subscriptions') ? 'active' : '' ?>" href="<?php echo $url . 'subscriptions'?>" data-page="subscription-management">
                    <i class="bi bi-currency-dollar"></i> Subscriptions
                </a>
            </li>
            <li class="nav-item">
                <a class="sidebar-link <?= ($currentPath == 'transactions') ? 'active' : '' ?>" href="<?php echo $url . 'transactions'?>" data-page="payments-transactions">
                    <i class="bi bi-credit-card-2-front-fill"></i> Transactions
                </a>
            </li>
            <li class="nav-item">
                <a class="sidebar-link <?= ($currentPath == 'reports') ? 'active' : '' ?>" href="<?php echo $url . 'reports'?>" data-page="reported-accounts">
                    <i class="bi bi-flag-fill"></i> Reported Accounts
                </a>
            </li>
            <li class="nav-item">
                <a class="sidebar-link <?= ($currentPath == 'messages') ? 'active' : '' ?>" href="<?php echo $url . 'messages'?>" data-page="messages-requests">
                    <i class="bi bi-chat-left-dots-fill"></i> Messages
                </a>
            </li>
            <li class="nav-item">
                <a class="sidebar-link <?= ($currentPath == 'admin_account') ? 'active' : '' ?>" href="<?php echo $url . 'admin_account' ?>" data-page="admin-accounts">
                    <i class="bi bi-person-gear"></i> Admin Accounts
                </a>
            </li>
        </ul>
    </div>
    <div class="mt-auto p-3 border-top border-secondary opacity-75">
        <p class="text-sm text-center mb-0 opacity-50">DevHire Admin Panel v1.0</p>
    </div>
</nav>