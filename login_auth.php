<?php 
	include 'db_connect.php';
	session_start();

	extract($_POST);
	$qry = $conn->query("SELECT * FROM users where username='$username' and password = '$password' ");
	$userData = $qry->fetch_array();

	if($qry->num_rows > 0){
		$isSuperAdmin = false;

		foreach($userData as $k => $val){
			if ($k === 'user_type' && (int) $val === 1) {
				$isSuperAdmin = true;
			}
			if($k != 'password') {
				$_SESSION['login_'.$k] = $val;
			}
		}
		$_SESSION['super_admin'] = $isSuperAdmin;
		echo 1;
	}else{
		echo 2;
	}
?>