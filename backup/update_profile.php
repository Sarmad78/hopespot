<?php
include 'config.php';

$conn = mysqli_connect($host, $username, $password, $database) or die('Connection failed: ' . mysqli_connect_error());

session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$message = array(); // Define the message array

// Check if user is logged in
if (!$user_id) {
    header("Location: login.php");
    exit();
}

// Handle image upload separately
if(isset($_FILES['update_image'])) {
  // Your existing code for handling image upload...

  if(empty($message)) {
      move_uploaded_file($file_tmp, "uploaded_img/" . $file_name);
      // Update image path in the database
      $update_image_query = mysqli_query($conn, "UPDATE `user` SET image = '$file_name' WHERE id = '$user_id'");
      if (!$update_image_query) {
          die('Query failed: ' . mysqli_error($conn));
      }

      // Store the uploaded image filename in a session variable
      $_SESSION['profile_image'] = $file_name;

      // Display success message
      echo "<script>showPopup('Image has been uploaded successfully.');</script>";
  }
}
if(isset($_POST['update_profile'])) {
    $update_name = isset($_POST['update_name']) ? mysqli_real_escape_string($conn, $_POST['update_name']) : '';
    $update_email = isset($_POST['update_email']) ? mysqli_real_escape_string($conn, $_POST['update_email']) : '';
    $old_pass = isset($_POST['old_pass']) ? $_POST['old_pass'] : '';
    $update_pass = isset($_POST['update_pass']) ? mysqli_real_escape_string($conn, md5($_POST['update_pass'])) : '';
    $new_pass = isset($_POST['new_pass']) ? mysqli_real_escape_string($conn, md5($_POST['new_pass'])) : '';
    $confirm_pass = isset($_POST['confirm_pass']) ? mysqli_real_escape_string($conn, md5($_POST['confirm_pass'])) : '';

    // Update username and email
    $update_query = mysqli_query($conn, "UPDATE `user` SET name = '$update_name', email = '$update_email' WHERE id = '$user_id'");
    if (!$update_query) {
        die('Query failed: ' . mysqli_error($conn));
    }

    // Check if passwords are being updated
    if (!empty($update_pass) && !empty($new_pass) && !empty($confirm_pass)) {
        if ($update_pass != $old_pass) {
            $message[] = "Old password does not match";
        } elseif ($new_pass != $confirm_pass) {
            $message[] = "New password and confirm password do not match";
        } else {
            // Update password
            $pass_update_query = mysqli_query($conn, "UPDATE `user` SET password = '$confirm_pass' WHERE id = '$user_id'");
            if (!$pass_update_query) {
                die('Query failed: ' . mysqli_error($conn));
            }
            // Display success message
            echo "<script>showPopup('Password has been changed successfully.');</script>";
        }
    }

    // If there are any messages, display them as alerts
    foreach ($message as $msg) {
        echo "<script>showPopup('$msg');</script>";
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

    <style>
      
      body {
          background-color: #F1F7FE;
          margin: 0%;
          padding: 0%;
        }
      
        .brand {
          margin-top: 9%;
          text-align: center;
          color: #1e319d;
      
        }
      
        .row {
          width: 100%;
        }
      
    
  .form-group  {
    margin-top: 10%;
    border: none;
    border-radius: 10%;
    
  }
      
        .btn-primary {
          margin-left: 30%;
          margin-top: 10%;
          width: 40%;
          background-color: #1e319d;
          border: none;
        }
      
        .form-control {
          border: none;
          box-shadow: 0px 4px 4px 4px #00000014;
        }
        .form-upload input {
    background: white;
    border: none;
    border-radius: 3px;
    width: 100%;
    margin-top: 5%;
    box-shadow: 0px 4px 4px 4px #00000014;
  }

  #menu {
    width: 45%;
    margin-top: 10%;
    margin-left: 16%;
  }

  #text {
    text-align: center;
    margin-top: 4%;
  }

  #bell {
    width: 42%;
    margin-top: 10%;
    margin-left: 43%;

  }
      
        .header {
          background-color: #1e319d;
          color: white;
          border-radius: 0px 0px 10px 10px;
          padding-top: 2%;
          padding-bottom: 2%;
        }
        .profile{
          display: flex;
      }
   
      /* Styles for profile image */
    .profile {
        width: 150px; /* Adjust width and height as needed */
        height: 150px;
        border-radius: 50%; /* Make it a perfect circle */
        overflow: hidden; /* Hide overflowing content */
         
        margin: 0 auto; /* Center the profile image */
    }

    .profile img {
        width: 100%; /* Ensure image fills the container */
        height: auto; /* Maintain aspect ratio */
    }
        .form-upload input {
          background: white;
          border: none;
          border-radius: 3px;
          width: 100%;
          margin-top: 5%;
          box-shadow: 0px 4px 4px 4px #00000014;
        }
      
        #footer1 {
          bottom: 0%;
          position: fixed;
          width: 100%;
        }
      
        /* Style the footer container */
        .footer {
          display: flex;
          box-shadow: 0px 0px 4px 0px;
          /* Change the background color */
          justify-content: space-around;
          padding: 10px 0;
        }
        .profile{
          background-color: #F1F7FE;
        }
        #preview {
            max-width: 200px; /* Adjust maximum width as needed */
            max-height: 200px; /* Adjust maximum height as needed */
            margin-bottom: 15px; /* Add space below the image */
        }
        
    </style>
     <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function changeProfileImage() {
            document.getElementById('pic').click();
        }

        function showPopup(message) {
            alert(message);
        }
    </script>
