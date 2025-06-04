<?php
include 'components/connect.php'; // Database connection
session_start();
include 'components/connect.php';

if (isset($_SESSION['user_id'])) {
    $student_id = $_SESSION['user_id'];
} elseif (isset($_COOKIE['user_id'])) {
    $student_id = $_COOKIE['user_id']; // Use cookie if session is missing
} else {
    echo "❌ Error: User not logged in!";
    exit;
}


// Get recommendation from AJAX request
if (isset($_POST['recommendation'])) {
    $recommendation = $_POST['recommendation'];

    // Check if student already has an entry
    $checkQuery = $conn->prepare("SELECT student_id FROM student_analysis WHERE student_id = ?");
    $checkQuery->execute([$student_id]);

    if ($checkQuery->rowCount() > 0) {
        // If record exists, update it
        $updateQuery = $conn->prepare("UPDATE student_analysis SET recommendation = ?, analysis_date = NOW() WHERE student_id = ?");
        if ($updateQuery->execute([$recommendation, $student_id])) {
            echo "✅ Performance analysis updated!";
        } else {
            echo "❌ Error updating analysis!";
        }
    } else {
        // If no record, insert a new one
        $insertQuery = $conn->prepare("INSERT INTO student_analysis (student_id, recommendation) VALUES (?, ?)");
        if ($insertQuery->execute([$student_id, $recommendation])) {
            echo "✅ New performance analysis stored!";
        } else {
            echo "❌ Error inserting analysis!";
        }
    }
} 
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌟 Student Performance Predictor</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.0.0"></script> <!-- Confetti Library -->
    <style>
        /* Beautiful Animated Background */
        body {
            font-family: 'Poppins', sans-serif;
            text-align: center;
            background: linear-gradient(135deg, #2E3192, #1BFFFF);
            color: white;
            padding: 20px;
            overflow-y: auto; /* Enable scrolling */
            position: relative;
        }

        /* Floating Glassmorphic Card */
        .container {
            width: 50%;
            margin: auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(12px);
            transition: all 0.4s ease-in-out;
        }

        /* Title */
        h2 {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #fff;
            position: relative;
            z-index: 10;
        }

        /* Input Fields */
        input, select {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.8);
            font-size: 16px;
            transition: all 0.3s;
        }

        input:focus, select:focus {
            outline: none;
            background: white;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
        }

        /* Predict Button */
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(90deg, #ff416c, #ff4b2b);
            color: white;
            font-size: 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: bold;
        }

        button:hover {
            background: linear-gradient(90deg, #ff4b2b, #ff416c);
            transform: scale(1.05);
        }
/* Apply consistent box-sizing */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Full-screen smooth background */
body {
    font-family: 'Poppins', sans-serif;
    text-align: center;
    background: linear-gradient(135deg, #8e44ad,rgb(177, 248, 248));
    color: white;
    padding: 20px;
    overflow-y: auto;
    min-height: 100vh;
}

/* Glassmorphic Form Container */
.container {
    width: 95%;
    max-width: 500px;
    margin: auto;
    padding: 30px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
    transition: all 0.4s ease-in-out;
}

/* Form Labels */
label {
    font-size: 16px;
    font-weight: 500;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    color: #fff;
}

/* Ensuring Uniform Inputs */
input, select {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: none;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.9);
    font-size: 16px;
    color: #333;
    transition: 0.3s;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    display: block;
}

/* Make sure select matches input fields */
select {
    appearance: none;  /* Removes default dropdown styling */
    -webkit-appearance: none;
    -moz-appearance: none;
}

/* Make all inputs and select elements the same height */
input, select {
    height: 45px;
    line-height: 1.5;
}

/* Fix for inconsistent box-sizing */
input:focus, select:focus {
    outline: none;
    background: white;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

/* Predict Button */
button {
    width: 100%;
    padding: 14px;
    background: #8e44ad;
    color: white;
    font-size: 18px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: 0.3s;
    font-weight: bold;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

button:hover {
    background: linear-gradient(90deg, #ff4b2b, #ff416c);
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
}

/* Responsive Design */
@media (max-width: 600px) {
    .container {
        width: 100%;
        padding: 25px;
    }

    label {
        font-size: 14px;
    }

    button {
        font-size: 16px;
    }

    input, select {
        font-size: 14px;
    }
}

        /* Result Box */
        #result {
            margin-top: 20px;
            font-size: 24px;
            font-weight: bold;
            padding: 15px;
            border-radius: 12px;
            display: none;
        }

        /* Dynamic Colors for Results */
        .needs-improvement { background: rgba(255, 69, 58, 0.9); color: #fff; }
        .average-performer { background: rgba(255, 159, 10, 0.9); color: #fff; }
        .high-performer { background: rgba(50, 205, 50, 0.9); color: #fff; }

        /* Falling Emoji & Confetti Effect */
        .emoji {
            position: absolute;
            font-size: 30px;
            opacity: 1;
            animation: fallEmojis 3s linear forwards;
        }

        @keyframes fallEmojis {
            0% { transform: translateY(-10vh) scale(1); opacity: 1; }
            100% { transform: translateY(100vh) scale(1.5); opacity: 0; }
        }
    </style>
</head>
<body>

    <h2 id="header">📊 AI-Powered Student Performance Predictor</h2>
    <!-- Quote Display -->
<h3 id="quoteDisplay">"Every expert was once a beginner!"</h3>

<script>
    // Array of motivational quotes
    const quotes = [
        "Every expert was once a beginner!",
        "Believe in yourself and all that you are!",
        "Do something today that your future self will thank you for!",
        "The journey of a thousand miles begins with a single step!",
        "Don't watch the clock; do what it does—keep going!",
        "You are capable of amazing things!",
        "Success is the sum of small efforts, repeated daily!"
    ];

    // Function to change the quote every second
    function changeQuote() {
        const quoteElement = document.getElementById("quoteDisplay");
        let index = 0;

        setInterval(() => {
            quoteElement.innerText = quotes[index];
            index = (index + 1) % quotes.length; // Loop through quotes
        }, 1000); // Change every second
    }

    // Start changing quotes when the page loads
    window.onload = changeQuote;
</script>
<br>
    <div class="container">
        <form id="predictionForm">
            <label>📖 Hours Studied per Week:</label>
            <input type="number" step="0.1" id="hours_studied" required>

            <label>🎓 Current CGPA (5.0 - 10.0):</label>
            <input type="number" step="0.1" id="current_cgpa" required>

            <label>🏆 Extracurricular Activities:</label>
            <select id="extracurricular_activities">
                <option value="" disabled selected>-- Select an option --</option>
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>


            <label>💤 Sleep Hours per Day:</label>
            <input type="number" step="0.1" id="sleep_hours" required>

            <label>📅 Attendance Percentage:</label>
            <input type="number" step="0.1" id="attendance_percentage" required>

            <label>📝 Past Papers Solved:</label>
            <input type="number" id="solved_past_papers" required>
            <label>💡 Project Participation:</label>
            <select id="project_participation">
                <option value="" disabled selected>-- Select an option --</option>
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>


            <button type="button" id="predictBtn">🚀 Predict Performance</button>
        </form>

        <div id="result"></div>
    </div>

    <div class="footer">✨ Keep striving! You are capable of amazing things. ✨</div>

    <script>
   $(document).ready(function () {
    $("#predictBtn").click(function () {
        var studentData = {
            "Hours_Studied": parseFloat($("#hours_studied").val()),
            "Current_CGPA": parseFloat($("#current_cgpa").val()),
            "Extracurricular_Activities": parseInt($("#extracurricular_activities").val()),
            "Sleep_Hours": parseFloat($("#sleep_hours").val()),
            "Attendance_Percentage": parseFloat($("#attendance_percentage").val()),
            "Solved_Past_Papers": parseInt($("#solved_past_papers").val()),
            "Project_Participation": parseInt($("#project_participation").val())
        };

        // 🔹 Call Flask API for Prediction
        $.ajax({
            url: "http://127.0.0.1:5003/predict",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(studentData),
            success: function (response) {
                var resultDiv = $("#result").removeClass().addClass("needs-improvement average-performer high-performer");
                var prediction = response.prediction; // "Needs Improvement", "Average Performer", or "High Performer"

                // 🎉 Apply Effects Based on Prediction
                if (prediction === "Needs Improvement") {
                    resultDiv.addClass("needs-improvement").html("😞 Needs Improvement");
                    sprinkleEmojis(["💪", "🔥", "🏆"], 100);
                } else if (prediction === "Average Performer") {
                    resultDiv.addClass("average-performer").html("🙂 Average Performer");
                    sprinkleEmojis(["⭐", "🌟", "✨"], 100);
                } else {
                    resultDiv.addClass("high-performer").html("🎉 High Performer");
                    confettiEffect();
                    sprinkleEmojis(["🎉", "🏅", "🚀"], 100);
                }

                resultDiv.fadeIn();

                // 🔹 Store the Prediction Result in the Database
                $.ajax({
                    url: "performance.php",
                    type: "POST",
                    data: { recommendation: prediction },
                    success: function (result) {
                        console.log("✅ Analysis Stored: " + prediction);
                    },
                    error: function (xhr, status, error) {
                        console.error("❌ Error storing analysis:", error);
                    }
                });
            },
            error: function (xhr, status, error) {
                console.error("❌ Error fetching prediction:", error);
            }
        });
    });
});

// 🎊 Confetti Effect
function confettiEffect() {
    confetti({
        particleCount: 500,
        spread: 360,
        startVelocity: 50,
        origin: { y: 0.2 } // 🎉 Starts from the Header
    });
}

// 🎈 Sprinkle Falling Emojis
function sprinkleEmojis(emojis, count) {
    for (let i = 0; i < count; i++) {
        setTimeout(() => {
            let emoji = $("<div>").addClass("emoji").text(emojis[Math.floor(Math.random() * emojis.length)]);
            $("body").append(emoji);
            let left = Math.random() * 100;
            emoji.css({ left: left + "%", top: "5vh" });

            setTimeout(() => emoji.remove(), 3000);
        }, Math.random() * 3000); // 🔥 Random start times
    }
}

</script>

</body>
</html>
