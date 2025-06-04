<?php
// Ensure no output is sent before headers
ob_start();
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Connect to MySQL
$conn = new mysqli("127.0.0.1", "root", "", "my_project_db", 3307);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Fetch placed student details with JOIN
$query = $conn->query("
    SELECT p.student_id, u.name AS student_name, p.company_name, p.job_role 
    FROM my_project_db.placement p
    INNER JOIN my_project_db.users u ON p.student_id = u.id
");

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set column headers
$sheet->setCellValue('A1', 'Student ID');
$sheet->setCellValue('B1', 'Student Name');
$sheet->setCellValue('C1', 'Company');
$sheet->setCellValue('D1', 'Job Role');

// Populate data
$row = 2;
while ($data = $query->fetch_assoc()) {
    $sheet->setCellValue('A' . $row, $data['student_id']);
    $sheet->setCellValue('B' . $row, $data['student_name']); // Fixed column name
    $sheet->setCellValue('C' . $row, $data['company_name']);
    $sheet->setCellValue('D' . $row, $data['job_role']);
    $row++;
}

// Save file to a temporary location
$filename = 'placement_report.xlsx';
$writer = new Xlsx($spreadsheet);
$writer->save($filename);

// Clear output buffer before sending headers
ob_end_clean();

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
readfile($filename);

// Delete the temporary file after download
unlink($filename);

// Close DB connection
$conn->close();
exit();
?>
