<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'resident' || !isset($_SESSION['resident_id'])) {
    header("Location: index.php");
    exit();
}
require 'db_connect.php';

$stmt_flat = $conn->prepare("SELECT flat_id FROM residents WHERE resident_id = ?");
$stmt_flat->bind_param("i", $_SESSION['resident_id']);
$stmt_flat->execute();
$stmt_flat->store_result();
if($stmt_flat->num_rows === 0) { die("Error: Critical - Could not find resident details."); }
$stmt_flat->bind_result($flat_id);
$stmt_flat->fetch();
$stmt_flat->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resident Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .log-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .log-table th, .log-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .log-table th { background-color: transparent; }
        .status-due { color: red; font-weight: bold; }
        .status-paid { color: green; font-weight: bold; }
        .visitor-card button.reject-btn {
            background-color: #f44336; 
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Resident Dashboard</h1>
            <a href="logout.php">Logout</a>
        </div>
        
        <h2>Pending Visitor Requests</h2>
        <div id="pending-requests-container">
            </div>

        <hr style="margin: 30px 0;">

        <h2>Regular Staff Attendance Log</h2>
        <table class="log-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Date</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $log_query = $conn->prepare("SELECT rv.name, rv.role, al.check_in_time, al.check_out_time 
                                             FROM attendance_log al
                                             JOIN regular_visitors rv ON al.regular_visitor_id = rv.regular_visitor_id
                                             WHERE rv.flat_id = ? 
                                             ORDER BY al.check_in_time DESC LIMIT 20");
                $log_query->bind_param("i", $flat_id);
                $log_query->execute();
                $log_query->store_result(); 
                $log_query->bind_result($name, $role, $check_in, $check_out); 
                
                if ($log_query->num_rows > 0) {
                    while ($log_query->fetch()) { 
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($name) . "</td>";
                        echo "<td>" . htmlspecialchars($role) . "</td>";
                        echo "<td>" . date('d-M-Y', strtotime($check_in)) . "</td>";
                        echo "<td>" . date('h:i A', strtotime($check_in)) . "</td>";
                        echo "<td>" . ($check_out ? date('h:i A', strtotime($check_out)) : '<i>Still inside</i>') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No attendance records found for your regular staff.</td></tr>";
                }
                $log_query->close();
                ?>
            </tbody>
        </table>

        <hr style="margin: 30px 0;">

        <h2>Maintenance History</h2>
        <table class="log-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Paid On</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $maint_query = $conn->prepare("SELECT month, year, amount, due_date, status, payment_date FROM maintenance WHERE flat_id = ? ORDER BY year DESC, month DESC");
                $maint_query->bind_param("i", $flat_id);
                $maint_query->execute();
                $maint_query->store_result(); 
                $maint_query->bind_result($month, $year, $amount, $due_date, $status, $payment_date); 
                
                if ($maint_query->num_rows > 0) {
                    while ($maint_query->fetch()) { 
                        echo "<tr>";
                        echo "<td>" . date('F Y', mktime(0, 0, 0, $month, 1, $year)) . "</td>";
                        echo "<td>₹" . htmlspecialchars($amount) . "</td>";
                        echo "<td>" . date('d-M-Y', strtotime($due_date)) . "</td>";
                        $status_class = 'status-' . strtolower($status);
                        echo "<td><span class='" . $status_class . "'>" . ucfirst($status) . "</span></td>";
                        echo "<td>" . ($payment_date ? date('d-M-Y', strtotime($payment_date)) : 'N/A') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No maintenance records found.</td></tr>";
                }
                $maint_query->close();
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function approveVisitor(visitorId) {
            fetch('api/approve_visitor.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ visitor_id: visitorId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchPendingRequests();
                } else {
                    alert('Error: ' + data.error);
                }
            });
        }

        function rejectVisitor(visitorId) {
            if (!confirm("Are you sure you want to deny entry for this visitor?")) {
                return;
            }
            fetch('api/reject_visitor.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ visitor_id: visitorId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchPendingRequests();
                } else {
                    alert('Error: ' + data.error);
                }
            });
        }

        function fetchPendingRequests() {
            const container = document.getElementById('pending-requests-container');
            
            fetch('api/get_pending_requests.php')
                .then(response => response.json())
                .then(data => {
                    container.innerHTML = '';
                    
                    if (data.error) {
                        container.innerHTML = `<p class="error">${data.error}</p>`;
                        return;
                    }

                    if (data.length === 0) {
                        container.innerHTML = '<p id="no-requests">No pending requests at the moment.</p>';
                    } else {
                        data.forEach(visitor => {
                            const visitorCard = document.createElement('div');
                            visitorCard.className = 'visitor-card';
                            visitorCard.innerHTML = `
                                <div>
                                    <strong>${visitor.name}</strong><br>
                                    <span>Contact: ${visitor.contact_number || 'N/A'}</span>
                                </div>
                                <div>
                                    <button onclick="approveVisitor(${visitor.visitor_id})">Approve</button>
                                    <button onclick="rejectVisitor(${visitor.visitor_id})" class="reject-btn">Reject</button>
                                </div>
                            `;
                            container.appendChild(visitorCard);
                        });
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', fetchPendingRequests);
        setInterval(fetchPendingRequests, 15000);
    </script>
</body>
</html>