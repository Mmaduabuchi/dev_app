<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";
//notification count
require_once __DIR__ . '/fetch_notification_count.php';
//get usertoken from session
$usertoken = $_SESSION['user']['usertoken'] ?? null;

// fetch user data from database
try {
    //Fetch user data
    $stmt = $conn->prepare("SELECT email, fullname FROM users WHERE id = ?");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        throw new Exception("User not found.");
    }

    //User details
    $user_email = $user['email'];
    $user_fullname = $user['fullname'];

    //mark all user notifications as read
    $stmt = $conn->prepare("UPDATE `notifications` SET status = 'read' WHERE user_id = ? AND deleted_at IS NULL");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
} catch (Exception $e) {
    // handle error gracefully
    die("Error: " . $e->getMessage());
} finally {
    //End connection
    if (isset($stmt)) $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title>Search | devhire - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo $base_url; ?>assets/images/favicon.ico">

    <!-- App css -->
    <link href="<?php echo $base_url; ?>assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons -->
    <link href="<?php echo $base_url; ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <script src="<?php echo $base_url; ?>assets/js/head.js"></script>

    <style>
        .search-bar {
            display: flex;
            align-items: center;
            border-radius: 50px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 6px 12px;
            gap: 10px;
        }

        .search-input {
            border: none;
            flex: 1;
            outline: none;
            font-size: 15px;
        }

        .category-toggle .btn {
            border: none;
            background: transparent;
            color: #6c757d;
            border-radius: 50px;
            font-weight: 500;
        }

        .category-toggle .btn.active {
            background: #48cae4;
            color: white;
        }

        .suggestions {
            background: white;
            border: 1px solid #ccc;
            border-radius: 8px;
            position: absolute;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
        }

        .suggestions div {
            padding: 8px 12px;
            cursor: pointer;
        }

        .suggestions div:hover {
            background-color: #f2f2f2;
        }
    </style>
</head>

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
                            <h4 class="fs-18 fw-semibold m-0">Search</h4>
                        </div>
                    </div>

                    <div class="row">
                        <?php if (!$sub_status): ?>
                            <div class="col-12">
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <strong>Hello <?= htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8'); ?>!</strong> Your do not have any active subscription.
                                    <a href="/devhire/dashboard/subscriptions" class="alert-link">Choose a plan.</a>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- General Form -->
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-header border-0 pb-0">
                                    <div class="container p-0">
                                        <div class="search-bar bg-light">
                                            <input type="text" class="search-input bg-light" id="searchBarDevhire" placeholder="Search Devhire...">

                                            <div class="category-toggle d-flex align-items-center gap-2">
                                                <button class="btn btn-outline-secondary active">People</button>
                                                <button class="btn btn-outline-secondary">Career</button>
                                            </div>
                                            <div class="suggestions" id="suggestionBox" style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <!-- Search Results Area -->
                                    <div class="mb-3">
                                        
                                        <!-- Loading Spinner -->
                                        <div id="searchLoader" class="text-center my-5" style="display: none;">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="text-muted mt-2">Searching...</p>
                                        </div>

                                        <!-- Results Container -->
                                        <div id="searchResults" class="row g-3">
                                            <div class="col-12 text-center text-muted my-5">
                                                <i class="mdi mdi-magnify mdi-48px"></i>
                                                <p>Type above to start searching for developers.</p>
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

    <!-- App js-->
    <script src="<?php echo $base_url; ?>assets/js/app.js"></script>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const searchInput = document.getElementById('searchBarDevhire');
        const suggestionBox = document.getElementById('suggestionBox');
        const categoryButtons = document.querySelectorAll('.category-toggle .btn');
        let activeCategory = 'People';
        let debounceTimer;

        // Handle category switching
        categoryButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                categoryButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                activeCategory = btn.textContent.trim();
                suggestionBox.style.display = 'none';
                searchInput.value = '';
            });
        });

        // Debounced input
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchSuggestions, 300);
        });

        // Fetch suggestions dynamically
        async function fetchSuggestions() {
            const query = this.value.trim();
            if (query.length < 2) {
                suggestionBox.style.display = 'none';
                return;
            }

            try {
                // Replace this URL with your backend endpoint
                const response = await fetch(`<?php echo $base_url; ?>process/process_search_suggestions.php?type=${activeCategory}&q=${encodeURIComponent(query)}`);
                const results = await response.json();

                suggestionBox.innerHTML = '';
                if (results.length > 0) {
                    results.forEach(item => {
                        const div = document.createElement('div');
                        div.textContent = item.name || item.title;
                        div.addEventListener('click', () => initiateSearch(item.name || item.title));
                        suggestionBox.appendChild(div);
                    });
                    suggestionBox.style.display = 'block';
                } else {
                    suggestionBox.style.display = 'none';
                }
            } catch (error) {
                console.error(error);
            }
        }

        // Handle Enter key press
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                initiateSearch(this.value.trim());
            }
        });

        // Trigger actual search
        async function initiateSearch(query) {
            if (!query) return;
            suggestionBox.style.display = 'none';
            console.log(`Searching for "${query}" in category "${activeCategory}"`);
            
            const resultsContainer = document.getElementById('searchResults');
            const loader = document.getElementById('searchLoader');
            
            // Show loader, clear previous results
            resultsContainer.innerHTML = '';
            loader.style.display = 'block';

            try {
                const response = await fetch(`<?php echo $base_url; ?>process/process_search.php?type=${activeCategory}&q=${encodeURIComponent(query)}`);
                const data = await response.json();

                loader.style.display = 'none';

                if (data.status === 'success' && data.data.length > 0) {
                    data.data.forEach(user => {
                        const avatar = user.picture_url || '<?php echo $base_url; ?>assets/images/users/avatar-1.jpg';
                        const role = user.role || 'Member';
                        const cardHtml = `
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="card shadow-none border mb-0 text-center h-100">
                                    <div class="card-body">
                                        <img src="${avatar}" class="rounded-circle avatar-lg img-thumbnail mb-3" alt="profile-image" style="object-fit:cover;">
                                        <h4 class="mb-1 text-truncate">${user.fullname}</h4>
                                        <p class="text-muted text-capitalize mb-2">${role}</p>
                                        <a href="<?php echo $base_url; ?>candidate.php?ref=${user.usertoken}&token=search" class="btn btn-sm btn-primary rounded-pill mt-2">View Profile</a>
                                    </div>
                                </div>
                            </div>
                        `;
                        resultsContainer.insertAdjacentHTML('beforeend', cardHtml);
                    });
                } else {
                    resultsContainer.innerHTML = `
                        <div class="col-12 text-center text-muted my-5">
                            <i class="mdi mdi-account-search mdi-48px"></i>
                            <p>No results found for "<strong>${query}</strong>" in ${activeCategory}.</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error(error);
                loader.style.display = 'none';
                resultsContainer.innerHTML = `
                    <div class="col-12 text-center text-danger my-5">
                        <i class="mdi mdi-alert-circle mdi-48px"></i>
                        <p>An error occurred while searching. Please try again.</p>
                    </div>
                `;
            }
        }

        // Hide suggestion box when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-bar')) suggestionBox.style.display = 'none';
        });
    </script>

</body>

</html>