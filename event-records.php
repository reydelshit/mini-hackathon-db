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
            INNER JOIN students ON students.student_id_code = event_records.student_code_id WHERE event_records.event_id = :event_id ";
        }

        if (isset($_GET['student_code_id'])) {
            $student_code_id = $_GET['student_code_id'];
            $event_id = $_GET['event_id'];
            $sql = "SELECT * FROM event_records WHERE student_code_id = :student_code_id AND event_id = :event_id";
        }

        if (!isset($event_id) && !isset($student_code_id)) {
            $sql = "SELECT * FROM event_records ORDER BY event_records_id DESC";
        }

        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($event_id)) {
                $stmt->bindParam(':event_id', $event_id);
            }

            if (isset($student_code_id)) {
                $stmt->bindParam(':student_code_id', $student_code_id);
                $stmt->bindParam(':event_id', $event_id);
            }

            $stmt->execute();
            $event = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($event);
        }

        break;


    case "POST":
        $payment = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO event_records (event_records_id, event_id, amount, student_code_id, created_at, payment_type, phone_number, proof_image, reference_no, payment_status, generatedReference_no) 
                VALUES (null, :event_id, :amount, :student_code_id, :created_at, :payment_type, :phone_number, :proof_image, :reference_no, :payment_status, :generatedReference_no)";

        $stmt = $conn->prepare($sql);

        $created_at = date('Y-m-d H:i:s');

        $stmt->bindParam(':event_id', $payment->event_id);
        $stmt->bindParam(':amount', $payment->amount);
        $stmt->bindParam(':student_code_id', $payment->student_code_id);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':payment_type', $payment->payment_type);
        $stmt->bindParam(':phone_number', $payment->phone_number);
        $stmt->bindParam(':proof_image', $payment->proof_image);
        $stmt->bindParam(':reference_no', $payment->reference_no);
        $stmt->bindParam(':payment_status', $payment->payment_status);
        $stmt->bindParam(':generatedReference_no', $payment->generatedReference_no);



        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "payment added successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "payment to add product"
            ];
        }

        echo json_encode($response);
        break;
}
