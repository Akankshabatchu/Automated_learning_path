<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
   header('location:home.php');
}

// Function to extract YouTube video ID
function extractYouTubeVideoId($url) {
    preg_match("/(?:https?:\/\/(?:www\.)?youtube\.com\/(?:[^\/\n\s]+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=))([a-zA-Z0-9_-]{11})/i", $url, $matches);
    if (isset($matches[1])) {
        return $matches[1]; // Return the matched video ID
    }
    return null; // Return null if no match found
}

if(isset($_POST['like_content'])){
   if($user_id != ''){
      $content_id = $_POST['content_id'];
      $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);

      $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $select_content->execute([$content_id]);
      $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);

      $tutor_id = $fetch_content['tutor_id'];

      $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND content_id = ?");
      $select_likes->execute([$user_id, $content_id]);

      if($select_likes->rowCount() > 0){
         $remove_likes = $conn->prepare("DELETE FROM `likes` WHERE user_id = ? AND content_id = ?");
         $remove_likes->execute([$user_id, $content_id]);
         $message[] = 'removed from likes!';
      }else{
         $insert_likes = $conn->prepare("INSERT INTO `likes`(user_id, tutor_id, content_id) VALUES(?,?,?)");
         $insert_likes->execute([$user_id, $tutor_id, $content_id]);
         $message[] = 'added to likes!';
      }

   }else{
      $message[] = 'please login first!';
   }
}

if(isset($_POST['add_comment'])){
   if($user_id != ''){
      $id = unique_id();
      $comment_box = $_POST['comment_box'];
      $comment_box = filter_var($comment_box, FILTER_SANITIZE_STRING);
      $content_id = $_POST['content_id'];
      $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);

      $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $select_content->execute([$content_id]);
      $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);

      $tutor_id = $fetch_content['tutor_id'];

      if($select_content->rowCount() > 0){
         $select_comment = $conn->prepare("SELECT * FROM `comments` WHERE content_id = ? AND user_id = ? AND tutor_id = ? AND comment = ?");
         $select_comment->execute([$content_id, $user_id, $tutor_id, $comment_box]);

         if($select_comment->rowCount() > 0){
            $message[] = 'comment already added!';
         }else{
            $insert_comment = $conn->prepare("INSERT INTO `comments`(id, content_id, user_id, tutor_id, comment) VALUES(?,?,?,?,?)");
            $insert_comment->execute([$id, $content_id, $user_id, $tutor_id, $comment_box]);
            $message[] = 'new comment added!';
         }

      }else{
         $message[] = 'something went wrong!';
      }

   }else{
      $message[] = 'please login first!';
   }
}

