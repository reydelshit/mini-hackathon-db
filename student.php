<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        if (isset($_GET['student_id'])) {
            $student_id = $_GET['student_id'];
            $sql = "SELECT * FROM students WHERE student_id = :student_id";
        }

        if (isset($_GET['student_id_code'])) {
            $student_id_code = $_GET['student_id_code'];
            $sql = "SELECT * FROM students WHERE student_id_code = :student_id_code";
        }

        if (!isset($student_id) && !isset($student_id_code)) {
            $sql = "SELECT * FROM students ORDER BY student_id DESC";
        }


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($student_id)) {
                $stmt->bindParam(':student_id', $student_id);
            }

            if (isset($student_id_code)) {
                $stmt->bindParam(':student_id_code', $student_id_code);
            }

            $stmt->execute();
            $stud = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($stud);
        }


        break;


    case "POST":
        $student = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO students (student_id, student_id_code, student_name, student_profile, created_at, student_course, year_block) 
        VALUES (null, :student_id_code, :student_name, :student_profile, :created_at, :student_course, :year_block)";

        $stmt = $conn->prepare($sql);

        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':student_id_code', $student->student_id_code);
        $stmt->bindParam(':student_name', $student->student_name);
        $stmt->bindParam(':student_profile', $student->student_profile);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':student_course', $student->student_course);
        $stmt->bindParam(':year_block', $student->year_block);

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
                    created_at = :created_at ,
                    student_course = :student_course,
                    year_block = :year_block
                WHERE student_id = :student_id";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':student_id', $student->student_id);
        $stmt->bindParam(':student_id_code', $student->student_id_code);
        $stmt->bindParam(':student_name', $student->student_name);
        $stmt->bindParam(':student_profile', $student->student_profile);
        $stmt->bindParam(':created_at', $student->created_at);
        $stmt->bindParam(':student_course', $student->student_course);
        $stmt->bindParam(':year_block', $student->year_block);

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
