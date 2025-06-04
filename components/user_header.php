<?php
// Fetch user profile if user ID is set
if (isset($user_id) && $user_id != null) {
    $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
    $select_profile->execute([$user_id]);
    $fetch_profile = ($select_profile->rowCount() > 0) ? $select_profile->fetch(PDO::FETCH_ASSOC) : null;
} else {
    $fetch_profile = null; // Default to null if not logged in
}
?>

<header class="header">
   <section class="flex">
      <a href="home.php" class="logo">EduPro.</a>

      <form action="search_course.php" method="post" class="search-form">
         <input type="text" name="search_course" placeholder="search courses..." required maxlength="100">
         <button type="submit" class="fas fa-search" name="search_course_btn"></button>
      </form>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="toggle-btn" class="fas fa-sun"></div>
      </div>

      <div class="profile">
         <?php if($fetch_profile): ?>
            <img src="uploaded_files/<?= htmlspecialchars($fetch_profile['image']); ?>" alt="">
            <h3><?= htmlspecialchars($fetch_profile['name']); ?></h3>
            <span>student</span>
            <a href="profile.php" class="btn">view profile</a>
            <a href="components/user_logout.php" onclick="return confirm('logout from this website?');" class="delete-btn">logout</a>
         <?php else: ?>
            <h3>please login or register</h3>
            <div class="flex-btn">
               <a href="login.php" class="option-btn">login</a>
               <a href="register.php" class="option-btn">register</a>
            </div>
         <?php endif; ?>
      </div>
   </section>
</header>

<!-- sidebar section starts -->
<div class="side-bar">
   <div class="close-side-bar">
      <i class="fas fa-times"></i>
   </div>

   <div class="profile">
      <?php if($fetch_profile): ?>
         <img src="uploaded_files/<?= htmlspecialchars($fetch_profile['image']); ?>" alt="">
         <h3><?= htmlspecialchars($fetch_profile['name']); ?></h3>
         <span>student</span>
         <a href="profile.php" class="btn">view profile</a>
      <?php else: ?>
         <h3>please login or register</h3>
         <div class="flex-btn" style="padding-top: .5rem;">
            <a href="login.php" class="option-btn">login</a>
            <a href="register.php" class="option-btn">register</a>
         </div>
      <?php endif; ?>
   </div>

   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span>home</span></a>
      <a href="about.php"><i class="fas fa-question"></i><span>about us</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>courses</span></a>
      <a href="teachers.php"><i class="fas fa-chalkboard-user"></i><span>teachers</span></a>
      <a href="learning_path.php"><i class="fas fa-book"></i><span>Learning Path</span></a>
      <a href="contact.php"><i class="fas fa-headset"></i><span>contact us</span></a>
   </nav>
</div>
<!-- side bar section ends -->
