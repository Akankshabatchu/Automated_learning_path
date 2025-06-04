<?php
session_start();
include 'components/connect.php';

if (isset($_POST['submit'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['pass']); // Hash password with SHA1

    $select_officer = $conn->prepare("SELECT * FROM `placement_officers` WHERE email = ? AND password = ?");
    $select_officer->execute([$email, $pass]);
    $row = $select_officer->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $_SESSION['placement_officer'] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'email' => $row['email']
        ];
        header("Location: placement_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Incorrect email or password!'); window.location.href='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placement Officer Login</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Global Styling */
        * {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(to right,#8e44ad,rgb(248, 249, 251));
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .form-container h3 {
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .form-container p {
            font-size: 14px;
            color: #666;
        }

        .box {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            outline: none;
            transition: 0.3s;
        }

        .box:focus {
            border-color: #8e44ad;
            box-shadow: 0px 0px 8px rgba(37, 117, 252, 0.3);
        }

        .btn {
            background: #8e44ad;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            padding: 12px;
            width: 100%;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background: #8e44ad;
        }

        .link {
            margin-top: 10px;
            font-size: 14px;
        }

        .link a {
            text-decoration: none;
            color: #8e44ad;
            font-weight: bold;
        }

        .link a:hover {
            text-decoration: underline;
        }

        .icon {
            font-size: 22px;
            color: #8e44ad;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<section class="form-container">
    <i class="icon fas fa-user-circle"></i>
    <h3>Welcome Back, Officer!</h3>
    <p>Please login to access your dashboard</p>
    <form action="" method="post" class="login">
        <p>Your Email <span>*</span></p>
        <input type="email" name="email" placeholder="Enter your email" required class="box">
        <p>Your Password <span>*</span></p>
        <input type="password" name="pass" placeholder="Enter your password" required class="box">
        <input type="submit" name="submit" value="Login Now" class="btn">
        <p class="link">Don't have an account? <a href="placement_officer_register.php">Register Now</a></p>
    </form>
</section>

</body>
</html>
