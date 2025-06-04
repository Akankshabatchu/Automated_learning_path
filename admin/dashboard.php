<?php 
include '../components/connect.php'; 
if(isset($_COOKIE['tutor_id'])){ 
    $tutor_id = $_COOKIE['tutor_id']; 
} else { 
    $tutor_id = ''; 
    header('location:login.php'); 
} 
$select_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?"); 
$select_contents->execute([$tutor_id]); 
$total_contents = $select_contents->rowCount(); 
$select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?"); 
$select_playlists->execute([$tutor_id]); 
$total_playlists = $select_playlists->rowCount(); 
$select_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ?"); 
$select_likes->execute([$tutor_id]); 
$total_likes = $select_likes->rowCount(); 
$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?"); 
$select_comments->execute([$tutor_id]); 
$total_comments = $select_comments->rowCount(); 
?> 

<!DOCTYPE html> 
<html lang="en"> 
<head> 
<meta charset="UTF-8"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<title>Dashboard</title> 
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> 
<!-- Custom CSS -->
<link rel="stylesheet" href="../css/admin_style.css"> 

<link rel="stylesheet" href="FAQChatbotProject/style.css">

</head> 
<body> 

<?php include '../components/admin_header.php'; ?> 

<section class="dashboard"> 
<h1 class="heading">Dashboard</h1> 
<div class="box-container"> 
    <div class="box"> 
        <h3>Welcome!</h3> 
        <p><?= $fetch_profile['name']; ?></p> 
        <a href="profile.php" class="btn">View Profile</a> 
    </div> 
    <div class="box"> 
        <h3><?= $total_contents; ?></h3> 
        <p>Total Contents</p> 
        <a href="add_content.php" class="btn">Add New Content</a> 
    </div> 
    <div class="box"> 
        <h3><?= $total_playlists; ?></h3> 
        <p>Total Playlists</p> 
        <a href="add_playlist.php" class="btn">Add New Playlist</a> 
    </div> 
    <div class="box"> 
        <h3><?= $total_likes; ?></h3> 
        <p>Total Likes</p> 
        <a href="contents.php" class="btn">View Contents</a> 
    </div> 
    <div class="box"> 
        <h3><?= $total_comments; ?></h3> 
        <p>Total Comments</p> 
        <a href="comments.php" class="btn">View Comments</a> 
    </div> 
</div> 
</section> 


<?php include '../components/footer.php'; ?> 

<!-- Chatbot Toggle Button -->
<button id="chatbot-toggle" onclick="toggleChatbot()">💬 Chat</button>

<!-- Chatbot Container (Initially Hidden) -->
<div id="chatbot-container">
<iframe src="../FAQChatbotProject/index.html" width="350" height="500" style="border:none;"></iframe>

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

<script src="../js/admin_script.js"></script> 
</body> 
</html>
