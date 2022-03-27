<?php 

    $host="localhost";
    $user="root";
    $db = "virus_tracer_db";
    $password="root";
    $con = new mysqli($host, $user, $password, $db);

    $response = array();

    if(isset($_POST['userID'])) {

        $userID = $_POST['userID'];
        $isVaccinated = 0;
        $isInfected = 0;

        $stmt = $con->prepare("INSERT INTO USERS (userID, isVaccinated, isInfected) VALUES (?,?,?)");
        $stmt->bind_param("sii", $userID, $isVaccinated, $isInfected);

        if($con->connect_error){
            die("Connection failed: " . $con->connect_error);
            $response["success"] = 0;
            $response["message"] = "Could not connect to the database.";
            echo json_encode($response);
        }

        if ($stmt->execute()) {
            $response["success"] = 1;
            $response["message"] = "User profile created.";
            echo json_encode($response);
        } else {
            $response["success"] = 0;
            $response["message"] = mysqli_error($con);
            echo json_encode($response);
        }

        $stmt->close();
        $con->close();

    } else {
        $response["success"] = 0;
        $response["message"] = "Required field is missing.";
        echo json_encode($response);
    }
    
?>