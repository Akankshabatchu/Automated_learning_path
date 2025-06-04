<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- about section starts  -->

<section class="about">

   <div class="row">

      <div class="image">
         <img src="images/about-img.svg" alt="">
      </div>

      <div class="content">
         <h3>why choose us?</h3>
         <p>✅ AI-Powered Learning Paths
         We analyze your skills, interests, and career goals to create a personalized roadmap for career success.</p>
         <p>✅ Placement and Career Support
         Our platform helps you transition from learning to employment by matching your skills with job opportunities.</p>
         <p>✅ All-in-One Platform
         Students, mentors, and placement officers—everyone benefits from a single, seamless system.</p>
         <a href="courses.php" class="inline-btn">our courses</a>
      </div>

   </div>

   <div class="box-container">

      <div class="box">
         <i class="fas fa-graduation-cap"></i>
         <div>
            <h3>+1k</h3>
            <span>online courses</span>
         </div>
      </div>

      <div class="box">
         <i class="fas fa-user-graduate"></i>
         <div>
            <h3>+25k</h3>
            <span>brilliants students</span>
         </div>
      </div>

      <div class="box">
         <i class="fas fa-chalkboard-user"></i>
         <div>
            <h3>+5k</h3>
            <span>expert teachers</span>
         </div>
      </div>

      <div class="box">
         <i class="fas fa-briefcase"></i>
         <div>
            <h3>100%</h3>
            <span>job placement</span>
         </div>
      </div>

   </div>

</section>

<!-- about section ends -->

<!-- reviews section starts  -->

<section class="reviews">

   <h1 class="heading">student's reviews</h1>

   <div class="box-container">

      <div class="box">
         <p>🌟 This platform transformed my learning journey!
         The AI-driven learning path helped me choose the right courses for my career goals. The mentor I was matched with provided incredible guidance!</p>
         <div class="user">
            <img src="images/pic-3.jpg" alt="">
            <div>
               <h3>— Aarav Patel</h3>
               <div class="stars">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star-half-alt"></i>
               </div>
            </div>
         </div>
      </div>

      <div class="box">
         <p>🌟Best mentorship experience!
         I struggled to find a mentor who truly understood my goals, but this system paired me with an industry expert who helped me land my dream job!</p>
         <div class="user">
            <img src="images/pic-2.jpg" alt="">
            <div>
               <h3>— Sara Khan</h3>
               <div class="stars">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star-half-alt"></i>
               </div>
            </div>
         </div>
      </div>

      <div class="box">
         <p>🌟Personalized learning made easy!
         The platform's adaptive learning path adjusted based on my progress. It made learning more efficient and enjoyable!</p>
         <div class="user">
            <img src="images/pic-4.jpg" alt="">
            <div>
               <h3>— Vikram Reddy</h3>
               <div class="stars">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star-half-alt"></i>
               </div>
            </div>
         </div>
      </div>

      <div class="box">
         <p>🌟Helped me get my first job!
The placement support feature connected me with job opportunities that matched my skills. I secured my first job within weeks!</p>
         <div class="user">
            <img src="images/pic-5.jpg" alt="">
            <div>
               <h3>— Meera Sharma</h3>
               <div class="stars">
                  <i class=— Meera Sharma"fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star-half-alt"></i>
               </div>
            </div>
         </div>
      </div>

      <div class="box">
         <p>🌟Mentorship that truly makes a difference!
         Having a mentor who understands my skill gaps was a game-changer. I received great career advice and valuable feedback.</p>
         <div class="user">
            <img src="images/pic-6.jpg" alt="">
            <div>
               <h3>— Rahul Verma</h3>
               <div class="stars">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star-half-alt"></i>
               </div>
            </div>
         </div>
      </div>

      <div class="box">
         <p>⭐ A game-changer for my career!
         This platform gave me a clear learning path tailored to my career goals. The mentor support was invaluable in helping me!</p>
         <div class="user">
            <img src="images/pic-7.jpg" alt="">
            <div>
               <h3>— Riya Mehta</h3>
               <div class="stars">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star-half-alt"></i>
               </div>
            </div>
         </div>
      </div>

   </div>

</section>

<!-- reviews section ends -->










<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>