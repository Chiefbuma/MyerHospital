<?php
date_default_timezone_set("Etc/GMT+8");

session_start();

require_once 'class.php';

$db = new db_class();

// Set timeout duration (5 minutes)
$timeout_duration = 300;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check for session hijacking (User-Agent mismatch)
if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Check for inactivity timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();

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
    $branch_name = "Main branch"; // Default if no branch found
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
                    <a href="assesphysiotherapy.php">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                            <path d="m221-313 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-228q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm0-320 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-548q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm339 353q-17 0-28.5-11.5T520-320q0-17 11.5-28.5T560-360h280q17 0 28.5 11.5T880-320q0 17-11.5 28.5T840-280H560Zm0-320q-17 0-28.5-11.5T520-640q0-17 11.5-28.5T560-680h280q17 0 28.5 11.5T880-640q0 17-11.5 28.5T840-600H560Z" />
                        </svg>

                        <span class="text">Pysiotherapy</span>
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
        <main>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Nutrition assesment </h3>
                        <i class='bx bx-search'></i>
                        <i class='bx bx-filter'></i>
                        <button type="button" style="border: none; background: none; cursor: pointer;" data-toggle="modal" data-target="#patientModal" aria-label="Add Patient">
                            <i class='bx bx-plus'></i>
                        </button>
                    </div>
                    <table id="patientTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>

                                <th>Patient Name</th>


                                <th>Assesment</th>
                                <th>Patient Status</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch patient records from the database
                            $visit = $db->AssesNutrition();

                            $rowNumber = 0; // Initialize row number

                            while ($fetch = $visit->fetch_array(MYSQLI_ASSOC)) {
                                // Check if the assessment is not 'Chronic' and add a hidden class to hide the row
                                $hiddenClass = ($fetch['assessment'] !== 'Nutrition') ? 'hidden-row' : '';

                            ?>
                                <tr class="<?php echo $hiddenClass; ?>">

                                    <td><?php echo $rowNumber++; ?></td>


                                    <td><?php echo $fetch['firstname'] . ' ' . $fetch['lastname']; ?></td>

                                    <td><?php echo $fetch['assessment']; ?></td>
                                    <td><?php echo $fetch['patient_status']; ?></td>

                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <!--<button type="button" class="btn btn-info" data-toggle="modal" data-target="#editModal<?php echo $fetch['nutrition_id']; ?>" style="background-color: black; color: white;">Edit</button>-->

                                            <!-- Button to trigger modal for delete -->
                                            <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal<?php echo $fetch['nutrition_id']; ?>">Delete</button>
                                            <!-- Modal for Deletion Confirmation -->
                                        </div>

                                        <div class="modal fade" id="deleteModal<?php echo $fetch['nutrition_id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
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
                                                        <a href="deletenutrition.php?id=<?php echo $fetch['nutrition_id']; ?>" class="btn btn-danger">Delete</a>
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </td>



                                    <!-- Add the following CSS to hide rows -->
                                    <style>
                                        .hidden-row {
                                            display: none;
                                        }
                                    </style>

                                    <!-- Modal Content -->
                                    <!-- Modal Content -->
                                    <div class="modal fade" id="neweditModal<?php echo $fetch['patient_id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $fetch['patient_id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>

                                                <form action="savenutrition.php" method="POST">
                                                    <div class="modal-body">
                                                        <div class="rounded-container">
                                                            <!-- Hidden Field -->
                                                            <input type="hidden" name="patient_id" value="<?php echo $fetch['patient_id']; ?>">

                                                            <h5>Patient Bio Details</h5>
                                                            <div class="row">
                                                                <!-- First Column -->
                                                                <div class="col-md-6">
                                                                    <div class="form-group d-flex">
                                                                        <label for="edit_first_name" class="w-50">First Name</label>
                                                                        <input type="text" class="form-control w-50" id="edit_first_name" name="first_name" value="<?php echo $fetch['firstname']; ?>" required>
                                                                    </div>

                                                                    <div class="form-group d-flex">
                                                                        <label for="edit_phone_no" class="w-50">Phone Number</label>
                                                                        <input type="tel" class="form-control w-50" id="edit_phone_no" name="phone_no" value="<?php echo $fetch['phone_no']; ?>" required>
                                                                    </div>

                                                                    <div class="form-group d-flex">
                                                                        <label for="scheme_id" class="w-50">Scheme</label>
                                                                        <select class="form-control w-50" id="scheme_id" name="scheme_id" required>
                                                                            <option value="" disabled selected>Select scheme</option>
                                                                            <?php
                                                                            $schemes = $db->display_schemes();
                                                                            if ($schemes !== false) {
                                                                                foreach ($schemes as $scheme) {
                                                                                    echo "<option value='{$scheme['scheme_id']}'>{$scheme['scheme_name']} - {$scheme['payment_method']}</option>";
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group d-flex">
                                                                        <label for="diagnosis" class="w-50">Diagnosis</label>
                                                                        <input type="text" class="form-control w-50" id="diagnois" name="diagnosis_id" value="<?php echo $fetch['diagnosis']; ?>" required>
                                                                    </div>
                                                                    <div class="form-group d-flex">
                                                                        <label class="w-50">status</label>
                                                                        <select class="form-control w-50" name="patient_status">
                                                                            <option value="active">Active</option>
                                                                            <option value="semi active">Semi Active</option>
                                                                            <option value="not active">Not Active</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group d-flex">
                                                                        <label for="edit_patient_no" class="w-50">Patient No.</label>
                                                                        <input type="tel" class="form-control w-50" id="edit_patient_no" name="patient_no" value="<?php echo $fetch['patient_no']; ?>" required>
                                                                    </div>

                                                                </div>

                                                                <!-- Second Column -->
                                                                <div class="col-md-6">
                                                                    <div class="form-group d-flex">
                                                                        <label for="edit_last_name" class="w-50">Last Name</label>
                                                                        <input type="text" class="form-control w-50" id="edit_last_name" name="last_name" value="<?php echo $fetch['lastname']; ?>" required>
                                                                    </div>
                                                                    <div class="orm-group d-flex">
                                                                        <label for="edit_dob" class="w-50"> Date of Birth</label>
                                                                        <input type="date" class="form-control" id="edit_dob" name="dob" value="<?php echo $fetch['dob']; ?>" required>
                                                                    </div>

                                                                    <div class="form-group d-flex">
                                                                        <label for="edit_age" class="w-50">Age</label>
                                                                        <input type="text" class="form-control w-50" id="edit_age" name="age" value="<?php echo $fetch['age']; ?>" readonly>
                                                                    </div>
                                                                    <div class="orm-group d-flex">
                                                                        <label for="Payment_method" class="w-50">Payment Method</label>
                                                                        <select class="form-select" id="Payment_method" name="Payment_method" required>
                                                                            <option value="" disabled selected>Select Payment Method</option>
                                                                            <?php
                                                                            // Fetch payment methods from the database
                                                                            $payment_methods = $db->display_payment_methods();
                                                                            if ($payment_methods !== false) {
                                                                                foreach ($payment_methods as $method) {
                                                                                    echo "<option value='{$method['scheme']}'>{$method['payment_method']}</option>";
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



                                                        <div class="rounded-container">
                                                            <h5>Nutritional Assessment</h5>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group d-flex">
                                                                        <label class="w-50">Last Visit</label>
                                                                        <input type="date" class="form-control w-50" name="last_visit" required>


                                                                    </div>
                                                                    <div class="form-group d-flex">
                                                                        <label class="w-50">Next Review</label>
                                                                        <input type="date" class="form-control w-50" name="next_review" required>
                                                                    </div>
                                                                    <div class="form-group d-flex">
                                                                        <label class="w-50">Muscle Mass</label>
                                                                        <input type="text" class="form-control w-50" name="muscle_mass" required>
                                                                    </div>
                                                                    <div class="form-group d-flex">
                                                                        <label class="w-50">Bone Mass</label>
                                                                        <input type="text" class="form-control w-50" name="bone_mass" required>
                                                                    </div>
                                                                    <div class="form-group d-flex">
                                                                        <label class="w-50">Weight</label>
                                                                        <input type="text" class="form-control w-50" name="weight" required>
                                                                    </div>
                                                                    <div class="form-group d-flex">
                                                                        <label class="w-50">BMI</label>
                                                                        <input type="text" class="form-control w-50" name="BMI" required>
                                                                    </div>
                                                                    <div class="form-group d-flex">
                                                                        <label class="w-50">Subcutaneous Fat</label>
                                                                        <input type="text" class="form-control w-50" name="subcutaneous_fat" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">

                                                                    <div class="form-group d-flex">
                                                                        <label class="w-50">Visceral Fat</label>
                                                                        <input type="text" class="form-control w-50" name="visceral_fat" required>
                                                                    </div>
                                                                    <div class="form-group d-flex">
                                                                        <label class="w-50">Weight Remarks</label>
                                                                        <select class="form-control w-50" name="weight_remarks" required>
                                                                            <option value="Normal">Normal</option>
                                                                            <option value="Overweight">Overweight</option>
                                                                            <option value="Obesity type 1">Obesity type 1</option>
                                                                            <option value="Obesity type 2">Obesity type 2</option>
                                                                            <option value="Obesity type 3">Obesity type 3</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group d-flex">
                                                                        <label class="w-50">Physical Activity</label>
                                                                        <select class="form-control w-50" name="physical_activity" required>
                                                                            <option value="active">Active</option>
                                                                            <option value="semi active">Semi Active</option>
                                                                            <option value="not active">Not Active</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group d-flex">
                                                                        <label class="w-50">Meal Plan Setup</label>
                                                                        <select class="form-control w-50" name="meal_plan_set_up" required>
                                                                            <option value="set up">Set Up</option>
                                                                            <option value="not set up">Not Set Up</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group d-flex">
                                                                        <label class="w-50">Nutrition Adherence</label>
                                                                        <select class="form-control w-50" name="nutrition_adherence" required>
                                                                            <option value="adherant">Adherant</option>
                                                                            <option value="not adherant">Not Adherant</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group d-flex">
                                                                        <label class="w-50">Nutrition Remarks</label>
                                                                        <input type="text" class="form-control w-50" name="nutrition_assessment_remarks" required>
                                                                    </div>
                                                                    <div class="form-group d-flex">
                                                                        <label class="w-50">Revenue</label>
                                                                        <input type="number" class="form-control w-50" name="revenue" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <div class="col-12 text-center">
                                                            <input type="submit" name="update" class="btn btn-info btn-large" value="Submit" style="background-color: black; color: white;">

                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                    <?php } ?>
                        </tbody>
                    </table>
                </div>



                <div class="todo">
                    <div class="todo">
                        <div class="head">
                            <h3>Assesment List</h3>

                        </div>

                        <!-- Right-aligned search input box -->
                        <div style="display: flex; justify-content: flex-end; margin-bottom: 5px;">
                            <form action="#" style="display: flex; align-items: center; gap: 5px;">
                                <input type="search" placeholder="Search..." id="searchInput" onkeyup="filterList()" style="padding: 10px; border-radius: 5px; border: 1px solid #ccc; width: 200px;">
                                <button type="submit" class="search-btn" style="padding: 10px 15px; border-radius: 5px; border: none; background-color: #007bff; color: white; cursor: pointer;">
                                    <i class='bx bx-search'></i>
                                </button>
                            </form>
                        </div>


                        <div style="max-height: 300px; overflow-y: auto; scrollbar-width: none; -ms-overflow-style: none; padding: 15px; border-radius: 5px;">
                            <ul class="todo-list" id="todoList">
                                <?php
                                // Fetch patient records from the database
                                $calls = $db->display_calls_patient();
                                $displayedPatients = []; // Array to track displayed patient IDs

                                while ($fetch = $calls->fetch_array(MYSQLI_ASSOC)) {
                                    // Check if the patient_id has already been displayed
                                    if (in_array($fetch['patient_id'], $displayedPatients)) {
                                        continue; // Skip this record if it has already been displayed
                                    }

                                    // Add the patient_id to the displayed array
                                    $displayedPatients[] = $fetch['patient_id'];

                                    // Calculate days since call_date
                                    $callDate = new DateTime($fetch['call_date']); // Assuming call_date is in 'Y-m-d' format
                                    $currentDate = new DateTime();
                                    $daysDifference = $callDate->diff($currentDate)->days;

                                    // Determine the background color based on the ranges
                                    if ($daysDifference < 28) {
                                        $bgColor = "background-color: #d4edda;"; // Light green
                                    } elseif ($daysDifference >= 28 && $daysDifference <= 60) {
                                        $bgColor = "background-color: #fff3cd;"; // Light yellow
                                    } elseif ($daysDifference > 60 && $daysDifference <= 90) {
                                        $bgColor = "background-color: #ffeeba;"; // Light orange
                                    } else {
                                        $bgColor = "background-color: #f8d7da;"; // Light red
                                    }
                                ?>
                                    <li class="completed" style="<?php echo $bgColor; ?>">
                                        <p>
                                            <strong>Patient:</strong> <?php echo htmlspecialchars($fetch['firstname'] . ' ' . $fetch['lastname']) ?> <br>
                                            <strong>Phone:</strong> <?php echo htmlspecialchars($fetch['phone_no']); ?> <br>
                                            <strong>Patient No:</strong> <?php echo htmlspecialchars($fetch['patient_no']); ?> <br>

                                        <div class="d-flex gap-2">
                                            <!-- Button to trigger the modal -->
                                            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#editModal<?php echo htmlspecialchars($fetch['patient_id']); ?>">Asses</button>
                                        </div>
                                    </li>

                                    <!-- Modal for editing the patient details -->
                                    <div class="modal fade" id="editModal<?php echo htmlspecialchars($fetch['patient_id']); ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo htmlspecialchars($fetch['patient_id']); ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <form action="savenutrition.php" method="POST">
                                                        <div class="modal-body">
                                                            <div class="rounded-container">
                                                                <!-- Hidden Field -->
                                                                <input type="hidden" name="patient_id" value="<?php echo $fetch['patient_id']; ?>">

                                                                <h5>Patient Bio Details</h5>
                                                                <div class="row">
                                                                    <!-- First Column -->
                                                                    <div class="col-md-6">
                                                                        <div class="form-group d-flex">
                                                                            <label for="edit_first_name" class="w-50">First Name</label>
                                                                            <input type="hidden" name="patient_id" value="<?php echo $fetch['patient_id']; ?>">
                                                                            <!-- Hidden Field -->
                                                                            <input type="hidden" name="patient_id" value="<?php echo $fetch['patient_id']; ?>">
                                                                            <input type="text" class="form-control w-50" id="edit_first_name" name="first_name" value="<?php echo $fetch['firstname']; ?>" readonly>
                                                                        </div>

                                                                        <div class="form-group d-flex">
                                                                            <label for="edit_phone_no" class="w-50">Phone Number</label>
                                                                            <input type="tel" class="form-control w-50" id="edit_phone_no" name="phone_no" value="<?php echo $fetch['phone_no']; ?>" readonly>
                                                                        </div>



                                                                        <!-- New Revenue Field -->


                                                                        <?php
                                                                        // Fetch all diagnoses using the display_diagnosis function
                                                                        $diagnoses = $db->display_diagnosis();

                                                                        // Find the diagnosis name that matches the diagnosis_id from the current record
                                                                        $diagnosis_name = '';
                                                                        foreach ($diagnoses as $diagnosis) {
                                                                            if ($diagnosis['diagnosis_id'] == $fetch['diagnosis_id']) {
                                                                                $diagnosis_name = $diagnosis['diagnosis_name'];  // Get the corresponding diagnosis name
                                                                                break; // Exit the loop once the matching diagnosis is found
                                                                            }
                                                                        }
                                                                        ?>



                                                                        <div class="form-group d-flex">
                                                                            <label for="diagnosis" class="w-50">Diagnosis</label>
                                                                            <input type="text" class="form-control w-50" id="diagnosis" name="diagnosis_id" value="<?php echo htmlspecialchars($diagnosis_name); ?>" readonly>
                                                                        </div>

                                                                        <div class="form-group d-flex">
                                                                            <label for="edit_patient_no" class="w-50">Patient No.</label>
                                                                            <input type="tel" class="form-control w-50" id="edit_patient_no" name="patient_no" value="<?php echo $fetch['patient_no']; ?>" readonly>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Second Column -->
                                                                    <div class="col-md-6">
                                                                        <div class="form-group d-flex">
                                                                            <label for="edit_last_name" class="w-50">Last Name</label>
                                                                            <input type="text" class="form-control w-50" id="edit_last_name" name="last_name" value="<?php echo $fetch['lastname']; ?>" readonly>
                                                                        </div>

                                                                        <div class="form-group d-flex">
                                                                            <label for="edit_dob" class="w-50">Date of Birth</label>
                                                                            <input type="date" class="form-control w-50" id="edit_dob" name="dob" value="<?php echo $fetch['dob']; ?>" readonly>
                                                                        </div>

                                                                        <div class="form-group d-flex">
                                                                            <label for="edit_age" class="w-50">Age</label>
                                                                            <input type="text" class="form-control w-50" id="edit_age" name="age" value="<?php echo $fetch['age']; ?>" readonly>
                                                                        </div>


                                                                        <div class="form-group d-flex">
                                                                            <label class="w-50">Status</label>
                                                                            <input type="patient_status" class="form-control w-50" id="edit_patient_status" name="patient_status" value="<?php echo $fetch['patient_status']; ?>" readonly>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="rounded-container">
                                                                <h5>Nutritional Assessment</h5>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group d-flex">
                                                                            <label class="w-50">Visit Date</label>
                                                                            <input type="date" class="form-control w-50" name="visit_date" required>
                                                                        </div>

                                                                        <div class="form-group d-flex">
                                                                            <label class="w-50">Next Review</label>
                                                                            <input type="date" class="form-control w-50" name="next_review" required>
                                                                        </div>
                                                                        <div class="form-group d-flex">
                                                                            <label class="w-50">Muscle Mass</label>
                                                                            <input type="number" class="form-control w-50" name="muscle_mass" min="1" max="100" required>
                                                                        </div>
                                                                        <div class="form-group d-flex">
                                                                            <label class="w-50">Bone Mass</label>
                                                                            <input type="number" class="form-control w-50" name="bone_mass" min="0.5" max="15" required>
                                                                        </div>
                                                                        <div class="form-group d-flex">
                                                                            <label class="w-50">Weight</label>
                                                                            <input type="number" class="form-control w-50" name="weight" min="1" max="200" required>
                                                                        </div>
                                                                        <div class="form-group d-flex">
                                                                            <label class="w-50">BMI</label>
                                                                            <input type="text" class="form-control w-50" name="BMI" required>
                                                                        </div>
                                                                        <div class="form-group d-flex">
                                                                            <label class="w-50">Subcutaneous Fat</label>
                                                                            <input type="number" class="form-control w-50" name="subcutaneous_fat" min="1" max="100" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">

                                                                        <div class="form-group d-flex">
                                                                            <label class="w-50">Visceral Fat</label>
                                                                            <input type="text" class="form-control w-50" name="visceral_fat" required>
                                                                        </div>
                                                                        <div class="form-group d-flex">
                                                                            <label class="w-50">Weight Remarks</label>
                                                                            <select class="form-control w-50" name="weight_remarks" required>
                                                                                <option value=""></option>
                                                                                <option value="Normal">Normal</option>
                                                                                <option value="Overweight">Overweight</option>
                                                                                <option value="Obesity type 1">Obesity type 1</option>
                                                                                <option value="Obesity type 2">Obesity type 2</option>
                                                                                <option value="Obesity type 3">Obesity type 3</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group d-flex">
                                                                            <label class="w-50">Physical Activity</label>
                                                                            <select class="form-control w-50" name="physical_activity" required>
                                                                                <option value=""></option>
                                                                                <option value="active">Active</option>
                                                                                <option value="semi active">Semi Active</option>
                                                                                <option value="not active">Not Active</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group d-flex">
                                                                            <label class="w-50">Meal Plan Setup</label>
                                                                            <select class="form-control w-50" name="meal_plan_set_up" required>
                                                                                <option value=""></option>
                                                                                <option value="set up">Set Up</option>
                                                                                <option value="not set up">Not Set Up</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group d-flex">
                                                                            <label class="w-50">Nutrition Adherence</label>
                                                                            <select class="form-control w-50" name="nutrition_adherence" required>
                                                                                <option value=""></option>
                                                                                <option value="adherant">Adherant</option>
                                                                                <option value="not adherant">Not Adherant</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group d-flex">
                                                                            <label class="w-50">Nutrition Remarks</label>
                                                                            <input type="text" class="form-control w-50" name="nutrition_assessment_remarks" required>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Revenue -->


                                                            <div class="rounded-container">
                                                                <div class="row">
                                                                    <!-- First Column - Revenue -->
                                                                    <div class="col-md-6">
                                                                        <div class="form-group d-flex">
                                                                            <label for="scheme_id" class="w-50">Scheme</label>
                                                                            <select class="form-control w-50" id="scheme_id" name="scheme_id" required>
                                                                                <option value=""></option>
                                                                                <?php
                                                                                $schemes = $db->display_schemes();
                                                                                if ($schemes !== false) {
                                                                                    foreach ($schemes as $scheme) {
                                                                                        echo "<option value='{$scheme['scheme_id']}'>{$scheme['scheme_name']} - {$scheme['payment_method']}</option>";
                                                                                    }
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Third Column - Payment Method -->
                                                                    <div class="col-md-6">
                                                                        <div class="form-group d-flex">
                                                                            <label for="revenue" class="w-50">Revenue</label>
                                                                            <input type="number" class="form-control w-50" id="revenue" name="revenue" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <div class="col-12 text-center">
                                                                <!-- Submit Button -->
                                                                <button type="submit" name="submit" class="btn btn-info btn-large" style="background-color: black; color: white;">
                                                                    Submit
                                                                </button>

                                                                <!-- Close Form Button -->
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                <li id="noData" style="display: none; text-align: center;">No data found</li>
                            </ul>
                        </div>

                        <script>
                            function filterList() {
                                var input, filter, ul, li, p, i, txtValue, found;
                                input = document.getElementById('searchInput');
                                filter = input.value.toUpperCase();
                                ul = document.getElementById("todoList");
                                li = ul.getElementsByTagName('li');
                                found = false;

                                for (i = 0; i < li.length; i++) {
                                    if (li[i].id !== 'noData') { // Skip the "No data found" element
                                        p = li[i].getElementsByTagName("p")[0];
                                        txtValue = p.textContent || p.innerText;
                                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                            li[i].style.display = "";
                                            found = true;
                                        } else {
                                            li[i].style.display = "none";
                                        }
                                    }
                                }

                                // Show "No data found" message if no items are found
                                document.getElementById('noData').style.display = found ? 'none' : 'block';
                            }
                        </script>
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




        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->



</body>

</html>