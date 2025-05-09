<?php
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['holiday_id'])) {
    header('Location: index.php');
    exit;
}

$conn = getConnection();

try {
    $holiday_id = $_POST['holiday_id'];
    $stmt = $conn->prepare("DELETE FROM company_holidays WHERE holiday_id = :holiday_id");
    $stmt->execute([':holiday_id' => $holiday_id]);
    header('Location: index.php?success=1&message=' . urlencode('Holiday deleted successfully.'));
    exit;
} catch (Exception $e) {
    header('Location: index.php?error=1&message=' . urlencode($e->getMessage()));
    exit;
} 