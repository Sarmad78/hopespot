<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming you have form fields like 'name' and 'email'
   
    $email = $_POST["email"];
    $comment = $_POST["comment"];

    // Your other form processing logic here

    // Send email notification
    $to = "sarmadsindhi352@gmail.com"; // Replace with the actual email address where you want to receive notifications
    $subject = "New Form Submission";
    $message = "Comment: $comment\nEmail: $email\n"; // Customize this message as per your form fields

    // Additional headers
    $headers =   $email;  

    // Send the email
    mail($to, $subject, $message, $headers);

 
    echo "<script>alert('message Send Successfully'); window.location.href = 'home.html';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Form</title>
</head>
<body>
    <!-- Your HTML form goes here -->
    <form method="post" action="">
        <!-- Your form fields go here -->
        <label for="name">Name:</label>
        <input type="text" name="name" id="name">

        <label for="email">Email:</label>
        <input type="email" name="email" id="email">

        <input type="submit" value="Submit">
    </form>
</body>
</html>
