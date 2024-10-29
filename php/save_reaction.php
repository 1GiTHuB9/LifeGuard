<?php
require "./php/dbConnect.php"; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $calendar_id = $_POST['calendar_id'];
    $user_id = $_POST['user_id'];
    $reaction = $_POST['reaction'];
    $reaction_date = $_POST['reaction_date'];

    $sql = "INSERT INTO Calendars (calendar_id, user_id, reaction, reaction_date) VALUES (:calendar_id, :user_id, :reaction, :reaction_date)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':calendar_id', $calendar_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':reaction', $reaction, PDO::PARAM_STR);
    $stmt->bindParam(':reaction_date', $reaction_date, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo "Record saved successfully";
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }
}
?>
