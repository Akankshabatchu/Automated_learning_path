<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Recommended Courses</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">

   <script>
        document.addEventListener("DOMContentLoaded", function() {
            let recommendedCourses = JSON.parse(localStorage.getItem("recommended_courses"));
            let container = document.getElementById("recommendation-list");

            if (recommendedCourses && recommendedCourses.length > 0) {
                recommendedCourses.forEach(course => {
                    let courseCard = document.createElement("div");
                    courseCard.classList.add("course-card");
                    courseCard.innerHTML = `
                        <div class="course-image">
                            <img src="images/course_placeholder.jpg" alt="Course Image">
                        </div>
                        <div class="course-content">
                            <h3 class="title">${course.Course_Name}</h3>
                            <p class="difficulty"><i class="fas fa-signal"></i> ${course.Difficulty_Level}</p>
                            <p class="description">${course.Description}</p>
                            <button class="btn view-course-btn" data-course="${course.Course_Name}">Active🟢</button>
                        </div>
                    `;
                    container.appendChild(courseCard);
                });

                // Add event listener for all "View Course" buttons
                document.querySelectorAll(".view-course-btn").forEach(button => {
                    button.addEventListener("click", function() {
                        let courseName = this.getAttribute("data-course");
                        checkPlaylist(courseName);
                    });
                });
            } else {
                container.innerHTML = '<p class="empty">No recommendations found!</p>';
            }
        });

        function checkPlaylist(courseName) {
    fetch(`check_playlist.php?course=${encodeURIComponent(courseName)}`)
        .then(response => response.json())
        .then(data => {
            console.log("Received Response:", data);  // Debugging
            if (data.exists) {
                window.location.href = `playlist.php?playlist_id=${data.playlist_id}`;
            } else {
                alert("This course does not have a playlist yet.");
            }
        })
        .catch(error => console.error("Error:", error));
}

   </script>

   <style>
    .header {
    text-align: right; /* Aligns text to the right */
    padding-left: 130px; /* Adds some spacing from the right edge */
}

       /* General Page Styling */
       body {
           font-family: 'Poppins', sans-serif;
           background-color: #f8f9fa;
           margin: 0;
           padding: 0;
       }

       .recommendation {
           max-width: 1100px;
           margin: 40px auto;
           text-align: center;
       }

       .heading {
           font-size: 28px;
           font-weight: 600;
           color: #333;
           margin-bottom: 20px;
       }

       /* Course Card Grid */
       .box-container {
           display: grid;
           grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
           gap: 20px;
           padding: 20px;
       }

       .course-card {
           background: #fff;
           border-radius: 12px;
           overflow: hidden;
           box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
           transition: transform 0.3s ease, box-shadow 0.3s ease;
       }

       .course-card:hover {
           transform: translateY(-5px);
           box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
       }

       .course-image img {
           width: 100%;
           height: 180px;
           object-fit: cover;
           border-bottom: 2px solid #ddd;
       }

       .course-content {
           padding: 15px;
       }

       .title {
           font-size: 20px;
           font-weight: 600;
           color: #333;
           margin-bottom: 5px;
       }

       .difficulty {
           font-size: 14px;
           color: #666;
           margin-bottom: 10px;
       }

       .description {
           font-size: 14px;
           color: #555;
           line-height: 1.6;
           margin-bottom: 10px;
       }

       .btn {
           display: inline-block;
           padding: 8px 15px;
           font-size: 14px;
           color: #fff;
           background: #8e44ad;
           border-radius: 5px;
           text-decoration: none;
           transition: background 0.3s ease;
           border: none;
           cursor: pointer;
       }

       .btn:hover {
           background: #732d91;
       }

       .empty {
           font-size: 18px;
           color: #888;
           margin-top: 20px;
       }
       /* Default content position */
.main-content {
    transition: margin-left 0.3s ease-in-out;
}

/* When sidebar is active, push content to the right */
body.active .main-content {
    margin-left: 250px; /* Adjust width to match sidebar */
}
.side-bar {
    width: 250px; /* Adjust width */
    position: fixed;
    height: 100vh;
    left: -250px; /* Initially hidden */
    transition: left 0.3s ease-in-out;
}

/* When active, slide in */
.side-bar.active {
    left: 0;
}

   </style>

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="recommendation">
   <h1 class="heading">Recommended Courses</h1>
   <div class="box-container" id="recommendation-list"></div>
</section>
<script>
document.querySelector('#menu-btn').onclick = () => {
    sideBar.classList.toggle('active');
    body.classList.toggle('active'); // This applies the CSS for content shift
}
</script>
<?php include 'components/footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
