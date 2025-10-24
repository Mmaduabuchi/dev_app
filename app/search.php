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

                    <!-- General Form -->
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <div class="container">
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
                                    <div class="mb-3">
                                        <!-- content goes in here  -->
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
        function initiateSearch(query) {
            if (!query) return;
            suggestionBox.style.display = 'none';
            console.log(`Searching for "${query}" in category "${activeCategory}"`);
            
            //redirect or search request:
            // window.location.href = `search_results.php?type=${activeCategory}&q=${encodeURIComponent(query)}`;
        }

        // Hide suggestion box when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-bar')) suggestionBox.style.display = 'none';
        });
    </script>

</body>

</html>