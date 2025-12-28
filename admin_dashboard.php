<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
require 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-section { border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .log-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .log-table th, .log-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .log-table th { background-color: transparent; }
        .status-due { color: red; font-weight: bold; }
        .status-paid { color: green; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Admin Dashboard</h1>
            <a href="logout.php">Logout</a>
        </div>
        <div class="admin-section">
            <h2>Manage Buildings</h2>
            <form action="add_building_process.php" method="post" class="login-form" style="margin:0;">
                <label for="building_name">New Building Name:</label>
                <input type="text" id="building_name" name="building_name" required>
                <button type="submit">Add Building</button>
            </form>
        </div>
        <div class="admin-section">
            <h2>Manage Flats</h2>
            <form action="add_flat_process.php" method="post" class="login-form" style="margin:0;">
                <label for="flat_number">New Flat Number:</label>
                <input type="text" id="flat_number" name="flat_number" required>
                <label for="building_id_add">Select Building:</label>
                <select name="building_id" id="building_id_add" required style="padding: 10px; margin-bottom: 10px;">
                    <option value="">--Select a Building--</option>
                    <?php
                    $query = "SELECT * FROM buildings ORDER BY building_name";
                    $result = $conn->query($query);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['building_id'] . "'>" . htmlspecialchars($row['building_name']) . "</option>";
                        }
                    }
                    ?>
                </select>
                <button type="submit">Add Flat</button>
            </form>
        </div>
        <div class="admin-section">
            <h2>Create New User Account</h2>
            <form action="create_user_process.php" method="post" class="login-form" style="margin:0;" onsubmit="return validateForm()">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" required>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <label for="role">User Role:</label>
                <select name="role" id="role" required style="padding: 10px; margin-bottom: 10px;" onchange="toggleFlatSelection()">
                    <option value="">--Select Role--</option>
                    <option value="resident">Resident</option>
                    <option value="supervisor">Supervisor</option>
                </select>
                <div id="resident-options" style="display: none;">
                    <label for="flat_id">Assign to Vacant Flat:</label>
                    <select name="flat_id" id="flat_id" style="padding: 10px; margin-bottom: 10px;">
                        <option value="">--Select a Vacant Flat--</option>
                        <?php
                        $vacant_flats_query = "SELECT f.flat_id, f.flat_number, b.building_name 
                                               FROM flats f
                                               JOIN buildings b ON f.building_id = b.building_id
                                               WHERE f.flat_id NOT IN (SELECT flat_id FROM residents)
                                               ORDER BY b.building_name, f.flat_number";
                        $vacant_flats_result = $conn->query($vacant_flats_query);
                        if ($vacant_flats_result->num_rows > 0) {
                            while ($row = $vacant_flats_result->fetch_assoc()) {
                                echo "<option value='" . $row['flat_id'] . "'>" . $row['flat_number'] . " (" . $row['building_name'] . ")</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <button type="submit">Create User</button>
            </form>
        </div>
        <div class="admin-section">
            <h2>Maintenance Management</h2>
            <h4>Generate Monthly Dues</h4>
            <form action="generate_dues.php" method="post" class="login-form" style="margin:0;">
                <select name="month" required>
                    <?php for ($m=1; $m<=12; $m++) { echo '<option value="'.$m.'">'.date('F', mktime(0,0,0,$m, 1, date('Y'))).'</option>'; } ?>
                </select>
                <input type="number" name="year" value="<?php echo date('Y'); ?>" required>
                <input type="number" name="amount" placeholder="Amount (e.g., 2500)" step="0.01" required>
                <input type="date" name="due_date" required>
                <button type="submit">Generate Dues for All Flats</button>
            </form>
            <hr>
            <h4>View Dues Status (Latest Month)</h4>
            <table class="log-table">
                <thead><tr><th>Flat No.</th><th>Resident</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    <?php
                    $latest_due_q = $conn->query("SELECT MAX(year) as year, MAX(month) as month FROM maintenance");
                    if ($latest_due_q->num_rows > 0) {
                        $latest_due = $latest_due_q->fetch_assoc();
                        $latest_year = $latest_due['year'];
                        $latest_month = $latest_due['month'];

                        if($latest_year && $latest_month) {
                            $dues_query = "SELECT m.maintenance_id, f.flat_number, r.name as resident_name, m.amount, m.status 
                                        FROM maintenance m
                                        JOIN flats f ON m.flat_id = f.flat_id
                                        LEFT JOIN residents r ON f.flat_id = r.flat_id
                                        WHERE m.year = $latest_year AND m.month = $latest_month
                                        ORDER BY f.flat_number";
                            $dues_result = $conn->query($dues_query);
                            while ($row = $dues_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['flat_number']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['resident_name'] ?? 'Vacant') . "</td>";
                                echo "<td>" . htmlspecialchars($row['amount']) . "</td>";
                                $status_class = 'status-' . strtolower($row['status']);
                                echo "<td><span class='" . $status_class . "'>" . ucfirst($row['status']) . "</span></td>";
                                echo "<td>";
                                if ($row['status'] == 'due') {
                                    echo "<form action='update_payment.php' method='post' style='display:flex; gap: 5px;'>
                                            <input type='hidden' name='maintenance_id' value='" . $row['maintenance_id'] . "'>
                                            <select name='payment_mode' style='padding: 2px;'><option value='Cash'>Cash</option><option value='Online'>Online</option></select>
                                            <button type='submit' style='padding: 2px 5px;'>Paid</button>
                                          </form>";
                                }
                                echo "</td></tr>";
                            }
                        }
                    } else {
                        echo "<tr><td colspan='5'>No maintenance records found. Generate dues to begin.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <hr>
    </div>

    <script>
        function toggleFlatSelection() {
            var role = document.getElementById('role').value;
            var residentOptions = document.getElementById('resident-options');
            var flatSelect = document.getElementById('flat_id');
            if (role === 'resident') {
                residentOptions.style.display = 'block';
                flatSelect.required = true;
            } else {
                residentOptions.style.display = 'none';
                flatSelect.required = false;
            }
        }
        function validateForm() {
            var role = document.getElementById('role').value;
            var flatId = document.getElementById('flat_id').value;
            if (role === 'resident' && flatId === '') {
                alert('Please assign a vacant flat to the resident.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>