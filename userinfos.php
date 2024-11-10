<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proj";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch user information from the database if the user is logged in
    if(isset($_SESSION['username'])) {
        $username = $_SESSION['username'];

        $sql = "SELECT regid, username, email, lname, fname, address, phone FROM sbf WHERE status = 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Redirect user to login page if not logged in
        header("Location: sign.php");
        exit();
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>List of Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
      
        table {
            width: 80%;
            margin-left: auto;
            margin-right: auto;
            border-collapse: collapse;
            margin-top: 60px;
        }
        h1 {
            margin-left: auto;
            margin-right: auto;
            transform: translate(10%, 5%);
        }
        hr {
            width: 80%;
        }
        th, td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f2f2f2;
        }
        .back-btn {
            display: block;
            margin: 20px auto;
            text-align: center;
            text-decoration: none;
            background-color: dimgray;
            color: #fff;
            padding: 10px 20px;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }
        .back-btn:hover {
            background-color: #555;
        }
    </style>
</head>
<body>

<?php
if ($result) {
    // Output data of each row
    echo "<h1>User Infos</h1><hr>";
    echo "<table>";
    echo "<tr><th>Record No</th><th>Name</th><th>Username</th><th>Email</th><th>Address</th><th>Phone</th></tr>";
    foreach($result as $row) {
        echo "<tr>";
        echo "<td>" . $row["regid"] . "</td>";
        echo "<td>" . $row["lname"] . " " . $row["fname"] . "</td>";
        echo "<td>" . $row["username"] . "</td>";
        echo "<td>" . $row["email"] . "</td>";
        echo "<td>" . $row["address"] . "</td>";
        echo "<td>" . $row["phone"] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}
?>

<button class="btn btn-danger mt-3 d-block mx-auto" onclick="window.location.href='admin.php'">Go back to Home</button>

</body>
</html>
