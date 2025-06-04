<?php
session_start();
include '../components/connect.php'; // Database connection

// **Fetch Student Performance Analysis**
$query = $conn->prepare("
    SELECT sa.student_id, u.name, u.email, sa.recommendation, sa.analysis_date
    FROM student_analysis sa
    JOIN users u ON sa.student_id = u.id
    ORDER BY sa.analysis_date DESC
");
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);

// Prepare Data for Chart
$data = ["High Performer" => 0, "Average Performer" => 0, "Needs Improvement" => 0];

foreach ($results as $row) {
    if (isset($data[$row['recommendation']])) {
        $data[$row['recommendation']]++;
    }
}

// Convert PHP data to JSON for JavaScript
$labels_json = json_encode(array_keys($data));
$data_json = json_encode(array_values($data));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📊 Mentor - Student Performance Analysis</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- jQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <style>
        /* Dark Mode */
        body.dark {
            background-color: #222;
            color: white;
        }

        .dark .container {
            background-color: #333;
            color: white;
        }

        .dark table {
            background-color: #444;
            color: white;
        }

        /* Chart Container */
        .chart-card {
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .chart-container {
            width: 40%; /* Reduced size */
            max-width: 300px; /* Set max size */
            margin: auto;
        }

        /* Performance Badge */
        .badge-high { background-color: #2ecc71; }
        .badge-average { background-color: #f1c40f; }
        .badge-needs { background-color: #e74c3c; }

        /* Dark Mode Toggle */
        .dark-mode-toggle {
            position: fixed;
            top: 20px;
            right: 30px;
            cursor: pointer;
            font-size: 22px;
            background: #f1c40f;
            padding: 10px;
            border-radius: 50%;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s;
        }

        .dark-mode-toggle:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="bg-light">

    <!-- Dark Mode Toggle -->
    <div class="dark-mode-toggle" onclick="toggleDarkMode()">🌙</div>

    <div class="container mt-5">
        <h2 class="text-center text-primary mb-4">📊 Student Performance Analysis</h2>

        <!-- Chart Section -->
        <div class="chart-card mb-4">
            <h5>Overall Performance Distribution</h5>
            <div class="chart-container">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
        <!-- Table Section -->
        <div class="card shadow-lg p-4">
            <h5 class="mb-3">Student Performance Details</h5>
            <table id="performanceTable" class="table table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Performance</th>
                        <th>Analysis Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['student_id']); ?></td>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['email']); ?></td>
                            <td>
                                <span class="badge 
                                    <?= ($row['recommendation'] === 'High Performer') ? 'badge-high' : 
                                       (($row['recommendation'] === 'Average Performer') ? 'badge-average' : 'badge-needs') ?>">
                                    <?= htmlspecialchars($row['recommendation']); ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['analysis_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Parse PHP JSON data into JavaScript variables
        const labels = <?= $labels_json; ?>;
        const data = <?= $data_json; ?>;
        
        // Chart.js Configuration (Reduced Doughnut Size)
        const ctx = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut', // Change to 'bar' or 'pie' if needed
            data: {
                labels: labels,
                datasets: [{
                    label: 'Student Performance',
                    data: data,
                    backgroundColor: ["#2ecc71", "#f1c40f", "#e74c3c"],
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Allow custom sizing
                aspectRatio: 1.5, // Reduced aspect ratio
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Initialize DataTable (Search, Sorting, Pagination)
        $(document).ready(function() {
            $('#performanceTable').DataTable();
        });

        // Dark Mode Toggle Function
        function toggleDarkMode() {
            document.body.classList.toggle("dark");
            let toggleIcon = document.querySelector(".dark-mode-toggle");
            toggleIcon.innerHTML = document.body.classList.contains("dark") ? "☀️" : "🌙";
        }
    </script>

</body>
</html>
