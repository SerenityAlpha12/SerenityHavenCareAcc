<?php
session_start();

// Database connection details
$host = 'localhost'; 
$dbname = 'serenity_alpha'; 
$username = 'root'; 
$password = ''; 

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch tasks assigned to the logged-in carer with pagination
try {
    // Assume $carerId is the ID of the logged-in carer
    $carerId = $_SESSION['carer_id']; // Retrieve carer ID from session or wherever it's stored
    
    // Pagination parameters
    $limit = 10; // Number of tasks per page
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number
    $offset = ($currentPage - 1) * $limit; // Offset for pagination
    
    // Fetch tasks for the logged-in carer with pagination
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE carer_id = :carerId LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':carerId', $carerId, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Count total number of tasks for pagination
    $stmtCount = $pdo->prepare("SELECT COUNT(*) AS total FROM tasks WHERE carer_id = :carerId");
    $stmtCount->bindParam(':carerId', $carerId, PDO::PARAM_INT);
    $stmtCount->execute();
    $totalTasks = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Calculate total pages for pagination
    $totalPages = ceil($totalTasks / $limit);
} catch (PDOException $e) {
    die("Error fetching tasks: " . $e->getMessage());
}

// Display tasks
foreach ($tasks as $task) {
    echo "Task ID: {$task['task_id']}<br>";
    echo "Description: {$task['description']}<br>";
    echo "Status: {$task['status']}<br>";
    echo "<a href='edit_task.php?id={$task['task_id']}'>Edit</a>";
    echo " | ";
    echo "<a href='delete_task.php?id={$task['task_id']}' onclick='return confirm(\"Are you sure you want to delete this task?\")'>Delete</a>";
    echo "<hr>";
}

// Pagination links
if ($totalPages > 1) {
    for ($i = 1; $i <= $totalPages; $i++) {
        echo "<a href='?page=$i'>$i</a> ";
    }
}
?>
