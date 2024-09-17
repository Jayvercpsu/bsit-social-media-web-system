<?php
// Check if session is already active before starting a new one
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('config.php');  // Ensure the database connection is properly set up

if (!isset($_SESSION['id'])) {
    die("Error: User not logged in.");
}

$user_id = $_SESSION['id'];

// Check if the connection is valid and not closed
if (!$conn || $conn->connect_errno) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the first SQL query
$sql = "SELECT * FROM fallowing WHERE User_Id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing the query: " . $conn->error);
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$ids = array();

while ($row = $result->fetch_array(MYSQLI_NUM)) {
    foreach ($row as $rows) {
        $ids[] = $rows;
    }
}

$stmt->close();  // Close the first prepared statement

if (empty($ids)) {
    $ids[] = $user_id;  // If no following users, include the current user ID
}

$fallowing_id = join(",", $ids);

// Prepare the second SQL query
$sql_query_two = "SELECT * FROM Users WHERE User_Id IN ($fallowing_id) AND USER_TYPE = '1' ORDER BY RAND() LIMIT 15";
$stmt_two = $conn->prepare($sql_query_two);

if ($stmt_two === false) {
    die("Error preparing the second query: " . $conn->error);
}

$stmt_two->execute();
$Clubs = $stmt_two->get_result();

// Process the results (e.g., display or use the data)

$stmt_two->close();  // Close the second prepared statement
$conn->close();  // Finally, close the database connection
?>
