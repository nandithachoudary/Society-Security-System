<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: index.php");
    exit();
}
require 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supervisor Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-section { border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .data-table th { background-color: transparent; }
        .status-pending { color: #ff9800; font-weight: bold; }
        .status-approved { color: #4CAF50; font-weight: bold; }
        .status-checked_out { color: #607D8B; font-weight: bold; }
        .status-denied { color: #f44336; font-weight: bold; }
        .checkout-btn, .inactivate-btn { background-color: #f44336; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; }
        .success-msg { color: green; font-weight: bold; }
        .code-display { 
            background-color: #e0e0e0; 
            padding: 10px; 
            border-radius: 4px; 
            font-family: monospace; 
            font-size: 1.2em; 
            text-align: center; 
            color: black; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Supervisor Dashboard</h1>
            <a href="logout.php">Logout</a>
        </div>

        <div class="admin-section">
            <h2>Society Staff Management</h2>
            <?php
            if (isset($_GET['staff_status'])) {
                if ($_GET['staff_status'] == 'invalid_phone') {
                    echo "<p class='error'>❌ Error: Contact number must be 10 digits and not start with 0.</p>";
                }
                if ($_GET['staff_status'] == 'invalid_name') {
                    echo "<p class='error'>❌ Error: Name must contain only letters and spaces.</p>";
                }
                if ($_GET['staff_status'] == 'success') {
                    echo "<p class='success-msg'>✅ Staff member added successfully!</p>";
                }
                if ($_GET['staff_status'] == 'inactivated') {
                    echo "<p class='success-msg'>✅ Staff member has been inactivated.</p>";
                }
            }
            ?>
            <form id="add-staff-form" action="add_staff_process.php" method="post" class="login-form" style="margin:0;" onsubmit="return validateStaffForm();">
                <h4>Add New Staff Member</h4>
                <input type="text" id="staff_name" name="name" placeholder="Full Name" required>
                <input type="text" name="role" placeholder="Role (e.g., Security Guard)" required>
                <input type="text" id="staff_contact_number" name="contact_number" placeholder="Contact Number (10 digits)">
                <label for="joining_date">Joining Date:</label>
                <input type="date" name="joining_date" id="joining_date">
                <button type="submit">Add Staff</button>
            </form>
            <hr>
            <h4>Current Staff List</h4>
            <table class="data-table">
                <thead><tr><th>Name</th><th>Role</th><th>Contact</th><th>Joining Date</th><th>Action</th></tr></thead>
                <tbody>
                    <?php
                    $staff_query = "SELECT * FROM staff WHERE status = 'active' ORDER BY name";
                    $staff_result = $conn->query($staff_query);
                    if ($staff_result->num_rows > 0) {
                        while ($row = $staff_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['contact_number']) . "</td>";
                            echo "<td>" . ($row['joining_date'] ? date('d-M-Y', strtotime($row['joining_date'])) : 'N/A') . "</td>";
                            echo "<td>
                                    <a href='inactivate_staff.php?staff_id=" . $row['staff_id'] . "' class='inactivate-btn' onclick='return confirm(\"Are you sure you want to inactivate this staff member?\");'>Inactivate</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No active staff members found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="admin-section">
            <h2>Regular Visitor / Vendor Management</h2>
            <h4>Log Attendance</h4>
             <?php
                if (isset($_GET['log_status'])) {
                    $name = htmlspecialchars($_GET['name'] ?? '');
                    if ($_GET['log_status'] == 'checked_in') echo "<p class='success-msg'>✅ $name has been CHECKED IN.</p>";
                    if ($_GET['log_status'] == 'checked_out') echo "<p class='success-msg'>🚪 $name has been CHECKED OUT.</p>";
                    if ($_GET['log_status'] == 'invalid_code') echo "<p class='error'>❌ Error: Invalid Security Code.</p>";
                }
            ?>
            <form action="log_attendance.php" method="post" style="display: flex; gap: 10px; align-items: center;">
                <input type="text" name="security_code" placeholder="Enter Security Code" required style="flex-grow: 1; margin:0;">
                <button type="submit">Log Entry / Exit</button>
            </form>
            <hr style="margin: 20px 0;">
            <h4>Register New Regular Visitor</h4>
            <?php
                if (isset($_GET['reg_status']) && $_GET['reg_status'] == 'success') {
                    echo "<p class='success-msg'>Registration successful! Please provide this code to the visitor:</p>";
                    echo "<p class='code-display'>" . htmlspecialchars($_GET['new_code']) . "</p>";
                }
                 if (isset($_GET['reg_status']) && $_GET['reg_status'] == 'error') {
                    echo "<p class='error'>❌ Error: Could not register visitor.</p>";
                }
            ?>
            <form action="register_regular_visitor.php" method="post" class="login-form" style="margin:0;">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="text" name="role" placeholder="Role (e.g., House Help)" required>
                <select name="flat_id" required style="padding: 10px; margin-bottom: 10px;">
                    <option value="">--Assign to Flat--</option>
                     <?php
                        $query = "SELECT f.flat_id, f.flat_number, b.building_name, r.name AS resident_name FROM flats f JOIN buildings b ON f.building_id = b.building_id LEFT JOIN residents r ON f.flat_id = r.flat_id ORDER BY b.building_name, f.flat_number";
                        $result = $conn->query($query);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $label = $row['flat_number'] . " (" . $row['building_name'] . ") - " . ($row['resident_name'] ?? 'Vacant');
                                echo "<option value='" . $row['flat_id'] . "'>" . $label . "</option>";
                            }
                        }
                    ?>
                </select>
                <button type="submit">Register Visitor</button>
            </form>
        </div>
        
        <hr style="margin: 30px 0;">

        <div class="admin-section">
            <h2>Add New (Normal) Visitor</h2>
             <?php
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 'success') {
                    echo '<p class="success-msg">Visitor added successfully!</p>';
                }
                if ($_GET['status'] == 'error') {
                    echo '<p class="error">Failed to add visitor. Please try again.</p>';
                }
                if ($_GET['status'] == 'invalid_name') {
                    echo '<p class="error">Error: Visitor name must only contain letters and spaces.</p>';
                }
                if ($_GET['status'] == 'invalid_phone') {
                    echo '<p class="error">Error: Phone number must be 10 digits and start with 6, 7, 8, or 9.</p>';
                }
            }
            ?>
            <form id="add-visitor-form" action="add_visitor_process.php" method="post" class="login-form" style="margin: 0;" onsubmit="return validateNormalVisitorForm();">
                <label for="visitor_name_normal">Visitor's Name:</label>
                <input type="text" id="visitor_name_normal" name="visitor_name" required>
                <label for="contact_number_normal">Contact Number:</label>
                <input type="text" id="contact_number_normal" name="contact_number">
                <label for="flat_id_normal">Select Flat to Visit:</label>
                <select name="flat_id" id="flat_id_normal" required style="padding: 10px; margin-bottom: 10px;">
                    <option value="">--Select a Flat--</option>
                    <?php
                    $query2 = "SELECT f.flat_id, f.flat_number, b.building_name, r.name AS resident_name FROM flats f JOIN buildings b ON f.building_id = b.building_id LEFT JOIN residents r ON f.flat_id = r.flat_id ORDER BY b.building_name, f.flat_number";
                    $result2 = $conn->query($query2);
                    if ($result2->num_rows > 0) {
                        while ($row = $result2->fetch_assoc()) {
                            $label = $row['flat_number'] . " (" . $row['building_name'] . ") - " . ($row['resident_name'] ?? 'Vacant');
                            echo "<option value='" . $row['flat_id'] . "'>" . $label . "</option>";
                        }
                    }
                    ?>
                </select>
                <button type="submit">Add Visitor & Notify Resident</button>
            </form>
        </div>

        <div class="admin-section">
            <h2>Live (Normal) Visitor Log</h2>
             <table class="data-table">
                <thead>
                    <tr>
                        <th>Visitor Name</th>
                        <th>Visiting Flat</th>
                        <th>Request Time</th>
                        <th>Check-out Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $log_query = "SELECT v.visitor_id, v.name, v.request_time, v.check_out_time, v.status, f.flat_number, b.building_name 
                                  FROM visitors v 
                                  JOIN flats f ON v.flat_id = f.flat_id 
                                  JOIN buildings b ON f.building_id = b.building_id
                                  WHERE DATE(v.request_time) = CURDATE()
                                  ORDER BY v.visitor_id DESC";
                    
                    $log_result = $conn->query($log_query);
                    if ($log_result->num_rows > 0) {
                        while ($log_row = $log_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($log_row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($log_row['flat_number']) . " (" . htmlspecialchars($log_row['building_name']) . ")</td>";
                            echo "<td>" . ($log_row['request_time'] ? date('h:i A', strtotime($log_row['request_time'])) : 'N/A') . "</td>";
                            
                            $check_out_display = ''; 
                            if ($log_row['check_out_time']) {
                                $check_out_display = date('h:i A', strtotime($log_row['check_out_time']));
                            } else if ($log_row['status'] == 'approved') {
                                $check_out_display = '<i>Inside</i>';
                            }
                            echo "<td>" . $check_out_display . "</td>";
                            
                            $status_class = 'status-' . str_replace(' ', '_', strtolower($log_row['status']));
                            echo "<td><span class='" . $status_class . "'>" . ucfirst($log_row['status']) . "</span></td>";
                            
                            echo "<td>";
                            if ($log_row['status'] == 'approved') {
                                echo "<a href='checkout_visitor.php?visitor_id=" . $log_row['visitor_id'] . "' class='checkout-btn'>Checkout</a>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No normal visitors have been registered yet today.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function validateStaffForm() {
        var staffName = document.getElementById('staff_name').value;
        var contactNumber = document.getElementById('staff_contact_number').value;
        
        var nameRegex = /^[a-zA-Z ]+$/;
        var phoneRegex = /^[1-9][0-9]{9}$/; 

        if (!nameRegex.test(staffName)) {
            alert("Error: Name must contain only letters and spaces (e.g., 'Ramesh Singh').");
            return false;
        }
        
        if (contactNumber !== "" && !phoneRegex.test(contactNumber)) {
            alert("Error: Contact number must be exactly 10 digits long and cannot start with 0.");
            return false;
        }
        
        return true;
    }

    function validateNormalVisitorForm() {
        var visitorName = document.getElementById('visitor_name_normal').value;
        var contactNumber = document.getElementById('contact_number_normal').value;

        var nameRegex = /^[a-zA-Z ]+$/;
        var phoneRegex = /^[6-9][0-9]{9}$/; 

        if (!nameRegex.test(visitorName)) {
            alert("Error: Visitor name must contain only letters and spaces.");
            return false;
        }

        if (contactNumber !== "" && !phoneRegex.test(contactNumber)) {
            alert("Error: Contact number must be 10 digits and start with 6, 7, 8, or 9.");
            return false;
        }

        return true;
    }
    </script>
</body>
</html>