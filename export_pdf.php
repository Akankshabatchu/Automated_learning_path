<?php
// Ensure no output is sent before headers
ob_start();
require 'vendor/autoload.php';

// ✅ No need for `use TCPDF;`
// ✅ Directly instantiate TCPDF class
$pdf = new TCPDF();

// ✅ Correct MySQL Connection
$conn = new mysqli("127.0.0.1", "root", "", "my_project_db", 3307);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// ✅ Correct SQL Query
$query = $conn->query("
    SELECT p.student_id, u.name AS student_name, p.company_name, p.job_role 
    FROM my_project_db.placement p
    INNER JOIN my_project_db.users u ON p.student_id = u.id
");

if (!$query) {
    die("❌ SQL Error: " . $conn->error);
}

// ✅ TCPDF Settings (Avoid Unsupported Operand Error)
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Placement Report');
$pdf->SetTitle('Placement Report');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->SetFont('helvetica', '', 10);
$pdf->AddPage();

// ✅ Table header
$html = '<h2 style="text-align:center;">📜 Placement Report</h2>';
$html .= '<table border="1" cellpadding="5">
            <tr style="background-color:#f2f2f2;">
                <th><b>Student ID</b></th>
                <th><b>Student Name</b></th>
                <th><b>Company Name</b></th>
                <th><b>Job Role</b></th>
            </tr>';

// ✅ Populate table with data
while ($data = $query->fetch_assoc()) {
    $html .= '<tr>
                <td>' . htmlspecialchars($data['student_id']) . '</td>
                <td>' . htmlspecialchars($data['student_name']) . '</td>
                <td>' . htmlspecialchars($data['company_name']) . '</td>
                <td>' . htmlspecialchars($data['job_role']) . '</td>
              </tr>';
}
$html .= '</table>';

// ✅ Write HTML content to PDF
$pdf->writeHTML($html, true, false, true, false, '');

// ✅ Force file download
$pdfFileName = 'placement_report.pdf';
$pdf->Output($pdfFileName, 'D'); // 'D' forces download

// ✅ Close DB connection
$conn->close();
exit();
?>
