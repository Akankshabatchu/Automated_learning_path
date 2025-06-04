<?php

include '../components/connect.php';

if (isset($_COOKIE['tutor_id'])) {
    $tutor_id = $_COOKIE['tutor_id'];
} else {
    header('location:login.php');
    exit();
}

// Fetch current tutor details
$select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
$select_tutor->execute([$tutor_id]);
$fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = [];

    // Define fields to update
    $fields = [
        'name', 'profession', 'email', 'department', 'expertise_domains',
        'average_rating', 'total_students_mentored', 'years_of_experience',
        'preferred_style_to_teach', 'availability', 'mentorship_mode', 'languages_spoken'
    ];

    // Update only fields that exist in $_POST
    foreach ($fields as $field) {
        if (isset($_POST[$field]) && !empty($_POST[$field])) {
            $value = filter_var($_POST[$field], FILTER_SANITIZE_STRING);
            if (!isset($fetch_tutor[$field]) || $fetch_tutor[$field] !== $value) { 
                $update = $conn->prepare("UPDATE `tutors` SET $field = ? WHERE id = ?");
                $update->execute([$value, $tutor_id]);
                $message[] = ucfirst(str_replace('_', ' ', $field)) . ' updated successfully!';
            }
        }
    }

    // Image update
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = filter_var($_FILES['image']['name'], FILTER_SANITIZE_STRING);
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $rename = uniqid() . '.' . $ext;
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        $image_folder = '../uploaded_files/' . $rename;

        if ($image_size > 2000000) {
            $message[] = 'Image size too large!';
        } else {
            $update_image = $conn->prepare("UPDATE `tutors` SET image = ? WHERE id = ?");
            $update_image->execute([$rename, $tutor_id]);
            move_uploaded_file($image_tmp_name, $image_folder);

            // Delete old image if exists
            if (!empty($fetch_tutor['image']) && file_exists('../uploaded_files/' . $fetch_tutor['image'])) {
                unlink('../uploaded_files/' . $fetch_tutor['image']);
            }
            $message[] = 'Image updated successfully!';
        }
    }

    // Password update
    if (!empty($_POST['old_pass']) && !empty($_POST['new_pass']) && !empty($_POST['cpass'])) {
        $old_pass_hash = sha1($_POST['old_pass']);
        $new_pass_hash = sha1($_POST['new_pass']);
        $cpass_hash = sha1($_POST['cpass']);

        if ($old_pass_hash !== $fetch_tutor['password']) {
            $message[] = 'Old password not matched!';
        } elseif ($new_pass_hash !== $cpass_hash) {
            $message[] = 'Confirm password not matched!';
        } else {
            $update_pass = $conn->prepare("UPDATE `tutors` SET password = ? WHERE id = ?");
            $update_pass->execute([$cpass_hash, $tutor_id]);
            $message[] = 'Password updated successfully!';
        }
    }

    // Display success messages without duplicates
    $message = array_unique($message);
    foreach ($message as $msg) {
        echo "<p>$msg</p>";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="form-container" style="min-height: calc(100vh - 19rem);">
    <form class="register" action="" method="post" enctype="multipart/form-data">
        <h3>Update Profile</h3>
        <div class="flex">
            <div class="col">
                <p>Your Name</p>
                <input type="text" name="name" value="<?= $fetch_tutor['name']; ?>" maxlength="50" class="box">
                
                <p>Your Profession</p>

<select name="profession" class="box" required>
    <option value="" disabled <?= empty($fetch_tutor['profession']) ? 'selected' : ''; ?>>-- Select Your Profession --</option>
    <option value="Teaching Assistant" <?= ($fetch_tutor['profession'] ?? '') === 'Teaching Assistant' ? 'selected' : ''; ?>>Teaching Assistant</option>
    <option value="Assistant Lecturer" <?= ($fetch_tutor['profession'] ?? '') === 'Assistant Lecturer' ? 'selected' : ''; ?>>Assistant Lecturer</option>
    <option value="Assistant Professor" <?= ($fetch_tutor['profession'] ?? '') === 'Assistant Professor' ? 'selected' : ''; ?>>Assistant Professor</option>
    <option value="Associate Professor" <?= ($fetch_tutor['profession'] ?? '') === 'Associate Professor' ? 'selected' : ''; ?>>Associate Professor</option>
    <option value="Professor" <?= ($fetch_tutor['profession'] ?? '') === 'Professor' ? 'selected' : ''; ?>>Professor</option>
    <option value="Head of Department (HOD)" <?= ($fetch_tutor['profession'] ?? '') === 'Head of Department (HOD)' ? 'selected' : ''; ?>>Head of Department (HOD)</option>
    <option value="Dean (Academics/Research)" <?= ($fetch_tutor['profession'] ?? '') === 'Dean (Academics/Research)' ? 'selected' : ''; ?>>Dean (Academics/Research)</option>
    <option value="Principal/Director" <?= ($fetch_tutor['profession'] ?? '') === 'Principal/Director' ? 'selected' : ''; ?>>Principal/Director</option>
</select>


                <p>Your Email</p>
                <input type="email" name="email" value="<?= $fetch_tutor['email']; ?>" class="box">
                
                <p>Department</p>

<select name="department" class="box" required>
    <option value="" disabled <?= empty($fetch_tutor['department']) ? 'selected' : ''; ?>>-- Select Your Department --</option>
    <option value="Computer Science and Engineering (CSE)" <?= ($fetch_tutor['department'] ?? '') === 'Computer Science and Engineering (CSE)' ? 'selected' : ''; ?>>Computer Science and Engineering (CSE)</option>
    <option value="Information Technology (IT)" <?= ($fetch_tutor['department'] ?? '') === 'Information Technology (IT)' ? 'selected' : ''; ?>>Information Technology (IT)</option>
    <option value="Electronics and Communication Engineering (ECE)" <?= ($fetch_tutor['department'] ?? '') === 'Electronics and Communication Engineering (ECE)' ? 'selected' : ''; ?>>Electronics and Communication Engineering (ECE)</option>
    <option value="Electrical and Electronics Engineering (EEE)" <?= ($fetch_tutor['department'] ?? '') === 'Electrical and Electronics Engineering (EEE)' ? 'selected' : ''; ?>>Electrical and Electronics Engineering (EEE)</option>
    <option value="Mechanical Engineering (ME)" <?= ($fetch_tutor['department'] ?? '') === 'Mechanical Engineering (ME)' ? 'selected' : ''; ?>>Mechanical Engineering (ME)</option>
    <option value="Civil Engineering (ME)" <?= ($fetch_tutor['department'] ?? '') === 'Civil Engineering (ME)' ? 'selected' : ''; ?>>Civil Engineering (ME)</option>
    <option value="Artificial Intelligence and Data Science (AI & DS)" <?= ($fetch_tutor['department'] ?? '') === 'Artificial Intelligence and Data Science (AI & DS)' ? 'selected' : ''; ?>>Artificial Intelligence and Data Science (AI & DS)</option>
    <option value="Internet of Things (IoT)" <?= ($fetch_tutor['department'] ?? '') === 'Internet of Things (IoT)' ? 'selected' : ''; ?>>Internet of Things (IoT)</option>
</select>


                <p>Expertise Domains</p>
                <input type="text" name="expertise_domains" value="<?= $fetch_tutor['Expertise_Domains']; ?>" class="box">

                <p>Average Rating</p>
                <input type="number" step="0.1" name="average_rating" value="<?= $fetch_tutor['Average_Rating']; ?>" class="box">

                <p>Total Students Mentored</p>
                <input type="number" name="total_students_mentored" value="<?= $fetch_tutor['Total_Students_Mentored']; ?>" class="box">

                <p>Years of Experience</p>
                <input type="number" name="years_of_experience" value="<?= $fetch_tutor['Years_of_Experience']; ?>" class="box">
            </div>
            <div class="col">
            <p>Preferred Style to Teach</p>

<select name="preferred_style_to_teach" class="box" required>
    <option value="" disabled <?= empty($fetch_tutor['Preferred_Style_to_Teach']) ? 'selected' : ''; ?>>-- Select Your Teaching Style --</option>
    <option value="Hands-on" <?= ($fetch_tutor['Preferred_Style_to_Teach'] ?? '') === 'Hands-on' ? 'selected' : ''; ?>>Hands-on</option>
    <option value="Interactive" <?= ($fetch_tutor['Preferred_Style_to_Teach'] ?? '') === 'Interactive' ? 'selected' : ''; ?>>Interactive</option>
    <option value="Lecture-based" <?= ($fetch_tutor['Preferred_Style_to_Teach'] ?? '') === 'Lecture-based' ? 'selected' : ''; ?>>Lecture-based</option>
    <option value="Project-based" <?= ($fetch_tutor['Preferred_Style_to_Teach'] ?? '') === 'Project-based' ? 'selected' : ''; ?>>Project-based</option>
</select>
<?php
// Ensure 'Availability' exists and trim any spaces to avoid mismatches
$availability = isset($fetch_tutor['Availability']) ? trim($fetch_tutor['Availability']) : '';
?>

<p>Availability</p>
<select name="availability" class="box" required>
    <option value="" disabled <?= empty($availability) ? 'selected' : ''; ?>>-- Select Availability --</option>
    <option value="Flexible" <?= ($availability == "Flexible") ? 'selected' : ''; ?>>Flexible</option>
    <option value="Weekdays - Afternoons" <?= ($availability == "Weekdays - Afternoons") ? 'selected' : ''; ?>>Weekdays - Afternoons</option>
    <option value="Weekdays - Evenings" <?= ($availability == "Weekdays - Evenings") ? 'selected' : ''; ?>>Weekdays - Evenings</option>
    <option value="Weekends - Mornings" <?= ($availability == "Weekends - Mornings") ? 'selected' : ''; ?>>Weekends - Mornings</option>
</select>



<p>Mentorship Mode</p>

<select name="mentorship_mode" class="box" required>
    <option value="" disabled selected>-- Select Mentorship Mode --</option>
    <option value="Offline (In-person)" <?= ($fetch_tutor['Mentorship_Mode'] == "Offline (In-person)") ? 'selected' : ''; ?>>Offline (In-person)</option>
    <option value="Online (Google Meet)" <?= ($fetch_tutor['Mentorship_Mode'] == "Online (Google Meet)") ? 'selected' : ''; ?>>Online (Google Meet)</option>
    <option value="Online (Zoom)" <?= ($fetch_tutor['Mentorship_Mode'] == "Online (Zoom)") ? 'selected' : ''; ?>>Online (Zoom)</option>
</select>

                <p>Languages Spoken</p>
                <input type="text" name="languages_spoken" value="<?= $fetch_tutor['Languages_Spoken']; ?>" class="box">

                <p>Old Password</p>
                <input type="password" name="old_pass" placeholder="Enter your old password" maxlength="20" class="box">

                <p>New Password</p>
                <input type="password" name="new_pass" placeholder="Enter your new password" maxlength="20" class="box">

                <p>Confirm Password</p>
                <input type="password" name="cpass" placeholder="Confirm your new password" maxlength="20" class="box">
            </div>
        </div>
        <p>Update Picture</p>
        <input type="file" name="image" accept="image/*" class="box">
        <input type="submit" name="submit" value="Update Now" class="btn">
    </form>
</section>

<?php include '../components/footer.php'; ?>
<script src="../js/admin_script.js"></script>
<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>

</body>
</html>
