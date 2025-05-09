<?php
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['leave_id']) || !isset($_POST['action_type'])) {
    header('Location: index.php');
    exit;
}

$conn = getConnection();

try {
    $leave_id = $_POST['leave_id'];
    $action_type = $_POST['action_type'];
    $remarks = trim($_POST['remarks'] ?? '');

    if (!in_array($action_type, ['Approve', 'Reject'])) {
        throw new Exception('Invalid action.');
    }

    // Check if leave exists and is pending
    $stmt = $conn->prepare("SELECT * FROM employee_leaves WHERE leave_id = :leave_id");
    $stmt->execute([':leave_id' => $leave_id]);
    $leave = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$leave) {
        throw new Exception('Leave request not found.');
    }
    if ($leave['status'] !== 'Pending') {
        throw new Exception('Only pending leave requests can be processed.');
    }

    $new_status = $action_type === 'Approve' ? 'Approved' : 'Rejected';
    $stmt = $conn->prepare("UPDATE employee_leaves SET status = :status, remarks = :remarks WHERE leave_id = :leave_id");
    $stmt->execute([
        ':status' => $new_status,
        ':remarks' => $remarks,
        ':leave_id' => $leave_id
    ]);

    $message = 'Leave request ' . strtolower($new_status) . ' successfully.';
    header('Location: index.php?success=1&message=' . urlencode($message));
    exit;
} catch (Exception $e) {
    header('Location: index.php?error=1&message=' . urlencode($e->getMessage()));
    exit;
} 