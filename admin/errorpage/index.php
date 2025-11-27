<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Error</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .error-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .error-container h1 {
            color: #dc3545;
            font-size: 3em;
            margin-bottom: 20px;
        }
        .error-container p {
            color: #6c757d;
            font-size: 1.1em;
            margin-bottom: 30px;
        }
        .error-container .btn {
            font-size: 1em;
            padding: 10px 20px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#dc3545" class="bi bi-exclamation-triangle-fill mb-4" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
        </svg>
        <h1>Oops! Something went wrong.</h1>
        <p>We're sorry, but an unexpected error occurred while processing your request.</p>
        <p>Please try again later or contact support if the problem persists.</p>
        <a href="/devhire/admin/dashboard/home" class="btn btn-primary">Go to Admin Dashboard</a>
    </div>

    <!-- Bootstrap JS (optional, for components that need it) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
