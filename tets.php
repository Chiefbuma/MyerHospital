                                                                    <div class="col-md-16">
                                                                        <div style="max-height: 300px; overflow-y: auto; scrollbar-width: none; -ms-overflow-style: none; padding: 10px; border-radius: 5px;">
                                                                            <table class="table table-sm table-bordered" style="width: 100%;">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Medication</th>
                                                                                        <th>Days Supplied</th>
                                                                                        <th>No. of Pills Dispensed</th>
                                                                                        <th>Frequency</th>
                                                                                        <th>Action</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <?php
                                                                                    // Set patient_id (this should come from user input, session, or a request parameter)
                                                                                    $patient_id = $fetch['patient_id']; // Example patient_id, replace with dynamic value

                                                                                    // Fetch medication records for the patient
                                                                                    $calls = $db->getMedicationByPatientId($patient_id);
                                                                                    $rowNumber = 1; // Initialize row number

                                                                                    // Fetch all medications to create a mapping of medication_id to item_name
                                                                                    $meds = $db->displayMedication();
                                                                                    $medicationMap = [];
                                                                                    if ($meds->num_rows > 0) {
                                                                                        while ($row = $meds->fetch_assoc()) {
                                                                                            $medicationMap[$row['medication_id']] = $row['item_name'];
                                                                                        }
                                                                                    }

                                                                                    // Check if records exist
                                                                                    if ($calls['count'] > 0) {
                                                                                        foreach ($calls['medications'] as $fetch) {
                                                                                            $medicationName = isset($medicationMap[$fetch['medication_id']]) ? $medicationMap[$fetch['medication_id']] : 'Unknown Medication';
                                                                                    ?>
                                                                                            <tr>
                                                                                                <form id="updateForm<?php echo $rowNumber; ?>" method="POST">
                                                                                                    <input type="hidden" name="medication_use_id" value="<?php echo htmlspecialchars($fetch['medication_use_id']); ?>">
                                                                                                    <input type="hidden" name="visit_date" value="<?php echo htmlspecialchars($fetch['visit_date']); ?>">
                                                                                                    <td>
                                                                                                        <input type="text" class="form-control" style="width: 150px; padding: 0px 10px;" value="<?php echo htmlspecialchars($medicationName); ?>" readonly>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <input type="number" name="days_supplied" id="days_supplied_<?php echo $rowNumber; ?>" class="form-control form-control-sm" style="width: 150px; padding: 0px 10px;" value="<?php echo htmlspecialchars($fetch['days_supplied']); ?>">
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <input type="number" name="no_pills_dispensed" id="no_pills_dispensed_<?php echo $rowNumber; ?>" class="form-control form-control-sm" style="width: 100px; padding: 0px 10px;" value="<?php echo htmlspecialchars($fetch['no_pills_dispensed']); ?>">
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <div class="col-md-2" style="padding: 5px;">
                                                                                                            <select name="frequency" class="form-control-sm custom-select" required id="frequency_<?php echo $rowNumber; ?>" class="form-control form-control-sm">
                                                                                                                <option value="OD" <?php echo ($fetch['frequency'] == 'OD') ? 'selected' : ''; ?>>OD</option>
                                                                                                                <option value="BD" <?php echo ($fetch['frequency'] == 'BD') ? 'selected' : ''; ?>>BD</option>
                                                                                                                <option value="TDS" <?php echo ($fetch['frequency'] == 'TDS') ? 'selected' : ''; ?>>TDS</option>
                                                                                                                <option value="QID" <?php echo ($fetch['frequency'] == 'QID') ? 'selected' : ''; ?>>QID</option>
                                                                                                                <option value="PRN" <?php echo ($fetch['frequency'] == 'PRN') ? 'selected' : ''; ?>>PRN</option>
                                                                                                                <option value="Weekly" <?php echo ($fetch['frequency'] == 'Weekly') ? 'selected' : ''; ?>>Weekly</option>
                                                                                                            </select>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <button type="button" class="btn btn-primary btn-sm update-button" style="padding: 2px 5px;" data-row-number="<?php echo $rowNumber; ?>">Update</button>
                                                                                                        <button type="button" class="btn btn-link text-danger p-0 delete-item" data-id="<?php echo $fetch['medication_use_id']; ?>">
                                                                                                            <i class="fa fa-trash"></i>
                                                                                                        </button>
                                                                                                    </td>
                                                                                                </form>
                                                                                            </tr>
                                                                                    <?php
                                                                                            $rowNumber++;
                                                                                        }
                                                                                    }
                                                                                    ?>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>