<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$name = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : '';
$fname = isset($_POST['fname']) ? mysqli_real_escape_string($conn, $_POST['fname']) : '';
$age = isset($_POST['age']) ? mysqli_real_escape_string($conn, $_POST['age']) : '';
$phonenumber = isset($_POST['phonenumber']) ? mysqli_real_escape_string($conn, $_POST['phonenumber']) : '';
$moredetail = isset($_POST['moredetail']) ? mysqli_real_escape_string($conn, $_POST['moredetail']) : '';

$target_dir = __DIR__ . "/static/recognize/";

if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$original_name = basename($_FILES["fileToUpload"]["name"]);
$name_without_ext = pathinfo($original_name, PATHINFO_FILENAME);
$imageFileType = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

// ✅ Hamesha .jpg extension use karo
$final_filename = $name_without_ext . '.jpg';
$target_file = $target_dir . $final_filename;

$uploadOk = 1;

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
    $tmp_file = $_FILES["fileToUpload"]["tmp_name"];

    // ✅ GD library se image ko JPG mein convert karo
    $converted = false;
    if ($imageFileType == 'jpeg' || $imageFileType == 'jpg') {
        $src = imagecreatefromjpeg($tmp_file);
        if ($src) {
            imagejpeg($src, $target_file, 95);
            imagedestroy($src);
            $converted = true;
        }
    } elseif ($imageFileType == 'png') {
        $src = imagecreatefrompng($tmp_file);
        if ($src) {
            // PNG transparency white background
            $bg = imagecreatetruecolor(imagesx($src), imagesy($src));
            imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
            imagecopy($bg, $src, 0, 0, 0, 0, imagesx($src), imagesy($src));
            imagejpeg($bg, $target_file, 95);
            imagedestroy($src);
            imagedestroy($bg);
            $converted = true;
        }
    } elseif ($imageFileType == 'gif') {
        $src = imagecreatefromgif($tmp_file);
        if ($src) {
            imagejpeg($src, $target_file, 95);
            imagedestroy($src);
            $converted = true;
        }
    } elseif ($imageFileType == 'webp') {
        $src = imagecreatefromwebp($tmp_file);
        if ($src) {
            imagejpeg($src, $target_file, 95);
            imagedestroy($src);
            $converted = true;
        }
    }

    // Agar convert nahi hua to directly move karo
    if (!$converted) {
        move_uploaded_file($tmp_file, $target_file);
    }

    if (file_exists($target_file)) {
        $sql = "INSERT INTO missing (name, fname, age, phonenumber, moredetail, filename, user_id) 
                VALUES ('$name', '$fname', '$age', '$phonenumber', '$moredetail', '$final_filename', '$user_id')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Data Successfully Added'); window.location.href = 'newsfeed.php';</script>";
        } else {
            echo "Database Error: " . mysqli_error($conn);
        }
    } else {
        echo "File upload mein problem aayi.";
    }
}

mysqli_close($conn);
?>