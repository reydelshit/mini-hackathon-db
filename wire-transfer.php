<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (isset($_GET['event_id'])) {
            $event_id = $_GET['event_id'];
            $sql = "SELECT * 
            FROM wire_transfer 
            WHERE event_id = :event_id AND (wire_type = 'gcash' OR wire_type = 'paymaya')
            ORDER BY created_at DESC
            LIMIT 2;";
        }

        if (!isset($event_id)) {
            $sql = "SELECT * FROM event_records ORDER BY event_records_id DESC";
        }

        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($event_id)) {
                $stmt->bindParam(':event_id', $event_id);
            }

            $stmt->execute();
            $event = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($event);
        }

        break;


    case "POST":
        $qr_code = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO wire_transfer (wire_id, wire_type, wire_image, created_at, event_id) 
                VALUES (null, :wire_type, :wire_image, :created_at, :event_id)";

        $stmt = $conn->prepare($sql);

        $created_at = date('Y-m-d H:i:s');

        $stmt->bindParam(':wire_type', $qr_code->wire_type);
        $stmt->bindParam(':wire_image', $qr_code->wire_image);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':event_id', $qr_code->event_id);


        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "qr_code added successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "qr_code to add product"
            ];
        }

        echo json_encode($response);
        break;
}
