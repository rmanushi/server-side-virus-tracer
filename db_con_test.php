<?php
	//Only used for testing.
	$host="localhost";
	$user="root";
	$db = "virus_tracer_db";
	$password="";
	$con = new mysqli($host, $user, $password, $db);
	if($con) {
	    echo 'Connected to MySQL';
	} else {
	    echo 'MySQL Server is not connected';
	}

	mysqli_close($con);
	echo 'Connection Closed';
?>