if(isset($_POST['delete_comment'])){
   $delete_id = $_POST['comment_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
   $verify_comment->execute([$delete_id]);

   if($verify_comment->rowCount() > 0){
      $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
      $delete_comment->execute([$delete_id]);
      $message[] = 'comment deleted successfully!';
   }else{
      $message[] = 'comment already deleted!';
   }
}

if(isset($_POST['update_now'])){
   $update_id = $_POST['update_id'];
   $update_id = filter_var($update_id, FILTER_SANITIZE_STRING);
   $update_box = $_POST['update_box'];
   $update_box = filter_var($update_box, FILTER_SANITIZE_STRING);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ? AND comment = ?");
   $verify_comment->execute([$update_id, $update_box]);

   if($verify_comment->rowCount() > 0){
      $message[] = 'comment already added!';
   }else{
      $update_comment = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ?");
      $update_comment->execute([$update_box, $update_id]);
      $message[] = 'comment edited successfully!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>watch video</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<?php
   if(isset($_POST['edit_comment'])){
      $edit_id = $_POST['comment_id'];
      $edit_id = filter_var($edit_id, FILTER_SANITIZE_STRING);
      $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ? LIMIT 1");
      $verify_comment->execute([$edit_id]);
      if($verify_comment->rowCount() > 0){
         $fetch_edit_comment = $verify_comment->fetch(PDO::FETCH_ASSOC);
?>
<section class="edit-comment">
   <h1 class="heading">edit comment</h1>
   <form action="" method="post">
      <input type="hidden" name="update_id" value="<?= $fetch_edit_comment['id']; ?>">
      <textarea name="update_box" class="box" maxlength="1000" required placeholder="please enter your comment" cols="30" rows="10"><?= $fetch_edit_comment['comment']; ?></textarea>
      <div class="flex">
         <a href="watch_video.php?get_id=<?= $get_id; ?>" class="inline-option-btn">cancel edit</a>
         <input type="submit" value="update now" name="update_now" class="inline-btn">
      </div>
   </form>
</section>
<?php
      }else{
         $message[] = 'comment was not found!';
      }
   }
?>

<!-- watch video section starts  -->

<section class="watch-video">

   <?php
      $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND status = ?");
      $select_content->execute([$get_id, 'active']);
      if($select_content->rowCount() > 0){
         while($fetch_content = $select_content->fetch(PDO::FETCH_ASSOC)){
            $content_id = $fetch_content['id'];

            // Use the YouTube URL to extract the video ID
            $youtube_url = $fetch_content['youtube_url'];
            $video_id = extractYouTubeVideoId($youtube_url);

            $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE content_id = ?");
            $select_likes->execute([$content_id]);
            $total_likes = $select_likes->rowCount();  

            $verify_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND content_id = ?");
            $verify_likes->execute([$user_id, $content_id]);

            $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
            $select_tutor->execute([$fetch_content['tutor_id']]);
            $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
   ?>
   
   <div class="video-details">
      <iframe width="560" height="315" src="https://www.youtube.com/embed/<?= htmlspecialchars($video_id); ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
      <h3 class="title"><?= htmlspecialchars($fetch_content['title']); ?></h3>
      <!-- Mentor Matching Section -->
<div class="info">
    <p><i class="fas fa-calendar"></i> <span><?= htmlspecialchars($fetch_content['date']); ?></span></p>
    <p><i class="fas fa-heart"></i> <span><?= $total_likes; ?> likes</span></p>
    <button class="mentor-matching-button" onclick="openModal()">Mentor Matching</button> 
</div>

<!-- Mentor Matching Modal (Initially Hidden) -->
<div id="mentor-matching-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Mentor Matching</h2>

        <form action="mentor_matching.php" method="post">
            <div class="input-group">
                <label for="course_name">Course Title:</label>
                <input type="text" name="course_name" id="course_name" placeholder="Enter course title" required>
            </div>

            <div class="input-group">
                <label for="difficulty">Difficulty Level:</label>
                <select name="difficulty" id="difficulty" required>
                    <option value="" disabled selected>Select difficulty level</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>

            <div class="input-group">
                <label for="rating">Expected Rating (1.0 - 5.0):</label>
                <input type="text" name="rating" id="rating" placeholder="e.g., 4.5" pattern="[1-5](\.[0-9])?" required>

            </div>

            <div class="button-group">
                <button type="submit" class="find-mentors-btn">Find Mentors</button>
                <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>
<script>
    function findMentors() {
        let course_name = document.getElementById('course_name').value;
        let difficulty = document.getElementById('difficulty').value;
        let rating = document.getElementById('rating').value;

        if (!course_name || !difficulty || !rating) {
            alert("Please fill all fields.");
            return;
        }

        // Send request to Flask API
        fetch("http://127.0.0.1:5001/recommend_mentors", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                course: course_name,
                difficulty: difficulty,
                min_rating: rating
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert("Error: " + data.error);
            } else {
                // Send results to mentor_matching.php
                fetch("mentor_matching.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(data)
                })
                .then(response => response.text())
                .then(html => {
                    document.body.innerHTML += `<div class="mentor-results">${html}</div>`;
                    closeModal();
                });
            }
        })
        .catch(error => console.error("Error fetching mentors:", error));
    }
</script>


<!-- CSS Enhancements -->
<style>
    /* Mentor Matching Button */
    .mentor-matching-button {
        background-color: #8e44ad;
        color: white;
        padding: 10px 15px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        border-radius: 5px;
    }

    /* Modal Styling */
    .modal {
        display: none; /* Hides modal initially */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.2);
        width: 350px;
        position: relative;
        text-align: center;
        animation: fadeIn 0.3s ease-in-out;
    }

    /* Close Button */
    .close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 20px;
        cursor: pointer;
    }

    /* Input Group */
    .input-group {
        margin-bottom: 15px;
        text-align: left;
    }

    .input-group label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .input-group input,
    .input-group select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    /* Buttons */
    .button-group {
        display: flex;
        justify-content: space-between;
    }

    .find-mentors-btn {
        background-color: #8e44ad;
        color: white;
        padding: 10px 15px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
    }

    .cancel-btn {
        background-color: #ffa500;
        color: white;
        padding: 10px 15px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
    }

    /* Fade In Animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
</style>

<!-- JavaScript for Modal Functionality -->
<script>
    function openModal() {
        document.getElementById('mentor-matching-modal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('mentor-matching-modal').style.display = 'none';
    }
</script>

      <div class="tutor">
         <img src="uploaded_files/<?= htmlspecialchars($fetch_tutor['image']); ?>" alt="">
         <div>
            <h3><?= htmlspecialchars($fetch_tutor['name']); ?></h3>
            <span><?= htmlspecialchars($fetch_tutor['profession']); ?></span>
         </div>
      </div>
      <form action="" method="post" class="flex">
         <input type="hidden" name="content_id" value="<?= htmlspecialchars($content_id); ?>">
         <a href="playlist.php?get_id=<?= $fetch_content['playlist_id']; ?>" class="inline-btn">view playlist</a>
         <?php
            if($verify_likes->rowCount() > 0){
         ?>
         <button type="submit" name="like_content"><i class="fas fa-heart"></i><span>liked</span></button>
         <?php
            }else{
         ?>
         <button type="submit" name="like_content"><i class="far fa-heart"></i><span>like</span></button>
         <?php
            }
         ?>
      </form>
      <div class="description"><p><?= htmlspecialchars($fetch_content['description']); ?></p></div>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">no videos added yet!</p>';
      }
   ?>

</section>


<!-- watch video section ends -->

<!-- comments section starts  -->

<section class="comments">

   <h1 class="heading">add a comment</h1>

   <form action="" method="post" class="add-comment">
      <input type="hidden" name="content_id" value="<?= $get_id; ?>">
      <textarea name="comment_box" required placeholder="write your comment..." maxlength="1000" cols="30" rows="10"></textarea>
      <input type="submit" value="add comment" name="add_comment" class="inline-btn">
   </form>

   <h1 class="heading">user comments</h1>

   <div class="show-comments">
      <?php
         $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE content_id = ?");
         $select_comments->execute([$get_id]);
         if($select_comments->rowCount() > 0){
            while($fetch_comment = $select_comments->fetch(PDO::FETCH_ASSOC)){   
               $select_commentor = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
               $select_commentor->execute([$fetch_comment['user_id']]);
               $fetch_commentor = $select_commentor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box" style="<?php if($fetch_comment['user_id'] == $user_id){echo 'order:-1;';} ?>">
         <div class="user">
            <img src="uploaded_files/<?= htmlspecialchars($fetch_commentor['image']); ?>" alt="">
            <div>
               <h3><?= htmlspecialchars($fetch_commentor['name']); ?></h3>
               <span><?= $fetch_comment['date']; ?></span>
            </div>
         </div>
         <p class="text"><?= htmlspecialchars($fetch_comment['comment']); ?></p>
         <?php
            if($fetch_comment['user_id'] == $user_id){ 
         ?>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="comment_id" value="<?= $fetch_comment['id']; ?>">
            <button type="submit" name="edit_comment" class="inline-option-btn">edit comment</button>
            <button type="submit" name="delete_comment" class="inline-delete-btn" onclick="return confirm('delete this comment?');">delete comment</button>
         </form>
         <?php
            }
         ?>
      </div>
      <?php
       }
      }else{
         echo '<p class="empty">no comments added yet!</p>';
      }
      ?>
      </div>
   </div>
   
</section>

<!-- comments section ends -->

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>
