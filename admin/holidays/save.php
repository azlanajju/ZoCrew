<?php
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$conn = getConnection();

try {
    $holiday_id = $_POST['holiday_id'] ?? null;
    $holiday_name = trim($_POST['holiday_name']);
    $holiday_date = $_POST['holiday_date'];
    $holiday_type = $_POST['holiday_type'];
    $is_federal = isset($_POST['is_federal']) ? (int)$_POST['is_federal'] : 0;
    $financial_year = trim($_POST['financial_year']);
    $description = trim($_POST['description'] ?? '');

    // Validate required fields
    if (empty($holiday_name) || empty($holiday_date) || empty($holiday_type) || empty($financial_year)) {
        throw new Exception('All required fields must be filled out.');
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $holiday_date)) {
        throw new Exception('Invalid date format.');
    }
    if (!preg_match('/^\d{4}-\d{4}$/', $financial_year)) {
        throw new Exception('Financial year must be in format YYYY-YYYY.');
    }
    if (!in_array($holiday_type, ['Normal', 'Floater'])) {
        throw new Exception('Invalid holiday type.');
    }

    if ($holiday_id) {
        // Update
        $stmt = $conn->prepare("UPDATE company_holidays SET holiday_name = :holiday_name, holiday_date = :holiday_date, holiday_type = :holiday_type, is_federal = :is_federal, financial_year = :financial_year, description = :description WHERE holiday_id = :holiday_id");
        $stmt->execute([
            ':holiday_name' => $holiday_name,
            ':holiday_date' => $holiday_date,
            ':holiday_type' => $holiday_type,
            ':is_federal' => $is_federal,
            ':financial_year' => $financial_year,
            ':description' => $description,
            ':holiday_id' => $holiday_id
        ]);
        $message = 'Holiday updated successfully.';
    } else {
        // Prevent duplicate for same date/year
        $stmt = $conn->prepare("SELECT COUNT(*) FROM company_holidays WHERE holiday_date = :holiday_date AND financial_year = :financial_year");
        $stmt->execute([':holiday_date' => $holiday_date, ':financial_year' => $financial_year]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('A holiday for this date already exists in this financial year.');
        }
        // Insert
        $stmt = $conn->prepare("INSERT INTO company_holidays (holiday_name, holiday_date, holiday_type, is_federal, financial_year, description) VALUES (:holiday_name, :holiday_date, :holiday_type, :is_federal, :financial_year, :description)");
        $stmt->execute([
            ':holiday_name' => $holiday_name,
            ':holiday_date' => $holiday_date,
            ':holiday_type' => $holiday_type,
            ':is_federal' => $is_federal,
            ':financial_year' => $financial_year,
            ':description' => $description
        ]);
        $message = 'Holiday added successfully.';
    }
    header('Location: index.php?success=1&message=' . urlencode($message));
    exit;
} catch (Exception $e) {
    header('Location: index.php?error=1&message=' . urlencode($e->getMessage()));
    exit;
} 