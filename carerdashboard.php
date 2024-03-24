<?php
session_start();

require_once 'vendor/autoload.php';
require 'db/configuration.php';

// If the carer is not logged in redirect to carerlogin.php
if (!isset($_SESSION['carer_id'])) {
    header("Location: carerlogin.php");
    exit();
}

// Connect to the database
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission to update carer's information
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    updateCarerInformation($conn, $_SESSION['carer_id']);
}

// Fetch carer data from the database
$carerData = fetchCarerData($conn, $_SESSION['carer_id']);

// Load and display the carer dashboard template
$template = $twig->load('carerdashboard.html');
echo $template->render([
    'carerName' => $carerData['name'],
    'carerEmail' => $carerData['email'],
    
]);

// Function to update carer's information
function updateCarerInformation($conn, $carerId) {
    $carerName = $_POST['carer_name'];
    $carerEmail = $_POST['carer_email'];
    $carerPassword = $_POST['carer_password']; // Remember to hash the password
    
    try {
        // Update carer's information in the database
        $updateQuery = "UPDATE carers SET name = :name, email = :email, password = :password WHERE id = :id";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bindParam(':name', $carerName);
        $stmt->bindParam(':email', $carerEmail);
        $stmt->bindParam(':password', $carerPassword);
        $stmt->bindParam(':id', $carerId);
        $stmt->execute();
        
        // Update carer's name in the session
        $_SESSION['carer_name'] = $carerName;
        
        // Redirect back to the carer dashboard after successful update
        header("Location: carerdashboard.php");
        exit();
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}

// Function to fetch carer data from the database
function fetchCarerData($conn, $carerId) {
    $sql = "SELECT name, email FROM carers WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $carerId);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
