<?php
// Include your database connection code here
$db = new PDO('mysql:host=localhost;dbname=news', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // Parse JSON request body
   $data = json_decode(file_get_contents('php://input'), true);

   // Validate and sanitize input data
   $articleID = intval($data['ArticleID']);

   // Delete the news article
   $sql = "DELETE FROM articles WHERE ArticleID = :articleID";
   $stmt = $db->prepare($sql);

   // Bind the parameter
   $stmt->bindParam(':articleID', $articleID, PDO::PARAM_INT);

   try {
       $stmt->execute();
       echo json_encode(array("message" => "News article deleted successfully"));
   } catch (PDOException $e) {
       echo json_encode(array("message" => "Error deleting news article: " . $e->getMessage()));
   }
} else {
   echo json_encode(array("message" => "Invalid request method. Use POST to delete news articles."));
}
?>
