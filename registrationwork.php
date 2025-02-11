<?php
date_default_timezone_set("Etc/GMT+8");

require_once 'class.php';

$db = new db_class();


// Set the timeout period in seconds (5 minutes = 300 seconds)
$timeout_duration = 10;

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
?>
<!DOCTYPE html>
<html lang="en">


<head>
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




    <!-- My CSS -->
    <link rel="stylesheet" href="style.css">

    <title>Patients</title>
</head>

<body>


    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bxs-smile'></i>
            <span class="text">AdminHub</span>
        </a>
        <ul class="side-menu top">
            <li class="active">
                <a href="registration.php">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Registration</span>
                </a>
            </li>
            <li>
                <a href="visits.php">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Visits</span>
                </a>
            </li>
            <li>
                <a href="monitoring.php">
                    <i class='bx bxs-doughnut-chart'></i>
                    <span class="text">Monitoring</span>
                </a>
            </li>
            <li>
                <a href="checkup.php">
                    <i class='bx bxs-doughnut-chart'></i>
                    <span class="text">Check ups</span>
                </a>
            </li>
            <li>
                <a href="analytics.php">
                    <i class='bx bxs-message-dots'></i>
                    <span class="text">Analytics</span>
                </a>
            </li>
        </ul>
        <ul class="side-menu">
            <li>
                <a href="#">
                    <i class='bx bxs-cog'></i>
                    <span class="text">Settings</span>
                </a>
            </li>
            <li>
                <a href="#" class="logout">
                    <i class='bx bxs-log-out-circle'></i>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>
    </section>
    <!-- SIDEBAR -->



    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu'></i>
            <a href="#" class="nav-link">Categories</a>
            <form action="#">
                <div class="form-input">
                    <input type="search" placeholder="Search...">
                    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
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
            <ul class="box-info">
                <li>
                    <i class='bx bxs-calendar-check'></i>
                    <span class="text">
                        <h3>1020</h3>
                        <p>New Patients</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-group'></i>
                    <span class="text">
                        <h3>2834</h3>
                        <p>Active Patients</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-dollar-circle'></i>
                    <span class="text">
                        <h3>$2543</h3>
                        <p>Dormant Patients</p>
                    </span>
                </li>
            </ul>


            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Patients</h3>
                        <i class='bx bx-search'></i>
                        <i class='bx bx-filter'></i>
                        <button type="button" style="border: none; background: none; cursor: pointer;" data-toggle="modal" data-target="#patientModal" aria-label="Add Patient">
                            <i class='bx bx-plus'></i>
                        </button>
                    </div>
                    <table id="patientTable" class="display">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Patient Name</th>
                                <th>Phone Number</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch patient records from the database
                            $patients = $db->display_patient();

                            $i = 1;
                            while ($fetch = $patients->fetch_array(MYSQLI_ASSOC)) {
                            ?>
                                <tr>

                                    <td><img src="image/people.png" alt="Patient Image"></td>
                                    <td><?php echo $fetch['firstname'] . ' ' . $fetch['lastname']; ?></td>
                                    <td><?php echo $fetch['phone_no']; ?></td>
                                    <td><?php echo $fetch['age']; ?></td>
                                    <td><?php echo $fetch['gender']; ?></td>
                                    <td><span class="status <?php echo strtolower($fetch['patient_status']); ?>"><?php echo $fetch['patient_status']; ?></span></td>
                                    <td>

                                        <!-- Buttons for Edit and Delete actions -->
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $fetch['patient_id']; ?>">Edit</button>
                                            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $fetch['patient_id']; ?>">Delete</button>
                                        </div>

                                    </td>
                                </tr>

                                <!--EDIT Modal Content (This should be inside your PHP loop) -->
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
                                                        <input type="hidden" name="patient_id" value="<?php echo $fetch['patient_id']; ?>">

                                                        <h5>Patient Bio Details</h5>
                                                        <div class="row">
                                                            <!-- First Column -->

                                                            <div class="form-group d-flex">
                                                                <label for="edit_first_name" class="w-50">First Name</label>
                                                                <input type="text" class="form-control w-50" id="edit_first_name" name="first_name" value="<?php echo $fetch['firstname']; ?>" required>
                                                            </div>

                                                            <div class="form-group d-flex">
                                                                <label for="edit_last_name" class="w-50">Last Name</label>
                                                                <input type="text" class="form-control w-50" id="edit_last_name" name="last_name" value="<?php echo $fetch['lastname']; ?>" required>
                                                            </div>

                                                            <div class="form-group d-flex">
                                                                <label for="edit_dob" class="w-50">Date of Birth</label>
                                                                <input type="date" class="form-control w-50" id="edit_dob" name="dob" value="<?php echo $fetch['dob']; ?>" required>
                                                            </div>








                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-success">Save Changes</button>
                                                        </div>
                                                    </div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Delete Patient Modal -->
                                    <div class="modal fade" id="deleteModal<?php echo $fetch['patient_id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $fetch['patient_id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <h5 class="modal-title text-white" id="deleteModalLabel<?php echo $fetch['patient_id']; ?>">Delete Patient</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete this patient?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <form action="deletepatient.php" method="POST">
                                                        <input type="hidden" name="patient_id" value="<?php echo $fetch['patient_id']; ?>">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php
                            }
                                ?>


                        </tbody>
                    </table>
                </div>
            </div>


            </div>
        </main>


        <!-- MAIN -->


        <!--EDIT Modal Content (This should be inside your PHP loop) -->
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
                                            <label for="edit_last_name" class="w-50">Last Name</label>
                                            <input type="text" class="form-control w-50" id="edit_last_name" name="last_name" value="<?php echo $fetch['lastname']; ?>" required>
                                        </div>

                                        <div class="form-group d-flex">
                                            <label for="edit_dob" class="w-50">Date of Birth</label>
                                            <input type="date" class="form-control w-50" id="edit_dob" name="dob" value="<?php echo $fetch['dob']; ?>" required>
                                        </div>

                                        <div class="form-group d-flex">
                                            <select class="form-control w-50" id="edit_branch_id" name="branch_id" required>
                                                <?php
                                                // Fetch the list of branches from the database using the display_branch method
                                                $branches = $this->display_branch(); // Assuming the method is part of a class

                                                if ($branches) {
                                                    // Loop through each branch and populate the select options
                                                    foreach ($branches as $branch) {
                                                        // Check if the branch ID is the one that is being edited
                                                        $selected = ($branch['branch_id'] == $fetch['branch_id']) ? 'selected' : '';
                                                        echo "<option value='{$branch['branch_id']}' {$selected}>{$branch['branch_name']}</option>";
                                                    }
                                                } else {
                                                    // If no branches are found, show an option indicating that
                                                    echo "<option value='' disabled>No branches available</option>";
                                                }
                                                ?>
                                            </select>

                                        </div>

                                        <div class="form-group d-flex">
                                            <label for="edit_gender" class="w-50">Gender</label>
                                            <select class="form-control w-50" id="edit_gender" name="gender" required>
                                                <option value="Male" <?php echo ($fetch['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                                <option value="Female" <?php echo ($fetch['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                                <option value="Other" <?php echo ($fetch['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                            </select>
                                        </div>

                                        <div class="form-group d-flex">
                                            <label for="edit_age" class="w-50">Age</label>
                                            <input type="text" class="form-control w-50" id="edit_age" name="age" value="<?php echo $fetch['age']; ?>" readonly>
                                        </div>

                                        <div class="form-group d-flex">
                                            <label for="edit_phone_no" class="w-50">Phone Number</label>
                                            <input type="tel" class="form-control w-50" id="edit_phone_no" name="phone_no" value="<?php echo $fetch['phone_no']; ?>" required>
                                        </div>

                                        <div class="form-group d-flex">
                                            <label for="edit_patient_no" class="w-50">Patient No.</label>
                                            <input type="text" class="form-control w-50" id="edit_patient_no" name="patient_no" value="<?php echo $fetch['patient_no']; ?>" required>
                                        </div>

                                        <div class="form-group d-flex">
                                            <label for="edit_email" class="w-50">Email</label>
                                            <input type="email" class="form-control w-50" id="edit_email" name="email" value="<?php echo $fetch['email']; ?>" required>
                                        </div>

                                        <div class="form-group d-flex">
                                            <label for="edit_scheme" class="w-50">Scheme</label>
                                            <select class="form-control w-50" id="edit_scheme" name="scheme" required>
                                                <option value="" disabled>Select Scheme</option>
                                                <?php
                                                // Assuming you have a variable $selected_scheme holding the current selected scheme_id
                                                $selected_scheme = $fetch['scheme_id'];  // Replace with the actual variable from your record

                                                // Fetch payment schemes from the database
                                                $scheme_methods = $db->display_schemes();

                                                if ($scheme_methods !== false) {
                                                    // Loop through the fetched schemes
                                                    foreach ($scheme_methods as $method) {
                                                        // Check if the current method's scheme_id matches the selected scheme
                                                        $selected = ($method['scheme_id'] == $selected_scheme) ? 'selected' : '';

                                                        // Display the 'scheme' as the value and 'scheme_name' as the visible text
                                                        echo "<option value='{$method['scheme_id']}' {$selected}>{$method['scheme_name']}</option>";
                                                    }
                                                } else {
                                                    echo "<option value='' disabled>Error fetching schemes</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Second Column -->
                                    <div class="form-group d-flex">
                                        <label for="edit_branch" class="w-50">Branch</label>
                                        <input type="text" class="form-control w-50" id="edit_branch" name="branch" value="<?php echo $fetch['branch']; ?>" required>
                                    </div>

                                    <div class="form-group d-flex">
                                        <label for="edit_fistula_in_placement" class="w-50">Fistula In Placement</label>
                                        <select class="form-control w-50" id="edit_fistula_in_placement" name="fistula_in_placement" required>
                                            <option value="Yes" <?php echo ($fetch['fistula_in_placement'] == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                            <option value="No" <?php echo ($fetch['fistula_in_placement'] == 'No') ? 'selected' : ''; ?>>No</option>
                                        </select>
                                    </div>

                                    <div class="form-group d-flex">
                                        <label for="edit_diagnosis" class="w-50">Diagnosis</label>
                                        <select class="form-control w-50" id="edit_diagnosis" name="diagnosis" required>
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

                                    <!-- Add other form fields as necessary -->

                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                // Function to calculate age for Edit Modal
                function calculateEditAge() {
                    const dob = document.getElementById('edit_dob').value;
                    if (dob) {
                        const birthDate = new Date(dob);
                        const currentDate = new Date();
                        let age = currentDate.getFullYear() - birthDate.getFullYear();
                        const monthDifference = currentDate.getMonth() - birthDate.getMonth();
                        if (monthDifference < 0 || (monthDifference === 0 && currentDate.getDate() < birthDate.getDate())) {
                            age--;
                        }
                        document.getElementById('edit_age').value = age;
                    }
                }
            </script>



</body>

</html>