<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

if(isset($_POST['submit'])){
   // Generate unique ID
   $id = unique_id();
   $status = $_POST['status'];
   $status = filter_var($status, FILTER_SANITIZE_STRING);
   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);
   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);
   
   // Handle Playlist (Optional)
   $playlist = ($_POST['playlist'] == "0") ? '' : $_POST['playlist'];
   if ($playlist !== '') {
       $playlist = filter_var($playlist, FILTER_SANITIZE_STRING);
   }

   // YouTube URL handling
   $youtube_url = $_POST['youtube_url'];
   $youtube_url = filter_var($youtube_url, FILTER_SANITIZE_URL);

   // Thumbnail handling (optional)
   if($_FILES['thumb']['name']) {
       $thumb = $_FILES['thumb']['name'];
       $thumb = filter_var($thumb, FILTER_SANITIZE_STRING);
       $thumb_ext = pathinfo($thumb, PATHINFO_EXTENSION);
       $rename_thumb = unique_id().'.'.$thumb_ext;
       $thumb_tmp_name = $_FILES['thumb']['tmp_name'];
       $thumb_folder = '../uploaded_files/'.$rename_thumb;
   } else {
       $rename_thumb = ''; // Set to empty string if no thumbnail
       $thumb_folder = ''; // No file to move if no thumbnail
   }

   // Insert data into the database
   $add_content = $conn->prepare("INSERT INTO `content`(id, tutor_id, playlist_id, title, description, youtube_url, thumb, status) VALUES(?,?,?,?,?,?,?,?)");
   $add_content->execute([$id, $tutor_id, $playlist, $title, $description, $youtube_url, $rename_thumb, $status]);

   // Move uploaded file (if thumbnail exists)
   if($thumb_folder) {
       move_uploaded_file($thumb_tmp_name, $thumb_folder);
   }

   $message[] = 'New course uploaded!';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="video-form">

   <h1 class="heading">Upload Content</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <p>Video Status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="" selected disabled>-- select status --</option>
         <option value="active">Active</option>
         <option value="deactive">Deactive</option>
      </select>

      <p>Video Title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="Enter video title" class="box">

      <p>Video Description <span>*</span></p>
      <textarea name="description" class="box" required placeholder="Write description" maxlength="1000" cols="30" rows="10"></textarea>

      <p>Video Playlist <span>(Optional)</span></p>
      <select name="playlist" class="box">
         <option value="0" selected>No Playlist</option>
         <?php
         $select_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
         $select_playlists->execute([$tutor_id]);
         if($select_playlists->rowCount() > 0){
            while($fetch_playlist = $select_playlists->fetch(PDO::FETCH_ASSOC)){
         ?>
         <option value="<?= $fetch_playlist['id']; ?>"><?= $fetch_playlist['title']; ?></option>
         <?php
            }
         }
         ?>
      </select>

      <p>YouTube Video URL <span>*</span></p>
      <input type="url" name="youtube_url" required placeholder="Enter YouTube video URL" class="box">

      <p>Thumbnail (optional)</p>
      <input type="file" name="thumb" accept="image/*" class="box">

      <input type="submit" value="Upload Video" name="submit" class="btn">
   </form>

</section>

<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
