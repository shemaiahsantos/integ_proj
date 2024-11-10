<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proj";

try {
    // Establishing connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch user information from the database
    if(isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $stmt = $conn->prepare("SELECT username, email, lname, fname, address, phone, img FROM sbf WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Handle form submission
    if (isset($_POST['submit'])) {
        // Fetch user information again after form submission
        if(isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            $stmt = $conn->prepare("SELECT regid FROM sbf WHERE username = ?");
            $stmt->execute([$username]);
            $id = $stmt->fetchColumn();
        } else {
            // Redirect user to login page if not logged in
            header("Location: login.php");
            exit();
        }

        // Update first name, last name, address, and phone
        $new_fname = $_POST['fname'];
        $new_lname = $_POST['lname'];
        $new_address = $_POST['address'];
        $new_phone = $_POST['phone'];
        $stmt_update = $conn->prepare("UPDATE sbf SET fname=?, lname=?, address=?, phone=? WHERE regid=?");
        $stmt_update->execute([$new_fname, $new_lname, $new_address, $new_phone, $id]);

        // Image Upload Logic
        if(isset($_FILES['fileImg']) && $_FILES['fileImg']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['fileImg']['tmp_name'];
            $imageData = file_get_contents($fileTmpPath);

            $stmt_img = $conn->prepare("UPDATE sbf SET img=? WHERE regid=?");
            $stmt_img->bindParam(1, $imageData, PDO::PARAM_LOB);
            $stmt_img->bindParam(2, $id, PDO::PARAM_INT);
            $stmt_img->execute();
            echo "Image uploaded successfully.";
        } else {
            echo "No file uploaded";
        }
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
    <link rel="stylesheet" type="text/css" href="style2.css">
    <title>User Profile</title>
</head>
<body>
   <!-- Here starts your HTML form -->
   <form class="form" method="post" enctype="multipart/form-data">
    <h1> User Profile </h1>
    <!-- Displaying username and email fetched from PHP -->
    <label>
        <input required="" placeholder="Username" type="text" class="input" name="name" pattern="[A-Za-z ]+" title="Please enter letters and spaces only" readonly value="<?php echo isset($user['username']) ? $user['username'] : ''; ?>">
    </label>
    
    <label>
        <input required="" placeholder="Email" type="email" class="input" name="email" readonly value="<?php echo isset($user['email']) ? $user['email'] : ''; ?>">
    </label> 
    <!-- Displaying first and last name -->
    <label>
        <input required="" placeholder="First Name" type="text" class="input" name="fname" value="<?php echo isset($user['fname']) ? $user['fname'] : ''; ?>">
    </label> 
    <label>
        <input required="" placeholder="Last Name" type="text" class="input" name="lname" value="<?php echo isset($user['lname']) ? $user['lname'] : ''; ?>">
    </label>
    <!-- Address and Phone fields -->
    <label>
        <input required="" placeholder="Address" type="text" class="input" name="address" value="<?php echo isset($user['address']) ? $user['address'] : ''; ?>">
    </label>
    <label>
        <input required="" placeholder="Phone" type="text" class="input" name="phone" value="<?php echo isset($user['phone']) ? $user['phone'] : ''; ?>" pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number">
    </label>
    <br>
    <!-- File input for profile picture -->
    <label>Profile Picture:</label>
    <input type="file" name="fileImg" accept=".jpg, .jpeg, .png">
    <br>
    
    <div class="text-center">
        <button type="submit" name="submit" class="btn btn-success">Save</button>
    </div>

    <div class="text-center mt-3">
    <a href="user.php" class="btn btn-secondary">Back to Home</a>
</div>

</form>

<script>
    // Add event listener to the edit button
    document.getElementById("editButton").addEventListener("click", function() {
        // Show the save button
        document.getElementById("saveButton").style.display = "inline-block";
        // Hide the edit button
        document.getElementById("editButton").style.display = "none";
    });
</script>

</body>
</html>
