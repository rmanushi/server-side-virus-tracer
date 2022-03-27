<?php 

    $host="localhost";
    $user="root";
    $db = "virus_tracer_db";
    $password="root";
    $con = new mysqli($host, $user, $password, $db);

    //Array for server response.
    $response = array();

    //Checking required value.
    if(isset($_POST['userID'])) {

        $userID = $_POST['userID'];
        $isInfected = 1;
        
        //Query that checks for close contacts with infected individuals 
        //that have been established by the user itself.
        $stmt_x = $con->prepare("SELECT COUNT(user_x) FROM contacts WHERE user_x = ? AND user_y IN (SELECT userID FROM USERS WHERE isInfected = ?)");
        $stmt_x->bind_param("si", $userID, $isInfected);

        //Query that checks for close contacts with infected individuals 
        //that have been established by the other user's. Therefore, the userID is checked for user_y field.
        $stmt_y = $con->prepare("SELECT COUNT(user_y) FROM contacts WHERE user_y = ? AND user_x IN (SELECT userID FROM USERS WHERE isInfected = ?)");
        $stmt_y->bind_param("si", $userID, $isInfected);

        if($con->connect_error){
            die("Connection failed: " . $con->connect_error);
            $response["success"] = 0;
            $response["message"] = "Could not connect to the database.";
            echo json_encode($response);
        }

        if ($stmt_x->execute()) {
    
            $result = $stmt_x->get_result();
            $row = $result->fetch_row();
            $numCloseContacts = $row[0];
            //In case a close contact is found from the user close contact 
            //establishments than a response for being close contact with an infected 
            //individual is sent to the user.
            if($numCloseContacts > 0){
                $response["success"] = 1;
                $response["message"] = "You have been in contact with an infected person.";
            } else {
                //If the previous query finds no close contacts the other close contacts 
                //set up by the other user's with the current user are checked.
                if($stmt_y->execute()){

                    $result = $stmt_y->get_result();
                    $row = $result->fetch_row();
                    $numCloseContacts = $row[0];
    
                    if($numCloseContacts > 0) {
                        $response["success"] = 1;
                        $response["message"] = "You have been in contact with an infected person.";
                    } else {
                        //If no close contact with infected individuals is found than no notification is sent.
                        $response["success"] = 0;
                        $response["message"] = "You are safe.";
                    }
                } else {
                    $response["success"] = 0;
                    $response["message"] = mysqli_error($con);
                    echo json_encode($response);
                }
            }

            echo json_encode($response);
        } else {
            $response["success"] = 0;
            $response["message"] = mysqli_error($con);
            echo json_encode($response);
        }

        $stmt_x->close();
        $stmt_y->close();
        $con->close();

    } else {
        $response["success"] = 0;
        $response["message"] = "Required fields are missing.";
        echo json_encode($response);
    }
    
?>