<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
}

$response = []; // Initialize response variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_name = isset($_POST["course_name"]) ? trim($_POST["course_name"]) : "";
    $difficulty = isset($_POST["difficulty"]) ? trim($_POST["difficulty"]) : "beginner";
    $expected_rating = isset($_POST["rating"]) ? floatval($_POST["rating"]) : 4.0; // Default rating: 4.0

    // Validate input
    if (empty($course_name)) {
        echo "<p class='error-message'>❌ Course title cannot be empty!</p>";
        exit;
    }

    $data = [
        "course" => $course_name,
        "difficulty" => $difficulty,
        "min_rating" => $expected_rating
    ];

    // Flask API URL
    $api_url = "http://127.0.0.1:5001/recommend_mentors";

    // Use cURL instead of file_get_contents for better error handling
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($http_code !== 200) {
        echo "<p class='error-message'>⚠️ Failed to connect to mentor recommendation system. HTTP Code: {$http_code}</p>";
    } else {
        $response = json_decode($result, true);
    }

    curl_close($ch);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor Matching</title>

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- Custom CSS File -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f0f4f8;
            color: #333;
        }

        .mentor-section {
            margin: 20px auto;
            padding: 20px;
            max-width: 1200px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            text-align: center;
            font-size: 28px;
            color: #4a4a4a;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .mentor-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .mentor-card {
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            color: black; 
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .mentor-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }

        .mentor-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50; /* Dark blue */
        }

        .mentor-image {
            width: 150px; /* Specific size for images */
            height: 150px;
            border-radius: 50%; 
            margin-bottom: 10px;
            border: 3px solid #3498db; /* Blue border around mentor images */
            transition: border 0.3s;
        }

        .mentor-image:hover {
            border-color: #2980b9; /* Darker blue on hover */
        }

        .mentor-expertise, 
        .mentor-rating, 
        .mentor-experience, 
        .mentor-students,
        .mentor-availability,
        .mentor-language,
        .mentor-contact {
            font-size: 14px;
            margin-bottom: 8px;
            color: #34495e; /* Dark gray */
        }

        .error-message, .no-mentors {
            text-align: center;
            color: #e74c3c; /* Red color for errors */
            font-size: 18px;
            margin-top: 20px;
        }

        /* Responsive */
        @media screen and (max-width: 768px) {
            .mentor-container {
                grid-template-columns: 1fr; 
            }
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        footer {
            background: #f8f9fa; 
            text-align: center;
            padding: 10px;
            width: 100%;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
<div class="wrapper">
    <!-- Include Header -->
    <?php include 'components/user_header.php'; ?>

    <!-- Mentor Matching Section -->
    <section class="mentor-section">
        <h2 class="section-title">🎓 Mentor Recommendations</h2>

        <?php if (!empty($response["recommendations"])): ?>
            <div class="mentor-container">
                <?php foreach ($response["recommendations"] as $mentor): ?>
                    <div class="mentor-card">
                    <img src="<?= !empty($mentor['C:\xampp\htdocs\project\images\image.jpg']) ? htmlspecialchars($mentor['C:\xampp\htdocs\project\images\image.jpg'], ENT_QUOTES, 'UTF-8') : 'images/image.jpg'; ?>" 
                        alt="Image of <?= htmlspecialchars($mentor['Mentor_Name'], ENT_QUOTES, 'UTF-8'); ?>" 
                        class="mentor-image">

                        <h3 class="mentor-name"><?= htmlspecialchars($mentor['Mentor_Name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="mentor-expertise"><strong>📌 Expertise:</strong> <?= htmlspecialchars($mentor['Expertise_Domains'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="mentor-rating"><strong>⭐ Rating:</strong> <?= htmlspecialchars($mentor['Average_Rating'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="mentor-experience"><strong>🕒 Years of Experience:</strong> <?= htmlspecialchars($mentor['Years_of_Experience'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="mentor-students"><strong>👩‍🎓 Total Students Mentored:</strong> <?= htmlspecialchars($mentor['Total_Students_Mentored'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="mentor-availability"><strong>📅 Availability:</strong> <?= htmlspecialchars($mentor['Availability'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="mentor-language"><strong>🌐 Languages Spoken:</strong> <?= htmlspecialchars($mentor['Languages Spoken'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="mentor-contact"><strong>📞 Contact:</strong> <?= htmlspecialchars($mentor['Contact Information'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-mentors">No mentors found matching your criteria.</p>
        <?php endif; ?>
    </section>

    <!-- Include Footer -->
    <?php include 'components/footer.php'; ?>
</div>

<!-- Custom JS File -->
<script src="js/script.js"></script>
</body>
</html>
