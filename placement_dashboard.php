<?php
session_start();
include 'components/connect.php';

// Redirect if session is not set
if (!isset($_SESSION['placement_officer'])) {
    header("Location: login.php");
    exit();
}

$officer_id = $_SESSION['placement_officer']['id'] ?? null;
$officer_name = $_SESSION['placement_officer']['name'] ?? 'Unknown Officer';
$officer_email = $_SESSION['placement_officer']['email'] ?? 'Unknown Email';
$profile_photo = 'uploaded_files/default.jpg'; // Default profile image

if ($officer_id) {
    $stmt = $conn->prepare("SELECT photo FROM placement_officers WHERE id = :id");
    $stmt->bindValue(':id', $officer_id, PDO::PARAM_INT);
    $stmt->execute();
    $officer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($officer['photo'])) {
        $profile_photo = 'uploaded_files/' . htmlspecialchars($officer['photo']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Placement Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f4f4f4;
        }
        .profile {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #8e44ad;
            margin-bottom: 10px;
        }
        .profile h3 {
            margin: 10px 0;
            color: #8e44ad;
        }
        .profile p {
            margin: 5px 0;
        }
        .profile .btn {
            margin: 5px;
        }
        .dashboard-container {
            margin-top: 30px;
        }
        .card {
            transition: 0.3s;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.2);
        }
        .card img {
            height: 200px;
            object-fit: cover;
        }
        .footer {
    text-align: center;
    padding: 10px;
    background: white;
    color: black; /* Font color set to black */
    font-size: 14px;
    position: fixed; /* Stays at bottom */
    bottom: 0;
    width: 100%;
    font-weight: bold;
}
.header {
    width: 100%;
    padding: 15px 0;
    background: #8e44ad;
    text-align: center;
    color: white; /* Text color */
    font-size: 28px;
    font-weight: bold;
    box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.3);
    position: fixed; /* Sticks to the top */
    top: 0;
    left: 0;
    z-index: 1000; /* Ensures it stays on top */
}

h1 {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    text-transform: uppercase;
    letter-spacing: 2px;
}


/* Floating Chatbot Button */
.chatbot-button {
    position: fixed;
    bottom: 90px;
    right: 20px;
    background-color: #8e44ad;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
}

/* Chatbot Container */
.chatbot-container {
    display: none;
    position: fixed;
    bottom: 100px;
    right: 20px;
    width: 350px;
    height: 500px;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    border: 1px solid #ccc;
    overflow: hidden;
}

/* Close Button */
.close-chatbot {
    position: absolute;
    top: 5px;
    right: 5px;
    background: red;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
}
    </style>
    
</head>
<body>
<header class="header">
    <h1>EduPro</h1>
</header>
<br><br><br>


<div class="container mt-4">
    <div class="profile">
        <img src="<?php echo $profile_photo; ?>" class="profile-img" alt="Profile Photo">
        <h3>Welcome, <?php echo htmlspecialchars($officer_name); ?></h3>
        <p>Email: <?php echo htmlspecialchars($officer_email); ?></p>
        <a href="placement_profile.php" class="btn btn-primary">View Profile</a>
        <a href="placement_logout.php" class="btn btn-danger">Logout</a>
    </div>

    <div class="container text-center dashboard-container">
        <h2 class="mb-4">Placement Officer Dashboard</h2>
        <div class="row">

            <!-- Student Filtering Card -->
            <div class="col-md-4">
                <div class="card">
                    <img src="https://venngage-wordpress.s3.amazonaws.com/uploads/2018/09/White-Desk-Simple-Background-Image-.jpg" class="card-img-top" alt="Filter Students">
                    <div class="card-body">
                        <h5 class="card-title">Student Filtering</h5>
                        <p class="card-text">Filter students based on skills, CGPA, and more.</p>
                        <a href="filter_students.php" class="btn btn-primary">Filter Students</a>
                    </div>
                </div>
            </div>

            <!-- Job & Internship Management Card -->
            <div class="col-md-4">
                <div class="card">
                    <img src="https://stackby.com/blog/content/images/2022/06/free-paid-google-analytics-reporting-tools-blog.png" class="card-img-top" alt="Manage Jobs">
                    <div class="card-body">
                        <h5 class="card-title">Job & Internship Management</h5>
                        <p class="card-text">Add job and internship positions to the portal.</p>
                        <a href="manage_jobs.php" class="btn btn-success">Manage Jobs</a>
                    </div>
                </div>
            </div>

            <!-- Reports & Analytics Card -->
            <div class="col-md-4">
                <div class="card">
                    <img src="https://www.soundandcommunications.com/wp-content/uploads/2019/11/Data-Ana.jpg" class="card-img-top" alt="Reports & Analytics">
                    <div class="card-body">
                        <h5 class="card-title">Reports & Analytics</h5>
                        <p class="card-text">View statistics on placed and unplaced students.</p>
                        <a href="reports.php" class="btn btn-warning">View Reports</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <br><br><br>
</div>
<!-- Chatbot Toggle Button -->
<button id="chatbot-toggle" onclick="toggleChatbot()">💬 Chat</button>

<!-- Chatbot Container (Initially Hidden) -->
<div id="chatbot-container">
    <iframe src="FAQChatbotProject/index.html" width="350" height="500" style="border:none;"></iframe>
</div>

<style>
    /* Chatbot Button */
    #chatbot-toggle {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #8e44ad;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 50px;
        cursor: pointer;
        font-size: 18px;
        z-index: 1000;
    }

    /* Chatbot Container */
    #chatbot-container {
        position: fixed;
        bottom: 80px;
        right: 20px;
        display: none; /* Initially Hidden */
        background: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border-radius: 10px;
        z-index: 1000;
    }
</style>

<script>
    function toggleChatbot() {
        var chatbot = document.getElementById("chatbot-container");
        if (chatbot.style.display === "none" || chatbot.style.display === "") {
            chatbot.style.display = "block"; // Show chatbot
        } else {
            chatbot.style.display = "none"; // Hide chatbot
        }
    }
</script>
<footer class="footer">
    © Copyright @ 2025 by Batch 1 | All rights reserved!
</footer>

<!-- Bootstrap JS -->


</body>
</html>
