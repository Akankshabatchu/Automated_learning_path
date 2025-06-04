<?php
session_start();
require "components/connect.php"; // ✅ Ensure correct DB connection
require "vendor/autoload.php"; // ✅ Load Composer packages (Dotenv)

use Dotenv\Dotenv;

// ✅ Enable error reporting (TEMPORARY for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

// ✅ Load environment variables securely
try {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} catch (Exception $e) {
    echo json_encode(["error" => "Error loading .env file: " . $e->getMessage()]);
    exit;
}

// ✅ Retrieve API Key securely
$apiKey = $_ENV["GEMINI_API_KEY"] ?? null;
if (!$apiKey) {
    echo json_encode(["error" => "API Key is missing."]);
    exit;
}

// ✅ Get JSON Input from Request
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "Invalid JSON input"]);
    exit;
}

// ✅ Extract Input Fields safely
$educationLevel = $data["educationLevel"] ?? null;
$technicalSkills = $data["technicalSkills"] ?? null;
$softSkills = $data["softSkills"] ?? null;
$careerGoal = $data["careerGoal"] ?? null;
$timeFrame = $data["timeFrame"] ?? null;

// ✅ Validate Required Fields
if (!$educationLevel || !$technicalSkills || !$softSkills || !$careerGoal || !$timeFrame) {
    echo json_encode(["error" => "All fields are required."]);
    exit;
}

// ✅ Check if the learning path already exists in the database
try {
    $stmt = $conn->prepare("SELECT learning_path FROM learning_paths WHERE education_level = ? AND technical_skills = ? AND soft_skills = ? AND career_goal = ? AND time_frame = ?");
    $stmt->execute([$educationLevel, $technicalSkills, $softSkills, $careerGoal, $timeFrame]);
    $existingPath = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingPath) {
        echo json_encode(["success" => true, "message" => "Learning path retrieved from database", "learningPath" => $existingPath['learning_path']]);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(["error" => "Database query error: " . $e->getMessage()]);
    exit;
}

// ✅ Generate a new learning path using the Gemini API
$prompt = "Generate a structured learning path based on:
🔹 Education Level: $educationLevel
🔹 Technical Skills: $technicalSkills
🔹 Soft Skills: $softSkills
🔹 Career Goal: $careerGoal
🔹 Time Frame: $timeFrame

✅ Include recommended resources, milestones, and projects.
✅ Provide a mix of theoretical and hands-on learning.
✅ Format the output clearly with headings and bullet points.";

$url = "https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:generateContent?key=$apiKey";

$requestData = ["contents" => [["parts" => [["text" => $prompt]]]]];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo json_encode(["error" => "Gemini API failed. HTTP Code: $httpCode", "response" => $response]);
    exit;
}

$responseData = json_decode($response, true);

// ✅ Validate API Response
if (!isset($responseData["candidates"][0]["content"]["parts"][0]["text"])) {
    echo json_encode(["error" => "Invalid response format from Gemini API", "response" => $responseData]);
    exit;
}

$learningPath = $responseData["candidates"][0]["content"]["parts"][0]["text"];

// ✅ Store the learning path in the database securely
try {
    $stmt = $conn->prepare("INSERT INTO learning_paths (education_level, technical_skills, soft_skills, career_goal, time_frame, learning_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$educationLevel, $technicalSkills, $softSkills, $careerGoal, $timeFrame, $learningPath]);
    
    echo json_encode(["success" => true, "message" => "New learning path generated and stored", "learningPath" => $learningPath]);
    exit;
} catch (Exception $e) {
    echo json_encode(["error" => "Database insert error: " . $e->getMessage()]);
    exit;
}
?>
