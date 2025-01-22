<?php 
	// connect to the database
	$conn = mysqli_connect('mysql.railway.internal', 'root', 'xWfHBVSlavcfwkBleyoZWeIiIKYPCgVg', 'railway', '3306');
	
	// check connection
	if(!$conn){
		echo 'Connection error: '. mysqli_connect_error();
	}
?>	
