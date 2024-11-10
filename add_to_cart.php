<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["itemName"])) {
    $itemName = $_POST["itemName"];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "proj";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT item_name, price FROM inventory WHERE item_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$itemName]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            // Add item to cart session
            if (!isset($_SESSION["cart"])) {
                $_SESSION["cart"] = [];
            }

            if (isset($_SESSION["cart"][$itemName])) {
                $_SESSION["cart"][$itemName]["quantity"] += 1;
            } else {
                $_SESSION["cart"][$itemName] = [
                    "item_name" => $item["item_name"],
                    "price" => $item["price"],
                    "quantity" => 1
                ];
            }

            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Item not found"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Connection failed: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>
