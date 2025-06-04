
<?php

session_start();

require_once "components/connect.php"; // Ensure the correct database connection file

// Check if the user is logged in
if (!isset($_SESSION['placement_officer'])) {
    header("Location: login.php");
    exit();
}

$officer_id = $_SESSION['placement_officer']['id'];

$stmt = $conn->prepare("SELECT * FROM placement_officers WHERE id = :id LIMIT 1");
$stmt->bindValue(':id', $officer_id, PDO::PARAM_STR); // Change to PDO::PARAM_STR
$stmt->execute();
$officer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$officer) {
    die("Error: Placement officer not found! Please log in again.");
}


// Initialize messages
$success_message = "";
$error_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $update_fields = [];
    $params = [':id' => $officer_id];

    if (!empty($_POST['name'])) {
        $update_fields[] = "name = :name";
        $params[':name'] = trim($_POST['name']);
    }

    if (!empty($_POST['email']) && $_POST['email'] !== $officer['email']) {
        // Check if the email already exists
        $email_check_stmt = $conn->prepare("SELECT id FROM placement_officers WHERE email = :email AND id != :id");
        $email_check_stmt->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
        $email_check_stmt->bindValue(':id', $officer_id, PDO::PARAM_INT);
        $email_check_stmt->execute();

        if ($email_check_stmt->rowCount() > 0) {
            $error_message = "Error: This email is already registered with another account!";
        } else {
            $update_fields[] = "email = :email";
            $params[':email'] = trim($_POST['email']);
        }
    }

    if (!empty($_POST['password'])) {
        $update_fields[] = "password = :password";
        $params[':password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    if (!empty($_POST['department'])) {
        $update_fields[] = "department = :department";
        $params[':department'] = trim($_POST['department']);
    }

    if (!empty($_POST['experience'])) {
        $update_fields[] = "experience = :experience";
        $params[':experience'] = trim($_POST['experience']);
    }

    if (!empty($_POST['phone'])) {
        $update_fields[] = "phone = :phone";
        $params[':phone'] = trim($_POST['phone']);
    }

    if (!empty($_POST['college'])) {
        $update_fields[] = "college = :college";
        $params[':college'] = trim($_POST['college']);
    }

    if (!empty($_POST['designation'])) {
        $update_fields[] = "designation = :designation";
        $params[':designation'] = trim($_POST['designation']);
    }

    // Handle profile photo upload
    if (!empty($_FILES['photo']['name'])) {
        $photo_name = time() . '_' . $_FILES['photo']['name'];
        $photo_tmp = $_FILES['photo']['tmp_name'];
        $photo_path = 'uploaded_files/' . $photo_name;
        move_uploaded_file($photo_tmp, $photo_path);
        $update_fields[] = "photo = :photo";
        $params[':photo'] = $photo_name;
    }

    if (!empty($update_fields)) {
        try {
            $sql = "UPDATE placement_officers SET " . implode(", ", $update_fields) . " WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $success_message = "Profile updated successfully!";
        } catch (PDOException $e) {
            $error_message = "Error updating profile: " . $e->getMessage();
        }
    } else {
        $error_message = "No fields were updated.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 60%;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: grid;
            gap: 15px;
        }
        label {
            font-size: 14px;
            color: #555;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="file"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            width: 100%;
            box-sizing: border-box;
        }
        button {
            background-color: #8e44ad;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        img {
            border-radius: 50%;
        }
        .form-actions {
            text-align: center;
        }
        a {
            color: #8e44ad;
            text-decoration: none;
            font-size: 16px;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Placement Officer Profile</h2>

    <?php if (!empty($success_message)) echo "<div class='message success'>$success_message</div>"; ?>
    <?php if (!empty($error_message)) echo "<div class='message error'>$error_message</div>"; ?>

    <form action="placement_profile.php" method="POST" enctype="multipart/form-data">
        <label>Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($officer['name']); ?>"><br>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($officer['email']); ?>"><br>

        <label>Password (Leave blank to keep current password):</label>
        <input type="password" name="password"><br>

        <label>Department:</label>
        <input type="text" name="department" value="<?php echo htmlspecialchars($officer['department']); ?>"><br>

        <label>Experience (Years):</label>
        <input type="number" name="experience" value="<?php echo htmlspecialchars($officer['experience']); ?>"><br>

        <label>Phone:</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($officer['phone']); ?>"><br>

        <label>College/University:</label>
        <input type="text" name="college" value="<?php echo htmlspecialchars($officer['college']); ?>"><br>

        <label>Designation:</label>
        <input type="text" name="designation" value="<?php echo htmlspecialchars($officer['designation']); ?>"><br>

        <label>Profile Photo:</label>
        <input type="file" name="photo"><br>
        <?php if (!empty($officer['photo'])): ?>
            <img src="uploaded_files/<?php echo htmlspecialchars($officer['photo']); ?>" width="100" height="100"><br>
        <?php endif; ?>

        <div class="form-actions">
            <button type="submit">Update Profile</button>
        </div>
    </form>
<br>
    <div class="form-actions">
        <a href="placement_dashboard.php">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
