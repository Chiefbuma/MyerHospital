<?php
require_once 'config.php';

class db_class extends db_connect
{

	public function __construct()
	{
		$this->connect();
	}


	/* User Function */
	/* User Function */

	public function add_user($username, $password, $firstname, $lastname, $cohort, $branch)
	{
		// Prepare the SQL query
		$query = $this->conn->prepare("
        INSERT INTO `users` (`username`, `password`, `firstname`, `lastname`, `cohort_id`, `branch_id`) 
        VALUES(?, ?, ?, ?, ?, ?)
    ") or die($this->conn->error);

		// Bind parameters
		$query->bind_param("ssssii", $username, $password, $firstname, $lastname, $cohort, $branch);

		// Execute the query and handle the result
		if ($query->execute()) {
			$query->close();
			$this->conn->close();
			return true;
		} else {
			$query->close();
			$this->conn->close();
			return false;
		}
	}

	/* Update User Function */

	public function update_user($user_id, $username, $password, $firstname, $lastname, $cohort, $branch)
	{
		// Prepare the SQL query
		$query = $this->conn->prepare("
        UPDATE `users` 
        SET `username`=?, `password`=?, `firstname`=?, `lastname`=?, `cohort_d`=?, `branch_id`=? 
        WHERE `user_id`=?
    ") or die($this->conn->error);

		// Bind parameters
		$query->bind_param("ssssiii", $username, $password, $firstname, $lastname, $cohort, $branch, $user_id);

		// Execute the query and handle the result
		if ($query->execute()) {
			$query->close();
			$this->conn->close();
			return true;
		} else {
			$query->close();
			$this->conn->close();
			return false;
		}
	}


	public function login($email, $password)
	{
		// Prepare the SQL query to get the user record based on the email
		$query = $this->conn->prepare("SELECT * FROM `users` WHERE `email` = ? LIMIT 1");
		$query->bind_param("s", $email); // Bind the email parameter

		if ($query->execute()) {
			$result = $query->get_result();

			// Check if user exists
			if ($result->num_rows > 0) {
				$fetch = $result->fetch_array();

				// Return user data, including password hash
				return array(
					'user_id' => isset($fetch['id']) ? $fetch['id'] : 0,
					'password_hash' => isset($fetch['password_hash']) ? $fetch['password_hash'] : 0,
					'count' => 1
				);
			} else {
				// User not found
				return array(
					'user_id' => 0,
					'password_hash' => 0,
					'count' => 0
				);
			}
		}

		// In case of query failure, return default values
		return array(
			'user_id' => 0,
			'password_hash' => 0,
			'count' => 0
		);
	}


	public function user_acc($user_id)
	{
		$query = $this->conn->prepare("SELECT * FROM `users` WHERE `id`='$user_id'") or die($this->conn->error);
		if ($query->execute()) {
			$result = $query->get_result();

			$valid = $result->num_rows;

			$fetch = $result->fetch_array();

			return $fetch['email'] . " " . $fetch['email'];
		}
	}

	function hide_pass($str)
	{
		$len = strlen($str);

		return str_repeat('*', $len);
	}




	/* Display User Function */
	public function display_user()
	{
		// Prepare the SQL query to fetch all users and join with the 'branch' and 'cohort' tables
		$query = $this->conn->prepare("
			SELECT users.*, branch.*, cohort.* 
			FROM `users`
			LEFT JOIN `branch` ON users.branch_id = branch.branch_id
			LEFT JOIN `cohort` ON users.cohort_id = cohort.cohort_id
		") or die($this->conn->error);

		// Execute the query
		if ($query->execute()) {
			// Get the result of the query
			$result = $query->get_result();

			// Fetch all the rows from the result set
			$users = $result->fetch_all(MYSQLI_ASSOC);

			// Free the result set to free up memory
			$result->free();

			// Close the query
			$query->close();

			// Return the users array
			return $users;
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}
	/* Display User Function */
	public function user_details($user_id)
	{
		// Prepare the SQL query to fetch role, branch_id, and cohort_id for the given user_id
		$query = $this->conn->prepare("
        SELECT 
            users.role, 
            users.branch_id, 
            users.cohort_id,
            branch.branch_name, 
            cohort.cohort_name
        FROM `users`
        LEFT JOIN `branch` ON users.branch_id = branch.branch_id
        LEFT JOIN `cohort` ON users.cohort_id = cohort.cohort_id
        WHERE users.id = ?
    ") or die($this->conn->error);

		// Bind the user_id to the query
		$query->bind_param("i", $user_id);

		// Execute the query
		if ($query->execute()) {
			// Get the result of the query
			$result = $query->get_result();

			// Fetch the row from the result set
			$user = $result->fetch_assoc();

			// Free the result set to free up memory
			$result->free();

			// Close the query
			$query->close();

			// Return the user details
			return $user;
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}




	/* Delete User Function */

	public function delete_user($user_id)
	{
		// Prepare the SQL query to delete the user by user_id
		$query = $this->conn->prepare("DELETE FROM `user` WHERE `user_id` = ?") or die($this->conn->error);

		// Bind the user_id parameter to the query
		$query->bind_param("i", $user_id); // "i" indicates an integer parameter

		// Execute the query
		if ($query->execute()) {
			// Close the query and connection
			$query->close();
			$this->conn->close();
			return true; // Return true if the deletion was successful
		} else {
			// Close the query and connection in case of an error
			$query->close();
			$this->conn->close();
			return false; // Return false if the deletion failed
		}
	}

	/* Insert Patient Function */

	// Insert Patient Function
	public function insert_patient($firstname, $lastname, $dob, $gender, $age, $location, $route_id, $phone_no, $email, $patient_no, $diagnosis_id, $patient_status, $branch_id, $scheme_id, $cohort_id)
	{
		// Prepare the SQL query to insert a new patient record (no patient_id included)
		$query = $this->conn->prepare("INSERT INTO `patient` (`firstname`, `lastname`, `dob`, `gender`, `age`, `location`, `route_id`, `phone_no`, `email`, `patient_no`, `diagnosis_id`, `patient_status`, `branch_id`, `scheme_id`, `cohort_id`) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)")
			or die($this->conn->error);

		// Bind parameters (removed patient_id)
		$query->bind_param("ssssisssssssiss", $firstname, $lastname, $dob, $gender, $age, $location, $route_id, $phone_no, $email, $patient_no, $diagnosis_id, $patient_status, $branch_id, $scheme_id, $cohort_id);

		// Execute the query
		if ($query->execute()) {
			// Close the query
			$query->close();
			return true; // Return true if the insert is successful
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}


	public function update_patient($patient_id, $firstname, $lastname, $dob, $gender, $age, $location, $route_id, $phone_no, $email, $patient_no, $diagnosis_id, $patient_status, $branch_id, $scheme_id, $cohort_id)
	{
		// Prepare the SQL query to update a patient record
		$query = $this->conn->prepare("
        UPDATE `patient` 
        SET 
            `firstname` = ?, 
            `lastname` = ?, 
            `dob` = ?, 
            `gender` = ?, 
            `age` = ?, 
            `location` = ?, 
            `route_id` = ?, 
            `phone_no` = ?, 
            `email` = ?, 
            `patient_no` = ?, 
            `diagnosis_id` = ?, 
            `patient_status` = ?, 
            `branch_id` = ?, 
            `scheme_id` = ?, 
            `cohort_id` = ? 
        WHERE 
            `patient_id` = ?
    ") or die($this->conn->error);

		// Bind parameters
		$query->bind_param(
			"ssssisissssssiii",
			$firstname,
			$lastname,
			$dob,
			$gender,
			$age,
			$location,
			$route_id,
			$phone_no,
			$email,
			$patient_no,
			$diagnosis_id,
			$patient_status,
			$branch_id,
			$scheme_id,
			$cohort_id,
			$patient_id
		);

		// Execute the query
		if ($query->execute()) {
			// Close the query
			$query->close();
			return true; // Return true if the update is successful
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}

	/* Delete Patient Function */

	public function delete_patient($patient_id)
	{
		// Prepare the SQL query to delete a patient record
		$query = $this->conn->prepare("DELETE FROM `patient` WHERE `patient_id`=?") or die($this->conn->error);

		// Bind parameters
		$query->bind_param("i", $patient_id);

		// Execute the query
		if ($query->execute()) {
			// Close the query
			$query->close();
			return true; // Return true if the delete is successful
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}
	/* Display Patient Function */

	function display_patient()
	{
		// Assuming $this->conn is your mysqli connection
		$query = "SELECT * FROM patient";  // Modify with your actual query
		$result = $this->conn->query($query);  // Execute the query and get the result

		if ($result === false) {
			die("Error in query: " . $this->conn->error);  // Handle query errors
		}

		return $result;  // Return the result as a mysqli_result object
	}
	function displaypatient()
	{
		// Assuming $this->conn is your mysqli connection
		$query = "
        SELECT 
            patient.*, 
            branch.*, 
            scheme.*, 
            diagnosis.*, 
            route.*, 
            cohort.*
        FROM patient
        LEFT JOIN branch ON patient.branch_id = branch.branch_id
        LEFT JOIN scheme ON patient.scheme_id = scheme.scheme_id
        LEFT JOIN diagnosis ON patient.diagnosis_id = diagnosis.diagnosis_id
        LEFT JOIN route ON patient.route_id = route.route_id  -- Assuming route_id exists in patient
        LEFT JOIN cohort ON patient.cohort_id = cohort.cohort_id  -- Assuming cohort_id exists in patient
    ";

		$result = $this->conn->query($query);  // Execute the query and get the result

		if ($result === false) {
			die("Error in query: " . $this->conn->error);  // Handle query errors
		}

		return $result;  // Return the result as a mysqli_result object
	}



	function countPatients($db)
	{
		$query = "SELECT COUNT(*) AS patient_count FROM patient";
		$result = $db->query($query);  // Use the query method

		if ($result) {
			$row = $result->fetch_assoc();
			return $row['patient_count'];
		} else {
			return 0;
		}
	}



	/* Function to create a new call record */

	public function create_call($patient_id, $call_results)
	{
		// Prepare the SQL query to insert a new call record
		$query = $this->conn->prepare("INSERT INTO `calls` (`patient_id`, `call_results`) VALUES (?, ?)") or die($this->conn->error);

		// Bind parameters
		$query->bind_param("is", $patient_id, $call_results);

		// Execute the query and return the result
		if ($query->execute()) {
			$query->close();
			$this->conn->close();
			return true;
		} else {
			$query->close();
			$this->conn->close();
			return false;
		}
	}

	/* Function to display all call records */

	/* Display Call Function */

	public function display_calls()
	{
		// Prepare the SQL query to fetch all columns from both calls and patients tables
		$query = $this->conn->prepare("
         SELECT 
        calls.*, 
        patient.*, 
        -- Add the call frequency column
        CASE
            WHEN DATEDIFF(CURDATE(), calls.call_date) < 28 THEN 'Regularly'
            WHEN DATEDIFF(CURDATE(), calls.call_date) BETWEEN 28 AND 60 THEN 'Irregularly'
            WHEN DATEDIFF(CURDATE(), calls.call_date) BETWEEN 61 AND 90 THEN 'Sometimes'
            WHEN DATEDIFF(CURDATE(), calls.call_date) > 90 THEN 'Never'
            ELSE 'Unknown'
        END AS call_frequency
    FROM 
        calls
    LEFT JOIN 
        patient
    ON 
        calls.patient_id = patient.patient_id
    ") or die($this->conn->error);

		// Execute the query
		if ($query->execute()) {
			// Get the result of the query
			$result = $query->get_result();

			// Fetch all the rows from the result set
			$calls = $result->fetch_all(MYSQLI_ASSOC);

			// Free the result set to free up memory
			$result->free();

			// Close the query
			$query->close();

			// Return the calls array with patient details
			return $calls;
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}



	/* Function to delete a call record by call_id */

	public function delete_call($call_id)
	{
		// Prepare the SQL query to delete a specific call record
		$query = $this->conn->prepare("DELETE FROM `calls` WHERE `call_id` = ?") or die($this->conn->error);

		// Bind the parameter
		$query->bind_param("i", $call_id);

		// Execute the query and return the result
		if ($query->execute()) {
			$query->close();
			$this->conn->close();
			return true;
		} else {
			$query->close();
			$this->conn->close();
			return false;
		}
	}


	/* Update Call Function */

	public function update_call($call_id, $patient_id, $call_results)
	{
		// Prepare the SQL query to update the call record
		$query = $this->conn->prepare("UPDATE `calls` SET `patient_id`=?, `call_results`=? WHERE `call_id`=?") or die($this->conn->error);

		// Bind parameters
		$query->bind_param("ssi", $patient_id, $call_results, $call_id);

		// Execute the query
		if ($query->execute()) {
			// Close the query
			$query->close();
			return true; // Return true if the update is successful
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}



	/* Insert Scheme Function */

	public function insert_scheme($scheme_name, $payment_method)
	{
		// Prepare the SQL query to insert a new scheme
		$query = $this->conn->prepare("INSERT INTO `scheme` (`scheme_name`, `payment_method`) VALUES (?, ?)") or die($this->conn->error);

		// Bind parameters
		$query->bind_param("ss", $scheme_name, $payment_method);

		// Execute the query
		if ($query->execute()) {
			// Close the query
			$query->close();
			return true; // Return true if the insert is successful
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}




	/* Update Scheme Function */

	public function update_scheme($scheme_id, $scheme_name, $payment_method)
	{
		// Prepare the SQL query to update a scheme record
		$query = $this->conn->prepare("UPDATE `scheme` SET `scheme_name`=?, `payment_method`=? WHERE `scheme_id`=?") or die($this->conn->error);

		// Bind parameters
		$query->bind_param("ssi", $scheme_name, $payment_method, $scheme_id);

		// Execute the query
		if ($query->execute()) {
			// Close the query
			$query->close();
			return true; // Return true if the update is successful
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}

	/* Delete Scheme Function */

	public function delete_scheme($scheme_id)
	{
		// Prepare the SQL query to delete a scheme record
		$query = $this->conn->prepare("DELETE FROM `scheme` WHERE `scheme_id`=?") or die($this->conn->error);

		// Bind parameters
		$query->bind_param("i", $scheme_id);

		// Execute the query
		if ($query->execute()) {
			// Close the query
			$query->close();
			return true; // Return true if the delete is successful
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}


	/* Display Scheme Function */

	public function display_scheme()
	{
		// Prepare the SQL query to fetch all schemes
		$query = $this->conn->prepare("SELECT * FROM `scheme`") or die($this->conn->error);

		// Execute the query
		if ($query->execute()) {
			// Get the result of the query
			$result = $query->get_result();

			// Fetch all the rows from the result set
			$schemes = $result->fetch_all(MYSQLI_ASSOC);

			// Free the result set to free up memory
			$result->free();

			// Close the query
			$query->close();

			// Return the schemes array
			return $schemes;
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}




	/* Display Medication Function */

	public function display_medication()
	{
		// Prepare the SQL query to fetch all medications
		$query = $this->conn->prepare("SELECT * FROM `medication`") or die($this->conn->error);

		// Execute the query
		if ($query->execute()) {
			// Get the result of the query
			$result = $query->get_result();

			// Fetch all the rows from the result set
			$medications = $result->fetch_all(MYSQLI_ASSOC);

			// Free the result set to free up memory
			$result->free();

			// Close the query
			$query->close();

			// Return the medications array
			return $medications;
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}


	/* Update Medication Function */

	public function update_medication($medication_id, $medication_name)
	{
		// Prepare the SQL query to update a medication record
		$query = $this->conn->prepare("UPDATE `medication` SET `medication_name`=? WHERE `medication_id`=?") or die($this->conn->error);

		// Bind parameters
		$query->bind_param("si", $medication_name, $medication_id);

		// Execute the query
		if ($query->execute()) {
			// Close the query
			$query->close();
			return true; // Return true if the update is successful
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}


	/* Delete Medication Function */

	public function delete_medication($medication_id)
	{
		// Prepare the SQL query to delete a medication record
		$query = $this->conn->prepare("DELETE FROM `medication` WHERE `medication_id`=?") or die($this->conn->error);

		// Bind parameters
		$query->bind_param("i", $medication_id);

		// Execute the query
		if ($query->execute()) {
			// Close the query
			$query->close();
			return true; // Return true if the delete is successful
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}



	/* Insert Medication Function */

	public function insert_medication($medication_name)
	{
		// Prepare the SQL query to insert a new medication
		$query = $this->conn->prepare("INSERT INTO `medication` (`medication_name`) VALUES (?)") or die($this->conn->error);

		// Bind parameters
		$query->bind_param("s", $medication_name);

		// Execute the query
		if ($query->execute()) {
			// Close the query
			$query->close();
			return true; // Return true if the insert is successful
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}


	/* Insert Branch Function */

	public function insert_branch($branch_name)
	{
		// Prepare the SQL query to insert a new branch
		$query = $this->conn->prepare("INSERT INTO `branch` (`branch_name`) VALUES (?)") or die($this->conn->error);

		// Bind parameters
		$query->bind_param("s", $branch_name);

		// Execute the query
		if ($query->execute()) {
			// Close the query
			$query->close();
			return true; // Return true if the insert is successful
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}
	/* Update Branch Function */

	public function update_branch($branch_id, $branch_name)
	{
		// Prepare the SQL query to update a branch record
		$query = $this->conn->prepare("UPDATE `branch` SET `branch_name`=? WHERE `branch_id`=?") or die($this->conn->error);

		// Bind parameters
		$query->bind_param("si", $branch_name, $branch_id);

		// Execute the query
		if ($query->execute()) {
			// Close the query
			$query->close();
			return true; // Return true if the update is successful
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}
	/* Delete Branch Function */

	public function delete_branch($branch_id)
	{
		// Prepare the SQL query to delete a branch record
		$query = $this->conn->prepare("DELETE FROM `branch` WHERE `branch_id`=?") or die($this->conn->error);

		// Bind parameters
		$query->bind_param("i", $branch_id);

		// Execute the query
		if ($query->execute()) {
			// Close the query
			$query->close();
			return true; // Return true if the delete is successful
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}

	function displaybranch()
	{
		// Assuming $this->conn is your mysqli connection
		$query = "SELECT * FROM `branch`";  // Modify with your actual query
		$result = $this->conn->query($query);  // Execute the query and get the result

		if ($result === false) {
			die("Error in query: " . $this->conn->error);  // Handle query errors
		}

		return $result;  // Return the result as a mysqli_result object


	}


	/* Insert Cohort Function */

	public function insert_cohort($cohort_name, $team_lead)
	{
		// Prepare the SQL query to insert a new cohort
		$query = $this->conn->prepare("INSERT INTO `cohort` (`cohort_name`, `team_lead`) VALUES (?, ?)") or die($this->conn->error);

		// Bind parameters
		$query->bind_param("ss", $cohort_name, $team_lead);

		// Execute the query
		if ($query->execute()) {
			// Close the query
			$query->close();
			return true; // Return true if the insert is successful
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}
	/* Update Cohort Function */

	public function update_cohort($cohort_id, $cohort_name, $team_lead)
	{
		$query = "UPDATE cohort SET cohort_name = ?, team_lead = ? WHERE cohort_id = ?";
		if ($stmt = $this->conn->prepare($query)) {
			$stmt->bind_param("ssi", $cohort_name, $team_lead, $cohort_id);
			if ($stmt->execute()) {
				return true;
			} else {
				return false; // Query execution failed
			}
		} else {
			return false; // Query preparation failed
		}
	}


	/* Delete Cohort Function */

	public function delete_cohort($cohort_id)
	{
		// Prepare the SQL query to delete a cohort record
		$query = $this->conn->prepare("DELETE FROM `cohort` WHERE `cohort_id`=?") or die($this->conn->error);

		// Bind parameters
		$query->bind_param("i", $cohort_id);

		// Execute the query
		if ($query->execute()) {
			// Close the query
			$query->close();
			return true; // Return true if the delete is successful
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}
	/* Display Cohort Function */


	function display_cohort()
	{
		// Assuming $this->conn is your mysqli connection
		$query = "SELECT * FROM `cohort`";  // Modify with your actual query
		$result = $this->conn->query($query);  // Execute the query and get the result

		if ($result === false) {
			die("Error in query: " . $this->conn->error);  // Handle query errors
		}

		return $result;  // Return the result as a mysqli_result object


	}

	public function display_diagnosis()
	{
		// Prepare the SQL query to fetch all diagnoses
		$query = $this->conn->prepare("SELECT * FROM `diagnosis` ORDER BY `diagnosis_id` ASC") or die($this->conn->error);

		// Execute the query
		if ($query->execute()) {
			// Get the result of the query
			$result = $query->get_result();

			// Fetch all the rows from the result set
			$diagnoses = $result->fetch_all(MYSQLI_ASSOC);

			// Free the result set to free up memory
			$result->free();

			// Close the query
			$query->close();

			// Return the diagnoses array
			return $diagnoses;
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}



	public function deleteDiagnosis($diagnosis_id)
	{
		// Prepare the SQL query to delete a diagnosis record
		$query = $this->conn->prepare("DELETE FROM diagnosis WHERE id=?") or die($this->conn->error);

		// Bind the parameter (diagnosis_id) to the prepared statement
		$query->bind_param("i", $diagnosis_id);

		// Execute the query
		if ($query->execute()) {
			// Close the query
			$query->close();
			return true; // Return true if the delete is successful
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}
	public function updateDiagnosis($diagnosis_id, $diagnosis_name, $idc10)
	{
		// Prepare the SQL query to update a diagnosis record
		$query = $this->conn->prepare("UPDATE diagnosis SET diagnosis_name=?, idc10=? WHERE id=?") or die($this->conn->error);

		// Bind parameters
		$query->bind_param("ssi", $diagnosis_name, $idc10, $diagnosis_id);

		// Execute the query
		if ($query->execute()) {
			// Close the query
			$query->close();
			return true; // Return true if the update is successful
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}

	public function insertDiagnosis($diagnosis_name, $idc10)
	{
		// Prepare the SQL query to insert a new diagnosis
		$query = $this->conn->prepare("INSERT INTO diagnosis (diagnosis_name, idc10) VALUES (?, ?)") or die($this->conn->error);

		// Bind parameters
		$query->bind_param("ss", $diagnosis_name, $idc10);

		// Execute the query
		if ($query->execute()) {
			// Close the query
			$query->close();
			return true; // Return true if the insert is successful
		} else {
			// Close the query if it fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}


	function display_route()
	{
		// Assuming $this->conn is your mysqli connection
		$query = "SELECT * FROM `route`";  // Modify with your actual query
		$result = $this->conn->query($query);  // Execute the query and get the result

		if ($result === false) {
			die("Error in query: " . $this->conn->error);  // Handle query errors
		}

		return $result;  // Return the result as a mysqli_result object


	}



	function display_visit()
	{
		// Assuming $this->conn is your mysqli connection
		$query = "SELECT * FROM `visit`";  // Modify with your actual query
		$result = $this->conn->query($query);  // Execute the query and get the result

		if ($result === false) {
			die("Error in query: " . $this->conn->error);  // Handle query errors
		}

		return $result;  // Return the result as a mysqli_result object


	}


	public function display_visit_patient()
	{
		$query = $this->conn->prepare("
        SELECT 
            patient.*,               -- Select all columns from the patient table
            nutrition.*,             -- Select all columns from the nutrition table
            medication_use.*,        -- Select all columns from the medication_use table
            psychosocial.*,          -- Select all columns from the psychosocial table
            chronic.*,               -- Select all columns from the chronic table
            branch.*,                -- Select all columns from the branch table
            calls.*,                 -- Select all columns from the calls table
            chronic.*  -- Select all columns from chronic table
        
        FROM 
            patient
        INNER JOIN 
            chronic ON patient.patient_id = chronic.patient_id
    ") or die($this->conn->error);

		if ($query->execute()) {
			$result = $query->get_result(); // Fetch the result set
			return $result;
		}
	}

	public function display_chronic_patient()
	{
		$query = $this->conn->prepare("
        SELECT 
            patient.*, 
            chronic.*
        FROM 
            patient
        LEFT JOIN 
            chronic ON patient.patient_id = chronic.patient_id
    ") or die($this->conn->error);

		if ($query->execute()) {
			$result = $query->get_result(); // Fetch the result set
			return $result;
		}
	}


	public function display_chronic()
	{
		$query = $this->conn->prepare("
        SELECT 
            patient.*,  -- Select all columns from the patient table
            chronic.*  -- Select all columns from the chronic table
        FROM 
            chronic
        LEFT JOIN 
            patient ON patient.patient_id = chronic.patient_id
    ") or die($this->conn->error);

		if ($query->execute()) {
			$result = $query->get_result(); // Fetch the result set
			return $result;
		}
	}

	public function display_nutrition_patient()
	{
		$query = $this->conn->prepare("
        SELECT 
            patient.*, 
            nutrition.*
        FROM 
            patient
        LEFT JOIN 
            nutrition ON patient.patient_id = nutrition.patient_id
    ") or die($this->conn->error);

		if ($query->execute()) {
			$result = $query->get_result(); // Fetch the result set
			return $result;
		}
	}

	public function display_psychosocial_patient()
	{
		$query = $this->conn->prepare("
        SELECT 
            patient.*, 
            psychosocial.*
        FROM 
            patient
        LEFT JOIN 
            psychosocial ON patient.patient_id = psychosocial.patient_id
    ") or die($this->conn->error);

		if ($query->execute()) {
			$result = $query->get_result(); // Fetch the result set
			return $result;
		}
	}

	public function display_calls_patient()
	{
		$query = $this->conn->prepare("
        SELECT 
            patient.*,               -- Select all columns from the patient table
            nutrition.*,             -- Select all columns from the nutrition table
            medication_use.*,        -- Select all columns from the medication_use table
            psychosocial.*,          -- Select all columns from the psychosocial table
            chronic.*,               -- Select all columns from the chronic table
            branch.*,                -- Select all columns from the branch table
            calls.*,                 -- Select all columns from the calls table
			       
            chronic.refill_date,     -- Display chronic refill date
            psychosocial.next_review, -- Display psychosocial next review date
            -- Add the assessment column
            CASE
                WHEN chronic.patient_id IS NOT NULL THEN 'Chronic'
                WHEN nutrition.patient_id IS NOT NULL THEN 'Nutrition'
                WHEN psychosocial.patient_id IS NOT NULL THEN 'Psychosocial'
                WHEN medication_use.patient_id IS NOT NULL THEN 'Medication'
                ELSE 'Unknown'
            END AS assessment
			
        FROM 
            patient
        INNER JOIN 
            calls ON patient.patient_id = calls.patient_id
        LEFT JOIN 
            nutrition ON patient.patient_id = nutrition.patient_id
        LEFT JOIN 
            medication_use ON patient.patient_id = medication_use.patient_id
        LEFT JOIN 
            psychosocial ON patient.patient_id = psychosocial.patient_id
        LEFT JOIN 
            chronic ON patient.patient_id = chronic.patient_id
        LEFT JOIN 
            branch ON patient.branch_id = branch.branch_id
        
    ") or die($this->conn->error);

		if ($query->execute()) {
			$result = $query->get_result(); // Fetch the result set
			return $result;
		}
	}

	public function visitsummary()
	{
		$query = $this->conn->prepare("
        SELECT 
            patient.*,               -- Select all columns from the patient table
            nutrition.*,             -- Select all columns from the nutrition table
            medication_use.*,        -- Select all columns from the medication_use table
            psychosocial.*,          -- Select all columns from the psychosocial table
            chronic.*,               -- Select all columns from the chronic table
            specialist.*,            -- Select all columns from the specialist table
            branch.*,                -- Select all columns from the branch table
            calls.*,                 -- Select all columns from the calls table
            chronic.refill_date,     -- Display chronic refill date
			
            psychosocial.next_review, -- Display psychosocial next review date
            -- Add the assessment column
            CASE
                WHEN chronic.patient_id IS NOT NULL THEN 'Chronic'
                WHEN nutrition.patient_id IS NOT NULL THEN 'Nutrition'
                WHEN psychosocial.patient_id IS NOT NULL THEN 'Psychosocial'
                WHEN medication_use.patient_id IS NOT NULL THEN 'Medication'
                ELSE 'Unknown'
            END AS assessment
        FROM 
            patient
        INNER JOIN 
            calls ON patient.patient_id = calls.patient_id
        LEFT JOIN 
            nutrition ON patient.patient_id = nutrition.patient_id
        LEFT JOIN 
            medication_use ON patient.patient_id = medication_use.patient_id
        LEFT JOIN 
            psychosocial ON patient.patient_id = psychosocial.patient_id
        LEFT JOIN 
            chronic ON patient.patient_id = chronic.patient_id
        LEFT JOIN 
            specialist ON chronic.speciality_id = specialist.specialist_id -- Updated join with chronic.speciality_id
        LEFT JOIN 
            branch ON patient.branch_id = branch.branch_id
        LEFT JOIN 
            cohort ON patient.cohort_id = cohort.cohort_id
        GROUP BY 
            patient.patient_id


			
    ") or die($this->conn->error);

		if ($query->execute()) {
			$result = $query->get_result(); // Fetch the result set
			return $result;
		}
	}

	public function visitreport($patient_id)
	{
		// Prepare the SQL query
		$query = $this->conn->prepare("
        SELECT 
            patient.patient_id,
            patient.firstname,
            patient.lastname,
            patient.patient_no,
            branch.branch_name,
            calls.call_date,
            calls.call_results,
            -- Include assessment type based on the available data
            CASE
                WHEN chronic.patient_id IS NOT NULL THEN 'Chronic'
                WHEN nutrition.patient_id IS NOT NULL THEN 'Nutrition'
                WHEN psychosocial.patient_id IS NOT NULL THEN 'Psychosocial'
                ELSE 'Unknown'
            END AS assessment,
            -- Include revenue based on the assessment type
            CASE
                WHEN chronic.patient_id IS NOT NULL THEN chronic.revenue
                WHEN nutrition.patient_id IS NOT NULL THEN nutrition.revenue
                WHEN psychosocial.patient_id IS NOT NULL THEN psychosocial.revenue
                ELSE 0
            END AS revenue,
            -- Include the assessment date from the relevant table
            COALESCE(chronic.refill_date, nutrition.visit_date, 
                     psychosocial.visit_date, medication_use.visit_date) AS assessment_date,
            -- Calculate the call frequency based on call_date
          
        FROM 
            patient
        INNER JOIN 
            calls ON patient.patient_id = calls.patient_id
        LEFT JOIN 
            nutrition ON patient.patient_id = nutrition.patient_id
        LEFT JOIN 
            chronic ON patient.patient_id = chronic.patient_id
        LEFT JOIN 
            psychosocial ON patient.patient_id = psychosocial.patient_id
        LEFT JOIN 
            medication_use ON patient.patient_id = medication_use.patient_id
        LEFT JOIN 
            branch ON patient.branch_id = branch.branch_id
        WHERE 
            patient.patient_id = ?
    ") or die($this->conn->error);

		// Bind the parameter
		$query->bind_param("i", $patient_id);

		// Execute the query
		if ($query->execute()) {
			$result = $query->get_result();
			return $result;
		} else {
			return false;
		}
	}

	public function AssesChronic()
	{
		$query = $this->conn->prepare("
        SELECT 
            patient.*,              -- Select all columns from the patient table
            chronic.*,              -- Select all columns from the chronic table
            chronic.refill_date,    -- Display chronic refill date
            CASE
                WHEN chronic.patient_id IS NOT NULL THEN 'Chronic'
                ELSE 'Unknown'
            END AS assessment
        FROM 
            patient
        LEFT JOIN 
            chronic ON patient.patient_id = chronic.patient_id
        LEFT JOIN 
            calls ON patient.patient_id = calls.patient_id
        GROUP BY 
            patient.patient_id
            
        
    ") or die($this->conn->error);

		if ($query->execute()) {
			$result = $query->get_result(); // Fetch the result set
			return $result;
		}
	}

	public function AssesNutrition()
	{
		$query = $this->conn->prepare("
			SELECT 
				patient.*,               -- Select all columns from the patient table
				nutrition.*,             -- Select all columns from the nutrition table
				
				CASE
					WHEN nutrition.patient_id IS NOT NULL THEN 'Nutrition'
					ELSE 'Unknown'
				END AS assessment
			FROM 
				patient
			INNER JOIN 
				calls ON patient.patient_id = calls.patient_id  -- Join calls table to patient
			LEFT JOIN 
				nutrition ON patient.patient_id = nutrition.patient_id  -- Join nutrition table to patient
			GROUP BY 
				nutrition.nutrition_id
				
		") or die($this->conn->error);

		if ($query->execute()) {
			$result = $query->get_result(); // Fetch the result set
			return $result;
		}
	}

	public function asseMedication()
	{
		$query = $this->conn->prepare("
        SELECT 
            patient.*,                -- Select all columns from the patient table
            medication_use.*,          -- Select all columns from the medication_use table
            CASE
                WHEN medication_use.patient_id IS NOT NULL THEN 'Medication'
                ELSE 'Unknown'
            END AS assessment
        FROM 
            patient
        LEFT JOIN 
            medication_use ON patient.patient_id = medication_use.patient_id  -- Join medication_use table to patient
        INNER JOIN 
            calls ON patient.patient_id = calls.patient_id  -- Join calls table to patient
        GROUP BY 
            medication_use.medication_use_id


			
    ") or die($this->conn->error);

		if ($query->execute()) {
			$result = $query->get_result(); // Fetch the result set
			return $result;
		}
	}

	public function AssesPsychosocial()
	{
		$query = $this->conn->prepare("
        SELECT 
            patient.*,               -- Select all columns from the patient table
            psychosocial.*,          -- Select all columns from the psychosocial table
            
            CASE
                WHEN psychosocial.patient_id IS NOT NULL THEN 'Psychosocial'
                ELSE 'Unknown'
            END AS assessment
        FROM 
            patient
        LEFT JOIN 
            psychosocial ON patient.patient_id = psychosocial.patient_id  -- Join psychosocial table to patient
        GROUP BY 
            psychosocial.id
    ") or die($this->conn->error);

		if ($query->execute()) {
			$result = $query->get_result(); // Fetch the result set
			return $result;
		}
	}




	public function chronic()
	{
		$query = $this->conn->prepare("
        SELECT 
            patient.*,           -- Select all columns from the patient table
            chronic.*,           -- Select all columns from the chronic table
            calls.*,             -- Select all columns from the calls table
            -- Add a new column to label the assessment
            CASE
                WHEN chronic.patient_id IS NOT NULL THEN 'Chronic'
                WHEN nutrition.patient_id IS NOT NULL THEN 'Nutrition'
                WHEN psychosocial.patient_id IS NOT NULL THEN 'Psychosocial'
                WHEN medication_use.patient_id IS NOT NULL THEN 'Medication'
                ELSE 'Unknown'
            END AS assessment
        FROM 
            patient
        LEFT JOIN 
            chronic ON patient.patient_id = chronic.patient_id
        LEFT JOIN 
            calls ON patient.patient_id = calls.patient_id
        LEFT JOIN 
            nutrition ON patient.patient_id = nutrition.patient_id
        LEFT JOIN 
            psychosocial ON patient.patient_id = psychosocial.patient_id
        LEFT JOIN 
            medication_use ON patient.patient_id = medication_use.patient_id
        ORDER BY 
            chronic.refill_date DESC -- Sort by refill date in descending order
    ") or die($this->conn->error);

		if ($query->execute()) {
			$result = $query->get_result(); // Fetch the result set
			return $result;
		}
	}



	public function display_call()
	{
		$query = $this->conn->prepare("
			SELECT 
				patient.*,           -- Select all columns from the patient table
				nutrition.*,         -- Select all columns from the nutrition table
				medication_use.*,     -- Select all columns from the medication_use table
				psychosocial.*,       -- Select all columns from the psychosocial table
				chronic.*,            -- Select all columns from the chronic table
				branch.*,             -- Select all columns from the branch table
				calls.*,              -- Select all columns from the calls table
				
				
				-- Calculate urgency status based on the dates
				CASE 
					WHEN DATEDIFF(MAX(chronic.refill_date), CURDATE()) <= 5 THEN 'Due'
					WHEN DATEDIFF(MAX(psychosocial.next_review), CURDATE()) <= 5 THEN 'Due'
					WHEN DATEDIFF(DATE_ADD(MAX(chronic.refill_date), INTERVAL 1 MONTH), CURDATE()) <= 5 THEN 'Due'
					WHEN DATEDIFF(DATE_ADD(MAX(psychosocial.next_review), INTERVAL 1 MONTH), CURDATE()) <= 5 THEN 'Due'
					ELSE 'Not due'
				END AS urgency
			FROM 
				chronic
			INNER JOIN 
				patient ON chronic.patient_id = patient.patient_id    -- Join chronic to patient
			INNER JOIN 
				calls ON patient.patient_id = calls.patient_id
			LEFT JOIN 
				nutrition ON patient.patient_id = nutrition.patient_id
			LEFT JOIN 
				medication_use ON patient.patient_id = medication_use.patient_id
			LEFT JOIN 
				psychosocial ON patient.patient_id = psychosocial.patient_id
			LEFT JOIN 
				branch ON patient.branch_id = branch.branch_id
			GROUP BY 
				patient.patient_id, 
				patient.firstname, 
				patient.lastname, 
				patient.dob, 
				patient.gender, 
				patient.age, 
				patient.location, 
				patient.phone_no, 
				patient.email, 
				patient.patient_no, 
				patient.diagnosis, 
				patient.patient_status, 
				branch.branch_name
		") or die($this->conn->error);

		if ($query->execute()) {
			$result = $query->get_result(); // Fetch the result set
			if ($result) {
				// Fetch the data using fetch_array() and process the results
				$data = [];
				while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
					$data[] = $row;
				}
				return $data;  // Return the result as an array
			} else {
				die("Error: No data returned.");
			}
		} else {
			die("Error executing query.");
		}
	}



	public function display_calls_chronic()
	{
		$query = $this->conn->prepare("
			SELECT 
				patient.*,           -- Select all columns from the patient table
				chronic.*,           -- Select all columns from the chronic table
				chronic.refill_date, -- Display chronic refill date
				-- Calculate urgency status based on the chronic refill date
				CASE 
					WHEN DATEDIFF(chronic.refill_date, CURDATE()) <= 5 THEN 'Due'
					WHEN DATEDIFF(DATE_ADD(chronic.refill_date, INTERVAL 1 MONTH), CURDATE()) <= 5 THEN 'Due'
					ELSE 'Not due'
				END AS urgency
			FROM 
				patient
			LEFT JOIN 
				chronic ON patient.patient_id = chronic.patient_id
			GROUP BY 
				patient.patient_id, 
				patient.firstname, 
				patient.lastname, 
				patient.dob, 
				patient.gender, 
				patient.age, 
				patient.location, 
				patient.phone_no, 
				patient.email, 
				patient.patient_no, 
				patient.diagnosis, 
				patient.patient_status,
				chronic.refill_date
		") or die($this->conn->error);

		if ($query->execute()) {
			$result = $query->get_result(); // Fetch the result set
			return $result;
		}
	}





	public function display_monitoring_patient()
	{
		$query = $this->conn->prepare("
        SELECT 
				patient.patient_id,
				patient.firstname,
				patient.lastname,
				patient.gender, 
                 patient.age, 
				patient.phone_no,
				monitoring.compliance,
				monitoring.exercise,
				monitoring.clinical_goals,
				monitoring.nutrition_follow_up,
				monitoring.psychosocial
			FROM 
				monitoring
			INNER JOIN 
				patient 
			ON 
				monitoring.patient_id = patient.patient_id
		") or die($this->conn->error);

		if ($query->execute()) {
			$result = $query->get_result();
			return $result;
		}
	}
	public function display_checkup_patient()
	{
		$query = $this->conn->prepare("
			SELECT 
				patient.patient_id,
				patient.firstname,
				patient.lastname,
				patient.age,
				patient.gender,
				patient.phone_no,
				checkup.annual_check_up,
				checkup.specialist_review
			FROM 
				checkup
			INNER JOIN 
				patient 
			ON 
				checkup.patient_id = patient.patient_id
		") or die($this->conn->error);

		if ($query->execute()) {
			$result = $query->get_result();
			return $result;
		}
	}

	public function display_payment_methods()
	{
		// Prepare the SQL query to fetch payment methods from the payment table
		$query = $this->conn->prepare("SELECT payment_id, patient_id, branch_id, scheme_id, payment_method, revenue FROM `payment`")
			or die($this->conn->error);

		// Execute the query
		if ($query->execute()) {
			// Get the result of the query
			$result = $query->get_result();

			// Fetch all the rows from the result set
			$payments = $result->fetch_all(MYSQLI_ASSOC);

			// Free the result set to free up memory
			$result->free();

			// Close the query
			$query->close();

			// Return the payments array
			return $payments;
		} else {
			// Close the query if execution fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}


	public function display_schemes()
	{
		// Prepare the SQL query to fetch scheme_id, scheme_name, and payment_method from the scheme table
		$query = $this->conn->prepare("SELECT scheme_id, scheme_name, payment_method FROM scheme")
			or die($this->conn->error);

		// Execute the query
		if ($query->execute()) {
			// Get the result of the query
			$result = $query->get_result();

			// Fetch all the rows from the result set
			$schemes = $result->fetch_all(MYSQLI_ASSOC);

			// Free the result set to free up memory
			$result->free();

			// Close the query
			$query->close();

			// Return the schemes array
			return $schemes;
		} else {
			// Close the query if execution fails
			$query->close();
			return false; // Or you can handle errors accordingly
		}
	}

	function displayMedication()
	{
		// Assuming $this->conn is your mysqli connection
		$query = "SELECT * FROM `medication`";  // Modify with your actual table name
		$result = $this->conn->query($query);  // Execute the query and get the result

		if ($result === false) {
			die("Error in query: " . $this->conn->error);  // Handle query errors
		}

		return $result;  // Return the result as a mysqli_result object
	}
	function displayProcedures()
	{
		// Assuming $this->conn is your mysqli connection
		$query = "SELECT * FROM `procedures`";  // Replace `procedure` with your actual table name, if different
		$result = $this->conn->query($query);  // Execute the query and get the result

		if ($result === false) {
			die("Error in query: " . $this->conn->error);  // Handle query errors
		}

		return $result;  // Return the result as a mysqli_result object
	}
	function displayspecialist()
	{
		// Assuming $this->conn is your mysqli connection
		$query = "SELECT * FROM `specialist`";  // Fetch all rows from `specialist` table
		$result = $this->conn->query($query);  // Execute the query

		if ($result === false) {
			die("Error in query: " . $this->conn->error);  // Handle query errors
		}
		return $result;  // Return the result object
	}
	function display_branch()
	{
		// Assuming $this->conn is your mysqli connection
		$query = "SELECT * FROM `branch`";  // Modify with your actual query
		$result = $this->conn->query($query);  // Execute the query and get the result

		if ($result === false) {
			die("Error in query: " . $this->conn->error);  // Handle query errors
		}

		return $result;  // Return the result as a mysqli_result object


	}
	public function insert_nutrition($patient_id, $scheme_id, $last_visit, $next_review, $muscle_mass, $bone_mass, $weight, $BMI, $subcutaneous_fat, $visceral_fat, $weight_remarks, $physical_activity, $meal_plan_set_up, $nutrition_adherence, $nutrition_assessment_remarks, $revenue)
	{
		// Prepare the SQL query
		$query = $this->conn->prepare("INSERT INTO `nutrition` 
        (`patient_id`, `scheme_id`, `last_visit`, `next_review`, `muscle_mass`, `bone_mass`, `weight`, `BMI`, `subcutaneous_fat`, `visceral_fat`, `weight_remarks`, `physical_activity`, `meal_plan_set_up`, `nutrition_adherence`, `nutrition_assessment_remarks`, `revenue`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
			or die($this->conn->error);

		// Bind parameters
		$query->bind_param(
			"iissssssssssssss",
			$patient_id,
			$scheme_id, // Added scheme_id
			$last_visit,
			$next_review,
			$muscle_mass,
			$bone_mass,
			$weight,
			$BMI,
			$subcutaneous_fat,
			$visceral_fat,
			$weight_remarks,
			$physical_activity,
			$meal_plan_set_up,
			$nutrition_adherence,
			$nutrition_assessment_remarks,
			$revenue
		);

		// Execute the query
		if ($query->execute()) {
			$query->close();
			return true;
		} else {
			$query->close();
			return false;
		}
	}

	public function insert_psychosocial_assessment($last_visit, $next_review, $muscle_mass, $bone_mass, $weight, $BMI, $subcutaneous_fat, $visceral_fat, $weight_remarks, $physical_activity, $meal_plan_set_up, $nutrition_adherence, $nutrition_assessment_remarks, $branch_id, $patient_id)
	{
		// Prepare the SQL query
		$query = $this->conn->prepare("INSERT INTO `psychosocial_assessment` 
        (`last_visit`, `next_review`, `muscle_mass`, `bone_mass`, `weight`, `BMI`, `subcutaneous_fat`, `visceral_fat`, `weight_remarks`, `physical_activity`, `meal_plan_set_up`, `nutrition_adherence`, `nutrition_assessment_remarks`, `branch_id`, `patient_id`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
			or die($this->conn->error);

		// Bind parameters
		$query->bind_param(
			"issssssssssssi",
			$last_visit,
			$next_review,
			$muscle_mass,
			$bone_mass,
			$weight,
			$BMI,
			$subcutaneous_fat,
			$visceral_fat,
			$weight_remarks,
			$physical_activity,
			$meal_plan_set_up,
			$nutrition_adherence,
			$nutrition_assessment_remarks,
			$branch_id,
			$patient_id
		);

		// Execute the query
		if ($query->execute()) {
			$query->close();
			return true;
		} else {
			$query->close();
			return false;
		}
	}

	public function insert_medication_use($days_supplied, $no_pills_dispensed, $frequency, $patient_id)
	{
		// Prepare the SQL query
		$query = $this->conn->prepare("INSERT INTO `medication_use` 
        (`days_supplied`, `no_pills_dispensed`, `frequency`, `patient_id`) 
        VALUES (?, ?, ?, ?)")
			or die($this->conn->error);

		// Bind parameters
		$query->bind_param(
			"iiis",
			$days_supplied,
			$no_pills_dispensed,
			$frequency,
			$patient_id
		);

		// Execute the query
		if ($query->execute()) {
			$query->close();
			return true;
		} else {
			$query->close();
			return false;
		}
	}

	public function insert_chronic_care($patient_id, $last_visit, $days_supplied, $procedure, $specialty, $refill_date, $compliance, $exercise, $clinical_goals, $nutrition_follow_up, $psychosocial, $vitals_monitoring, $vital_signs_monitor, $revenue, $annual_check_up)
	{
		// Prepare the SQL query
		$query = $this->conn->prepare("INSERT INTO `chronic` 
    (`patient_id`, `last_visit`, `days_supplied`, `procedure`, `specialty`, `refill_date`, `compliance`, `exercise`, `clinical_goals`, `nutrition_follow_up`, `psychosocial`, `vitals_monitoring`, `vital_signs_monitor`, `revenue`, `annual_check_up`) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
			or die($this->conn->error);

		// Bind parameters
		$query->bind_param(
			"issssssssssssss",
			$patient_id,
			$last_visit,
			$days_supplied,
			$procedure,
			$specialty,
			$refill_date,
			$compliance,
			$exercise,
			$clinical_goals,
			$nutrition_follow_up,
			$psychosocial,
			$vitals_monitoring,
			$vital_signs_monitor,
			$revenue,
			$annual_check_up
		);

		// Execute the query
		if ($query->execute()) {
			$query->close();
			return true;
		} else {
			$query->close();
			return false;
		}
	}
	function display_call_results()
	{
		// Assuming $this->conn is your mysqli connection
		$query = "SELECT * FROM call_results";  // Modify with your actual query
		$result = $this->conn->query($query);  // Execute the query and get the result

		if ($result === false) {
			die("Error in query: " . $this->conn->error);  // Handle query errors
		}

		return $result;  // Return the result as a mysqli_result object
	}
}
