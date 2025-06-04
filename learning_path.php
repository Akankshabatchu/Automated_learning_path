<?php
//include 'components/connect.php';
require "components/connect.php";
//require "components/user_header.php"; // ✅ Include Header
if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personalized Learning Path</title>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

    <style>
       /* General Styling */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    font-size: 18px;
}






/* Container */
.container {
    width: 80%;
    margin: auto;
    overflow: hidden;
    background: white;
    padding: 50px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}
header {
    height: 120px; /* Adjust this to match other pages */
    overflow: hidden; /* Prevents extra spacing */
    padding: 10px 0; /* Reduce padding if needed */
}

.header .logo {
    padding-left: 120px; /* Adjust this value as needed */
}

header input[type="text"] {
    width: 200px; /* Adjust width as needed */
    height: 30px; /* Adjust height */
    font-size: 14px; /* Reduce font size */
    padding: 5px 10px; /* Adjust padding */
    border-radius: 5px; /* Optional: Keep it rounded */
    
    
}




.container {
    padding-top: 30px;
    margin-top: 20px;
}

.button:hover {
    background: #218838;
}

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    font-size: 18px;
}

th, td {
    padding: 14px;
    border: 1px solid #ddd;
    text-align: left;
}

th {
    background: #007bff;
    color: white;
}

tr:nth-child(even) {
    background: #f2f2f2;
}

/* Form Styling */
form {
    margin: 20px 0;
}

input[type="text"], input[type="email"], textarea {
    width: 100%;
    padding: 12px;
    margin: 6px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 18px;
}

input[type="submit"] {
    background: #007bff;
    color: white;
    border: none;
    padding: 12px 18px;
    cursor: pointer;
    border-radius: 5px;
    font-size: 18px;
}

input[type="submit"]:hover {
    background: #8e44ad;
}

/* Style for the Button */
#generateBtn {
    background: #8e44ad;
    color: white;
    border: none;
    padding: 12px 24px;
    font-size: 18px;
    font-weight: bold;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px; /* Space between text and spinner */
}

/* Hover Effect */
#generateBtn:hover {
    background: #8e44ad;
    transform: scale(1.05);
}

/* Spinner Styling */
.loading-spinner {
    display: none;
    width: 25px;
    height: 25px;
    border: 4px solid rgba(0, 0, 0, 0.2);
    border-top: 4px solid #fff; /* White spinner */
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

/* Animation for spinner */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Button Container */
.button-container {
    display: flex;
    align-items: center;
    gap: 15px; /* Space between button and spinner */
}


    </style>


</head>
<body>

<?php include 'components/user_header.php'; ?>

<h1 style="text-align: center; margin-top: 40px;">Generate Your Learning Path</h1>


    <div class="container">
        <!-- Left Panel -->
        <div class="left-panel">
            <h3>📚 Educational Background</h3>
            <p>- Enter your current education level</p>
            <p>- Include your major or field of study</p>
            <p>- List relevant courses you have taken</p>
            <p>- Mention any certifications</p>

            <h3>🛠 Skills</h3>
            <p>- List your technical skills, including programming languages</p>
            <p>- Add tools you’re familiar with</p>
            <p>- Mention soft skills</p>

            <h3>🎯 Goals</h3>
            <p>- Specify your career objectives</p>
            <p>- Mention target roles or positions</p>
            <p>- Include time frames for goals</p>
            <p>- List the specializations you want to learn</p>

            <h3>🚀 Generate & Download</h3>
            <p>- Click ‘Generate Learning Path’</p>
            <p>- Review the customized path</p>
            <p>- Download as a Word document</p>

            <h3>💡 Tips for Best Results</h3>
            <ul>
                <li>✅ Be specific in your description.</li>
                <li>✅ Include skill levels where relevant.</li>
                <li>✅ Clearly state your timeline.</li>
                <li>✅ Mention any preferences for learning style.</li>
            </ul>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <form id="learningPathForm">
                <h3>Your journey to success starts with a single step. Define your path today!</h3>

                <label for="educationLevel">Education Level:</label>
                <input type="text" id="educationLevel" name="educationLevel" placeholder="Enter your education level" required>

                <label for="branch">Branch/Stream:</label>
                <input type="text" id="branch" name="branch" placeholder="Enter your branch or stream" required>

                <label for="technicalSkills">Technical Skills:</label>
                <input type="text" id="technicalSkills" name="technicalSkills" placeholder="List your technical skills" required>

                <label for="certifications">Certifications:</label>
                <input type="text" id="certifications" name="certifications" placeholder="Mention any certifications" required>

                <label for="softSkills">Soft Skills:</label>
                <input type="text" id="softSkills" name="softSkills" placeholder="Mention your soft skills" required>

                <label for="careerGoal">Career Goal:</label>
                <input type="text" id="careerGoal" name="careerGoal" placeholder="Specify your career objectives" required>

                <label for="timeFrame">Time Frame (in months/years):</label>
                <input type="text" id="timeFrame" name="timeFrame" placeholder="Specify time frame" required>

                <div class="button-container">
    <button id="generateBtn" type="button" onclick="generateLearningPath()">🚀 Generate Learning Path</button>
    <div class="loading-spinner" id="loadingSpinner"></div>
</div>

            </form>

            <br>
            <div id="response"></div>
        </div>

        <!-- Diagonal Divider -->
        <div class="divider"></div>
    </div>

    
    <?php require "components/footer.php"; // ✅ Include Footer ?>
    <script>
        function generateLearningPath() {
    let requestData = {
        educationLevel: document.getElementById("educationLevel").value.trim(),
        technicalSkills: document.getElementById("technicalSkills").value.trim(),
        softSkills: document.getElementById("softSkills").value.trim(),
        careerGoal: document.getElementById("careerGoal").value.trim(),
        timeFrame: document.getElementById("timeFrame").value.trim()
    };

    // ✅ Show the spinner and disable button
    document.getElementById("loadingSpinner").style.display = "block";
    document.getElementById("generateBtn").disabled = true;
    document.getElementById("generateBtn").style.opacity = "0.7";

    fetch("generate_learning_path.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(requestData)
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            window.location.href = `view_learning_path.php?educationLevel=${encodeURIComponent(requestData.educationLevel)}&technicalSkills=${encodeURIComponent(requestData.technicalSkills)}&softSkills=${encodeURIComponent(requestData.softSkills)}&careerGoal=${encodeURIComponent(requestData.careerGoal)}&timeFrame=${encodeURIComponent(requestData.timeFrame)}`;
        } else {
            document.getElementById("response").innerHTML = res.error || "Error generating learning path.";
        }
    })
    .catch(error => {
        console.error("Error:", error);
        document.getElementById("response").innerHTML = "Server error. Please try again.";
    })
    .finally(() => {
        // ✅ Hide spinner and enable button
        document.getElementById("loadingSpinner").style.display = "none";
        document.getElementById("generateBtn").disabled = false;
        document.getElementById("generateBtn").style.opacity = "1";
    });
}

    </script>
    
<script src="js/script.js"></script>
</body>
</html>
