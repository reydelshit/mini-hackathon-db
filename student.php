<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        $sql = "SELECT * FROM students ORDER BY student_id DESC";


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $bidding = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($bidding);
        }



        break;





    case "POST":
        $student = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO students (student_id, student_id_code, student_name, student_profile, created_at) 
        VALUES (null, :student_id_code, :student_name, :student_profile, :created_at)";

        $stmt = $conn->prepare($sql);

        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':student_id_code', $student->student_id_code);
        $stmt->bindParam(':student_name', $student->student_name);
        $stmt->bindParam(':student_profile', $student->student_profile);
        $stmt->bindParam(':created_at', $created_at);

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
        $student = json_decode(file_get_contents('php://input'));

        $sql = "UPDATE students 
                SET student_id_code = :student_id_code, 
                    student_name = :student_name, 
                    student_profile = :student_profile, 
                    created_at = :created_at 
                WHERE student_id = :student_id";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':student_id', $student->student_id);
        $stmt->bindParam(':student_id_code', $student->student_id_code);
        $stmt->bindParam(':student_name', $student->student_name);
        $stmt->bindParam(':student_profile', $student->student_profile);
        $stmt->bindParam(':created_at', $student->created_at);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "student updated successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "student to update product"
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
