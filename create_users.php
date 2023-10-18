<?php

// Connect to the MySQL database
$db = new PDO('mysql:host=localhost;dbname=news', 'root', '');

// Get the JSON data from the POST request
$json = file_get_contents('php://input');

// Decode the JSON data into a PHP array
$data = json_decode($json, true);

// Validate the data
if ( !isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
    // Return an error response
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Invalid JSON data'
    ]);
    exit();
}

// Generate a salt for password hashing
$salt = bin2hex(random_bytes(32));

// Hash the password with the salt
$hashedPassword = hash('sha256', $data['password'] . $salt);

// Start a database transaction
$db->beginTransaction();

try {
    // Insert user information into the 'users' table
    $sql = 'INSERT INTO users ( username, email, dateCreated) VALUES ( :username, :email, CURRENT_TIMESTAMP)';
    $stmt = $db->prepare($sql);

    // Bind the values to the SQL statement
    // $stmt->bindParam(':firstName', $data['firstName']);
    // $stmt->bindParam(':lastName', $data['lastName']);
    $stmt->bindParam(':username', $data['username']);
    $stmt->bindParam(':email', $data['email']);


    // Execute the SQL query
    $stmt->execute();

    // Get the last inserted user ID
    $lastInsertedUserId = $db->lastInsertId();

    // Insert user credentials into the 'userCredentials' table
    $sql = 'INSERT INTO usercredentials (userID, saltedPassword, salt, dateCreated) VALUES (:userID, :saltedPassword, :salt, CURRENT_TIMESTAMP)';
    $stmt = $db->prepare($sql);

    // Bind the values to the SQL statement
    $stmt->bindParam(':userID', $lastInsertedUserId);
    $stmt->bindParam(':saltedPassword', $hashedPassword);
    $stmt->bindParam(':salt', $salt);

    // Execute the SQL query
    $stmt->execute();

    // Commit the transaction
    $db->commit();

    // Return a success response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'userID' => $lastInsertedUserId
    ]);
} catch (PDOException $e) {
    // Rollback the transaction on error
    $db->rollBack();

    // Return an error response
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

?>