</head>
<body>
<header>
    <div class="header">
      <div class="row" style="display:flex; width: 100%;">
        <div class="col-3">
          <img src="/FYP/images/icons8-menu-48.png" alt="" id="menu">
        </div>
        <div class="col-6">
          <h3 id="text"> HopeSpot</h3>
        </div>
        <div class="col-3">
          <img id="bell" src="/FYP/images/icons8-bell-48.png" alt="">
        </div>
      </div>

    </div>
  </header>
<div class="container">
        <?php
        $select = mysqli_query($conn, "SELECT * FROM `user` WHERE id='$user_id'");
        if (!$select) {
            die('Query failed: ' . mysqli_error($conn));
        }
        if (mysqli_num_rows($select) > 0) {
            $fetch = mysqli_fetch_assoc($select);
            if ($fetch['image'] == '') {
                echo "<div class='profile'>";
                echo '<img src="images/profile.png" id="preview" onclick="changeProfileImage()">';
                echo "</div>";
            } else {
                echo '<img src="uploaded_img/'.$fetch['image'].'" id="preview" onclick="changeProfileImage()">';
            }
           
            echo '<h3 style="text-align:center;">'.$fetch['name'].'</h3>';
           
        }
        ?>
        <form action="#" method="post" enctype="multipart/form-data" onsubmit="showPopup('Changes saved successfully.')">
    <div class="form-group">
    
        <input type="text" class="form-control" id="username" name="update_name" value="<?php echo $fetch['name']?>">
    </div>

    <div class="form-group">

        <input type="email" class="form-control" id="email" name="update_email" value="<?php echo $fetch['email']?>">
    </div>

    <div class="form-group">
        <input type="file" class="form-control" id="pic" name="update_image" accept="image/jpg, image/jpeg, image/png" onchange="previewImage(event)" style="display: none;">
    </div>

    <div class="form-group">
        <input type="hidden" class="form-control" id="oldpassword" name="old_pass" value="<?php echo $fetch['password']?>">
        
        <input type="password" class="form-control" id="oldpassword" name="update_pass" placeholder="Enter Old Password">
    </div>

    

    <div class="form-group">

        <input type="password" class="form-control" id="newpassword" name="new_pass" placeholder="Enter New Password">
    </div>

    <div class="form-group">
        
        <input type="password" class="form-control" id="confirmpassword" name="confirm_pass" placeholder="Confirm New Password">
    </div>

    <input type="submit" class="btn btn-primary" value="Submit" name="update_profile">
   
</form>
    </div>
    <footer>

<div id="footer1">

  <div class="row" id="row" style="width:107%; ">

    <div class="col-12" style="padding: 0%;">
      <div class="footer">
        <div class="footer-column">

          <a class="nav-item nav-link " href="/FYP/home.php">
            <img src="/FYP/images/home.png" alt="">
          </a>

        </div>
        <div class="footer-column">

          <a class="nav-item nav-link " href="/FYP/newsfeed.html">
            <img src="/FYP/images/news.png" alt="">
          </a>

        </div>
        <div class="footer-column">

          <a class="nav-item nav-link " href="/FYP/feedback.html">
            <img src="/FYP/images/comments.png" alt="">
          </a>

        </div>
        <div class="footer-column">

          <a class="nav-item nav-link " id="nav-home-tab" data-toggle="tab" href="update_profile.php" role="tab"
            aria-controls="nav-home" aria-selected="true">
            <img src="/FYP/images/maleuser.png" alt="">
          </a>


        </div>
      </div>
    </div>
  </div>
</div>
</footer>
</body>
</html>
