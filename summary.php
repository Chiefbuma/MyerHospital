<?php
date_default_timezone_set("Etc/GMT+8");

session_start();

require_once 'class.php';

$db = new db_class();

// Set the timeout period in seconds (5 minutes = 300 seconds)
$timeout_duration = 600;

// Check if 'user_id' session is set and if the timeout period has passed
if (isset($_SESSION['user_id'])) {
    // If the session variable 'last_activity' is set, check the time elapsed
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
        // If the session has been inactive for more than 5 minutes, destroy the session and redirect
        session_unset();     // Remove all session variables
        session_destroy();   // Destroy the session
        header('Location: login.php');
        exit;
    }
    // Update the last activity time
    $_SESSION['last_activity'] = time();
} else {
    // Redirect to login page if the user is not logged in
    header('Location: login.php');
    exit;
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch the role and cohort
$query = "SELECT branch_id,email, role,cohort_id FROM users WHERE id = '$user_id'";

$result = $db->conn->query($query);

if ($result && $row = mysqli_fetch_assoc($result)) {
    $user_location = $row['branch_id'];
    $user_role = $row['role'];
    $user_cohort = $row['cohort_id'];
    $user_email = $row['email'];
} else {
    echo "Error fetching location_id: " . mysqli_error($con);
    exit;
}

// Query to fetch branch name based on branch_id
$branch_query = "SELECT branch_name FROM branch WHERE branch_id = ?";
$stmt = $db->conn->prepare($branch_query);
$stmt->bind_param("i", $user_location);
$stmt->execute();
$branch_result = $stmt->get_result();

if ($branch_result && $branch_row = $branch_result->fetch_assoc()) {
    $branch_name = $branch_row['branch_name'];
} else {
    $branch_name = "No branch"; // Default if no branch found
}


?>
<!DOCTYPE html>
<html lang="en">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="css/sb-admin-2.css" rel="stylesheet">
<link href="css/custom.css" rel="stylesheet">

<!-- Custom styles for this page -->
<link href="css/dataTables.bootstrap4.css" rel="stylesheet">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Bundle JS (includes Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Boxicons -->
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<!-- FontAwesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- Bootstrap CSS (Add this inside the <head> tag) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GX1C1fVD06Bl0i6WSj1sxDoxJukG6Jg9tyR5dXlskv19q+WYFsN6m4hLuJ7uKfL6" crossorigin="anonymous">

<!-- Bootstrap JS Bundle (Add this before closing </body> tag) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoJihVSrGkUBkHgjLM0i02/5H2jIb0JiK/8Aks65XJUsGPb" crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Monthly Reports</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>


<!-- My CSS -->
<link rel="stylesheet" href="style.css">

<title>Patients</title>

<style>
    /* Apply Times New Roman font globally */
    body {
        font-family: 'Verdana', sans-serif;
    }

    /* Align label and input side by side */
    .form-group-custom {
        display: flex;
        align-items: left;
        margin-bottom: 10px;
    }

    .form-group-custom label {
        width: 30%;
        margin-bottom: 0;
    }

    .form-group-custom input,
    .form-group-custom select,
    .form-group-custom textarea {
        flex: 1;
    }

    .rounded-container {
        border: 2px solid black;
        border-radius: 15px;
        padding: 20px;
        margin-top: 10px;
        background-color: #f9f9f9;
    }

    .rounded-container h5 {
        font-size: 1.25rem;
        margin-bottom: 15px;
        font-family: 'Verdana', sans-serif;
    }

    .rounded-container .form-group {
        margin-bottom: 15px;
    }

    .custom-select {
        width: 100px;
    }

    /* General form styles */
    .form-control,
    .col-form-label {
        font-family: 'Verdana', sans-serif;
        font-size: 10px;
        font-weight: normal;
    }

    .col-form-label {
        font-weight: normal;
        margin-right: 10px;
    }

    .form-control {
        height: 28px;
        padding: 2px 8px;
    }

    .mb-3 {
        margin-bottom: 8px !important;
    }

    .row {
        margin-top: 4px;
    }

    .form-group {
        margin-bottom: 8px !important;
    }

    h5 {
        font-size: 12px;
        font-family: 'Verdana', sans-serif;
    }

    select.form-control,
    input.form-control {
        font-size: 12px;
    }

    label {
        font-weight: bold;
    }

    /* styles.css */
    #toast-container {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
    }

    .toast {
        background-color: #4CAF50;
        /* Default green background */
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 16px;
        margin-top: 10px;
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        width: auto;
        max-width: 300px;
    }

    /* styles.css */
    .toast {
        background-color: green;
        /* Default green background for success */
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 16px;
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        max-width: 300px;
        width: auto;
    }

    .toast.success {
        background-color: green;
        /* Green background for success */
    }

    .toast.error {
        background-color: red;
        /* Red background for error */
    }
</style>

</head>

<body>

    <script src="script.js"></script>

    <!-- SIDEBAR -->
    <section id="sidebar">
        <h1></h1>
        <h1></h1>
        <h1></h1>

        <a href="#" class="brand">
            <i class='bx bxs-smile'></i>

        </a>

        <li class="has-submenu">
            <a href="patients.php">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                    <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-240v-32q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v32q0 33-23.5 56.5T720-160H240q-33 0-56.5-23.5T160-240Zm80 0h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm0-80Zm0 400Z" />
                </svg>
                <span class="text">Registration</span>
            </a>
        </li>

        <li class="has-submenu">
            <a href="addcalls.php">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                    <path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h207q16 0 30.5 6t25.5 17l57 57h320q33 0 56.5 23.5T880-640v400q0 33-23.5 56.5T800-160H160Zm0-80h640v-400H447l-80-80H160v480Zm0 0v-480 480Zm400-160v40q0 17 11.5 28.5T600-320q17 0 28.5-11.5T640-360v-40h40q17 0 28.5-11.5T720-440q0-17-11.5-28.5T680-480h-40v-40q0-17-11.5-28.5T600-560q-17 0-28.5 11.5T560-520v40h-40q-17 0-28.5 11.5T480-440q0 17 11.5 28.5T520-400h40Z" />
                </svg>
                <span class="text">Appointments</span>
            </a>
        </li>


        <li class="has-submenu">
            <a href="#" class="submenu-toggle">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                    <path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-40q0-17 11.5-28.5T280-880q17 0 28.5 11.5T320-840v40h320v-40q0-17 11.5-28.5T680-880q17 0 28.5 11.5T720-840v40h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Zm280 240q-17 0-28.5-11.5T440-440q0-17 11.5-28.5T480-480q17 0 28.5 11.5T520-440q0 17-11.5 28.5T480-400Zm-160 0q-17 0-28.5-11.5T280-440q0-17 11.5-28.5T320-480q17 0 28.5 11.5T360-440q0 17-11.5 28.5T320-400Zm320 0q-17 0-28.5-11.5T600-440q0-17 11.5-28.5T640-480q17 0 28.5 11.5T680-440q0 17-11.5 28.5T640-400ZM480-240q-17 0-28.5-11.5T440-280q0-17 11.5-28.5T480-320q17 0 28.5 11.5T520-280q0 17-11.5 28.5T480-240Zm-160 0q-17 0-28.5-11.5T280-280q0-17 11.5-28.5T320-320q17 0 28.5 11.5T360-280q0 17-11.5 28.5T320-240Zm320 0q-17 0-28.5-11.5T600-280q0-17 11.5-28.5T640-320q17 0 28.5 11.5T680-280q0 17-11.5 28.5T640-240Z" />
                </svg>
                <span class="text">Assessment</span>
                <i class='bx bx-chevron-down dropdown-icon'></i>
            </a>
            <ul class="submenu">
                <li>
                    <a href="assesnutrition.php">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                            <path d="m221-313 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-228q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm0-320 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-548q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm339 353q-17 0-28.5-11.5T520-320q0-17 11.5-28.5T560-360h280q17 0 28.5 11.5T880-320q0 17-11.5 28.5T840-280H560Zm0-320q-17 0-28.5-11.5T520-640q0-17 11.5-28.5T560-680h280q17 0 28.5 11.5T880-640q0 17-11.5 28.5T840-600H560Z" />
                        </svg>

                        <span class="text">Nutrition</span>
                    </a>
                </li>
                <li>
                    <a href="assespsycho.php">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                            <path d="m221-313 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-228q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm0-320 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-548q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm339 353q-17 0-28.5-11.5T520-320q0-17 11.5-28.5T560-360h280q17 0 28.5 11.5T880-320q0 17-11.5 28.5T840-280H560Zm0-320q-17 0-28.5-11.5T520-640q0-17 11.5-28.5T560-680h280q17 0 28.5 11.5T880-640q0 17-11.5 28.5T840-600H560Z" />
                        </svg>

                        <span class="text">pyschosocial</span>
                    </a>
                </li>
                <li>
                    <a href="asseschronic.php">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                            <path d="m221-313 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-228q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm0-320 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-548q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm339 353q-17 0-28.5-11.5T520-320q0-17 11.5-28.5T560-360h280q17 0 28.5 11.5T880-320q0 17-11.5 28.5T840-280H560Zm0-320q-17 0-28.5-11.5T520-640q0-17 11.5-28.5T560-680h280q17 0 28.5 11.5T880-640q0 17-11.5 28.5T840-600H560Z" />
                        </svg>

                        <span class="text">Chronic</span>
                    </a>
                </li>
                <li>
                    <a href="assesmeds.php">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                            <path d="m221-313 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-228q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm0-320 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-548q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm339 353q-17 0-28.5-11.5T520-320q0-17 11.5-28.5T560-360h280q17 0 28.5 11.5T880-320q0 17-11.5 28.5T840-280H560Zm0-320q-17 0-28.5-11.5T520-640q0-17 11.5-28.5T560-680h280q17 0 28.5 11.5T880-640q0 17-11.5 28.5T840-600H560Z" />
                        </svg>

                        <span class="text">Medication</span>
                    </a>
                </li>
            </ul>
        </li>
        <?php if ($user_role == 'admin'): ?>
            <li class="has-submenu">
                <a href="#" class="submenu-toggle">
                    <i class='bx bxs-dashboard'></i>


                    <span class="text">Settings</span>
                    <i class='bx bx-chevron-down dropdown-icon'></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="addbranch.php">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                                <path d="m221-313 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-228q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm0-320 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-548q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm339 353q-17 0-28.5-11.5T520-320q0-17 11.5-28.5T560-360h280q17 0 28.5 11.5T880-320q0 17-11.5 28.5T840-280H560Zm0-320q-17 0-28.5-11.5T520-640q0-17 11.5-28.5T560-680h280q17 0 28.5 11.5T880-640q0 17-11.5 28.5T840-600H560Z" />
                            </svg>

                            <span class="text">Branch</span>
                        </a>
                    </li>
                    <li>
                        <a href="addscheme.php">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                                <path d="m221-313 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-228q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm0-320 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-548q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm339 353q-17 0-28.5-11.5T520-320q0-17 11.5-28.5T560-360h280q17 0 28.5 11.5T880-320q0 17-11.5 28.5T840-280H560Zm0-320q-17 0-28.5-11.5T520-640q0-17 11.5-28.5T560-680h280q17 0 28.5 11.5T880-640q0 17-11.5 28.5T840-600H560Z" />
                            </svg>

                            <span class="text">schemes</span>
                        </a>
                    </li>
                    <li>
                        <a href="addroutes.php">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                                <path d="m221-313 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-228q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm0-320 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-548q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm339 353q-17 0-28.5-11.5T520-320q0-17 11.5-28.5T560-360h280q17 0 28.5 11.5T880-320q0 17-11.5 28.5T840-280H560Zm0-320q-17 0-28.5-11.5T520-640q0-17 11.5-28.5T560-680h280q17 0 28.5 11.5T880-640q0 17-11.5 28.5T840-600H560Z" />
                            </svg>

                            <span class="text">routes</span>
                        </a>
                    </li>
                    <li>
                        <a href="addmedication.php">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                                <path d="m221-313 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-228q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm0-320 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-548q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm339 353q-17 0-28.5-11.5T520-320q0-17 11.5-28.5T560-360h280q17 0 28.5 11.5T880-320q0 17-11.5 28.5T840-280H560Zm0-320q-17 0-28.5-11.5T520-640q0-17 11.5-28.5T560-680h280q17 0 28.5 11.5T880-640q0 17-11.5 28.5T840-600H560Z" />
                            </svg>

                            <span class="text">Medications</span>
                        </a>
                    </li>
                    <li>
                        <a href="addspecialist.php">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                                <path d="m221-313 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-228q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm0-320 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-548q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm339 353q-17 0-28.5-11.5T520-320q0-17 11.5-28.5T560-360h280q17 0 28.5 11.5T880-320q0 17-11.5 28.5T840-280H560Zm0-320q-17 0-28.5-11.5T520-640q0-17 11.5-28.5T560-680h280q17 0 28.5 11.5T880-640q0 17-11.5 28.5T840-600H560Z" />
                            </svg>

                            <span class="text">specialists</span>
                        </a>
                    </li>
                    <li>
                        <a href="addprocedure.php">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                                <path d="m221-313 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-228q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm0-320 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-548q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm339 353q-17 0-28.5-11.5T520-320q0-17 11.5-28.5T560-360h280q17 0 28.5 11.5T880-320q0 17-11.5 28.5T840-280H560Zm0-320q-17 0-28.5-11.5T520-640q0-17 11.5-28.5T560-680h280q17 0 28.5 11.5T880-640q0 17-11.5 28.5T840-600H560Z" />
                            </svg>

                            <span class="text">procedures</span>
                        </a>
                    </li>
                    <li>
                        <a href="addcohort.php">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                                <path d="m221-313 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-228q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm0-320 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-548q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm339 353q-17 0-28.5-11.5T520-320q0-17 11.5-28.5T560-360h280q17 0 28.5 11.5T880-320q0 17-11.5 28.5T840-280H560Zm0-320q-17 0-28.5-11.5T520-640q0-17 11.5-28.5T560-680h280q17 0 28.5 11.5T880-640q0 17-11.5 28.5T840-600H560Z" />
                            </svg>

                            <span class="text">cohorts</span>
                        </a>
                    </li>
                    <li>
                        <a href="adduser.php">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                                <path d="m221-313 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-228q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm0-320 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-548q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm339 353q-17 0-28.5-11.5T520-320q0-17 11.5-28.5T560-360h280q17 0 28.5 11.5T880-320q0 17-11.5 28.5T840-280H560Zm0-320q-17 0-28.5-11.5T520-640q0-17 11.5-28.5T560-680h280q17 0 28.5 11.5T880-640q0 17-11.5 28.5T840-600H560Z" />
                            </svg>

                            <span class="text">users</span>
                        </a>
                    </li>
                    <li>
                        <a href="adddiagnosis.php"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                                <path d="m221-313 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-228q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm0-320 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-548q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm339 353q-17 0-28.5-11.5T520-320q0-17 11.5-28.5T560-360h280q17 0 28.5 11.5T880-320q0 17-11.5 28.5T840-280H560Zm0-320q-17 0-28.5-11.5T520-640q0-17 11.5-28.5T560-680h280q17 0 28.5 11.5T880-640q0 17-11.5 28.5T840-600H560Z" />
                            </svg>

                            <span class="text">diagnosis</span>
                        </a>
                    </li>
                    <li>
                        <a href="addcallresults.php"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                                <path d="m221-313 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-228q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm0-320 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-548q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm339 353q-17 0-28.5-11.5T520-320q0-17 11.5-28.5T560-360h280q17 0 28.5 11.5T880-320q0 17-11.5 28.5T840-280H560Zm0-320q-17 0-28.5-11.5T520-640q0-17 11.5-28.5T560-680h280q17 0 28.5 11.5T880-640q0 17-11.5 28.5T840-600H560Z" />
                            </svg>

                            <span class="text">Call Results</span>
                        </a>
                    </li>
                </ul>
            <?php endif; ?>

            </li>
            <li class="has-submenu">
                <a href="summary.php">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                        <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-240v-32q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v32q0 33-23.5 56.5T720-160H240q-33 0-56.5-23.5T160-240Zm80 0h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm0-80Zm0 400Z" />
                    </svg>
                    <span class="text">Summary</span>
                </a>
            </li>



    </section>
    <!-- SIDEBAR -->

    <!-- Add the following CSS and JavaScript for submenu toggle functionality -->
    <style>
        .has-submenu .submenu {
            display: none;
            list-style: none;
            padding-left: 20px;
        }

        .has-submenu .submenu li a {
            font-size: 0.9rem;
        }

        .has-submenu.active .submenu {
            display: block;
        }

        .dropdown-icon {
            margin-left: auto;
            transition: transform 0.3s ease;
        }

        .has-submenu.active .dropdown-icon {
            transform: rotate(180deg);
        }
    </style>

    <script>
        document.querySelectorAll('.submenu-toggle').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.parentElement;
                parent.classList.toggle('active');
            });
        });
    </script>


    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>

            <a href="#" class="nav-link">Branch</a>
            <span class="text">
                <?php echo htmlspecialchars($branch_name); ?>
            </span>
            <form action="#">
                <div class="form-input">

                </div>
            </form>

            <a href="#" class="notification">
                <i class='bx bxs-bell'></i>

            </a>
            <a href="#" class="profile">
                <img src="img/people.png">
                <span class="text">
                    <?php echo htmlspecialchars($user_email); ?>
                </span>
            </a>
            <div class="logout-container">

                <label>Log Out</label>
                <label for="switch-mode" class="switch-mode" onclick="logout()"></label>
            </div>

            <script>
                function logout() {
                    // Redirect to the logout page
                    window.location.href = "login.php";
                }
            </script>

            <style>
                .logout-container {
                    display: flex;
                    /* Use flexbox for layout */
                    align-items: center;
                    /* Align items vertically */
                    gap: 15px;
                    /* Add space between widgets */

                }

                #switch-mode {
                    display: inline-block;
                    width: 20px;
                    height: 20px;
                    margin-right: 10px;
                }

                .switch-mode {
                    cursor: pointer;
                    font-size: 16px;
                    color: #333;
                }

                .switch-mode:hover {
                    text-decoration: underline;
                }
            </style>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="container-fluid">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card_body">
                            <!-- Black Background for Inputs -->
                            <div class="row justify-content-right align-items-center pt-4" style="background-color: grey; padding: 20px; border-radius: 8px;">
                                <div class="container">
                                    <!-- Row for Dropdown and Buttons -->
                                    <div class="row align-items-centre justify-content-end">
                                        <!-- Dropdown Column -->
                                        <div class="col-md-4 ms-auto">
                                            <!-- Dropdown for Report Selection -->
                                            <label for="report-select" class="form-label" style="color: white;">Select Report</label>
                                            <select id="report-select" class="form-select" style="background-color: white; color: black; width: 100%; height: calc(2.75rem + 2px);">
                                                <option value="">Select a report</option>
                                                <option value="chronic">Chronic care</option>
                                                <option value="nutrition">Nutrition</option>
                                                <option value="psychosocial">Psychosocial</option>
                                                <option value="medication">Medication</option>
                                            </select>
                                        </div>


                                    </div>
                                </div>

                                <!-- JavaScript for Dynamic Button -->
                                <script>
                                    document.getElementById('report-select').addEventListener('change', function() {
                                        const category = this.value; // Get the selected value

                                        // Close any already open modal
                                        $('.modal').modal('hide');

                                        // Open the corresponding modal based on the selected category
                                        if (category === 'chronic') {
                                            $('#viewChronicModal').modal('show');
                                        } else if (category === 'nutrition') {
                                            $('#viewNutritionModal').modal('show');
                                        } else if (category === 'psychosocial') {
                                            $('#viewPsychosocialModal').modal('show');
                                        } else if (category === 'medication') {
                                            $('#viewMedicationModal').modal('show');
                                        }
                                    });
                                </script>


                                <!-- Include Select2 CSS and JS -->
                                <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
                                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
                                <script>
                                    $(document).ready(function() {
                                        $('#report-select').select2({
                                            placeholder: "Select a report",
                                            allowClear: true
                                        });
                                    });
                                </script>




                            </div>
                        </div>
                    </div>
                </div>
                <!-- View nutrition Modal -->
                <div class="modal fade" id="viewNutritionModal" tabindex="-1" role="dialog" aria-labelledby="viewReportModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="NutritionModal">View Report</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="container">
                                    <!-- Date Filter Form -->
                                    <div class="container">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;">

                                            <form id="dateFilterForm" class="form-inline" method="GET">
                                                <label for="from_date" class="mr-2">From:</label>
                                                <input type="date" name="from_date" id="from_date" class="form-control mr-2"
                                                    value="<?= isset($_GET['from_date']) ? $_GET['from_date'] : '' ?>">

                                                <label for="to_date" class="mr-2">To:</label>
                                                <input type="date" name="to_date" id="to_date" class="form-control mr-2"
                                                    value="<?= isset($_GET['to_date']) ? $_GET['to_date'] : '' ?>">
                                            </form>

                                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                                <a id="downloadNutrition" href="downloadNutrition.php" style="text-decoration: underline; color: blue; display: inline-flex; align-items: center;">
                                                    <i class="fa fa-download" style="margin-right: 5px;"></i> Daily Nutrition
                                                </a>

                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        $(document).ready(function() {
                                            function updateDownloadLink() {
                                                const fromDate = $('#from_date').val();
                                                const toDate = $('#to_date').val();
                                                const baseUrl = "downloadNutrition.php";
                                                const url = `${baseUrl}?from=${fromDate}&to=${toDate}`;
                                                $('#downloadNutrition').attr('href', url);
                                            }

                                            // Update the link on form input change
                                            $('#from_date, #to_date').on('change', function() {
                                                updateDownloadLink();
                                            });

                                            // Initialize the link on page load
                                            updateDownloadLink();
                                        });
                                    </script>





                                    <!-- Report Table -->
                                    <table class="table table-bordered table-striped table-hover" id="summaryTable">
                                        <thead>
                                            <tr>
                                                <th>Visit Date</th>
                                                <th>Patient Name</th>
                                                <th>Patient No</th>
                                                <th>Patient Status</th>
                                                <th>Branch</th>
                                                <th>Cohort</th>
                                                <th>Revenue</th>


                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Placeholder for dynamic data -->
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>



                    <script>
                        $(document).ready(function() {
                            // Handle date filter changes
                            $('#from_date, #to_date').on('change', function() {
                                // Serialize form data
                                const formData = $('#dateFilterForm').serialize();

                                // Send AJAX request
                                $.ajax({
                                    url: 'fetch.php', // Replace with your PHP script URL to fetch filtered data
                                    type: 'GET',
                                    data: formData,
                                    success: function(response) {
                                        // Replace the table body with the new data
                                        $('#summaryTable tbody').html(response);

                                        // Keep the modal open
                                        $('#viewNutritionModal').modal('show');
                                    },
                                    error: function() {
                                        alert('Error fetching filtered data. Please try again.');
                                    }
                                });
                            });

                            // Ensure the modal remains open when triggered
                            $('#viewNutritionModal').on('shown.bs.modal', function() {
                                $(this).find('.modal-body').scrollTop(0); // Optional: Scroll to top
                            });
                        });
                    </script>
                </div>

                <!-- View Chronic Modal -->
                <div class="modal fade" id="viewChronicModal" tabindex="-1" role="dialog" aria-labelledby="viewChronicModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ChronicModalLabel">Chronic Report</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="container">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;">
                                        <!-- Date Filter Form for Chronic -->
                                        <form id="chronicDateFilterForm" class="form-inline" method="GET">
                                            <label for="chronic_from_date" class="mr-2">From:</label>
                                            <input type="date" name="chronic_from_date" id="chronic_from_date" class="form-control mr-2"
                                                value="<?= isset($_GET['chronic_from_date']) ? $_GET['chronic_from_date'] : '' ?>">

                                            <label for="chronic_to_date" class="mr-2">To:</label>
                                            <input type="date" name="chronic_to_date" id="chronic_to_date" class="form-control mr-2"
                                                value="<?= isset($_GET['chronic_to_date']) ? $_GET['chronic_to_date'] : '' ?>">
                                        </form>

                                        <div style="display: flex; flex-direction: column; gap: 10px;">
                                            <a id="downloadChronic" href="downloadChronic.php" style="text-decoration: underline; color: blue; display: inline-flex; align-items: center;">
                                                <i class="fa fa-download" style="margin-right: 5px;"></i> Download chronic
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                $(document).ready(function() {
                                    function updateDownloadLink() {
                                        const fromDate = $('#chronic_from_date').val();
                                        const toDate = $('#chronic_to_date').val();
                                        const baseUrl = "downloadChronic.php";
                                        const url = `${baseUrl}?from=${fromDate}&to=${toDate}`;
                                        $('#downloadChronic').attr('href', url);
                                    }

                                    // Update the link on form input change
                                    $('#chronic_from_date, #chronic_to_date').on('change', function() {
                                        updateDownloadLink();
                                    });

                                    // Initialize the link on page load
                                    updateDownloadLink();
                                });
                            </script>

                            <!-- Chronic Report Table -->
                            <table class="table table-bordered table-striped mt-3" id="chronicSummaryTable">
                                <thead>
                                    <tr>
                                        <th>Visit Date</th>
                                        <th>Patient Name</th>
                                        <th>Patient No</th>
                                        <th>Patient Status</th>
                                        <th>Branch</th>
                                        <th>cohort</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Chronic Data Rows -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            </div>

            <script>
                $(document).ready(function() {
                    // Handle date filter changes
                    $('#chronic_from_date, #chronic_to_date').on('change', function() {
                        // Serialize form data
                        const formData = $('#chronicDateFilterForm').serialize();

                        // Send AJAX request
                        $.ajax({
                            url: 'fetchChronic.php', // Replace with your PHP script URL to fetch filtered data
                            type: 'GET',
                            data: formData,
                            success: function(response) {
                                // Replace the table body with the new data
                                $('#chronicSummaryTable tbody').html(response);

                                // Keep the modal open
                                $('#viewChronicModal').modal('show');
                            },
                            error: function() {
                                alert('Error fetching filtered data. Please try again.');
                            }
                        });
                    });

                    // Ensure the modal remains open when triggered
                    $('#viewChronicModal').on('shown.bs.modal', function() {
                        $(this).find('.modal-body').scrollTop(0); // Optional: Scroll to top
                    });
                });
            </script>
            </div>
            <!-- View Psychosocial Modal -->
            <div class="modal fade" id="viewPsychosocialModal" tabindex="-1" role="dialog" aria-labelledby="viewPsychosocialModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="PsychosocialModal">View Psychosocial Report</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <!-- Date Filter Form -->
                                <div class="container">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;">
                                        <form id="psychosocialDateFilterForm" class="form-inline" method="GET">
                                            <label for="psychosocial_from_date" class="mr-2">From:</label>
                                            <input type="date" name="psychosocial_from_date" id="psychosocial_from_date" class="form-control mr-2"
                                                value="<?= isset($_GET['psychosocial_from_date']) ? $_GET['psychosocial_from_date'] : '' ?>">

                                            <label for="psychosocial_to_date" class="mr-2">To:</label>
                                            <input type="date" name="psychosocial_to_date" id="psychosocial_to_date" class="form-control mr-2"
                                                value="<?= isset($_GET['psychosocial_to_date']) ? $_GET['psychosocial_to_date'] : '' ?>">
                                        </form>

                                        <div style="display: flex; flex-direction: column; gap: 10px;">
                                            <a id="downloadPsychosocial" href="downloadPsychosocial.php" style="text-decoration: underline; color: blue; display: inline-flex; align-items: center;">
                                                <i class="fa fa-download" style="margin-right: 5px;"></i> Daily Psychosocial
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    $(document).ready(function() {
                                        function updateDownloadLink() {
                                            const fromDate = $('#psychosocial_from_date').val();
                                            const toDate = $('#psychosocial_to_date').val();
                                            const baseUrl = "downloadPsychosocial.php";
                                            const url = `${baseUrl}?from=${fromDate}&to=${toDate}`;
                                            $('#downloadPsychosocial').attr('href', url);
                                        }

                                        // Update the link on form input change
                                        $('#psychosocial_from_date, #psychosocial_to_date').on('change', function() {
                                            updateDownloadLink();
                                        });

                                        // Initialize the link on page load
                                        updateDownloadLink();
                                    });
                                </script>

                                <!-- Report Table -->
                                <table class="table table-bordered table-striped table-hover" id="psychosocialSummaryTable">
                                    <thead>
                                        <tr>
                                            <th>Visit Date</th>
                                            <th>Patient Name</th>
                                            <th>Patient No</th>
                                            <th>Patient Status</th>
                                            <th>Branch</th>
                                            <th>Cohort</th>
                                            <th>Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Placeholder for dynamic data -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    $(document).ready(function() {
                        // Handle date filter changes
                        $('#psychosocial_from_date, #psychosocial_to_date').on('change', function() {
                            // Serialize form data
                            const formData = $('#psychosocialDateFilterForm').serialize();

                            // Send AJAX request
                            $.ajax({
                                url: 'fetchPsychosocial.php', // Replace with your PHP script URL to fetch filtered data
                                type: 'GET',
                                data: formData,
                                success: function(response) {
                                    // Replace the table body with the new data
                                    $('#psychosocialSummaryTable tbody').html(response);

                                    // Keep the modal open
                                    $('#viewPsychosocialModal').modal('show');
                                },
                                error: function() {
                                    alert('Error fetching filtered data. Please try again.');
                                }
                            });
                        });

                        // Ensure the modal remains open when triggered
                        $('#viewPsychosocialModal').on('shown.bs.modal', function() {
                            $(this).find('.modal-body').scrollTop(0); // Optional: Scroll to top
                        });
                    });
                </script>
            </div>
            <!-- View Medication Modal -->
            <div class="modal fade" id="viewMedicationModal" tabindex="-1" role="dialog" aria-labelledby="viewMedicationModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="MedicationModal">View Medication Report</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <!-- Date Filter Form -->
                                <div class="container">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;">
                                        <form id="medicationDateFilterForm" class="form-inline" method="GET">
                                            <label for="medication_from_date" class="mr-2">From:</label>
                                            <input type="date" name="medication_from_date" id="medication_from_date" class="form-control mr-2"
                                                value="<?= isset($_GET['medication_from_date']) ? $_GET['medication_from_date'] : '' ?>">

                                            <label for="medication_to_date" class="mr-2">To:</label>
                                            <input type="date" name="medication_to_date" id="medication_to_date" class="form-control mr-2"
                                                value="<?= isset($_GET['medication_to_date']) ? $_GET['medication_to_date'] : '' ?>">
                                        </form>

                                        <div style="display: flex; flex-direction: column; gap: 10px;">
                                            <a id="downloadMedication" href="downloadMedication.php" style="text-decoration: underline; color: blue; display: inline-flex; align-items: center;">
                                                <i class="fa fa-download" style="margin-right: 5px;"></i> Daily Medication Report
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    $(document).ready(function() {
                                        function updateDownloadLink() {
                                            const fromDate = $('#medication_from_date').val();
                                            const toDate = $('#medication_to_date').val();
                                            const baseUrl = "downloadMedication.php";
                                            const url = `${baseUrl}?from=${fromDate}&to=${toDate}`;
                                            $('#downloadMedication').attr('href', url);
                                        }

                                        // Update the link on form input change
                                        $('#medication_from_date, #medication_to_date').on('change', function() {
                                            updateDownloadLink();
                                        });

                                        // Initialize the link on page load
                                        updateDownloadLink();
                                    });
                                </script>

                                <!-- Report Table -->
                                <table class="table table-bordered table-striped table-hover" id="medicationSummaryTable">
                                    <thead>
                                        <tr>
                                            <th>Visit Date</th>
                                            <th>Patient Name</th>
                                            <th>Patient No</th>
                                            <th>Medication Name</th>
                                            <th>Cohort</th>
                                            <th>No.of pills</th>
                                            <th>Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Placeholder for dynamic data -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    $(document).ready(function() {
                        // Handle date filter changes
                        $('#medication_from_date, #medication_to_date').on('change', function() {
                            // Serialize form data
                            const formData = $('#medicationDateFilterForm').serialize();

                            // Send AJAX request
                            $.ajax({
                                url: 'fetchMedication.php', // Replace with your PHP script URL to fetch filtered data
                                type: 'GET',
                                data: formData,
                                success: function(response) {
                                    // Replace the table body with the new data
                                    $('#medicationSummaryTable tbody').html(response);

                                    // Keep the modal open
                                    $('#viewMedicationModal').modal('show');
                                },
                                error: function() {
                                    alert('Error fetching filtered data. Please try again.');
                                }
                            });
                        });

                        // Ensure the modal remains open when triggered
                        $('#viewMedicationModal').on('shown.bs.modal', function() {
                            $(this).find('.modal-body').scrollTop(0); // Optional: Scroll to top
                        });
                    });
                </script>
            </div>


            <div class="table-data" style="width: 100%;">
                <div class="order">
                    <div class="head">
                        <h3>Visits Summary</h3>





                        </button>
                    </div>
                    <table id="patientTable" class="table table-bordered">
                        <thead>
                            <tr>

                                <th>Patient Name</th>
                                <th>Patient No</th>
                                <th>Location</th>
                                <th>Branch</th>
                                <th>Cohort</th>
                                <th>Patient Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch patient records from the database
                            $patients = $db->visitsummary();


                            foreach ($patients as $fetchAll) {
                                $patient_id = $fetchAll['patient_id'];

                                $patient_All = $patient_id

                            ?>
                                <tr>

                                    <td><?php echo $fetchAll['firstname'] . ' ' . $fetchAll['lastname']; ?></td> <!-- Concatenated First and Last Name -->
                                    <td><?php echo $fetchAll['patient_no']; ?></td>
                                    <td><?php echo $fetchAll['location']; ?></td>
                                    <td><?php echo $fetchAll['branch_name']; ?></td>
                                    <td><?php echo $fetchAll['cohort_id']; ?></td>
                                    <td><?php echo $fetchAll['patient_status']; ?></td>


                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#editModal<?php echo $fetchAll['patient_id']; ?>" style="background-color: black; color: white;">History</button>


                                        </div>
    </section>
    <!-- CONTENT -->

    <!-- Modal Content -->
    <div class="modal fade" id="patientModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="savepatient.php" method="POST">
                    <div class="modal-body">
                        <div class="rounded-container">

                            <div class="row">
                                <!-- First Column -->
                                <div class="col-md-6">
                                    <div class="form-group d-flex">
                                        <label for="first_name" class="w-50">First Name</label>
                                        <input type="text" class="form-control w-50" id="first_name" name="first_name" required>
                                    </div>

                                    <div class="form-group d-flex">
                                        <label for="ast_name" class="w-50">Last Name</label>
                                        <input type="text" class="form-control w-50" id="last_name" name="last_name" required>
                                    </div>

                                    <div class="form-group d-flex">
                                        <label for="dob" class="w-50">Date of Birth</label>
                                        <input type="date" class="form-control w-50" id="dob" name="dob" required onchange="calculateAge()">
                                    </div>

                                    <div class="form-group d-flex">
                                        <label for="gender" class="w-50">Gender</label>
                                        <select class="form-control w-50" id="gender" name="gender" required>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>

                                    <div class="form-group d-flex">
                                        <label for="age" class="w-50">Age</label>
                                        <input type="text" class="form-control w-50" id="age" name="age" readonly>
                                    </div>

                                    <div class="form-group d-flex">
                                        <label for="phone_no" class="w-50">Phone Number</label>
                                        <input type="tel" class="form-control w-50" id="phone_no" name="phone_no" required>
                                    </div>

                                    <div class="form-group d-flex">
                                        <label for="patient_no" class="w-50">Patient No.</label>
                                        <input type="text" class="form-control w-50" id="patient_no" name="patient_no" required>
                                    </div>
                                    <div class="form-group d-flex">
                                        <label for="email" class="w-50">Email</label>
                                        <input type="email" class="form-control w-50" id="email" name="email" required>
                                    </div>
                                    <div class="form-group d-flex">
                                        <label for="branch_id" class="w-50">Branch</label>
                                        <select class="form-control w-50" id="branch_id" name="branch_id" required>
                                            <option value="" disabled selected>Select Branch</option>
                                            <?php
                                            // Fetch branch details from the database
                                            $branches = $db->display_branch();  // Call the function to get branches
                                            if ($branches !== false) {
                                                foreach ($branches as $branch) {
                                                    echo "<option value='{$branch['branch_id']}'>{$branch['branch_name']}</option>";
                                                }
                                            } else {
                                                echo "<option value='' disabled>Error fetching branches</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Second Column -->
                                <div class="col-md-6">
                                    <div class="form-group d-flex">
                                        <label for="fistula_in_placement" class="w-50">Fistula In Placement</label>
                                        <select class="form-control w-50" id="fistula_in_placement" name="fistula_in_placement" required>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>

                                    <div class="form-group d-flex">
                                        <label for="diagnosis" class="w-50">Diagnosis</label>
                                        <select class="form-control w-50" id="diagnosis" name="diagnosis_id" required>
                                            <option value="" disabled selected>Select Diagnosis</option>
                                            <?php
                                            // Fetch diagnoses from the database
                                            $diagnoses = $db->display_diagnosis();  // Call the function to get diagnoses
                                            if ($diagnoses !== false) {
                                                foreach ($diagnoses as $diagnosis) {
                                                    echo "<option value='{$diagnosis['diagnosis_id']}'>ICD: {$diagnosis['ICD_10']} - {$diagnosis['diagnosis_name']}</option>";
                                                }
                                            } else {
                                                echo "<option value='' disabled>Error fetching diagnoses</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group d-flex">
                                        <label for="patient_status" class="w-50">Patient Status</label>
                                        <select class="form-control w-50" id="patient_status" name="patient_status" required>
                                            <option value="active">Active</option>
                                            <option value="semi active">Semi-active</option>
                                            <option value="not active">Newly enrolled</option>
                                            <option value="not active">Dormant</option>
                                        </select>
                                    </div>

                                    <div class="form-group d-flex">
                                        <label for="cohort" class="w-50">Cohort</label>
                                        <select class="form-control w-50" id="cohort" name="cohort_id" required>
                                            <option value="" disabled selected>Select cohort</option>
                                            <?php
                                            // Fetch cohorts from the database
                                            $cohorts = $db->display_cohort();  // Call the function to get cohorts
                                            if ($cohorts !== false) {
                                                while ($cohort = $cohorts->fetch_assoc()) {  // Fetch each cohort
                                                    echo "<option value='{$cohort['cohort_id']}'>{$cohort['cohort_name']}</option>";
                                                }
                                            } else {
                                                echo "<option value='' disabled>Error fetching cohorts</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group d-flex">
                                        <label for="scheme" class="w-50">Scheme</label>
                                        <select class="form-control w-50" id="scheme" name="scheme_id" required>
                                            <option value="" disabled selected>Select Scheme</option>
                                            <?php
                                            // Fetch payment methods from the database
                                            $scheme_methods = $db->display_schemes();

                                            if ($scheme_methods !== false) {
                                                foreach ($scheme_methods as $method) {
                                                    echo "<option value='{$method['scheme_id']}'>{$method['scheme_name']}</option>";
                                                }
                                            } else {
                                                echo "<option value='' disabled>Error fetching payment methods</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group d-flex">
                                        <label for="location" class="w-50">Location</label>
                                        <input type="text" class="form-control w-50" id="location" name="location" required>
                                    </div>

                                    <div class="form-group d-flex">
                                        <label for="route" class="w-50">Route</label>
                                        <select class="form-control w-50" id="route" name="route_id" required>
                                            <option value="" disabled selected>Select Route</option>
                                            <?php
                                            // Fetch routes from the database
                                            $routes = $db->display_route();  // Call the function to get routes
                                            if ($routes !== false) {
                                                while ($route = $routes->fetch_assoc()) {  // Fetch each route
                                                    echo "<option value='{$route['route_id']}'>{$route['route_name']}</option>";
                                                }
                                            } else {
                                                echo "<option value='' disabled>Error fetching routes</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group d-flex">
                                        <label for="payment_method" class="w-50">Payment Method</label>
                                        <select class="form-control w-50" id="payment_method" name="payment_method" required>
                                            <option value="" disabled selected>Select Payment Method</option>
                                            <?php
                                            $payment_methods = $db->display_schemes();
                                            if ($payment_methods !== false) {
                                                foreach ($payment_methods as $method) {
                                                    echo "<option value='{$method['scheme_id']}'>{$method['scheme_name']} - {$method['payment_method']}</option>";
                                                }
                                            } else {
                                                echo "<option value='' disabled>Error fetching payment methods</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-12 text-center">
                                <input type="submit" name="submit" class="btn btn-info btn-large" value="Submit" style="background-color: black; color: white;">
                                <button type="button" class="btn btn-default" data-dismiss="modal" style="background-color: grey; color: white;">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- Link to external JavaScript file -->
                <script src="toast.js"></script> <!-- The file containing the alert_toast function -->
                <script src="script.js"></script> <!-- If you have other JavaScript logic -->
            </div>
        </div>
    </div>


    <div class="modal fade" id="deleteModal<?php echo $fetchAll['patient_id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Patient Record</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this patient record?</p>
                </div>
                <div class="modal-footer">
                    <a href="deletepatient.php?id=<?php echo $fetchAll['patient_id']; ?>" class="btn btn-danger">Delete</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    </td>
    </tr>

    <div class="modal fade" id="editModal<?php echo $fetchAll['patient_id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="updatepatient.php" method="POST">
                    <div class="modal-body">
                        <div class="rounded-container">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-7 col-xl-7">
                                        <p>Patient Name:<strong><?php echo $fetchAll['firstname'] . ' ' . $fetchAll['lastname']; ?></strong></p>
                                        <p>Patient No:<strong><?php echo $fetchAll['patient_no']; ?></strong></p>
                                    </div>

                                </div>
                                <hr />

                                <div class="rounded-container">
                                    <div class="modal-body">
                                        <div class="row">
                                            <h3>Call history</h3>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <center>Call Date</center>
                                            </div>
                                            <div class="col-sm-6">
                                                <center>Call Results</center>
                                            </div>
                                        </div>
                                        <?php
                                        $calls = $db->conn->query("SELECT * FROM `calls` WHERE `patient_id`='" . $fetchAll['patient_id'] . "' ORDER BY call_date DESC");



                                        while ($fetch = $calls->fetch_array()) {
                                        ?>

                                            <div class="row">
                                                <div class="col-sm-6 p-2 pl-5" style="border-right: 1px solid black; border-bottom: 1px solid black;">
                                                    <strong><?php echo date("F d, Y", strtotime($fetch['call_date'])); ?></strong>

                                                </div>
                                                <div class="col-sm-6 p-2 pl-5" style="border-bottom: 1px solid black;">
                                                    <strong><?php echo $fetch['call_results']; ?></strong>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>

                                <div class="rounded-container">
                                    <div class="modal-body">
                                        <div class="row">
                                            <h3>Refill history</h3>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <center>Refill Date</center>
                                            </div>
                                            <div class="col-sm-6">
                                                <center>Compliance</center>
                                            </div>
                                        </div>
                                        <?php


                                        $refill = $db->conn->query("
                                            SELECT * 
                                            FROM `chronic` 
                                            WHERE `patient_id` = '" . $fetchAll['patient_id'] . "' 
                                            ORDER BY `refill_date` DESC
                                            ");


                                        while ($fetch = $refill->fetch_array()) {
                                        ?>

                                            <div class="row">
                                                <div class="col-sm-6 p-2 pl-5" style="border-right: 1px solid black; border-bottom: 1px solid black;">
                                                    <strong><?php echo date("F d, Y", strtotime($fetch['refill_date'])); ?></strong>

                                                </div>
                                                <div class="col-sm-6 p-2 pl-5" style="border-bottom: 1px solid black;">
                                                    <strong><?php echo $fetch['compliance']; ?></strong>
                                                </div>
                                            </div>
                                        <?php
                                        }

                                        ?>
                                    </div>
                                </div>

                                <div class="rounded-container">
                                    <div class="modal-body">
                                        <div class="row">

                                            <h3>Assesment history</h3>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-4">
                                                <center>Visit Date</center>
                                            </div>
                                            <div class="col-sm-4">
                                                <center>Assesment</center>

                                            </div>
                                            <div class="col-sm-4">
                                                <center>Revenue</center>

                                            </div>
                                        </div>
                                        <hr />
                                        <?php

                                        $combinedQuery = "
                                                                                SELECT * 
                                                                                FROM (
                                                                                    SELECT 'nutrition' AS source, 'Nutrition' AS assessment, visit_date AS date, revenue
                                                                                    FROM `nutrition`
                                                                                    WHERE `patient_id` = '$patient_id'
                                                                                    UNION ALL
                                                                                    SELECT 'psychosocial' AS source, 'Psychosocial' AS assessment, visit_date AS date, revenue
                                                                                    FROM `psychosocial`
                                                                                    WHERE `patient_id` = '$patient_id'
                                                                                ) AS combined
                                                                                ORDER BY date DESC
                                                                                ";


                                        $result = $db->conn->query($combinedQuery);


                                        while ($fetch = $result->fetch_array()) {
                                        ?>

                                            <div class="row">
                                                <div class="col-sm-4 p-2 pl-5" style="border-right: 1px solid black; border-bottom: 1px solid black;">
                                                    <strong><?php echo date("F d, Y", strtotime($fetch['date'])); ?></strong>
                                                </div>
                                                <div class="col-sm-4 p-2 pl-5" style="border-right: 1px solid black; border-bottom: 1px solid black;">
                                                    <strong><?php echo $fetch['assessment']; ?></strong>
                                                </div>
                                                <div class="col-sm-4 p-2 pl-5" style="border-bottom: 1px solid black;">
                                                    <strong><?php echo "Ksh. " . number_format($fetch['revenue']) ?></strong>

                                                </div>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="modal-footer">
                            <div class="col-12 text-center">

                                <button type="button" class="btn btn-default" data-dismiss="modal" style="background-color: grey; color: white;">Close</button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>
</tbody>
</table>
</div>




</div>
<script>
    $(document).ready(function() {
        $('#patientTable').DataTable({
            "searching": true,
            "paging": true,
            "ordering": true,
            "info": true,
            "language": {
                "emptyTable": "",
                "zeroRecords": ""
            },
            "pageLength": 5 // Set the number of entries to 5
        });
    });
</script>



</body>

</html>

</main>
<!-- MAIN -->






</body>

</html>