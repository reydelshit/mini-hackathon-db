<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (isset($_GET['event_id'])) {
            $event_id = $_GET['event_id'];
            $sql = "SELECT * FROM events WHERE event_id = :event_id";
        }

        if (!isset($event_id)) {
            $sql = "SELECT * FROM events ORDER BY event_id DESC";
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
        $event = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO events (event_id, event_title, event_type, event_deadline, created_at, status, description) 
                VALUES (null, :event_title, :event_type, :event_deadline, :created_at, :status, :description)";

        $stmt = $conn->prepare($sql);

        $created_at = date('Y-m-d H:i:s');

        $stmt->bindParam(':event_title', $event->event_title);
        $stmt->bindParam(':event_type', $event->event_type);
        $stmt->bindParam(':event_deadline', $event->event_deadline);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':status', $event->status);
        $stmt->bindParam(':description', $event->description);



        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Product added successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Failed to add product"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":
        $event = json_decode(file_get_contents('php://input'));

        $sql = "UPDATE events 
                SET status = :status
                WHERE event_id = :event_id";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':event_id', $event->event_id);
        $stmt->bindParam(':status', $event->status);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "event updated successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "event to update product"
            ];
        }

        echo json_encode($response);
        break;

    case "DELETE":
        $student = json_decode(file_get_contents('php://input'));
        $sql = "DELETE FROM students WHERE student_id = :student_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':student_id', $student->student_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "students deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "students delete failed"
            ];
        }

        echo json_encode($response);
        break;
}
