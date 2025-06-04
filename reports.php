<?php
session_start();

// Connect to MySQL on port 3307
$conn = new mysqli("127.0.0.1", "root", "", "my_project_db", 3307);

// Check for connection errors
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Fetch total students from 'my_project_db.users'
$totalStudentsQuery = $conn->query("SELECT COUNT(*) AS total FROM my_project_db.users");
$totalStudents = $totalStudentsQuery ? $totalStudentsQuery->fetch_assoc()['total'] : 0;

// Fetch total placed students from 'my_project_db.placement'
$placedStudentsQuery = $conn->query("SELECT COUNT(*) AS placed FROM my_project_db.placement");
$placedStudents = $placedStudentsQuery ? $placedStudentsQuery->fetch_assoc()['placed'] : 0;

// Calculate not placed students
$notPlacedStudents = $totalStudents - $placedStudents;

// Fetch company-wise placement data
$companyPlacements = $conn->query("
    SELECT company_name, COUNT(student_id) AS total_placed 
    FROM my_project_db.placement 
    GROUP BY company_name
");

// Fetch job-wise placement data
$jobPlacements = $conn->query("
    SELECT job_role, COUNT(student_id) AS total_placed 
    FROM my_project_db.placement 
    GROUP BY job_role
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placement Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">📊 Placement Reports</h2>

        <!-- Placement Summary -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h4 class="card-title">✔ Placed Students</h4>
                        <h2><?= htmlspecialchars($placedStudents) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <h4 class="card-title">❌ Not Placed Students</h4>
                        <h2><?= htmlspecialchars($notPlacedStudents) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h4 class="card-title">🎓 Total Students</h4>
                        <h2><?= htmlspecialchars($totalStudents) ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Company-wise Placement Report -->
        <h4 class="mt-5">🏢 Company-wise Placement</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Total Placed</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $companyPlacements->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['company_name']) ?></td>
                        <td><?= htmlspecialchars($row['total_placed']) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Job-wise Placement Report -->
        <h4 class="mt-5">📌 Job-wise Placement</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Total Placed</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $jobPlacements->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['job_role']) ?></td>
                        <td><?= htmlspecialchars($row['total_placed']) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <div class="mt-4">
            <a href="add_placement.php" class="btn btn-primary">➕ Add Placed Student</a>
        </div>

        <!-- Export Reports -->
        <div class="mt-4">
            <a href="export_excel.php" class="btn btn-success">📤 Export to Excel</a>
            <a href="export_pdf.php" class="btn btn-danger">📄 Export to PDF</a>
        </div>
    </div>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
