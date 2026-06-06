<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

$message = array(); // Initialize $message array

if (isset($_POST['submit'])) {
    // Escaping user inputs to prevent SQL injection
    $name = mysqli_real_escape_string($conn, $_POST['username']); 
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, md5($_POST['password'])); // Consider using a stronger hashing algorithm than md5
    $cpass = mysqli_real_escape_string($conn, md5($_POST['confirmpassword'])); // Change $_POST['cpassword'] to $_POST['confirmpassword']
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;
    
   
    $select = mysqli_query($conn, "SELECT * FROM `user` WHERE email='$email'");
    if (!$select) {
        die('Query failed: ' . mysqli_error($conn));
    }
    
    if (mysqli_num_rows($select) > 0) {
        $message[] = 'User already exists';
    } else {
        if ($pass != $cpass) {
            $message[] = 'Confirm password not matched';
        } elseif ($image_size > 20000) {
            $message[] = 'Image size should be less than 20KB';
        } else {
            // Execute query to insert user data into database
            $insert = mysqli_query($conn, "INSERT INTO `user` (name, email, password, image) VALUES ('$name', '$email','$pass','$image')");
            if (!$insert) {
                die('Insert query failed: ' . mysqli_error($conn));
            }
            
            // Move uploaded image to destination folder
            move_uploaded_file($image_tmp_name, $image_folder);
            
            // Redirect to login page after successful registration
            header('Location: login.php');
            exit;
        }
    }
}       
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <title>Document</title>
</head>
<style>
  body {
    background-color: #F1F7FE;
  }

  .brand {
    margin-top: 25%;
    text-align: center;
    color: #1e319d;

  }

  .form-group {
    margin-top: 10%;
    border: none;
    border-radius: 10%;
    box-shadow: gainsboro;
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

  .form-group label {
    margin-bottom: 5%;
  }

  .icon {
    margin-left: 24%;
    display: FLEX;
    margin-top: 5%;
  }

  .subicon {
    background-color: white;
    box-shadow: 0px 3px 3px 2px #00000024;
    border-radius: 10%;
    margin-left: 10%;
  }

  .social {
    text-align: center;
    margin-top: 5%;
  }

  .signup {
    margin-top: 15%;
  }
</style>

<body>
  <div class="container-fluid">
    <div class="brand">
      <h1>HopeSpot</h1>
    </div>
    <form method="post" enctype="multipart/form-data">
    <?php
    if(isset($message)){
        foreach($message as $msg){
            echo '<div class="message">'.$msg.'</div>';
        }
    }?>
      <div class="form-group">
        <label for="exampleInputName">Signup Account</label>
        <input type="text" class="form-control" id="exampleInputName" aria-describedby="nameHelp"
          placeholder="User Name" name="username" required>

      </div>
      <div class="form-group">

        <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
          placeholder="Enter email" name="email" required>

      </div>
      <div class="form-group">
        <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name="password"
          required>
      </div>

      <div class="form-group">
        <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Confirm Password"
          name="confirmpassword" required>
      </div>

      <button type="submit" name ="submit" class="btn btn-primary"> Signup </button>
    </form>
    <div class="social">
      <span>
        or Signup with
      </span>
      <div class="icon">
        <div class="subicon">
          <img src="/FYP/images/icons8-google-48.png" alt="">
        </div>
        <div class="subicon">
          <img src="/FYP/images/icons8-facebook-48.png" alt="">
        </div>

      </div>
      <div class="signup">
        <span>
          have an account? <a href="login.php">login</a>
        </span>

      </div>
    </div>
  </div>
</body>

</html>
