<?php
session_start();

// ✅ Connect to the database
$conn = new mysqli("127.0.0.1", "root", "", "my_project_db", 3307);

// ✅ Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// ✅ Fetch students from users table
$studentsQuery = $conn->query("SELECT id, name FROM users");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = trim($_POST['student_id']);
    $company_name = trim($_POST['company_name']);
    $job_role = trim($_POST['job_role']);
    $package = trim($_POST['package']);
    $placement_date = $_POST['placement_date'];

    // ✅ Debug: Check if student_id exists in users table
    $checkStudent = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $checkStudent->bind_param("s", $student_id);
    $checkStudent->execute();
    $result = $checkStudent->get_result();
    
    if ($result->num_rows === 0) {
        echo "<script>alert('❌ Error: Student ID not found! Please select a valid student.');</script>";
    } else {
        // ✅ Insert into placement table
        $stmt = $conn->prepare("INSERT INTO placement (student_id, company_name, job_role, package, placement_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $student_id, $company_name, $job_role, $package, $placement_date);

        if ($stmt->execute()) {
            echo "<script>alert('✅ Student placement added successfully!'); window.location.href='reports.php';</script>";
        } else {
            echo "<script>alert('❌ Error adding student placement: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
    $checkStudent->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Placed Student</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">🎓 Add Placed Student</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Student Name</label>
                <select name="student_id" class="form-control select2" required>
                    <option value="">Select a student</option>
                    <?php while ($row = $studentsQuery->fetch_assoc()) { ?>
                        <option value="<?= htmlspecialchars(trim($row['id'])) ?>">
                            <?= htmlspecialchars($row['name']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Company Name</label>
                <input type="text" name="company_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Job Role</label>
                <input type="text" name="job_role" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Package (in LPA)</label>
                <input type="text" name="package" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Placement Date</label>
                <input type="date" name="placement_date" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">✅ Add Placement</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Search and select a student",
                allowClear: true
            });
        });
    </script>
</body>
</html>
