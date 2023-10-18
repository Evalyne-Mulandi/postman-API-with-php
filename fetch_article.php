<?php
// Include your database connection code here
$db = new PDO('mysql:host=localhost;dbname=news', 'root', '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // Parse JSON request body
   $data = json_decode(file_get_contents('php://input'), true);

   // Validate and sanitize input data
   $category = htmlspecialchars($data['category']);

   // Prepare a SQL statement to fetch articles by category
   $sql = "SELECT * FROM articles WHERE category = :category";
   $stmt = $db->prepare($sql);

   // Bind the parameter
   $stmt->bindParam(':category', $category, PDO::PARAM_STR);

   // Execute the query
   $stmt->execute();

   // Fetch and store the results in an array
   $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

   echo json_encode($articles);
} else {
   echo json_encode(array("message" => "Invalid request method. Use POST to fetch articles by category."));
}
?>
