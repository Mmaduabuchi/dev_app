<?php
require_once "config.php";
// Start session and check authentication
require_once "auth.php";
//notification count
require_once __DIR__ . '/fetch_notification_count.php';
//get usertoken from session
$usertoken = $_SESSION['user']['usertoken'] ?? null;

try {
    // fetch user data from database
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    //close stmt
    $stmt->close();

    //user details
    $user_email = $user['email'];
    $user_fullname = $user['fullname'];
} catch (Exception $e) {
    $conn->close();
    error_log($e->getMessage());
    echo "Something went wrong. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title>Support Ticket | devhire - Dashboard</title>
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
        .badge-dark {
            background-color: black !important;
            padding: 0.4rem !important;
        }

        .badge-success {
            background-color: yellow !important;
            padding: 0.4rem !important;
        }

        .badge-warning {
            background-color: blueviolet !important;
            padding: 0.4rem !important;
        }

        .badge-secondary {
            background-color: #FFBF00 !important;
            padding: 0.4rem !important;
        }

        .badge-primary {
            background-color: green !important;
            padding: 0.4rem !important;
        }
    </style>

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
                            <h4 class="fs-18 fw-semibold m-0">My Support Tickets</h4>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card overflow-hidden">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h5 class="card-title mb-0">Your ticket history</h5>
                                        <div class="ms-auto">
                                            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Open ticket</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-traffic mb-0">

                                            <thead>
                                                <tr>
                                                    <th>Department</th>
                                                    <th>Subject</th>
                                                    <th>Status</th>
                                                    <th>Created Date</th>
                                                </tr>
                                            </thead>
                                            <?php
                                            function fetchTicketshistory()
                                            {
                                                global $conn;
                                                global $user_id;
                                                try {
                                                    $stmt = $conn->prepare("SELECT * FROM `support_ticket` WHERE user_id = ? AND deleted_at IS NULL ORDER BY created_at DESC");
                                                    if (!$stmt) {
                                                        throw new Exception('Database error: ' . $conn->error);
                                                    }
                                                    $stmt->bind_param("i", $user_id);
                                                    if ($stmt->execute()) {
                                                        $result = $stmt->get_result();

                                                        //Check if no tickets is found
                                                        if ($result->num_rows < 1) {
                                                            echo '<tr><td colspan="4" class="text-center text-muted">No support tickets found.</td></tr>';
                                                            return;
                                                        }

                                                        while ($ticketData = $result->fetch_assoc()) {
                                                            $rawDate = $ticketData['created_at'];
                                                            $status = ucfirst($ticketData['status']);
                                                            $subject = htmlspecialchars($ticketData['title']);
                                                            $category = htmlspecialchars($ticketData['category'] ?? 'Support');
                                                            $badgeColors = [
                                                                'open' => 'badge-success',
                                                                'in progress' => 'badge-warning',
                                                                'closed' => 'badge-dark',
                                                                'pending' => 'badge-secondary',
                                                                'resolved' => 'badge-primary'
                                                            ];

                                                            $badgeClass = $badgeColors[strtolower($status)] ?? 'badge-light';


                                                            // Create DateTime object
                                                            $date = new DateTime($rawDate);
                                            ?>
                                                            <tr>
                                                                <td class="text-nowrap text-reset">
                                                                    <?= $category ?>
                                                                </td>
                                                                <td>
                                                                    <a href="#" class="text-reset"><?= $subject ?></a>
                                                                </td>
                                                                <td>
                                                                    <a href="#" class="text-reset">
                                                                        <span class="badge badge-pill <?= $badgeClass ?>"><?= $status ?></span>
                                                                    </a>
                                                                </td>
                                                                <td class="text-nowrap text-reset">
                                                                    <i data-feather="calendar" style="height: 18px; width: 18px;" class="me-1"></i>
                                                                    <?= $date->format("l, F jS Y g:i:s A"); ?>
                                                                </td>
                                                            </tr>
                                            <?php
                                                        }
                                                    }
                                                } catch (Exception $e) {
                                                    $conn->close();
                                                    error_log($e->getMessage());
                                                    echo '<tr><td colspan="4" class="text-center text-danger">Something went wrong. Please try again later.</td></tr>';
                                                }
                                            }
                                            fetchTicketshistory();
                                            ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <!-- Modal -->
                            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form id="openTicketForm" method="POST" action="#">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Support Ticket</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Ticket Title -->
                                                <div class="mb-3">
                                                    <label for="ticketTitle" class="form-label">Title</label>
                                                    <input type="text" class="form-control" id="ticketTitle" name="title" placeholder="Enter ticket title" required>
                                                </div>
                                                <!-- Category -->
                                                <div class="mb-3">
                                                    <label for="ticketCategory" class="form-label">Category</label>
                                                    <select class="form-select" id="ticketCategory" name="category" required>
                                                        <option value="" selected disabled>Select a category</option>
                                                        <option value="Technical Support">Technical Support</option>
                                                        <option value="Billing">Billing</option>
                                                        <option value="Account Issue">Account Issue</option>
                                                        <option value="Feedback">Feedback</option>
                                                        <option value="General Inquiry">General Inquiry</option>
                                                    </select>
                                                </div>
                                                <!-- Priority -->
                                                <div class="mb-3">
                                                    <label for="ticketPriority" class="form-label">Priority</label>
                                                    <select class="form-select" id="ticketPriority" name="priority" required>
                                                        <option value="Low" selected>Low</option>
                                                        <option value="Medium">Medium</option>
                                                        <option value="High">High</option>
                                                    </select>
                                                </div>

                                                <!-- Message -->
                                                <div class="mb-3">
                                                    <label for="ticketMessage" class="form-label">Message</label>
                                                    <textarea class="form-control" id="ticketMessage" name="message" rows="4" placeholder="Describe your issue" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Open Ticket</button>
                                            </div>
                                        </form>
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
        document.querySelector("#openTicketForm").addEventListener("submit", (evt) => {
            evt.preventDefault();

            const form = evt.target;
            //Create a FormData object from the form
            const formData = new FormData(form);

            //Get form values
            const ticketTitle = formData.get("title");
            const ticketCategory = formData.get("category");
            const ticketPriority = formData.get("priority");
            const ticketMessage = formData.get("message");

            //validate user inputs
            if (!ticketTitle || !ticketCategory || !ticketPriority || !ticketMessage) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'All fields are required.',
                    showConfirmButton: false,
                    timer: 2500
                });
                return;
            }

            //Convert all form data to an object
            const data = Object.fromEntries(formData.entries());
            console.log(data);

            //send data to server
            fetch("<?php echo $base_url; ?>process/process_submit_ticket.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 2500
                    });
                    setTimeout(() => location.reload(), 2600);
                } else {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 2500
                    });
                }
            })
            .catch(error => {
                console.error("Error:", error);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'An error occurred while processing your request. Please try again later.',
                    showConfirmButton: false,
                    timer: 2500
                });
                return;
            });
        });
    </script>

</body>

</html>