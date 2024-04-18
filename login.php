<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":
        $username = $_GET['username'];
        $password = $_GET['password'];

        $sql = "SELECT * FROM user_accounts WHERE username = :username AND password = :password";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($users) {

            $response = [
                "status" => "success",
                "message" => "User login successful"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Failed to login "
            ];
        }


        echo json_encode($users);

        break;


    case "POST":
        $account = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO user_accounts (account_id, username, password, first_name, last_name, middle_name, address, email_address, phone_number, id_image, is_verified, created_on) 
                VALUES (null, :username, :password, :first_name, :last_name, :middle_name, :address, :email_address, :phone_number, :id_image, :is_verified, :created_on)";

        $stmt = $conn->prepare($sql);
        $created_on = date('Y-m-d');
        $stmt->bindParam(':username', $account->username);
        $stmt->bindParam(':password', $account->password);
        $stmt->bindParam(':first_name', $account->first_name);
        $stmt->bindParam(':last_name', $account->last_name);
        $stmt->bindParam(':middle_name', $account->middle_name);
        $stmt->bindParam(':address', $account->address);
        $stmt->bindParam(':email_address', $account->email_address);
        $stmt->bindParam(':phone_number', $account->phone_number);
        $stmt->bindParam(':id_image', $account->id_image);
        $stmt->bindParam(':is_verified', $account->is_verified);
        $stmt->bindParam(':created_on', $created_on);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Account created successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Account creation failed"
            ];
        }

        echo json_encode($response);
        break;
}
