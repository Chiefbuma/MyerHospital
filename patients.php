<?php
date_default_timezone_set("Etc/GMT+8");

session_start();

require_once 'class.php';

$db = new db_class();
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

<!-- Include DataTables JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- Include jQuery (required for DataTables) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>



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
            <span class="text">Menu</span>
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
            </ul>
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
            <i class='bx bx-menu'></i>
            <a href="#" class="nav-link">Categories</a>
            <form action="#">
                <div class="form-input">

                </div>
            </form>
            <input type="checkbox" id="switch-mode" hidden>
            <label for="switch-mode" class="switch-mode"></label>
            <a href="#" class="notification">
                <i class='bx bxs-bell'></i>
                <span class="num">8</span>
            </a>
            <a href="#" class="profile">
                <img src="img/people.png">
            </a>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>

            <div class="table-data" style="width: 70%;">
                <div class="order">
                    <div class="head">
                        <h3>Patients Registration</h3>


                        <button type="button" class="btn btn-info" data-toggle="modal" style="background-color: black; color: white;" data-target="#patientModal" aria-label="Add Patient">Register</button>


                        </button>
                    </div>
                    <table id="patientTable" class="table table-bordered">
                        <thead>
                            <tr>

                                <th>Patient Name</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Patient No</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch patient records from the database
                            $patients = $db->display_patient();
                            foreach ($patients as $fetch) {
                            ?>
                                <tr>

                                    <td><?php echo $fetch['firstname'] . ' ' . $fetch['lastname']; ?></td> <!-- Concatenated First and Last Name -->
                                    <td><?php echo $fetch['age']; ?></td>
                                    <td><?php echo $fetch['gender']; ?></td>
                                    <td><?php echo $fetch['patient_no']; ?></td>
                                    <td><?php echo $fetch['location']; ?></td>
                                    <td><?php echo $fetch['patient_status']; ?></td>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#editModal<?php echo $fetch['patient_id']; ?>" style="background-color: black; color: white;">Edit</button>

                                            <!-- Button to trigger modal for delete -->
                                            <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal<?php echo $fetch['patient_id']; ?>">Delete</button>
                                            <!-- Modal for Deletion Confirmation -->
                                        </div>
                                        <div class="modal fade" id="deleteModal<?php echo $fetch['patient_id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
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
                                                        <a href="deletepatient.php?id=<?php echo $fetch['patient_id']; ?>" class="btn btn-danger">Delete</a>
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                </tr>

                                <div class="modal fade" id="editModal<?php echo $fetch['patient_id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>

                                            <form action="updatepatient.php" method="POST">
                                                <div class="modal-body">
                                                    <div class="rounded-container">
                                                        <!-- Hidden Field -->
                                                        <input type="hidden" name="patient_id" id="patient_id_<?php echo $fetch['patient_id']; ?>" value="<?php echo $fetch['patient_id']; ?>">



                                                        <div class="row">
                                                            <!-- First Column -->
                                                            <div class="col-md-6">
                                                                <div class="form-group d-flex">
                                                                    <label for="edit_first_name_<?php echo $fetch['patient_id']; ?>" class="w-50">First Name</label>
                                                                    <input type="text" class="form-control w-50" id="edit_first_name_<?php echo $fetch['patient_id']; ?>" name="first_name" value="<?php echo $fetch['firstname']; ?>" required>
                                                                </div>

                                                                <div class="form-group d-flex">
                                                                    <label for="edit_last_name_<?php echo $fetch['patient_id']; ?>" class="w-50">Last Name</label>
                                                                    <input type="text" class="form-control w-50" id="edit_last_name_<?php echo $fetch['patient_id']; ?>" name="last_name" value="<?php echo $fetch['lastname']; ?>" required>
                                                                </div>

                                                                <div class="form-group d-flex">
                                                                    <label for="edit_dob_<?php echo $fetch['patient_id']; ?>" class="w-50">Date of Birth</label>
                                                                    <input type="date" class="form-control w-50" id="edit_dob_<?php echo $fetch['patient_id']; ?>" name="dob" value="<?php echo $fetch['dob']; ?>" required>
                                                                </div>

                                                                <div class="form-group d-flex">
                                                                    <label for="edit_branch" class="w-50">Branch</label>
                                                                    <select class="form-control w-50" id="edit_branch" name="branch_id" required>
                                                                        <option value="" disabled>Select Branch</option>
                                                                        <?php
                                                                        // Assuming you have a variable $selected_scheme holding the current selected scheme_id
                                                                        $selected_scheme = $fetch['branch_id'];  // Replace with the actual variable from your record

                                                                        // Fetch payment schemes from the database
                                                                        $scheme_methods = $db->display_branch();

                                                                        if ($scheme_methods !== false) {
                                                                            // Loop through the fetched schemes
                                                                            foreach ($scheme_methods as $method) {
                                                                                // Check if the current method's scheme_id matches the selected scheme
                                                                                $selected = ($method['branch_id'] == $selected_scheme) ? 'selected' : '';

                                                                                // Display the 'scheme' as the value and 'scheme_name' as the visible text
                                                                                echo "<option value='{$method['branch_id']}' {$selected}>{$method['branch_name']}</option>";
                                                                            }
                                                                        } else {
                                                                            echo "<option value='' disabled>Error fetching schemes</option>";
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group d-flex">
                                                                    <label for="edit_gender_<?php echo $fetch['patient_id']; ?>" class="w-50">Gender</label>
                                                                    <select class="form-control w-50" id="edit_gender_<?php echo $fetch['patient_id']; ?>" name="gender" required>
                                                                        <option value="Male" <?php echo ($fetch['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                                                        <option value="Female" <?php echo ($fetch['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                                                        <option value="Other" <?php echo ($fetch['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                                                    </select>
                                                                </div>

                                                                <div class="form-group d-flex">
                                                                    <label for="edit_age_<?php echo $fetch['patient_id']; ?>" class="w-50">Age</label>
                                                                    <input type="text" class="form-control w-50" id="edit_age_<?php echo $fetch['patient_id']; ?>" name="age" value="<?php echo $fetch['age']; ?>" readonly>
                                                                </div>
                                                                <div class="form-group d-flex">
                                                                    <label for="edit_phone_no_<?php echo $fetch['patient_id']; ?>" class="w-50">Phone Number</label>
                                                                    <input type="tel" class="form-control w-50" id="edit_phone_no_<?php echo $fetch['patient_id']; ?>" name="phone_no" value="<?php echo $fetch['phone_no']; ?>" required>
                                                                </div>

                                                                <div class="form-group d-flex">
                                                                    <label for="edit_patient_no_<?php echo $fetch['patient_id']; ?>" class="w-50">Patient No.</label>
                                                                    <input type="text" class="form-control w-50" id="edit_patient_no_<?php echo $fetch['patient_id']; ?>" name="patient_no" value="<?php echo $fetch['patient_no']; ?>" required>
                                                                </div>



                                                            </div>

                                                            <!-- Second Column -->
                                                            <div class="col-md-6">
                                                                <div class="form-group d-flex">
                                                                    <label for="edit_email_<?php echo $fetch['patient_id']; ?>" class="w-50">Email</label>
                                                                    <input type="email" class="form-control w-50" id="edit_email_<?php echo $fetch['patient_id']; ?>" name="email" value="<?php echo $fetch['email']; ?>" required>
                                                                </div>


                                                                <div class="form-group d-flex">
                                                                    <label for="edit_scheme_<?php echo $fetch['patient_id']; ?>" class="w-50">Scheme</label>
                                                                    <select class="form-control w-50" id="edit_scheme_<?php echo $fetch['patient_id']; ?>" name="scheme_id" required>
                                                                        <option value="" disabled>Select Scheme</option>
                                                                        <?php
                                                                        $selected_scheme = $fetch['scheme_id']; // Replace with the actual variable from your record
                                                                        $scheme_methods = $db->display_schemes();

                                                                        if ($scheme_methods !== false) {
                                                                            foreach ($scheme_methods as $method) {
                                                                                $selected = ($method['scheme_id'] == $selected_scheme) ? 'selected' : '';
                                                                                echo "<option value='{$method['scheme_id']}' {$selected}>{$method['scheme_name']}</option>";
                                                                            }
                                                                        } else {
                                                                            echo "<option value='' disabled>Error fetching schemes</option>";
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>


                                                                <div class="form-group d-flex">
                                                                    <label for="edit_diagnosis_<?php echo $fetch['patient_id']; ?>" class="w-50">Diagnosis</label>
                                                                    <select class="form-control w-50" id="edit_diagnosis_<?php echo $fetch['patient_id']; ?>" name="diagnosis_id" required>
                                                                        <option value="" disabled>Select Diagnosis</option>
                                                                        <?php
                                                                        $diagnoses = $db->display_diagnosis();
                                                                        if ($diagnoses !== false) {
                                                                            foreach ($diagnoses as $diagnosis) {
                                                                                $selected = ($diagnosis['diagnosis_id'] == $fetch['diagnosis_id']) ? 'selected' : '';
                                                                                echo "<option value='{$diagnosis['diagnosis_id']}' {$selected}>ICD: {$diagnosis['ICD_10']} - {$diagnosis['diagnosis_name']}</option>";
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>

                                                                <div class="form-group d-flex">
                                                                    <label for="edit_patient_status" class="w-50">Patient Status</label>
                                                                    <select class="form-control w-50" id="edit_patient_status" name="patient_status" required>
                                                                        <option value="Active" <?php echo ($fetch['patient_status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                                                        <option value="Semi-active" <?php echo ($fetch['patient_status'] == 'Semi-active') ? 'selected' : ''; ?>>Semi-active</option>
                                                                        <option value="Newly enrolled" <?php echo ($fetch['patient_status'] == 'Newly enrolled') ? 'selected' : ''; ?>>Newly enrolled</option>
                                                                        <option value="Dormant" <?php echo ($fetch['patient_status'] == 'Dormant') ? 'selected' : ''; ?>>Dormant</option>
                                                                    </select>
                                                                </div>


                                                                <div class="form-group d-flex">
                                                                    <label for="edit_cohort_<?php echo $fetch['patient_id']; ?>" class="w-50">Cohort</label>
                                                                    <select class="form-control w-50" id="edit_cohort_<?php echo $fetch['patient_id']; ?>" name="cohort_id" required>
                                                                        <option value="" disabled>Select Cohort</option>
                                                                        <?php
                                                                        // Fetch the route_id for the selected patient
                                                                        $cohort_id = $fetch['cohort_id']; // Assuming $fetch contains the patient data, including route_id

                                                                        // Fetch routes from the database
                                                                        $cohorts = $db->display_cohort();  // Call the function to get routes
                                                                        if ($cohorts !== false) {
                                                                            foreach ($cohorts as $cohort) {
                                                                                // Check if the current route matches the route_id for the patient
                                                                                $selected = ($cohort['route_id'] == $cohort_id) ? 'selected' : '';
                                                                                echo "<option value='{$cohort['cohort_id']}' {$selected}>{$cohort['cohort_name']}</option>";
                                                                            }
                                                                        } else {
                                                                            echo "<option value='' disabled>Error fetching cohorts</option>";
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>


                                                                <div class="form-group d-flex">
                                                                    <label for="edit_route_<?php echo $fetch['patient_id']; ?>" class="w-50">Route</label>
                                                                    <select class="form-control w-50" id="edit_route_<?php echo $fetch['patient_id']; ?>" name="route_id" required>
                                                                        <option value="" disabled>Select Route</option>
                                                                        <?php
                                                                        // Fetch the route_id for the selected patient
                                                                        $route_id = $fetch['route_id']; // Assuming $fetch contains the patient data, including route_id

                                                                        // Fetch routes from the database
                                                                        $routes = $db->display_route();  // Call the function to get routes
                                                                        if ($routes !== false) {
                                                                            foreach ($routes as $route) {
                                                                                // Check if the current route matches the route_id for the patient
                                                                                $selected = ($route['route_id'] == $route_id) ? 'selected' : '';
                                                                                echo "<option value='{$route['route_id']}' {$selected}>{$route['route_name']}</option>";
                                                                            }
                                                                        } else {
                                                                            echo "<option value='' disabled>Error fetching routes</option>";
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group d-flex">
                                                                    <label for="edit_location_<?php echo $fetch['patient_id']; ?>" class="w-50">Location</label>
                                                                    <input type="text" class="form-control w-50" id="edit_location_<?php echo $fetch['patient_id']; ?>" name="location" value="<?php echo $fetch['location']; ?>" required>
                                                                </div>

                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <div class="col-12 text-center">
                                                        <input type="submit" name="update" class="btn btn-info btn-large" value="Submit" style="background-color: black; color: white;">
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

        </main>
        <!-- MAIN -->

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
    <script>
        // Function to calculate age from date of birth
        function calculateAge() {
            var dob = document.getElementById('dob').value;
            if (dob) {
                var dobDate = new Date(dob);
                var today = new Date();

                var age = today.getFullYear() - dobDate.getFullYear();
                var month = today.getMonth() - dobDate.getMonth();

                // Adjust age if the birthday hasn't occurred yet this year
                if (month < 0 || (month === 0 && today.getDate() < dobDate.getDate())) {
                    age--;
                }

                document.getElementById('age').value = age;
            }
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


</body>

</html>