<?php

    $host="localhost";
	$user="root";
	$db = "virus_tracer_db";
	$password="root";
	$con = new mysqli($host, $user, $password, $db);

    //Array to store server response.
    $response = array();

    //Checking required fields.
    if(isset($_POST['user_x']) && isset($_POST['user_y'])) {

        //Exctracting required values from the POST method.
        $user_x = $_POST['user_x'];
        $user_y = $_POST['user_y'];

        //Prepared statements to reduce risk of SQL injeciton.
        $stmt_check_close_contact = $con->prepare("SELECT COUNT(*) FROM contacts WHERE user_x = ? AND user_y = ?");
        $stmt_check_close_contact->bind_param("ss", $user_y, $user_x);

        $stmt = $con->prepare("INSERT INTO contacts (user_x, user_y, dateContact) VALUES (?, ?, CURDATE())");
        $stmt->bind_param("ss", $user_x, $user_y);

        //Checking conneciton to db.
        if($con->connect_error){
            die("Connection failed: " . $con->connect_error);
            $response["success"] = 0;
            $response["message"] = "Could not connect to the database.";
            echo json_encode($response);
        }

        //Checking if the other user has already established close contact
        //with the current user in order to reduce reduntant close contact entry.
        if ($stmt_check_close_contact->execute()) {

            $result = $stmt_check_close_contact->get_result();
            $row = $result->fetch_row();
            $numCloseContacts = $row[0];
            
            if($numCloseContacts > 0){
                $response["success"] = 0;
                $response["message"] = "Close contact has already been stablished with this user.";
                echo json_encode($response);
            } else {
                //In case no previous contact has been established by 
                //the previous user with the current one then the insertion proceeds normally. 
                if($stmt->execute()){
                    $response["success"] = 1;
                    $response["message"] = "Close contact established successfully.";
                    echo json_encode($response);
                } else {
                    $response["success"] = 0;
                    $response["message"] = mysqli_error($con);
                    echo json_encode($response);
                }
            }
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