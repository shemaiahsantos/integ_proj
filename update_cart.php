<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["itemName"]) && isset($_POST["action"])) {
    $itemName = $_POST["itemName"];
    $action = $_POST["action"];

    if (isset($_SESSION["cart"][$itemName])) {
        switch ($action) {
            case "increase":
                $_SESSION["cart"][$itemName]["quantity"] += 1;
                break;

            case "decrease":
                if ($_SESSION["cart"][$itemName]["quantity"] > 1) {
                    $_SESSION["cart"][$itemName]["quantity"] -= 1;
                } else {
                    unset($_SESSION["cart"][$itemName]); // Remove item if quantity goes to zero
                }
                break;

            case "remove":
                unset($_SESSION["cart"][$itemName]);
                break;

            default:
                echo json_encode(["success" => false, "message" => "Invalid action"]);
                exit();
        }

        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Item not found in cart"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>
