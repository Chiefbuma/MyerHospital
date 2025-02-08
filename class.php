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
		try {
			// Prepare the SQL query
			$query = $this->conn->prepare("
            INSERT INTO `users` (`username`, `password`, `firstname`, `lastname`, `cohort_id`, `branch_id`) 
            VALUES(?, ?, ?, ?, ?, ?)
        ");

			// Bind parameters
			$query->bind_param("ssssii", $username, $password, $firstname, $lastname, $cohort, $branch);

			// Execute the query and handle the result
			if ($query->execute()) {
				$query->close();
				$this->conn->close();
				return true;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Update User Function */

	public function update_user($user_id, $username, $password, $firstname, $lastname, $cohort, $branch)
	{
		try {
			// Prepare the SQL query
			$query = $this->conn->prepare("
            UPDATE `users` 
            SET `username`=?, `password`=?, `firstname`=?, `lastname`=?, `cohort_id`=?, `branch_id`=? 
            WHERE `user_id`=?
        ");

			// Bind parameters
			$query->bind_param("ssssiii", $username, $password, $firstname, $lastname, $cohort, $branch, $user_id);

			// Execute the query and handle the result
			if ($query->execute()) {
				$query->close();
				$this->conn->close();
				return true;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}


	public function login($email, $password)
	{
		try {
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
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return array(
				'user_id' => 0,
				'password_hash' => 0,
				'count' => 0
			);
		}
	}

	public function getMedicationByPatientId($patient_id)
	{
		try {
			// Prepare the SQL query to fetch medication_use records based on patient_id
			$query = $this->conn->prepare("SELECT * FROM `medication_use` WHERE `patient_id` = ?");
			$query->bind_param("i", $patient_id); // Bind patient_id as an integer

			if ($query->execute()) {
				$result = $query->get_result();

				// Check if records exist
				if ($result->num_rows > 0) {
					$medications = [];

					while ($row = $result->fetch_assoc()) {
						$medications[] = $row;
					}

					return array(
						'medications' => $medications,
						'count' => count($medications)
					);
				} else {
					// No records found
					return array(
						'medications' => [],
						'count' => 0
					);
				}
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return array(
				'medications' => [],
				'count' => 0
			);
		}
	}



	public function user_acc($user_id)
	{
		try {
			$query = $this->conn->prepare("SELECT * FROM `users` WHERE `id`=?");
			$query->bind_param("i", $user_id); // Bind the user_id parameter

			if ($query->execute()) {
				$result = $query->get_result();

				if ($result->num_rows > 0) {
					$fetch = $result->fetch_array();
					return $fetch['email'] . " " . $fetch['email'];
				} else {
					return "User not found";
				}
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return "Error fetching user account";
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
		try {
			// Prepare the SQL query to fetch all users and join with the 'branch' and 'cohort' tables
			$query = $this->conn->prepare("
            SELECT users.*, branch.*, cohort.* 
            FROM `users`
            LEFT JOIN `branch` ON users.branch_id = branch.branch_id
            LEFT JOIN `cohort` ON users.cohort_id = cohort.cohort_id
        ");

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
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Display User Function */
	public function user_details($user_id)
	{
		try {
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
        ");

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
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Delete User Function */
	public function delete_user($user_id)
	{
		try {
			// Prepare the SQL query to delete the user by user_id
			$query = $this->conn->prepare("DELETE FROM `user` WHERE `user_id` = ?");

			// Bind the user_id parameter to the query
			$query->bind_param("i", $user_id); // "i" indicates an integer parameter

			// Execute the query
			if ($query->execute()) {
				// Close the query and connection
				$query->close();
				$this->conn->close();
				return true; // Return true if the deletion was successful
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Insert Patient Function */
	public function insert_patient($firstname, $lastname, $dob, $gender, $age, $location, $route_id, $phone_no, $email, $patient_no, $diagnosis_id, $patient_status, $branch_id, $scheme_id, $cohort_id)
	{
		try {
			// Prepare the SQL query to insert a new patient record (no patient_id included)
			$query = $this->conn->prepare("INSERT INTO `patient` (`firstname`, `lastname`, `dob`, `gender`, `age`, `location`, `route_id`, `phone_no`, `email`, `patient_no`, `diagnosis_id`, `patient_status`, `branch_id`, `scheme_id`, `cohort_id`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

			// Bind parameters (removed patient_id)
			$query->bind_param("ssssisssssssiss", $firstname, $lastname, $dob, $gender, $age, $location, $route_id, $phone_no, $email, $patient_no, $diagnosis_id, $patient_status, $branch_id, $scheme_id, $cohort_id);

			// Execute the query
			if ($query->execute()) {
				// Close the query
				$query->close();
				return true; // Return true if the insert is successful
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function update_patient($patient_id, $firstname, $lastname, $dob, $gender, $age, $location, $route_id, $phone_no, $email, $patient_no, $diagnosis_id, $patient_status, $branch_id, $scheme_id, $cohort_id)
	{
		try {
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
			");

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
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Delete Patient Function */
	public function delete_patient($patient_id)
	{
		try {
			// Prepare the SQL query to delete a patient record
			$query = $this->conn->prepare("DELETE FROM `patient` WHERE `patient_id`=?");

			// Bind parameters
			$query->bind_param("i", $patient_id);

			// Execute the query
			if ($query->execute()) {
				// Close the query
				$query->close();
				return true; // Return true if the delete is successful
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Display Patient Function */
	public function display_patient()
	{
		try {
			// Assuming $this->conn is your mysqli connection
			$query = "SELECT * FROM patient";  // Modify with your actual query
			$result = $this->conn->query($query);  // Execute the query and get the result

			if ($result === false) {
				throw new Exception("Error in query: " . $this->conn->error);  // Handle query errors
			}

			return $result;  // Return the result as a mysqli_result object
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function displaypatient()
	{
		try {
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
				throw new Exception("Error in query: " . $this->conn->error);  // Handle query errors
			}

			return $result;  // Return the result as a mysqli_result object
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function countPatients($db)
	{
		try {
			$query = "SELECT COUNT(*) AS patient_count FROM patient";
			$result = $db->query($query);  // Use the query method

			if ($result) {
				$row = $result->fetch_assoc();
				return $row['patient_count'];
			} else {
				throw new Exception("Error executing query: " . $db->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return 0;
		}
	}

	/* Function to create a new call record */
	public function create_call($patient_id, $call_results)
	{
		try {
			// Prepare the SQL query to insert a new call record
			$query = $this->conn->prepare("INSERT INTO `calls` (`patient_id`, `call_results`) VALUES (?, ?)");

			// Bind parameters
			$query->bind_param("is", $patient_id, $call_results);

			// Execute the query and return the result
			if ($query->execute()) {
				$query->close();
				return true;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Function to display all call records */
	public function display_calls()
	{
		try {
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
        ");

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
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}


	/* Function to delete a call record by call_id */
	public function delete_call($call_id)
	{
		try {
			// Prepare the SQL query to delete a specific call record
			$query = $this->conn->prepare("DELETE FROM `calls` WHERE `call_id` = ?");

			// Bind the parameter
			$query->bind_param("i", $call_id);

			// Execute the query and return the result
			if ($query->execute()) {
				$query->close();
				return true;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Update Call Function */
	public function update_call($call_id, $patient_id, $call_results)
	{
		try {
			// Prepare the SQL query to update the call record
			$query = $this->conn->prepare("UPDATE `calls` SET `patient_id`=?, `call_results`=? WHERE `call_id`=?");

			// Bind parameters
			$query->bind_param("ssi", $patient_id, $call_results, $call_id);

			// Execute the query
			if ($query->execute()) {
				// Close the query
				$query->close();
				return true; // Return true if the update is successful
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Insert Scheme Function */
	public function insert_scheme($scheme_name, $payment_method)
	{
		try {
			// Prepare the SQL query to insert a new scheme
			$query = $this->conn->prepare("INSERT INTO `scheme` (`scheme_name`, `payment_method`) VALUES (?, ?)");

			// Bind parameters
			$query->bind_param("ss", $scheme_name, $payment_method);

			// Execute the query
			if ($query->execute()) {
				// Close the query
				$query->close();
				return true; // Return true if the insert is successful
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Update Scheme Function */
	public function update_scheme($scheme_id, $scheme_name, $payment_method)
	{
		try {
			// Prepare the SQL query to update a scheme record
			$query = $this->conn->prepare("UPDATE `scheme` SET `scheme_name`=?, `payment_method`=? WHERE `scheme_id`=?");

			// Bind parameters
			$query->bind_param("ssi", $scheme_name, $payment_method, $scheme_id);

			// Execute the query
			if ($query->execute()) {
				// Close the query
				$query->close();
				return true; // Return true if the update is successful
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Delete Scheme Function */
	public function delete_scheme($scheme_id)
	{
		try {
			// Prepare the SQL query to delete a scheme record
			$query = $this->conn->prepare("DELETE FROM `scheme` WHERE `scheme_id`=?");

			// Bind parameters
			$query->bind_param("i", $scheme_id);

			// Execute the query
			if ($query->execute()) {
				// Close the query
				$query->close();
				return true; // Return true if the delete is successful
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Display Scheme Function */
	public function display_scheme()
	{
		try {
			// Prepare the SQL query to fetch all schemes
			$query = $this->conn->prepare("SELECT * FROM `scheme`");

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
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}


	/* Display Medication Function */
	public function display_medication()
	{
		try {
			// Prepare the SQL query to fetch all medications
			$query = $this->conn->prepare("SELECT * FROM `medication`");

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
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Update Medication Function */
	public function update_medication($medication_id, $medication_name)
	{
		try {
			// Prepare the SQL query to update a medication record
			$query = $this->conn->prepare("UPDATE `medication` SET `medication_name`=? WHERE `medication_id`=?");

			// Bind parameters
			$query->bind_param("si", $medication_name, $medication_id);

			// Execute the query
			if ($query->execute()) {
				// Close the query
				$query->close();
				return true; // Return true if the update is successful
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Delete Medication Function */
	public function delete_medication($medication_id)
	{
		try {
			// Prepare the SQL query to delete a medication record
			$query = $this->conn->prepare("DELETE FROM `medication` WHERE `medication_id`=?");

			// Bind parameters
			$query->bind_param("i", $medication_id);

			// Execute the query
			if ($query->execute()) {
				// Close the query
				$query->close();
				return true; // Return true if the delete is successful
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Insert Medication Function */
	public function insert_medication($medication_name)
	{
		try {
			// Prepare the SQL query to insert a new medication
			$query = $this->conn->prepare("INSERT INTO `medication` (`medication_name`) VALUES (?)");

			// Bind parameters
			$query->bind_param("s", $medication_name);

			// Execute the query
			if ($query->execute()) {
				// Close the query
				$query->close();
				return true; // Return true if the insert is successful
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Insert Branch Function */
	public function insert_branch($branch_name)
	{
		try {
			// Prepare the SQL query to insert a new branch
			$query = $this->conn->prepare("INSERT INTO `branch` (`branch_name`) VALUES (?)");

			// Bind parameters
			$query->bind_param("s", $branch_name);

			// Execute the query
			if ($query->execute()) {
				// Close the query
				$query->close();
				return true; // Return true if the insert is successful
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Update Branch Function */
	public function update_branch($branch_id, $branch_name)
	{
		try {
			// Prepare the SQL query to update a branch record
			$query = $this->conn->prepare("UPDATE `branch` SET `branch_name`=? WHERE `branch_id`=?");

			// Bind parameters
			$query->bind_param("si", $branch_name, $branch_id);

			// Execute the query
			if ($query->execute()) {
				// Close the query
				$query->close();
				return true; // Return true if the update is successful
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Delete Branch Function */
	public function delete_branch($branch_id)
	{
		try {
			// Prepare the SQL query to delete a branch record
			$query = $this->conn->prepare("DELETE FROM `branch` WHERE `branch_id`=?");

			// Bind parameters
			$query->bind_param("i", $branch_id);

			// Execute the query
			if ($query->execute()) {
				// Close the query
				$query->close();
				return true; // Return true if the delete is successful
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	function displaybranch()
	{
		try {
			// Assuming $this->conn is your mysqli connection
			$query = "SELECT * FROM `branch`";  // Modify with your actual query
			$result = $this->conn->query($query);  // Execute the query and get the result

			if ($result === false) {
				throw new Exception("Error in query: " . $this->conn->error);  // Handle query errors
			}

			return $result;  // Return the result as a mysqli_result object
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Insert Cohort Function */
	public function insert_cohort($cohort_name, $team_lead)
	{
		try {
			// Prepare the SQL query to insert a new cohort
			$query = $this->conn->prepare("INSERT INTO `cohort` (`cohort_name`, `team_lead`) VALUES (?, ?)");

			// Bind parameters
			$query->bind_param("ss", $cohort_name, $team_lead);

			// Execute the query
			if ($query->execute()) {
				// Close the query
				$query->close();
				return true; // Return true if the insert is successful
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Update Cohort Function */
	public function update_cohort($cohort_id, $cohort_name, $team_lead)
	{
		try {
			// Prepare the SQL query to update a cohort record
			$query = $this->conn->prepare("UPDATE cohort SET cohort_name = ?, team_lead = ? WHERE cohort_id = ?");

			// Bind parameters
			$query->bind_param("ssi", $cohort_name, $team_lead, $cohort_id);

			// Execute the query
			if ($query->execute()) {
				// Close the query
				$query->close();
				return true; // Return true if the update is successful
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Delete Cohort Function */
	public function delete_cohort($cohort_id)
	{
		try {
			// Prepare the SQL query to delete a cohort record
			$query = $this->conn->prepare("DELETE FROM `cohort` WHERE `cohort_id`=?");

			// Bind parameters
			$query->bind_param("i", $cohort_id);

			// Execute the query
			if ($query->execute()) {
				// Close the query
				$query->close();
				return true; // Return true if the delete is successful
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	/* Display Cohort Function */
	function display_cohort()
	{
		try {
			// Assuming $this->conn is your mysqli connection
			$query = "SELECT * FROM `cohort`";  // Modify with your actual query
			$result = $this->conn->query($query);  // Execute the query and get the result

			if ($result === false) {
				throw new Exception("Error in query: " . $this->conn->error);  // Handle query errors
			}

			return $result;  // Return the result as a mysqli_result object
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}



	public function display_diagnosis()
	{
		try {
			// Prepare the SQL query to fetch all diagnoses
			$query = $this->conn->prepare("SELECT * FROM `diagnosis` ORDER BY `diagnosis_id` ASC");

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
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function deleteDiagnosis($diagnosis_id)
	{
		try {
			// Prepare the SQL query to delete a diagnosis record
			$query = $this->conn->prepare("DELETE FROM diagnosis WHERE id=?");

			// Bind the parameter (diagnosis_id) to the prepared statement
			$query->bind_param("i", $diagnosis_id);

			// Execute the query
			if ($query->execute()) {
				// Close the query
				$query->close();
				return true; // Return true if the delete is successful
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function updateDiagnosis($diagnosis_id, $diagnosis_name, $idc10)
	{
		try {
			// Prepare the SQL query to update a diagnosis record
			$query = $this->conn->prepare("UPDATE diagnosis SET diagnosis_name=?, idc10=? WHERE id=?");

			// Bind parameters
			$query->bind_param("ssi", $diagnosis_name, $idc10, $diagnosis_id);

			// Execute the query
			if ($query->execute()) {
				// Close the query
				$query->close();
				return true; // Return true if the update is successful
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function insertDiagnosis($diagnosis_name, $idc10)
	{
		try {
			// Prepare the SQL query to insert a new diagnosis
			$query = $this->conn->prepare("INSERT INTO diagnosis (diagnosis_name, idc10) VALUES (?, ?)");

			// Bind parameters
			$query->bind_param("ss", $diagnosis_name, $idc10);

			// Execute the query
			if ($query->execute()) {
				// Close the query
				$query->close();
				return true; // Return true if the insert is successful
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	function display_route()
	{
		try {
			// Assuming $this->conn is your mysqli connection
			$query = "SELECT * FROM `route`";  // Modify with your actual query
			$result = $this->conn->query($query);  // Execute the query and get the result

			if ($result === false) {
				throw new Exception("Error in query: " . $this->conn->error);  // Handle query errors
			}

			return $result;  // Return the result as a mysqli_result object
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	function display_visit()
	{
		try {
			// Assuming $this->conn is your mysqli connection
			$query = "SELECT * FROM `visit`";  // Modify with your actual query
			$result = $this->conn->query($query);  // Execute the query and get the result

			if ($result === false) {
				throw new Exception("Error in query: " . $this->conn->error);  // Handle query errors
			}

			return $result;  // Return the result as a mysqli_result object
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function display_visit_patient()
	{
		try {
			$query = $this->conn->prepare("
            SELECT 
                patient.*,               -- Select all columns from the patient table
                nutrition.*,             -- Select all columns from the nutrition table
                medication_use.*,        -- Select all columns from the medication_use table
                psychosocial.*,          -- Select all columns from the psychosocial table
                chronic.*,               -- Select all columns from the chronic table
                branch.*,                -- Select all columns from the branch table
                calls.*,                 -- Select all columns from the calls table
                chronic.*                -- Select all columns from chronic table
            FROM 
                patient
            INNER JOIN 
                chronic ON patient.patient_id = chronic.patient_id
        ");

			if ($query->execute()) {
				$result = $query->get_result(); // Fetch the result set
				return $result;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}


	public function display_chronic_patient()
	{
		try {
			$query = $this->conn->prepare("
            SELECT 
                patient.*, 
                chronic.*
            FROM 
                patient
            LEFT JOIN 
                chronic ON patient.patient_id = chronic.patient_id
        ");

			if ($query->execute()) {
				$result = $query->get_result(); // Fetch the result set
				return $result;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function display_chronic()
	{
		try {
			$query = $this->conn->prepare("
            SELECT 
                patient.*,  -- Select all columns from the patient table
                chronic.*  -- Select all columns from the chronic table
            FROM 
                chronic
            LEFT JOIN 
                patient ON patient.patient_id = chronic.patient_id
        ");

			if ($query->execute()) {
				$result = $query->get_result(); // Fetch the result set
				return $result;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function display_nutrition_patient()
	{
		try {
			$query = $this->conn->prepare("
            SELECT 
                patient.*, 
                nutrition.*
            FROM 
                patient
            LEFT JOIN 
                nutrition ON patient.patient_id = nutrition.patient_id
        ");

			if ($query->execute()) {
				$result = $query->get_result(); // Fetch the result set
				return $result;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function display_psychosocial_patient()
	{
		try {
			$query = $this->conn->prepare("
            SELECT 
                patient.*, 
                psychosocial.*
            FROM 
                patient
            LEFT JOIN 
                psychosocial ON patient.patient_id = psychosocial.patient_id
        ");

			if ($query->execute()) {
				$result = $query->get_result(); // Fetch the result set
				return $result;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function display_calls_patient()
	{
		try {
			$query = $this->conn->prepare("
            SELECT 
                patient.*,               
                nutrition.*,             
                medication_use.*,        
                psychosocial.*,          
                chronic.*,               
                branch.*,                
                calls.*,                 
                chronic.refill_date,     
                psychosocial.next_review, 
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
        ");

			if ($query->execute()) {
				$result = $query->get_result();

				// Convert result to array if needed
				return $result ? $result : [];
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return [];
		}
	}
	public function display_calls_med()
	{
		try {
			$query = $this->conn->prepare("
            SELECT 
                patient.*,             
                medication_use.*,   
                calls.*,     
                CASE
                    WHEN medication_use.patient_id IS NOT NULL THEN 'Medication'
                    ELSE 'Unknown'
                END AS assessment
            FROM 
                patient
            INNER JOIN 
                calls ON patient.patient_id = calls.patient_id
            LEFT JOIN 
                medication_use ON patient.patient_id = medication_use.patient_id
        ");

			if (!$query) {
				throw new Exception("Query preparation failed: " . $this->conn->error);
			}

			if ($query->execute()) {
				$result = $query->get_result();

				if ($result === false) {
					throw new Exception("Error fetching result: " . $query->error);
				}

				$data = [];
				while ($row = $result->fetch_assoc()) {
					$data[] = $row;
				}

				return $data; // Return as an array
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return [];
		}
	}


	public function displa_patient()
	{
		try {
			// Assuming $this->conn is your mysqli connection
			$query = "
        SELECT 
            patient_id,
            firstname,
            lastname,
            dob,
            gender,
            age,
            location,
            phone_no,
            patient_no,
            email,
            diagnosis,
            patient_status,
            cohort_id,
            branch_id,
            diagnosis_id,
            scheme_id,
            route_id
        FROM 
            patient
        ";

			$result = $this->conn->query($query);  // Execute the query and get the result

			if ($result === false) {
				throw new Exception("Error in query: " . $this->conn->error);  // Handle query errors
			}

			return $result;  // Return the result as a mysqli_result object
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function visitsummary()
	{
		try {
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
        ");

			if ($query->execute()) {
				$result = $query->get_result(); // Fetch the result set
				return $result;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function visitreport($patient_id)
	{
		try {
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
                         psychosocial.visit_date, medication_use.visit_date) AS assessment_date
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
        ");

			// Bind the parameter
			$query->bind_param("i", $patient_id);

			// Execute the query
			if ($query->execute()) {
				$result = $query->get_result();
				return $result;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}


	public function AssesChronic()
	{
		try {
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
        ");

			if ($query === false) {
				throw new Exception("Error preparing query: " . $this->conn->error);
			}

			if ($query->execute()) {
				$result = $query->get_result(); // Fetch the result set
				return $result;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function AssessPhysiotherapy()
	{
		try {
			$query = $this->conn->prepare("
            SELECT 
                patient.patient_id,       -- Patient ID
                patient.scheme_id,        -- Scheme ID
                physiotherapy.visit_date, -- Visit date from physiotherapy
                patient.*,                -- Select all columns from the patient table
                physiotherapy.*,          -- Select all columns from the physiotherapy table
                physiotherapy.progress,   -- Display progress from physiotherapy
                CASE
                    WHEN physiotherapy.patient_id IS NOT NULL THEN 'Physiotherapy'
                    ELSE 'Unknown'
                END AS assessment
            FROM 
                patient
            LEFT JOIN 
                physiotherapy ON patient.patient_id = physiotherapy.patient_id
            LEFT JOIN 
                calls ON patient.patient_id = calls.patient_id
            GROUP BY 
                patient.patient_id, 
                patient.scheme_id, 
                physiotherapy.visit_date
        ");

			if ($query === false) {
				throw new Exception("Error preparing query: " . $this->conn->error);
			}

			if ($query->execute()) {
				$result = $query->get_result(); // Fetch the result set
				return $result;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}


	public function AssesNutrition()
	{
		try {
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
        ");

			if ($query === false) {
				throw new Exception("Error preparing query: " . $this->conn->error);
			}

			if ($query->execute()) {
				$result = $query->get_result(); // Fetch the result set
				return $result;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function asseMedication()
	{
		try {
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
        ");

			if ($query === false) {
				throw new Exception("Error preparing query: " . $this->conn->error);
			}

			if ($query->execute()) {
				$result = $query->get_result(); // Fetch the result set
				return $result;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}




	public function getMedicationUseByPatientId($patient_id)
	{
		try {
			$query = $this->conn->prepare("
            SELECT 
                mu.*, 
                m.item_name 
            FROM 
                medication_use mu
            JOIN 
                medication m ON m.medication_id = mu.medication_id
            WHERE 
                mu.patient_id = ?
        ");

			if ($query === false) {
				throw new Exception("Error preparing query: " . $this->conn->error);
			}

			$query->bind_param("i", $patient_id);  // Bind the patient_id parameter

			if ($query->execute()) {
				$result = $query->get_result(); // Fetch the result set
				return $result;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function AssesPsychosocial()
	{
		try {
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
        ");

			if ($query === false) {
				throw new Exception("Error preparing query: " . $this->conn->error);
			}

			if ($query->execute()) {
				$result = $query->get_result(); // Fetch the result set
				return $result;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function chronic()
	{
		try {
			$query = $this->conn->prepare("
            SELECT 
                patient.*,           -- Select all columns from the patient table
                chronic.*,           -- Select all columns from the chronic table
                calls.*,             -- Select all columns from the calls table
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
        ");

			if ($query === false) {
				throw new Exception("Error preparing query: " . $this->conn->error);
			}

			if ($query->execute()) {
				$result = $query->get_result(); // Fetch the result set
				return $result;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}



	public function display_call()
	{
		try {
			$query = $this->conn->prepare("
            SELECT 
                patient.*,           -- Select all columns from the patient table
                nutrition.*,         -- Select all columns from the nutrition table
                medication_use.*,     -- Select all columns from the medication_use table
                psychosocial.*,       -- Select all columns from the psychosocial table
                chronic.*,            -- Select all columns from the chronic table
                branch.*,             -- Select all columns from the branch table
                calls.*,              -- Select all columns from the calls table
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
        ");

			if ($query === false) {
				throw new Exception("Error preparing query: " . $this->conn->error);
			}

			if ($query->execute()) {
				$result = $query->get_result(); // Fetch the result set
				if ($result) {
					$data = [];
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
						$data[] = $row;
					}
					return $data;  // Return the result as an array
				} else {
					throw new Exception("Error: No data returned.");
				}
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function display_calls_chronic()
	{
		try {
			$query = $this->conn->prepare("
            SELECT 
                patient.*,           -- Select all columns from the patient table
                chronic.*,           -- Select all columns from the chronic table
                chronic.refill_date, -- Display chronic refill date
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
        ");

			if ($query === false) {
				throw new Exception("Error preparing query: " . $this->conn->error);
			}

			if ($query->execute()) {
				$result = $query->get_result(); // Fetch the result set
				return $result;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function display_monitoring_patient()
	{
		try {
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
        ");

			if ($query === false) {
				throw new Exception("Error preparing query: " . $this->conn->error);
			}

			if ($query->execute()) {
				$result = $query->get_result();
				return $result;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function display_checkup_patient()
	{
		try {
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
			");

			if ($query === false) {
				throw new Exception("Error preparing query: " . $this->conn->error);
			}

			if ($query->execute()) {
				$result = $query->get_result();
				return $result;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function display_payment_methods()
	{
		try {
			$query = $this->conn->prepare("SELECT payment_id, patient_id, branch_id, scheme_id, payment_method, revenue FROM `payment`");

			if ($query === false) {
				throw new Exception("Error preparing query: " . $this->conn->error);
			}

			if ($query->execute()) {
				$result = $query->get_result();
				$payments = $result->fetch_all(MYSQLI_ASSOC);
				$result->free();
				$query->close();
				return $payments;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function display_schemes()
	{
		try {
			$query = $this->conn->prepare("SELECT scheme_id, scheme_name, payment_method FROM scheme");

			if ($query === false) {
				throw new Exception("Error preparing query: " . $this->conn->error);
			}

			if ($query->execute()) {
				$result = $query->get_result();
				$schemes = $result->fetch_all(MYSQLI_ASSOC);
				$result->free();
				$query->close();
				return $schemes;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	function displayMedication()
	{
		try {
			$query = "SELECT * FROM `medication`";
			$result = $this->conn->query($query);

			if ($result === false) {
				throw new Exception("Error in query: " . $this->conn->error);
			}

			return $result;
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}


	function displayProcedures()
	{
		try {
			$query = "SELECT * FROM `procedures`";
			$result = $this->conn->query($query);

			if ($result === false) {
				throw new Exception("Error in query: " . $this->conn->error);
			}

			return $result;
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	function displayspecialist()
	{
		try {
			$query = "SELECT * FROM `specialist`";
			$result = $this->conn->query($query);

			if ($result === false) {
				throw new Exception("Error in query: " . $this->conn->error);
			}
			return $result;
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	function display_branch()
	{
		try {
			$query = "SELECT * FROM `branch`";
			$result = $this->conn->query($query);

			if ($result === false) {
				throw new Exception("Error in query: " . $this->conn->error);
			}

			return $result;
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}


	public function insert_nutrition($patient_id, $scheme_id, $last_visit, $next_review, $muscle_mass, $bone_mass, $weight, $BMI, $subcutaneous_fat, $visceral_fat, $weight_remarks, $physical_activity, $meal_plan_set_up, $nutrition_adherence, $nutrition_assessment_remarks, $revenue)
	{
		try {
			$query = $this->conn->prepare("INSERT INTO `nutrition` 
			(`patient_id`, `scheme_id`, `last_visit`, `next_review`, `muscle_mass`, `bone_mass`, `weight`, `BMI`, `subcutaneous_fat`, `visceral_fat`, `weight_remarks`, `physical_activity`, `meal_plan_set_up`, `nutrition_adherence`, `nutrition_assessment_remarks`, `revenue`) 
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

			if ($query === false) {
				throw new Exception("Error preparing query: " . $this->conn->error);
			}

			$query->bind_param(
				"iissssssssssssss",
				$patient_id,
				$scheme_id,
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

			if ($query->execute()) {
				$query->close();
				return true;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function insert_psychosocial_assessment($last_visit, $next_review, $muscle_mass, $bone_mass, $weight, $BMI, $subcutaneous_fat, $visceral_fat, $weight_remarks, $physical_activity, $meal_plan_set_up, $nutrition_adherence, $nutrition_assessment_remarks, $branch_id, $patient_id)
	{
		try {
			$query = $this->conn->prepare("INSERT INTO `psychosocial_assessment` 
			(`last_visit`, `next_review`, `muscle_mass`, `bone_mass`, `weight`, `BMI`, `subcutaneous_fat`, `visceral_fat`, `weight_remarks`, `physical_activity`, `meal_plan_set_up`, `nutrition_adherence`, `nutrition_assessment_remarks`, `branch_id`, `patient_id`) 
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

			if ($query === false) {
				throw new Exception("Error preparing query: " . $this->conn->error);
			}

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

			if ($query->execute()) {
				$query->close();
				return true;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function insert_medication_use($days_supplied, $no_pills_dispensed, $frequency, $patient_id)
	{
		try {
			$query = $this->conn->prepare("INSERT INTO `medication_use` 
			(`days_supplied`, `no_pills_dispensed`, `frequency`, `patient_id`) 
			VALUES (?, ?, ?, ?)");

			if ($query === false) {
				throw new Exception("Error preparing query: " . $this->conn->error);
			}

			$query->bind_param(
				"iiis",
				$days_supplied,
				$no_pills_dispensed,
				$frequency,
				$patient_id
			);

			if ($query->execute()) {
				$query->close();
				return true;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	public function insert_chronic_care($patient_id, $last_visit, $days_supplied, $procedure, $specialty, $refill_date, $compliance, $exercise, $clinical_goals, $nutrition_follow_up, $psychosocial, $vitals_monitoring, $vital_signs_monitor, $revenue, $annual_check_up)
	{
		try {
			$query = $this->conn->prepare("INSERT INTO `chronic` 
		(`patient_id`, `last_visit`, `days_supplied`, `procedure`, `specialty`, `refill_date`, `compliance`, `exercise`, `clinical_goals`, `nutrition_follow_up`, `psychosocial`, `vitals_monitoring`, `vital_signs_monitor`, `revenue`, `annual_check_up`) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

			if ($query === false) {
				throw new Exception("Error preparing query: " . $this->conn->error);
			}

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

			if ($query->execute()) {
				$query->close();
				return true;
			} else {
				throw new Exception("Error executing query: " . $query->error);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}

	function display_call_results()
	{
		try {
			$query = "SELECT * FROM call_results";
			$result = $this->conn->query($query);

			if ($result === false) {
				throw new Exception("Error in query: " . $this->conn->error);
			}

			return $result;
		} catch (Exception $e) {
			error_log($e->getMessage());
			echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
			return false;
		}
	}
}
