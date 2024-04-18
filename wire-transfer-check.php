<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        if (isset($_GET['event_id'])) {
            $event_id = $_GET['event_id'];
            $sql = "SELECT event_records.*, events.event_title, students.student_name, students.student_profile
            FROM event_records 
            INNER JOIN events ON events.event_id = event_records.event_id 
            INNER JOIN students ON students.student_id_code = event_records.student_code_id WHERE event_records.event_id = :event_id AND event_records.payment_type != 'cash'";
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

    case "PUT":
        $student = json_decode(file_get_contents('php://input'));

        $sql = "UPDATE event_records 
                    SET payment_status = :payment_status
                    WHERE event_records_id = :event_records_id";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':payment_status', $student->payment_status);
        $stmt->bindParam(':event_records_id', $student->event_records_id);


        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "event_records updated successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "event_records to update product"
            ];
        }

        echo json_encode($response);
        break;
}
