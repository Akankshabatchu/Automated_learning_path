<?php
include 'components/connect.php';
//require "components/connect.php";
//require "components/user_header.php"; // ✅ Include Header
if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
}

// Get parameters from URL
$educationLevel = $_GET['educationLevel'] ?? '';
$technicalSkills = $_GET['technicalSkills'] ?? '';
$softSkills = $_GET['softSkills'] ?? '';
$careerGoal = $_GET['careerGoal'] ?? '';
$timeFrame = $_GET['timeFrame'] ?? '';

// Fetch learning path from database
$stmt = $conn->prepare("SELECT learning_path FROM learning_paths WHERE education_level = ? AND technical_skills = ? AND soft_skills = ? AND career_goal = ? AND time_frame = ?");
$stmt->execute([$educationLevel, $technicalSkills, $softSkills, $careerGoal, $timeFrame]);
$learningPath = $stmt->fetchColumn();

if (!$learningPath) {
    echo "<div class='no-path'>No learning path found.</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Personalized Learning Path</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

<!-- Custom CSS file link -->
<link rel="stylesheet" href="css/style.css">

    <script>
        function copyToClipboard() {
            var text = document.getElementById("learningPath").innerText;
            navigator.clipboard.writeText(text).then(function() {
                alert("Copied to clipboard!");
            }, function(err) {
                alert("Failed to copy: " + err);
            });
        }
    </script>
</head>
<body>
<?php include 'components/user_header.php'; ?>

<div class="container">
    
    
    <div class="learning-path-container">
        <h2><h1>📚 Your Personalized Learning Path</h1></h2>
        <div id="learningPath">
    <?php 
        $cleanedLearningPath = preg_replace('/[#*]/', '', $learningPath); // Remove # and *
        echo nl2br(htmlspecialchars($cleanedLearningPath)); 
    ?>
</div>

        <!-- Copy to Clipboard Button -->
        <button onclick="copyToClipboard()">📋 Copy to Clipboard</button>

        <!-- Download as Word Button -->
        <button onclick="downloadWord()">📥 Download as Word</button>
    </div>

    <!-- Back Button -->
     <br>
    <a href="learning_path.php" class="back-btn">⬅ Go Back</a>
</div>

    <style>
        .container {
    max-width: 800px;
    margin: auto;
    padding: 20px;
    text-align: center;
}

.learning-path-container {
    background: #f9f9f9;
    width: 100%;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

h1 {
    font-size: 30px;
    font-weight: bold;
    color: #222;
}

h2 {
    font-size: 26px;
    color: #8e44ad;
    margin-bottom: 15px;
}

#learningPath {
    font-size: 18px;
    line-height: 1.6;
    font-family: 'Georgia', serif;
    color: #444;
    background: #ffffff;
    border-left: 5px solid #8e44ad;
    padding: 15px;
    border-radius: 5px;
    box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: justify;

}

button {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    font-size: 16px;
    font-weight: bold;
    color: #fff;
    background: #8e44ad;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

button:hover {
    background: #8e44ad;
}

/* Back Button Styling */
.back-btn {
    display: inline-block;
    padding: 12px 20px;
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
    color: #fff;
    background:#8e44ad;
    border-radius: 5px;
    transition: background 0.3s ease;
}

.back-btn:hover {
    background: #C0392B;
}


    </style>

<?php require "components/footer.php"; // ✅ Include Footer ?>
<script>
    function copyToClipboard() {
    var text = document.getElementById("learningPath").innerText;
    navigator.clipboard.writeText(text).then(function() {
        alert("Copied to clipboard!");
    }, function(err) {
        alert("Failed to copy: " + err);
    });
}

function downloadWord() {
    var content = document.getElementById("learningPath").innerHTML;
    var blob = new Blob(['<html><head><meta charset="UTF-8"></head><body>' + content + '</body></html>'], { type: "application/msword" });
    var a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = "Learning_Path.doc";
    a.click();
}

</script>
<script src="js/script.js"></script>
</body>
</html>
