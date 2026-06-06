<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Missing Persons Data</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="script.js"></script>
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

    .missing-persons-data {
        margin-top: 20px;
    }

    .missing-person {
        background-color: #fff;
        /* Example background color */
        border: 1px solid #ccc;
        padding: 10px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
         
    }

    .missing-person p {
        margin: 0;
        margin-left: 10px;
    }

    .missing-person img {
    width: 100px; /* Set the width of the image */
    height: 100px; /* Set the height of the image */
    object-fit: cover; /* Ensure the image fills the container without distortion */
    }

    .pagination {
         
        justify-content: center;
        width: 100%;
      

    }
 
    .brand{
        text-align: center;
        background: #4678ec91;
    }

    .pagination a {
        display: inline-block;
        padding: 5px 10px;
        margin: 0 5px;
        background-color: #f8f9fa;
        color: #1e319d;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        text-decoration: none;
    }

    .pagination a.active {
        background-color: #1e319d;
        color: #fff;
    }
</style>

<body>
<script src="http://localhost/FYP/js/script.js"></script>

<div id="header-container"></div>


    <div class="missing-persons-data">
        <div class="container">
            <div class="row" style="margin-bottom: 19%;">
            <div class="brand">
        <h4 style="line-height: 249%;">Missing Persons</h4>
    </div>
            <?php
// Database connection parameters
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'hopespot';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch data from the database
$sql = "SELECT name, fname, age, phonenumber, filename FROM missing";

// Execute the query
$result = $conn->query($sql);

// Check if any rows were returned
if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        // Output other information
    
        echo "<div style='line-height: 2px; margin-top: 8%;' class='col-md-6'>";
        echo "<p>Name: " . $row["name"]. "</p>";
        echo "<br>";
        echo "<p>Father's Name: " . $row["fname"]. "</p>";
        echo "<br>";
        echo "<p>Age: " . $row["age"]. "</p>";
        echo "<br>";
        echo "<p>Contact: " . $row["phonenumber"]. "</p>";
        echo "</div>";
        echo "<img src='/FYP/static/recognize/" . basename($row["filename"]) . "' alt='Image'>";
     
        // echo "<div class='missing-person'>";
        // echo "<img src='/FYP/static/recognize/" . basename($row["filename"]) . "' alt='Image'>";
        // echo "</div>";

    }
} else {
    echo "0 results";
}

// Close the connection
$conn->close();
?>
            </div>
            <?php
          // Display pagination links

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

                            <a class="nav-item nav-link " id="nav-home-tab" data-toggle="tab" href="/FYP/update_profile.php" role="tab"
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
