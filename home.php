
<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

// Fetch user details
$select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$select_user->execute([$user_id]);
$user_details = $select_user->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <style>
     
      .quick-select {
         padding: 50px 0;
      }
      .inline-btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: #8e44ad !important; /* Force button color */
    color: white !important; /* Force font color */
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    font-size: 1.8rem;
    border: none;
    transition: background-color 0.3s ease;
}

.inline-btn:hover {
    background-color:rgb(8, 8, 9) !important; /* Darker shade on hover */
}


      /* Features Section Styling */
      #features {
         background-color: #f4f4f4;
         padding: 50px 0; /* Reduced padding */
         text-align: center;
         margin-bottom: 20px;
      }

      #features h2 {
         font-size: 5em;
         margin-bottom: 20px;
         color: #333;
      }

      .feature-list {
         display: flex;
         justify-content: space-around;
         flex-wrap: wrap;
         gap: 20px;
      }

      .feature {
         background-color: #fff;
         box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
         padding: 30px;
         border-radius: 20px;
         width: 370px;
         text-align: center;
         transition: all 0.3s ease;
      }

      .feature:hover {
         transform: translateY(-12px);
      }

      .feature img {
         width: 200px;
         height: 200px;
         margin-bottom: 20px;
         object-fit: contain;
      }

      .feature h3 {
         font-size: 1.8em;
         color: #333;
         margin-bottom: 15px;
      }

      .feature p {
         color: #555;
         font-size: 1.2em;
      }

      @media (max-width: 768px) {
         .feature-list {
            flex-direction: column;
            align-items: center;
         }

         .feature {
            width: 90%;
            margin-bottom: 30px;
         }
      }

      /* User Feedback Section Styling */
      #user-feedback {
         background-color: #f9f9f9;
         padding: 50px 0;
         text-align: center;
      }

      #user-feedback h2 {
         font-size: 2.8em;
         margin-bottom: 20px;
         color: #333;
      }

      #user-feedback p {
         font-size: 1.5em;
         color: #555;
         margin-bottom: 15px;
         line-height: 1.6;
      }

      #user-feedback footer {
         font-size: 1.2em;
         font-weight: bold;
         color: #333;
      }

      /* Join Students Section Styling */
      #join-students {
         background-color: #8e44ad;
         color: #fff;
         padding: 50px 0;
         text-align: center;
      }

      #join-students h2 {
         font-size: 2.5em;
         margin-bottom: 30px;
      }

      .inline-btn {
         background-color: #333;
         background-color: #8e44ad !important; /* Force button color */
         color: #fff !important; /* Force font color */
         padding: 15px 30px;
         border-radius: 30px;
         text-decoration: none;
         font-size: 1.8em;
         
         transition: background-color 0.3s ease;
         display: inline-block;
      }

      .inline-btn:hover {
         background-color: #444;
      }

      #join-students .inline-btn {
         background-color: white;
         color: black;
         font-size: 1.9em; /* Apply specific color to the Sign Up button */
      }
      
      /* Floating Chatbot Button */
#chatbot-container {
    position: fixed;
    bottom: 90px;
    right: 20px;
    z-index: 9999;
}

#chatbot-btn {
    background-color: #8e44ad;
    color: white;
    border: none;
    padding: 15px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 24px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    transition: 0.3s;
}

#chatbot-btn:hover {
    background-color: #732d91;
}

/* Chatbot Popup */
#chatbot-popup {
    position: fixed;
    bottom: 90px;
    right: 20px;
    width: 350px;
    height: 500px;
    background: white;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    display: none;
    border-radius: 10px;
    overflow: hidden;
}

#chatbot-header {
    background: #8e44ad;
    color: white;
    padding: 10px;
    text-align: center;
    font-size: 18px;
    position: relative;
}

#close-chatbot {
    position: absolute;
    right: 10px;
    top: 5px;
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
}

#chatbot-frame {
    width: 100%;
    height: 450px;
    border: none;
}


   </style>

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- quick select section starts  -->
<section class="quick-select">
      <h1 class="heading">Quick Options</h1>
     
   <div class="box-container">

      <?php
         if($user_id != ''){ // If user is logged in
      ?>
      <!-- Logged In User Section -->
      <div class="box">
         <h3 class="title">Welcome, <?= $user_details['name']; ?>!</h3>
         <p>Manage your courses and other details.</p>
         <a href="performance.php" class="inline-btn">Performance Analysis</a>
      </div>
      <?php
         }
      ?>

      <!-- Popular Topics Section -->
      <div class="box">
         <h3 class="title">Popular Topics</h3>
         <div class="flex">
            <a href="#"><i class="fab fa-html5"></i><span>HTML</span></a>
            <a href="#"><i class="fab fa-css3"></i><span>CSS</span></a>
            <a href="#"><i class="fab fa-js"></i><span>JavaScript</span></a>
            <a href="#"><i class="fab fa-react"></i><span>React</span></a>
            <a href="#"><i class="fab fa-php"></i><span>PHP</span></a>
            <a href="#"><i class="fab fa-bootstrap"></i><span>Bootstrap</span></a>
         </div>
      </div>

      <!-- Become a Tutor Section -->
      <div class="box tutor">
         <h3 class="title">Become a Tutor</h3>
         <p>Academic Excellence through Personalized Coaching.</p>
         <a href="admin/register.php" class="inline-btn">Get Started</a>
      </div>

      <!-- Placement Officer Section -->
      <div class="box">
         <h3 class="title">Placement Officer Dashboard</h3>
         <p>Add placements opportunities and manage student data.</p>
         <a href="placement_officer_register.php" class="inline-btn">Get Started</a>
      </div>

   </div>

</section>
<!-- quick select section ends -->

<!-- Features Section -->
<section id="features">
   <h2>Key Features</h2>
   <div class="feature-list">
      <div class="feature">
         <img src="learning-path-icon.png" alt="Learning Path">
         <h3>Automated Learning Path</h3>
         <p>Generate personalized learning paths based on your goals.</p>
      </div>
      <div class="feature">
         <img src="mentor-icon.png" alt="Mentor Matching">
         <h3>Mentor Matching</h3>
         <p>Connect with mentors who align with your career goals.</p>
      </div>
      <div class="feature">
         <img src="placement-cell-image.jpg" alt="Placement Cell Support">
         <h3>Placement Cell Support</h3>
         <p>Facilitate recruitment of students for job opportunities after graduation.</p>
      </div>
   </div>
</section>


<!-- User Feedback Section -->
<section id="user-feedback">
   <h2>What Our Users Say</h2>
   <p><strong>"Thanks to my personalized learning path, I secured my dream job in AI! - Student"</strong></p>
   <p><strong>"Mentoring through this platform has been fulfilling. I feel connected to the next generation of leaders. - Mentor"</strong></p>
</section>

<!-- Join Students Section -->
<section id="join-students">
   <h2>Join Thousands of Students Who’ve Already Started Their Learning Path</h2>
   <a href="register.php" class="inline-btn">Sign Up Now</a>
</section>

<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->

<!-- custom js file link  -->
<script src="js/script.js"></script>
<!-- Floating Chatbot Button -->
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
        background:#8e44ad;
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

</body>
</html>
