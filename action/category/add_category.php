<?php
include "../../db/dbConnection.php";

if (!empty($_POST['name'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);

    // Check if category already exists
    $checkQuery = "SELECT id FROM category WHERE name = '$name' AND status = 'Active'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "Category already exists.";
    } else {
        // Proceed to insert
        $query = "INSERT INTO category (name) VALUES ('$name')";
        if (mysqli_query($conn, $query)) {
            echo "New category added successfully.";
        } else {
            echo "Failed to add category.";
        }
    }
} else {
    echo "Category name is required.";
}
?>
