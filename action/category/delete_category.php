<?php
include "../../db/dbConnection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id =$_POST['id'];


    $sql = "UPDATE category SET status='Inactive' WHERE id='$id'";
    if (mysqli_query($conn, $sql)) {
        echo "Category Deleted successfully";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
