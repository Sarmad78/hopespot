<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hopespot";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
$fname = isset($_POST['fname']) ? $conn->real_escape_string($_POST['fname']) : '';
$age = isset($_POST['age']) ? $conn->real_escape_string($_POST['age']) : '';
$phonenumber = isset($_POST['phonenumber']) ? $conn->real_escape_string($_POST['phonenumber']) : '';
$moredetail = isset($_POST['moredetail']) ? $conn->real_escape_string($_POST['moredetail']) : '';

// ✅ FIX: Hardcoded C:/xampp path hata diya — ab relative path use hoga
$target_dir = __DIR__ . "/static/recognize/";

// Agar folder nahi hai toh bana do
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$filename = basename($_FILES["fileToUpload"]["name"]);
$target_file = $target_dir . $filename;
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

if (isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check === false) {
        echo "File image nahi hai.";
        $uploadOk = 0;
    }
}

if ($uploadOk == 0) {
    echo "File upload nahi hua.";
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {

        // ✅ JPG conversion agar zaroorat ho (ImageMagick available ho to)
        if ($imageFileType !== 'jpg' && $imageFileType !== 'jpeg') {
            $newFilename = pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
            $newTargetFile = $target_dir . $newFilename;
            // exec() se convert karo sirf agar ImageMagick installed ho
            if (function_exists('exec')) {
                exec("magick convert \"$target_file\" \"$newTargetFile\"");
                if (file_exists($newTargetFile)) {
                    unlink($target_file);
                    $target_file = $newTargetFile;
                    $filename = $newFilename;
                }
            }
        }

        // DB mein sirf filename store karo, pura path nahi
        // Taake Flask bhi isse dhundh sake
        $imagePath = $filename;

        $sql = "INSERT INTO missing (name, fname, age, phonenumber, moredetail, filename) 
                VALUES ('$name', '$fname', '$age', '$phonenumber', '$moredetail', '$imagePath')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Data Successfully Added'); window.location.href = 'feedback.html';</script>";
        } else {
            echo "Database Error: " . $conn->error;
        }
    } else {
        echo "File upload mein problem aayi.";
    }
}

$conn->close();
?>
