<?php
require 'conx.php';

if (isset($_SESSION['uID'])) {
    $activity_time = date("Y-m-d H:i:s");
    $ip_add = $_SERVER['REMOTE_ADDR'];
    $regid = $_SESSION['uID'];

    // Determine activity type based on session existence
    $activity_type = 'logout';

    $sql = "INSERT INTO user_activity (regid, activity_type, activity_time) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$regid, $activity_type, $activity_time]);

    if ($activity_type === 'logout') {
        unset($_SESSION['uID']); // Remove user ID from session
        session_destroy();
    }


} else {
    header("Location: index.php");
}
?>
