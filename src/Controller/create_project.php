<?php
include '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_name = $_POST['project_name'];
    $project_description = $_POST['project_description'];
    $created_by = $_POST['created_by'];
    $assigned_to = $_POST['assigned_to'];

    $sql = "INSERT INTO projects (project_name, description, created_by, assigned_to, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $project_name, $project_description, $created_by, $assigned_to);

    if ($stmt->execute()) {
        echo "New project created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>