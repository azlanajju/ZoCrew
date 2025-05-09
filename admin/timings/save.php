<?php
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$conn = getConnection();

try {
    $timing_id = $_POST['timing_id'] ?? null;
    $shift_name = $_POST['shift_name'];
    $work_start = $_POST['work_start'];
    $work_end = $_POST['work_end'];
    $break_start = $_POST['break_start'] ?: null;
    $break_end = $_POST['break_end'] ?: null;
    $grace_period_minutes = $_POST['grace_period_minutes'];
    $is_active = $_POST['is_active'];
    $effective_hours_required = $_POST['effective_hours_required'];
    $gross_hours_required = $_POST['gross_hours_required'];

    // Validate required fields
    if (empty($shift_name) || empty($work_start) || empty($work_end) || 
        empty($effective_hours_required) || empty($gross_hours_required)) {
        throw new Exception('All required fields must be filled out.');
    }

    // Validate time formats
    if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $work_start) ||
        !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $work_end) ||
        ($break_start && !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $break_start)) ||
        ($break_end && !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $break_end))) {
        throw new Exception('Invalid time format.');
    }

    // Check if work end time is after work start time
    if (strtotime($work_end) <= strtotime($work_start)) {
        throw new Exception('Work end time must be after work start time.');
    }

    // Check if break end time is after break start time (if break times are provided)
    if ($break_start && $break_end && strtotime($break_end) <= strtotime($break_start)) {
        throw new Exception('Break end time must be after break start time.');
    }

    // Check if break time is within work hours
    if ($break_start && strtotime($break_start) < strtotime($work_start)) {
        throw new Exception('Break start time must be after work start time.');
    }
    if ($break_end && strtotime($break_end) > strtotime($work_end)) {
        throw new Exception('Break end time must be before work end time.');
    }

    if ($timing_id) {
        // Update existing timing
        $stmt = $conn->prepare("UPDATE company_timings SET 
            shift_name = :shift_name,
            work_start = :work_start,
            work_end = :work_end,
            break_start = :break_start,
            break_end = :break_end,
            grace_period_minutes = :grace_period_minutes,
            is_active = :is_active,
            effective_hours_required = :effective_hours_required,
            gross_hours_required = :gross_hours_required
            WHERE timing_id = :timing_id");
        
        $stmt->execute([
            ':shift_name' => $shift_name,
            ':work_start' => $work_start,
            ':work_end' => $work_end,
            ':break_start' => $break_start,
            ':break_end' => $break_end,
            ':grace_period_minutes' => $grace_period_minutes,
            ':is_active' => $is_active,
            ':effective_hours_required' => $effective_hours_required,
            ':gross_hours_required' => $gross_hours_required,
            ':timing_id' => $timing_id
        ]);

        $message = 'Shift updated successfully.';
    } else {
        // Check if shift name already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM company_timings WHERE shift_name = :shift_name");
        $stmt->execute([':shift_name' => $shift_name]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('A shift with this name already exists.');
        }

        // Insert new timing
        $stmt = $conn->prepare("INSERT INTO company_timings (
            shift_name, work_start, work_end, break_start, break_end,
            grace_period_minutes, is_active, effective_hours_required, gross_hours_required
        ) VALUES (
            :shift_name, :work_start, :work_end, :break_start, :break_end,
            :grace_period_minutes, :is_active, :effective_hours_required, :gross_hours_required
        )");
        
        $stmt->execute([
            ':shift_name' => $shift_name,
            ':work_start' => $work_start,
            ':work_end' => $work_end,
            ':break_start' => $break_start,
            ':break_end' => $break_end,
            ':grace_period_minutes' => $grace_period_minutes,
            ':is_active' => $is_active,
            ':effective_hours_required' => $effective_hours_required,
            ':gross_hours_required' => $gross_hours_required
        ]);

        $message = 'New shift added successfully.';
    }

    header('Location: index.php?success=1&message=' . urlencode($message));
    exit;

} catch (Exception $e) {
    header('Location: index.php?error=1&message=' . urlencode($e->getMessage()));
    exit;
} 