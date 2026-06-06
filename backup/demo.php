<!DOCTYPE html>
<html>
<head>
    <title>Display Data</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        img {
            max-width: 100px;
            max-height: 100px;
        }
    </style>
</head>
<body>

<h2>Data from Database</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Image</th>
    </tr>
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

    // SQL query to fetch data from database
    $sql = "SELECT id, name,  filename FROM missing";
    $result = $conn->query($sql);

    // Check if there is data returned by the query
    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>".$row["id"]."</td>";
            echo "<td>".$row["name"]."</td>";
            $image_filename = basename($row["filename"]);
        
            // Display image using <img> tag
            echo "<td><img src='/FYP/static/recognize/".$image_filename."' alt='Image'></td>";
            echo "</tr>";
        }
    } else {
        echo "0 results";
    }

    // Close database connection
    $conn->close();
    ?>
</table>

</body>
</html>
