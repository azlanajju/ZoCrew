<?php
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['timing_id'])) {
    header('Location: index.php');
    exit;
}

$conn = getConnection();

try {
    $timing_id = $_POST['timing_id'];

    // Check if any employees are assigned to this shift
    $stmt = $conn->prepare("SELECT COUNT(*) FROM employees WHERE timing_id = :timing_id");
    $stmt->execute([':timing_id' => $timing_id]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Cannot delete this shift as it is assigned to one or more employees.');
    }

    // Delete the timing
    $stmt = $conn->prepare("DELETE FROM company_timings WHERE timing_id = :timing_id");
    $stmt->execute([':timing_id' => $timing_id]);

    header('Location: index.php?success=1&message=' . urlencode('Shift deleted successfully.'));
    exit;

} catch (Exception $e) {
    header('Location: index.php?error=1&message=' . urlencode($e->getMessage()));
    exit;
} 