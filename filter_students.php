<?php
// Database Connection (Modify with your credentials)
$host = 'localhost';
$port = '3307'; // Ensure this is the correct MySQL port
$dbname = 'my_project_db';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Search Logic
$students = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $certifications = trim($_POST['certifications']);
    $technical_skills = trim($_POST['technical_skills']);
    $cgpa = trim($_POST['cgpa']);
    $internships_projects = trim($_POST['internships_projects']);
    $aptitude_logical_reasoning = trim($_POST['aptitude_logical_reasoning']);
    $soft_skills = trim($_POST['soft_skills']);

    // Base query
    $query = "SELECT * FROM users WHERE 1=1";
    $params = [];

    if (!empty($certifications)) {
        $query .= " AND certifications LIKE :certifications";
        $params[':certifications'] = "%$certifications%";
    }

    if (!empty($technical_skills)) {
        $query .= " AND technical_skills LIKE :technical_skills";
        $params[':technical_skills'] = "%$technical_skills%";
    }

    if (!empty($cgpa)) {
        $query .= " AND cgpa >= :cgpa";
        $params[':cgpa'] = $cgpa;
    }

    if (!empty($internships_projects)) {
        $query .= " AND internships_projects LIKE :internships_projects";
        $params[':internships_projects'] = "%$internships_projects%";
    }

    if (!empty($aptitude_logical_reasoning)) {
        $query .= " AND aptitude_logical_reasoning = :aptitude";
        $params[':aptitude'] = $aptitude_logical_reasoning;
    }

    if (!empty($soft_skills)) {
        $query .= " AND soft_skills LIKE :soft_skills";
        $params[':soft_skills'] = "%$soft_skills%";
    }

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placement Officer Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .hero {
            background: linear-gradient(135deg, #8e44ad, #2575fc);
            color: white;
            text-align: center;
            padding: 40px 20px;
            border-radius: 10px;
        }
        .search-form {
            background: white;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            display: grid;
            gap: 15px;
        }
        .search-form input, .search-form select {
            padding: 12px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .search-form button {
            background: #8e44ad;
            color: white;
            padding: 12px;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: 0.3s;
            border-radius: 5px;
        }
        .search-form button:hover {
            background: #8e44ad;
        }
        .students-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        .students-table th, .students-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .students-table th {
            background: #8e44ad;
            color: white;
        }
        .students-table tr:hover {
            background: #f5f5f5;
        }
        @media (max-width: 768px) {
            .search-form {
                display: flex;
                flex-direction: column;
            }
            .students-table th, .students-table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <section class="hero">
        <h1>Welcome to the Placement Officer Dashboard</h1>
        <p>Search and manage student profiles for campus placements</p>
    </section>

    <section class="search-form">
        <h3>Search Students</h3>
        <form action="" method="POST">
            <input type="text" name="certifications" placeholder="Certifications (e.g., AWS, Python)">
            <input type="text" name="technical_skills" placeholder="Technical Skills (e.g., Java, React)">
            <select name="cgpa">
                <option value="">Select CGPA</option>
                <option value="10">10</option>
                <option value="9">9+</option>
                <option value="8">8+</option>
                <option value="7">7+</option>
                <option value="6">6+</option>
            </select>
            <input type="text" name="internships_projects" placeholder="Internships/Projects">
            
            <select name="aptitude_logical_reasoning">
                <option value="">Select Aptitude Level</option>
                <option value="Beginner">Beginner</option>
                <option value="Intermediate">Intermediate</option>
                <option value="Advanced">Advanced</option>
            </select>

            <input type="text" name="soft_skills" placeholder="Soft Skills (e.g., Communication, Leadership)">
            
            <button type="submit" name="search">Search</button>
        </form>
    </section>

    <table class="students-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Certifications</th>
                <th>Technical Skills</th>
                <th>CGPA</th>
                <th>Internships/Projects</th>
                <th>Aptitude</th>
                <th>Soft Skills</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($students)): ?>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['name']) ?></td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                        <td><?= htmlspecialchars($student['certifications']) ?></td>
                        <td><?= htmlspecialchars($student['technical_skills']) ?></td>
                        <td><?= htmlspecialchars($student['cgpa']) ?></td>
                        <td><?= htmlspecialchars($student['internships_projects']) ?></td>
                        <td><?= htmlspecialchars($student['aptitude_logical_reasoning']) ?></td>
                        <td><?= htmlspecialchars($student['soft_skills']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8">No results found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
