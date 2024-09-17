<?php
	include_once('config.php');
	$IsDBoK = false;	
	$IsFreePBX = false;	
	$IsIssabel = false;	
	define("WAV_FOLDER", $WAV_FOLDER, TRUE);
	$freepbx_conf = '/etc/freepbx.conf';
	if (file_exists($freepbx_conf)) {
		$IsFreePBX = true;
		$file = file($freepbx_conf);

	//	print_r($file);exit;
		$DB_HOST = $file[5];
		$DB_NAME = 'asteriskcdrdb';
		$DB_USER = $file[3];
		$DB_PASS = $file[4];
		preg_match('/\$amp_conf\["AMPDBHOST"\] = "(.*)";/', $DB_HOST, $matches, PREG_OFFSET_CAPTURE);
		$DB_HOST = trim($matches[1][0]);
	//	preg_match('/\$amp_conf\["AMPDBNAME"\] = "(.*)";/', $DB_NAME, $matches, PREG_OFFSET_CAPTURE);
	//	$DB_NAME = trim($matches[1][0]);
		preg_match('/\$amp_conf\["AMPDBUSER"\] = "(.*)";/', $DB_USER, $matches, PREG_OFFSET_CAPTURE);
		$DB_USER = trim($matches[1][0]);
		preg_match('/\$amp_conf\["AMPDBPASS"\] = "(.*)";/', $DB_PASS, $matches, PREG_OFFSET_CAPTURE);
		$DB_PASS = trim($matches[1][0]);

	//	print_r($DB_PASS);exit;
		$con1 = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
		$result1 = mysqli_query($con1, "SHOW TABLES LIKE 'cdr'");
		if ($result1->num_rows) {
			$IsDBoK = true;
			$XP_DB_HOST = $DB_HOST;
			$XP_DB_NAME = $DB_NAME;
			$XP_DB_USER = $DB_USER;
			$XP_DB_PASS = $DB_PASS;
		}
		mysqli_free_result($result1);
		mysqli_close($con1);
	}
	$issabel_conf = '/etc/issabel.conf';
	if (file_exists($issabel_conf) && !$IsDBoK) {
		$IsIssabel = true;
		$file = file($issabel_conf);

	//	print_r($file);exit;
		$DB_HOST = 'localhost';
		$DB_NAME = 'asteriskcdrdb';
		$DB_USER = 'root';
		$DB_PASS = $file[1];
		preg_match('/mysqlrootpwd=(.*)\n/', $DB_PASS, $matches, PREG_OFFSET_CAPTURE);
		$DB_PASS = trim($matches[1][0]);

	//	print_r($matches);exit;
		$con2 = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
		$result2 = mysqli_query($con2, "SHOW TABLES LIKE 'cdr'");
		if ($result2->num_rows) {
			$IsDBoK = true;
			$XP_DB_HOST = $DB_HOST;
			$XP_DB_NAME = $DB_NAME;
			$XP_DB_USER = $DB_USER;
			$XP_DB_PASS = $DB_PASS;
		}
		mysqli_free_result($result2);
		mysqli_close($con2);
	}
	if (!$IsDBoK) {
		$DB_HOST = $XP_DB_HOST;
		$DB_NAME = $XP_DB_NAME;
		$DB_USER = $XP_DB_USER;
		$DB_PASS = $XP_DB_PASS;
		$con3 = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
		if (mysqli_connect_errno()) {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  exit();
		}
		$result3 = mysqli_query($con3, "SHOW TABLES LIKE 'cdr'");
		if ($result3->num_rows) {
			$IsDBoK = true;
		} else {
			echo "Table 'cdr' does not exist.";
			exit;
		}
		mysqli_free_result($result3);
		mysqli_close($con3);
	}
