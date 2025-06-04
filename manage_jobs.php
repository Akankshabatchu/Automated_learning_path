<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer is included

// Database connection for jobs table
$jobs_conn = new mysqli("localhost", "root", "", "placement_portal", 3307);

// Check jobs database connection
if ($jobs_conn->connect_error) {
    die("Connection to Jobs Database Failed: " . $jobs_conn->connect_error);
}

// Function to send email using PHPMailer
function sendEmail($to, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Use Gmail SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'akhilendra2124@gmail.com'; // Replace with your Gmail
        $mail->Password = 'wvbi qoar gcph efth'; // Use App Password (not your actual password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email Content
        $mail->setFrom('akhilendra2124@gmail.com', 'Placement Team');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = nl2br($message);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

// Handle job posting
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $company = $_POST['company'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $apply_link = $_POST['apply_link'];

    // Secure query to insert job into the jobs table
    $stmt = $jobs_conn->prepare("INSERT INTO jobs (title, company, location, description, apply_link) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $company, $location, $description, $apply_link);

    if ($stmt->execute()) {
        // Now, connect to the users database (my_project_db) to fetch student emails
        $users_conn = new mysqli("localhost", "root", "", "my_project_db", 3307);

        // Check users database connection
        if ($users_conn->connect_error) {
            die("Connection to Users Database Failed: " . $users_conn->connect_error);
        }

        // Fetch all student emails
        $emailQuery = "SELECT email FROM users WHERE email IS NOT NULL";
        $result = $users_conn->query($emailQuery);

        if ($result && $result->num_rows > 0) {
            // Email details
            $subject = "New Job Opportunity: $title at $company";
            $message = "Dear Student,<br><br>A new job has been posted on the Placement Portal.<br><br>"
                     . "<b>📌 Job Title:</b> $title<br>"
                     . "<b>🏢 Company:</b> $company<br>"
                     . "<b>📍 Location:</b> $location<br>"
                     . "<b>📝 Description:</b> $description<br>"
                     . "<b>🔗 Apply Here:</b> <a href='$apply_link'>$apply_link</a><br><br>"
                     . "Best Regards,<br>Placement Team";

            // Send email to all students
            while ($student = $result->fetch_assoc()) {
                sendEmail($student['email'], $subject, $message);
            }
        }

        // Close user database connection
        $users_conn->close();

        echo "<script>alert('Job added & students notified via email!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle job deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $jobs_conn->query("DELETE FROM jobs WHERE id=$id");
    echo "<script>alert('Job deleted successfully!'); window.location='manage_jobs.php';</script>";
}

// Fetch jobs from the database
$result = $jobs_conn->query("SELECT * FROM jobs ORDER BY posted_on DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Jobs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            max-width: 900px;
        }
        .card-custom {
            border-radius: 12px;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        }
        .card-custom:hover {
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        .btn-primary {
            background: #8e44ad;
            border: none;
            transition: all 0.3s ease-in-out;
        }
        .btn-primary:hover {
            background: #8e44ad;
        }
        .btn-danger {
            transition: all 0.3s ease-in-out;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .job-card {
            border-radius: 12px;
            padding: 15px;
            background: white;
            transition: all 0.3s ease-in-out;
        }
        .job-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        .job-icon {
            font-size: 40px;
            color: #8e44ad;
        }
        .badge-custom {
            background-color: #8e44ad;
            color: white;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 12px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4"><i class="fas fa-briefcase"></i> Manage Jobs & Internships</h2>

    <!-- Job Posting Form -->
    <div class="card-custom mb-4">
        <h5 class="mb-3"><i class="fas fa-plus-circle"></i> Add a New Job</h5>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Job Title:</label>
                <input type="text" class="form-control" name="title" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Company Name:</label>
                <input type="text" class="form-control" name="company" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Location:</label>
                <input type="text" class="form-control" name="location" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Job Description:</label>
                <textarea class="form-control" name="description" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Apply Link:</label>
                <input type="url" class="form-control" name="apply_link" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Post Job</button>
        </form>
    </div>

    <!-- Display Jobs -->
    <h5 class="mb-3"><i class="fas fa-list"></i> Current Job Listings</h5>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="job-card mb-3 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1"><?= htmlspecialchars($row['title']) ?> <span class="badge-custom"><?= htmlspecialchars($row['company']) ?></span></h5>
                    <p class="text-muted mb-1"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($row['location']) ?></p>
                    <p class="small text-muted"><i class="fas fa-calendar-alt"></i> Posted on <?= date("d-m-Y", strtotime($row['posted_on'])) ?></p>
                </div>
                <div>
                    <i class="fas fa-briefcase job-icon"></i>
                </div>
            </div>
            <div class="mt-2">
                <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Delete</a>
            </div>
        </div>
    <?php endwhile; ?>

    <a href="placement_dashboard.php" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</div>

</body>
</html>
