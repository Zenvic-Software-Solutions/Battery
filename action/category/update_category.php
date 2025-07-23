<?php
include "../../db/dbConnection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);

    // Check if the name already exists in another row
    $checkQuery = "SELECT id FROM category WHERE name = '$name' AND id != '$id' AND status = 'Active'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "Category name already exists.";
    } else {
        // Proceed with the update
        $sql = "UPDATE category SET name = '$name' WHERE id = '$id'";
        if (mysqli_query($conn, $sql)) {
            echo "Category updated successfully.";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>
