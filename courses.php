<?php
include 'components/connect.php';

// Get user ID from cookie if available
$user_id = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Course Recommendations</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">

   <script>
        function getRecommendations() {
            let courseName = document.getElementById("course_name").value.trim();
            let difficulty = document.getElementById("difficulty_level").value;

            if (!courseName) {
                alert("Please enter a course name.");
                return;
            }

            fetch("http://localhost:5000/recommend", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    query: courseName,
                    level: difficulty
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.recommendations && data.recommendations.length > 0) {
                    localStorage.setItem("recommended_courses", JSON.stringify(data.recommendations));
                    window.location.href = "recommend_course.php";
                } else {
                    alert("No recommendations found!");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Error fetching recommendations. Check console.");
            });
        }
   </script>

   <style>
      /* Improved Form Styling */
      .recommendation {
         background: #ffffff;
         padding: 20px;
         border-radius: 10px;
         box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
         width: 100%;
         max-width: 600px;
         margin: 20px auto;
         text-align: center;
      }

      .recommendation h1 {
         font-size: 22px;
         font-weight: bold;
         color: #333;
         margin-bottom: 15px;
      }

      .recommendation form {
         display: flex;
         flex-direction: column;
         gap: 10px;
         align-items: center;
      }

      .recommendation input,
      .recommendation select {
         width: 90%;
         padding: 12px;
         border-radius: 6px;
         border: 1px solid #ccc;
         font-size: 16px;
         transition: 0.3s;
      }

      .recommendation input:focus,
      .recommendation select:focus {
         border-color: #6c63ff;
         box-shadow: 0 0 5px rgba(108, 99, 255, 0.4);
      }

      .recommendation button {
         background: #8e44ad;
         color: white;
         font-size: 16px;
         font-weight: bold;
         padding: 12px 20px;
         border: none;
         border-radius: 6px;
         cursor: pointer;
         transition: 0.3s;
         width: 90%;
      }

      .recommendation button:hover {
         background: #8e44ad;
      }

      /* Course Section */
      .courses .box-container {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
         gap: 20px;
         margin-top: 20px;
      }

      .courses .box {
         background: #fff;
         padding: 15px;
         border-radius: 8px;
         box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
         text-align: center;
      }

      .courses .tutor {
         display: flex;
         align-items: center;
         gap: 10px;
      }

      .courses .tutor img {
         width: 50px;
         height: 50px;
         border-radius: 50%;
         object-fit: cover;
      }

      .courses .title {
         font-size: 18px;
         font-weight: bold;
         color: #333;
         margin: 10px 0;
      }

      .inline-btn {
         background: #6c63ff;
         color: white;
         padding: 8px 15px;
         border-radius: 5px;
         text-decoration: none;
         font-weight: bold;
         transition: 0.3s;
      }

      .inline-btn:hover {
         background: #554be1;
      }
   </style>

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- Course Recommendation Form -->
<section class="recommendation">
   <h1>Find Your Course</h1>

   <form onsubmit="event.preventDefault(); getRecommendations();">
      <input type="text" id="course_name" name="course_name" placeholder="Enter Course Name" required>
      <select id="difficulty_level" name="difficulty_level" required>
         <option value="" disabled selected>Select Difficulty</option>
         <option value="Beginner">Beginner</option>
         <option value="Intermediate">Intermediate</option>
         <option value="Advanced">Advanced</option>
      </select>
      <button type="submit">Recommend Course</button>
   </form>
</section>

<!-- Courses Section Starts -->
<section class="courses">
   <h1 class="heading">All Courses</h1>

   <div class="box-container">
      <?php
         // Fetch courses with tutors in one optimized query
         $query = "SELECT p.id, p.title, p.date, p.tutor_id, t.name AS tutor_name, t.image AS tutor_image 
                   FROM playlist p 
                   JOIN tutors t ON p.tutor_id = t.id 
                   WHERE p.status = ? 
                   ORDER BY p.date DESC";

         $select_courses = $conn->prepare($query);
         $select_courses->execute(['active']);

         if ($select_courses->rowCount() > 0) {
            while ($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)) {
      ?>
      <div class="box">
         <div class="tutor">
            <img src="uploaded_files/<?= htmlspecialchars($fetch_course['tutor_image']); ?>" alt="Tutor Image">
            <div>
               <h3><?= htmlspecialchars($fetch_course['tutor_name']); ?></h3>
               <span><?= htmlspecialchars($fetch_course['date']); ?></span>
            </div>
         </div>
         <h3 class="title"><?= htmlspecialchars($fetch_course['title']); ?></h3>
         <a href="playlist.php?get_id=<?= $fetch_course['id']; ?>" class="inline-btn">View Playlist</a>
      </div>
      <?php
         }
      } else {
         echo '<p class="empty">No courses added yet!</p>';
      }
      ?>
   </div>
</section>
<!-- Courses Section Ends -->

<?php include 'components/footer.php'; ?>

<!-- Custom JS file link -->
<script src="js/script.js"></script>
</body>
</html>
