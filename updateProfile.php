<?php

    $host="localhost";
	$user="root";
	$db = "virus_tracer_db";
	$password="root";
	$con = new mysqli($host, $user, $password, $db);

    //Array to store server response.
    $response = array();

    //Checking required fileds.
    if(isset($_POST['userID']) && isset($_POST['isVaccinated']) && isset($_POST['isInfected'])) {

        //Extracting values from POST method.
        $userID = $_POST['userID'];
        $isVaccinated = $_POST['isVaccinated'];
        $isInfected = $_POST['isInfected'];

        //Prepared SQL statement in order to prevent injections.
        $stmt = $con->prepare("UPDATE USERS SET isVaccinated = ?, isInfected = ? WHERE userID = ?");
        $stmt->bind_param("iis", $isVaccinated, $isInfected, $userID);

        //Conneciton check.
        if($con->connect_error){
            die("Connection failed: " . $con->connect_error);
            $response["success"] = 0;
            $response["message"] = "Could not connect to the database.";
            echo json_encode($response);
        }

        //Excecution and error checking.
        if ($stmt->execute()) {
            $response["success"] = 1;
            $response["message"] = "Profile updated successfully";
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
        $response["message"] = "Missing required values.";
        echo json_encode($response);
    }

?>