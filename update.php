<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
    header('location:login.php');
    exit();
}

// Fetch existing user data
$select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ? LIMIT 1");
$select_user->execute([$user_id]);
$fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

// Initialize default values for form fields
$name = $fetch_user['name'] ?? '';
$email = $fetch_user['email'] ?? '';
$image = $fetch_user['image'] ?? '';
$cgpa = $fetch_user['cgpa'] ?? '';
$technical_skills = $fetch_user['technical_skills'] ?? '';
$internships_projects = $fetch_user['internships_projects'] ?? '';
$aptitude_logical_reasoning = $fetch_user['aptitude_logical_reasoning'] ?? '';
$soft_skills = $fetch_user['soft_skills'] ?? '';
$resume_link = $fetch_user['resume_link'] ?? '';
$linkedin_profile = $fetch_user['linkedin_profile'] ?? '';

// Handle form submission
if (isset($_POST['submit'])) {
    try {
        // Get previous values
        $prev_pass = $fetch_user['password'];
        $prev_image = $fetch_user['image'];

        // Update Name
        if (!empty($_POST['name'])) {
            $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $update_name = $conn->prepare("UPDATE `users` SET name = ? WHERE id = ?");
            $update_name->execute([$name, $user_id]);
            $message[] = 'Username updated successfully!';
        }

        // Update Email
        if (!empty($_POST['email'])) {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $select_email = $conn->prepare("SELECT email FROM `users` WHERE email = ? AND id != ?");
            $select_email->execute([$email, $user_id]);
            if ($select_email->rowCount() > 0) {
                $message[] = 'Email already taken!';
            } else {
                $update_email = $conn->prepare("UPDATE `users` SET email = ? WHERE id = ?");
                $update_email->execute([$email, $user_id]);
                $message[] = 'Email updated successfully!';
            }
        }

        // Update Image
        if (!empty($_FILES['image']['name'])) {
            $image = filter_var($_FILES['image']['name'], FILTER_SANITIZE_STRING);
            $ext = pathinfo($image, PATHINFO_EXTENSION);
            $rename = uniqid() . '.' . $ext;
            $image_size = $_FILES['image']['size'];
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $image_folder = 'uploaded_files/' . $rename;

            if ($image_size > 2000000) {
                $message[] = 'Image size too large!';
            } else {
                $update_image = $conn->prepare("UPDATE `users` SET `image` = ? WHERE id = ?");
                $update_image->execute([$rename, $user_id]);
                move_uploaded_file($image_tmp_name, $image_folder);

                if ($prev_image != '' && file_exists('uploaded_files/' . $prev_image)) {
                    unlink('uploaded_files/' . $prev_image);
                }

                $message[] = 'Profile image updated successfully!';
            }
        }

        // Update Password
        if (!empty($_POST['old_pass']) && !empty($_POST['new_pass']) && !empty($_POST['cpass'])) {
            $old_pass = $_POST['old_pass'];
            $new_pass = $_POST['new_pass'];
            $cpass = $_POST['cpass'];

            if (!password_verify($old_pass, $prev_pass)) {
                $message[] = 'Old password does not match!';
            } elseif ($new_pass !== $cpass) {
                $message[] = 'Confirm password does not match!';
            } else {
                $hashed_pass = password_hash($cpass, PASSWORD_DEFAULT);
                $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
                $update_pass->execute([$hashed_pass, $user_id]);
                $message[] = 'Password updated successfully!';
            }
        }

        // Update Additional Profile Fields
        $cgpa = isset($_POST['cgpa']) ? filter_var($_POST['cgpa'], FILTER_SANITIZE_STRING) : '';
        $technical_skills = isset($_POST['technical_skills']) ? filter_var($_POST['technical_skills'], FILTER_SANITIZE_STRING) : '';
        $internships_projects = isset($_POST['internships_projects']) ? filter_var($_POST['internships_projects'], FILTER_SANITIZE_STRING) : '';
        $aptitude_logical_reasoning = isset($_POST['aptitude_logical_reasoning']) ? filter_var($_POST['aptitude_logical_reasoning'], FILTER_SANITIZE_STRING) : '';
        $soft_skills = isset($_POST['soft_skills']) ? filter_var($_POST['soft_skills'], FILTER_SANITIZE_STRING) : '';
        $resume_link = isset($_POST['resume_link']) ? filter_var($_POST['resume_link'], FILTER_SANITIZE_URL) : '';
        $linkedin_profile = isset($_POST['linkedin_profile']) ? filter_var($_POST['linkedin_profile'], FILTER_SANITIZE_URL) : '';

        // Check if any field is updated before running SQL
        if (!empty($cgpa) || !empty($technical_skills) || !empty($internships_projects) ||
            !empty($aptitude_logical_reasoning) || !empty($soft_skills) ||
            !empty($resume_link) || !empty($linkedin_profile)) {

            $update_details = $conn->prepare("
                UPDATE `users` SET 
                    cgpa = ?, 
                    technical_skills = ?, 
                    internships_projects = ?, 
                    aptitude_logical_reasoning = ?, 
                    soft_skills = ?, 
                    resume_link = ?, 
                    linkedin_profile = ? 
                WHERE id = ?
            ");

            $update_details->execute([
                $cgpa,
                $technical_skills,
                $internships_projects,
                $aptitude_logical_reasoning,
                $soft_skills,
                $resume_link,
                $linkedin_profile,
                $user_id
            ]);

            $message[] = 'Profile details updated successfully!';
        }

    } catch (PDOException $e) {
        die("Error updating profile: " . $e->getMessage());
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Profile</title>

   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <!-- Custom CSS -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container" style="min-height: calc(100vh - 19rem);">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>Update Profile</h3>

      <div class="flex">
         <div class="col">
            <p>Your Name</p>
            <input type="text" name="name" value="<?= $fetch_user['name']; ?>" maxlength="100" class="box">
            <p>Your Email</p>
            <input type="email" name="email" value="<?= $fetch_user['email']; ?>" maxlength="100" class="box">
            <p>Update Pic</p>
            <input type="file" name="image" accept="image/*" class="box">
         </div>

         <div class="col">
            <p>Old Password</p>
            <input type="password" name="old_pass" placeholder="Enter your old password" maxlength="50" class="box">
            <p>New Password</p>
            <input type="password" name="new_pass" placeholder="Enter your new password" maxlength="50" class="box">
            <p>Confirm Password</p>
            <input type="password" name="cpass" placeholder="Confirm your new password" maxlength="50" class="box">
         </div>
      </div>

      <p>CGPA</p>
      <input type="text" name="cgpa" value="<?= $fetch_user['cgpa']; ?>" class="box">

      <p>Technical Skills</p>
      <textarea name="technical_skills" class="box"><?= $fetch_user['technical_skills']; ?></textarea>

      <p>Certifications</p>
      <textarea name="certifications" class="box"><?= $fetch_user['certifications']; ?></textarea>

      <p>Internships/Projects</p>
      <textarea name="internships_projects" class="box"><?= $fetch_user['internships_projects']; ?></textarea>

      <p>Aptitude/Logical Reasoning Level</p>
      <input type="text" name="aptitude_logical_reasoning" value="<?= $fetch_user['aptitude_logical_reasoning']; ?>" class="box">

      <p>Soft Skills</p>
      <input type="text" name="soft_skills" value="<?= $fetch_user['soft_skills']; ?>" class="box">

      <p>Resume Link</p>
      <input type="url" name="resume_link" value="<?= $fetch_user['resume_link']; ?>" class="box">

      <p>LinkedIn Profile</p>
      <input type="url" name="linkedin_profile " value="<?= $fetch_user['linkedin_profile']; ?>" class="box">

      <input type="submit" name="submit" value="Update Profile" class="btn">
   </form>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>
