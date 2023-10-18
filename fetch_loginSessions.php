<?php
// Include your database connection code here
$db = new PDO('mysql:host=localhost;dbname=news', 'root', '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // Get the user ID from the client (ensure you have this data)
   $userId = $_POST['userID'];

   // Query the database to retrieve the login count and timestamps for the specified user
   $sql = "SELECT COUNT(*) AS loginCount, GROUP_CONCAT(dateCreated ORDER BY dateCreated DESC) AS loginTimestamps
           FROM loginSessions
           WHERE userID = :userId";

   $stmt = $db->prepare($sql);
   $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
   $stmt->execute();

   $result = $stmt->fetch(PDO::FETCH_ASSOC);

   if ($result) {
       $loginCount = $result['loginCount'];
       $loginTimestamps = $result['loginTimestamps'];

       $loginTimestampsArray = explode(',', $loginTimestamps);

       echo json_encode(array("loginCount" => $loginCount, "loginTimestamps" => $loginTimestampsArray));
   } else {
       echo json_encode(array("message" => "Error querying the database"));
   }
} else {
   echo json_encode(array("message" => "Invalid request method. Use POST to retrieve login count."));
}
?>
