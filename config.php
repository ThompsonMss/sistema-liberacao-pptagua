<?php 
	try {
		$user = "grj";
		$pass = "12345grj";
    	$conn = new PDO('mysql:host=192.168.3.100;dbname=ppliberacao', $user, $pass);
    	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (Exception $e) {
		echo 'ERROR: ' . $e->getMessage();
	}
?>