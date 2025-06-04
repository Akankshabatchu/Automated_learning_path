<?php
session_start();
include 'C:\xampp\htdocs\project\components\connect.php';

if (isset($_POST['submit'])) {
    $id = unique_id();
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['pass']);
    $cpass = sha1($_POST['cpass']);
    $department = filter_var($_POST['department'], FILTER_SANITIZE_STRING);
    $experience = filter_var($_POST['experience'], FILTER_SANITIZE_STRING);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $college = filter_var($_POST['college'], FILTER_SANITIZE_STRING);
    $designation = filter_var($_POST['designation'], FILTER_SANITIZE_STRING);

    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $rename = unique_id() . '.' . $ext;
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_files/' . $rename;

    $select_officer = $conn->prepare("SELECT * FROM placement_officers WHERE email = ?");
    $select_officer->execute([$email]);

    if ($select_officer->rowCount() > 0) {
        $message[] = 'Email already taken!';
    } else {
        if ($pass != $cpass) {
            $message[] = 'Passwords do not match!';
        } else {
            $insert_officer = $conn->prepare("INSERT INTO placement_officers 
                (id, name, email, password, department, experience, phone, college, designation, photo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if ($insert_officer->execute([$id, $name, $email, $cpass, $department, $experience, $phone, $college, $designation, $rename])) {
                move_uploaded_file($image_tmp_name, $image_folder);
                header('Location: placement_officer_login.php');
                exit();
            } else {
                $message[] = 'Registration failed!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register as Placement Officer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f4f8;
            color: #333;
        }
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-top: 5px solid #8e44ad;
        }
        .btn-primary {
            background-color: #8e44ad;
            border-color: #8e44ad;
        }
        .btn-primary:hover {
            background-color: #8e44ad;
        }
        input.form-control {
            border-radius: 5px;
            border: 1px solid #ced4da;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h3 class="text-center mb-4">Register as Placement Officer</h3>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <input type="text" name="name" class="form-control" placeholder="Full Name *" required>
                </div>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email Address *" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="pass" class="form-control" placeholder="Password *" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="cpass" class="form-control" placeholder="Confirm Password *" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="department" class="form-control" placeholder="Department *" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="experience" class="form-control" placeholder="Years of Experience *" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="phone" class="form-control" placeholder="Phone Number *" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="college" class="form-control" placeholder="College/University *" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="designation" class="form-control" placeholder="Designation *" required>
                </div>
                <div class="mb-3">
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary w-100">Register Now</button>
                <p class="text-center mt-3">Already have an account? <a href="placement_officer_login.php">Login now</a></p>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>