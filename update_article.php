<?php
// Include your database connection code here
$db = new PDO('mysql:host=localhost;dbname=news', 'root', '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // Parse JSON request body
   $data = json_decode(file_get_contents('php://input'), true);

   // Validate and sanitize input data
   $articleID = $data['ArticleID'];
   $title = $data['title'];
   $description = $data['description'];
   $url = $data['url'];
   $imageUrl = $data['imageUrl'];
   $category = $data['category'];
   $content = $data['content'];

   // Update the news article
   $sql = "UPDATE articles
           SET title = :title, description = :description, url = :url, imageUrl = :imageUrl, category = :category, content = :content, dateModified = CURRENT_TIMESTAMP
           WHERE ArticleID = :articleID";

   $stmt = $db->prepare($sql);

   // Bind the parameters
   $stmt->bindParam(':articleID', $articleID, PDO::PARAM_INT);
   $stmt->bindParam(':title', $title, PDO::PARAM_STR);
   $stmt->bindParam(':description', $description, PDO::PARAM_STR);
   $stmt->bindParam(':url', $url, PDO::PARAM_STR);
   $stmt->bindParam(':imageUrl', $imageUrl, PDO::PARAM_STR);
   $stmt->bindParam(':category', $category, PDO::PARAM_STR);
   $stmt->bindParam(':content', $content, PDO::PARAM_STR);

   try {
       $stmt->execute();
       echo json_encode(array("message" => "News article updated successfully"));
   } catch (PDOException $e) {
       echo json_encode(array("message" => "Error updating news article: " . $e->getMessage()));
   }
} else {
   echo json_encode(array("message" => "Invalid request method. Use POST to update news articles."));
}
?>
