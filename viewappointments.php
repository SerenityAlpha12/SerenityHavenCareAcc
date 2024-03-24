<?php
// Database connection details
$host = 'localhost'; 
$dbname = 'serenity_alpha'; 
$username = 'root'; 
$password = ''; 

try {
    // Attempt to connect to the database, if errors the pdo will throw exceptions
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected successfully";

    // Fetch appointments from the database
    $stmt = $pdo->query('SELECT * FROM appointments');
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if appointments were found
    if ($appointments) {
        echo '<div class="appointments-list">';
        echo '<h3>Appointments</h3>';
        echo '<ul>';
        foreach ($appointments as $appointment) {
            echo "<li>" . $appointment['appointment_title'] . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    } else {
        // Display a message if no appointments were found
        echo "No appointments found.";
    }

} catch (PDOException $e) {
    // Display an error message if connection or query fails
    die("Connection failed: " . $e->getMessage());
}
?>
