<?php
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$conn = getConnection();

try {
    $balance_id = $_POST['balance_id'] ?? null;
    $employee_id = $_POST['employee_id'];
    $leave_type = $_POST['leave_type'];
    $total_leaves = $_POST['total_leaves'];
    $leaves_used = $_POST['leaves_used'];
    $year = $_POST['year'];

    // Validate required fields
    if (empty($employee_id) || empty($leave_type) || $total_leaves === '' || $leaves_used === '' || empty($year)) {
        throw new Exception('All required fields must be filled out.');
    }
    if (!in_array($leave_type, ['Casual Leave', 'Sick Leave', 'Earned Leave', 'Maternity Leave', 'Paternity Leave', 'Other'])) {
        throw new Exception('Invalid leave type.');
    }
    if (!is_numeric($total_leaves) || !is_numeric($leaves_used) || $total_leaves < 0 || $leaves_used < 0) {
        throw new Exception('Leave values must be non-negative numbers.');
    }
    if (!is_numeric($year) || $year < 2000 || $year > 2100) {
        throw new Exception('Invalid year.');
    }

    if ($balance_id) {
        // Update
        $stmt = $conn->prepare("UPDATE employee_leave_balances SET employee_id = :employee_id, leave_type = :leave_type, total_leaves = :total_leaves, leaves_used = :leaves_used, year = :year WHERE balance_id = :balance_id");
        $stmt->execute([
            ':employee_id' => $employee_id,
            ':leave_type' => $leave_type,
            ':total_leaves' => $total_leaves,
            ':leaves_used' => $leaves_used,
            ':year' => $year,
            ':balance_id' => $balance_id
        ]);
        $message = 'Leave balance updated successfully.';
    } else {
        // Prevent duplicate for same employee/leave_type/year
        $stmt = $conn->prepare("SELECT COUNT(*) FROM employee_leave_balances WHERE employee_id = :employee_id AND leave_type = :leave_type AND year = :year");
        $stmt->execute([':employee_id' => $employee_id, ':leave_type' => $leave_type, ':year' => $year]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('A leave balance for this employee, leave type, and year already exists.');
        }
        // Insert
        $stmt = $conn->prepare("INSERT INTO employee_leave_balances (employee_id, leave_type, total_leaves, leaves_used, year) VALUES (:employee_id, :leave_type, :total_leaves, :leaves_used, :year)");
        $stmt->execute([
            ':employee_id' => $employee_id,
            ':leave_type' => $leave_type,
            ':total_leaves' => $total_leaves,
            ':leaves_used' => $leaves_used,
            ':year' => $year
        ]);
        $message = 'Leave balance added successfully.';
    }
    header('Location: index.php?success=1&message=' . urlencode($message));
    exit;
} catch (Exception $e) {
    header('Location: index.php?error=1&message=' . urlencode($e->getMessage()));
    exit;
} 