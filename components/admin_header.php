<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">
   <section class="flex">

      <a href="dashboard.php" class="logo">Admin.</a>

      <form action="search_page.php" method="post" class="search-form">
         <input type="text" name="search" placeholder="Search here..." required maxlength="100">
         <button type="submit" class="fas fa-search" name="search_btn"></button>
      </form>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="toggle-btn" class="fas fa-sun"></div>
      </div>

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
            $select_profile->execute([$tutor_id]);

            if($select_profile->rowCount() > 0){
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <img src="../uploaded_files/<?= htmlspecialchars($fetch_profile['image']); ?>" alt="Profile Picture">
         <h3><?= htmlspecialchars($fetch_profile['name']); ?></h3>
         <span><?= htmlspecialchars($fetch_profile['profession']); ?></span>
         <a href="profile.php" class="btn">View Profile</a>

         <a href="../components/admin_logout.php" onclick="return confirm('Logout from this website?');" class="delete-btn">Logout</a>
         <?php
            } else {
         ?>
         <h3>Please Login or Register</h3>
         <div class="flex-btn">
            <a href="login.php" class="option-btn">Login</a>
            <a href="register.php" class="option-btn">Register</a>
         </div>
         <?php
            }
         ?>
      </div>
   </section>
</header>

<!-- Sidebar Section Starts -->
<div class="side-bar">

   <div class="close-side-bar">
      <i class="fas fa-times"></i>
   </div>

   <div class="profile">
      <?php if(isset($fetch_profile)): ?>
         <img src="../uploaded_files/<?= htmlspecialchars($fetch_profile['image']); ?>" alt="Profile Picture">
         <h3><?= htmlspecialchars($fetch_profile['name']); ?></h3>
         <span><?= htmlspecialchars($fetch_profile['profession']); ?></span>
         <a href="profile.php" class="btn">View Profile</a>
      <?php else: ?>
         <h3>Please Login or Register</h3>
         <div class="flex-btn">
            <a href="login.php" class="option-btn">Login</a>
            <a href="register.php" class="option-btn">Register</a>
         </div>
      <?php endif; ?>
   </div>

   <nav class="navbar">
      <a href="admin_performance.php" style="color: grey;">
         <i class="fas fa-chart-line"></i> <span>Performance Analysis</span>
      </a>
      <a href="dashboard.php"><i class="fas fa-home"></i> <span>Home</span></a>
      <a href="playlists.php"><i class="fa-solid fa-bars-staggered"></i> <span>Playlists</span></a>
      <a href="contents.php"><i class="fas fa-graduation-cap"></i> <span>Contents</span></a>
      <a href="comments.php"><i class="fas fa-comment"></i> <span>Comments</span></a>
      <a href="../components/admin_logout.php" onclick="return confirm('Logout from this website?');">
         <i class="fas fa-right-from-bracket"></i> <span>Logout</span>
      </a>
   </nav>
</div>
<!-- Sidebar Section Ends -->
