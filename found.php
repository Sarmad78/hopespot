<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hopespot";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from the form
$name = $_POST['name'];
$fname = $_POST['fname'];
$age = $_POST['age'];
$phonenumber = $_POST['phonenumber'];
$moredetail = $_POST['moredetail'];


// Insert data into the database
$sql = "INSERT INTO found (name, fname, age, phonenumber,moredetail) VALUES ('$name', '$fname', '$age', '$phonenumber','$moredetail')";

if ($conn->query($sql) === TRUE) {
    echo "Data Successfully Added";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
