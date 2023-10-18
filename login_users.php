<?php
// Establish a database connection using PDO
$db = new PDO('mysql:host=localhost;dbname=news', 'root', '');


$json = file_get_contents('php://input');

// Decode the JSON data into a PHP array
$data = json_decode($json, true);

// Validate the data
if (!isset($data['email']) || !isset($data['password'])) {
    // Return an error response
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Invalid JSON data'
    ]);
    exit();
}

// Get the login credentials from the client
$email = $data['email'];
$password = $data['password'];

// Prepare a SQL query to retrieve user's credentials
$sql = "SELECT users.userID, userCredentials.saltedPassword, userCredentials.salt
       FROM users
       INNER JOIN userCredentials ON users.userID = userCredentials.userID
       WHERE users.email = :email";

$stmt = $db->prepare($sql);
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
   $userID = $result['userID'];
   $saltedPasswordFromDatabase = $result['saltedPassword'];
   $saltFromDatabase = $result['salt'];

   // Validate the provided password
   $hashedPassword = hash('sha256', $password . $saltFromDatabase);

   if ($hashedPassword === $saltedPasswordFromDatabase) {


     error_log("password " . $hashedPassword);

     $insertQuery = "INSERT INTO loginSessions (userID, counter)
                     SELECT :userID, IFNULL(MAX(counter), 0) + 1
                     FROM loginSessions
                     WHERE userID = :userID";

     $stmt = $db->prepare($insertQuery);
     $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
     $stmt->execute();




       // Password is correct

       // Insert a new login session record

       // You should generate a new session token and handle user authentication here
       // For simplicity, we'll just provide a success message in this code.
       if ($stmt->rowCount() > 0) {
           $response = array(
               "status" => "success",
               "message" => "Login successful"
           );
           echo json_encode($response);
       } else {
           $response = array(
               "status" => "error",
               "message" => "Failed to insert login session record"
           );
           echo json_encode($response);
       }
       echo json_encode($response);
   } else {
       // Password is incorrect
       $response = array(
           "status" => "error",
           "message" => "Invalid password"
       );
       echo json_encode($response);
   }
} else {
   // Handle the case where the user was not found
   $response = array(
       "status" => "error",
       "message" => "Invalid email or password 2"
   );
   echo json_encode($response);
}
?>
