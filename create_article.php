<?php

// Connect to the MySQL database
$db = new PDO('mysql:host=localhost;dbname=news', 'root', '');

// Get the JSON data from the POST request
$json = file_get_contents('php://input');

// Decode the JSON data into a PHP array
$data = json_decode($json, true);

// Validate the data
if (!isset($data['author']) || !isset($data['title']) || !isset($data['description']) || !isset($data['url']) || !isset($data['imageUrl']) || !isset($data['category']) || !isset($data['content'])) {
    // Return an error response
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Invalid JSON data'
    ]);
    exit();
}

// Generate a salt for password hashing
$salt = bin2hex(random_bytes(32));


// Start a database transaction
$db->beginTransaction();

try {
    // Insert user information into the 'users' table
    $sql = 'INSERT INTO articles (author, title, description, url, imageUrl, category,content, dateCreated) VALUES (:author, :title, :description, :url, :imageUrl, :category, :content, CURRENT_TIMESTAMP)';
    $stmt = $db->prepare($sql);

    // Bind the values to the SQL statement
    $stmt->bindParam(':author', $data['author']);
    $stmt->bindParam(':title', $data['title']);
    $stmt->bindParam(':description', $data['description']);
    $stmt->bindParam(':url', $data['url']);
    $stmt->bindParam(':imageUrl', $data['url']);
    $stmt->bindParam(':category', $data['category']);
    $stmt->bindParam(':content', $data['content']);

    // Execute the SQL query
    $stmt->execute();

    // Get the last inserted user ID
    $lastInsertedUserId = $db->lastInsertId();



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
