<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
 
</head>
 

<style>
  body {
    background-color: #F1F7FE;
    padding: 0;
    margin: 0;

  }

  .header {
    background-color: #1e319d;
    color: white;
    border-radius: 0px 0px 8px 8px;
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

  .search {
    box-shadow: 0px 0px 8px 0px #00000052;
    margin-top: 5%;
    border-radius: 7px;
    background-color: white;
  }

  #box {
    background-color: white;
    border-radius: 6px 6px 6px 6px;
    margin-top: 15px;
    width: 45%;
    margin: 8px;
    box-shadow: 0px 0px 8px 0px #00000052;
    text-align: center;

  }


  /* Slideshow container */
  .slideshow-container {
    box-sizing: border-box;
    max-width: 1000px;
    position: relative;
    margin: auto;
  }


  /* Hide the images by default */
  #slide2,
  #slide3 {
    display: none;
  }

 
  .missing-person-container {
    display: flex;
    justify-content: center; /* Center the items horizontally */
    align-items: flex-start; /* Align items to the top vertically */
    flex-wrap: wrap; /* Allow items to wrap to the next line if needed */
    margin-bottom: 20%;
}

.missing-person {
    text-align: left; /* Align text to the left */
    margin: 0 10px; /* Add some margin between items */
}
 
.missing-person-container {
    display: flex;  
    text-align: center;
    /* margin-right: 20px;   */
}

.missing-person-container img {
    max-width: 100px;  
    max-height: 100px;  
    /* margin-bottom: 10px;   */
}
.missing-person p {
    margin-bottom: 5px; /* Adjust this value as needed */
    font-weight: 500;
}
.missing-person img {
    width: 100px; /* Adjust the width as needed */
    height: 100px; /* Adjust the height as needed */
    object-fit:cover; /* Ensures the aspect ratio is maintained */
}


  /* Next & previous buttons */
  .prev,
  .next {
    cursor: pointer;
    position: absolute;
    top: 50%;
    width: auto;
    margin-top: -22px;
    padding: 16px;
    color: white;
    font-weight: bold;
    font-size: 18px;
    border-radius: 0 3px 3px 0;
    user-select: none;
  }

  /* Position the "next button" to the right */
  .next {
    right: 0;
    border-radius: 3px 0 0 3px;
  }

  /* On hover, add a black background color with a little bit see-through */
  .prev:hover,
  .next:hover {
    background-color: rgba(0, 0, 0, 0.8);
  }

  /* Caption text */
  .text {
    color: #f2f2f2;
    font-size: 15px;
    padding: 8px 12px;
    position: absolute;
    bottom: 8px;
    width: 100%;
    text-align: center;
  }

  /* Number text (1/3 etc) */
  .numbertext {
    color: #f2f2f2;
    font-size: 12px;
    padding: 8px 12px;
    position: absolute;
    top: 0;
  }

  /* The dots/bullets/indicators */
  .dot {
    cursor: pointer;
    height: 15px;
    width: 15px;
    margin: 0 2px;
    background-color: #ffffff;
    border-radius: 50%;
    display: inline-block;
    transition: background-color 0.6s ease;
    margin-top: -22px;
  }

  .active,
  .dot:hover {
    background-color: #717171;
  }

  #footer1 {
    bottom: 0%;
    position: fixed;
    width: 100%;
    background: #F1F7FE;
  }

  /* Style the footer container */
  .footer {
    display: flex;
    box-shadow: 0px 0px 4px 0px;
    /* Change the background color */
    justify-content: space-around;
    padding: 10px 0;
  }
</style>

<body>
<script src="http://localhost/FYP/js/script.js"></script>

<div id="header-container"></div>
  <div class="container-fluid">
    <div class="search">
      <div class="row">
        <div class="col-3">


          <img src="/FYP/images/search.png" alt="" style="margin-top: 16%;">
        </div>
        <div class="col-9">
          <input type="search" placeholder="Search..." value="Search" style="border: none; margin-bottom: 3%; 
               margin-top: 3%;     margin-left: -20%;
               font-size: 20px;">
        </div>
      </div>
    </div>
    <div class="row" style=" margin-top: 5%;
        margin-bottom: 5%;">
      <div class="col-6" id="box" style="background-color: white;">
        <a href="/FYP/missingperson.html" style="text-decoration: none;
        font-weight: bold;
        color: black;">
          <img src="/FYP/images/group9.png" alt="" style="margin-top: 5%;">
          <p>Missing Person</p>
        </a>
      </div>
      <div class="col-6" id="box" style="background-color: white;">
        <a href="http://localhost:5000/foundperson" style="text-decoration: none; font-weight: bold; color: black;">
          <img src="/FYP/images/clientmanagement.png" alt="" style="margin-top: 5%;">
          <p>Found Person</p>
        </a>
      </div>
    </div>
    <!--Slider Staring Here-->

    <div class="slideshow-container">


      <div class="mySlides " id="slide1">
        <img src="/FYP/images/Slider Imagge.png" style="width:100%">


        <div class="mySlides " id="slide2">
          <img src="/FYP/images/Slider Imagge.png" style="width:100%">

        </div>

        <div class="mySlides " id="slide3">
          <img src="/FYP/images/Slider Imagge.png" style="width:100%">

        </div>



      </div>
      <div style="text-align:center; margin-top: -7%;">
        <span class="dot" onclick="currentSlide1()"></span>
        <span class="dot" onclick="currentSlide1()"></span>
        <span class="dot" onclick="currentSlide1()"></span>
      </div>
    </div>
    <!--slider ending here-->
    <div class="row" style="margin-top: 9%; font-weight:bold;">
  <div class="col-6">
    <p> Missing Person</p>
  </div>
  <div class="col-6">
    <!-- Modify the link to point to the new PHP file -->
    <a href="more.php" style="margin-left: 37%;"> See More </a>
  </div>
</div>
<!-- Your HTML code -->

<div class="row missing-person-container">
    <?php
    // Database connection parameters
    $db_host = 'localhost';
    $db_user = 'root';
    $db_password = ''; // Empty string for password
    $db_name = 'hopespot'; // Replace with your database name

    // Create connection
    $conn = new mysqli($db_host, $db_user, $db_password, $db_name);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL query to fetch only the 3 most recent entries from the database
    $sql = "SELECT name, fname, age, filename FROM missing ORDER BY id DESC LIMIT 3";

    // Execute the query
    $result = $conn->query($sql);

    // Check if any rows were returned
    if ($result->num_rows > 0) {
        // Output data of each row
        echo '<table>'; // Start table
        echo '<tr>'; // Start first row for images
        while ($row = $result->fetch_assoc()) {
            echo '<td>';
            echo '<div class="col-md-4">';
            echo '<div class="missing-person">';
            echo '<img src="/FYP/static/recognize/' . basename($row["filename"]) . '" alt="Image" class="img-fluid">';
            echo '</div>';
            echo '</div>';
            echo '</td>';
        }
        echo '</tr>'; // End first row
        echo '<tr>'; // Start second row for text
        // Reset pointer to the beginning of the result set
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            echo '<td>';
            echo '<div class="col-md-4">';
            echo '<div class="missing-person">';
            echo '<p> '   . $row["name"] . '</p>';
            echo '<p>  ' . $row["fname"] . '</p>';
            echo '<p>  ' . $row["age"] . '</p>';
            echo '</div>';
            echo '</div>';
            echo '</td>';
        }
        echo '</tr>'; // End second row
        echo '</table>'; // End table
    } else {
        echo "0 results";
    }

    // Close the connection
    $conn->close();
    ?>
</div>


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
