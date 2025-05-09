<?php
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['balance_id'])) {
    header('Location: index.php');
    exit;
}

$conn = getConnection();

try {
    $balance_id = $_POST['balance_id'];
    $stmt = $conn->prepare("DELETE FROM employee_leave_balances WHERE balance_id = :balance_id");
    $stmt->execute([':balance_id' => $balance_id]);
    header('Location: index.php?success=1&message=' . urlencode('Leave balance deleted successfully.'));
    exit;
} catch (Exception $e) {
    header('Location: index.php?error=1&message=' . urlencode($e->getMessage()));
    exit;
} 