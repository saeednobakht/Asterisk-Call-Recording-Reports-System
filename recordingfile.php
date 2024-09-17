<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg12.php" ?>
<?php $EW_ROOT_RELATIVE_PATH = ""; ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql12.php") ?>
<?php include_once "phpfn12.php" ?>
<?php include_once "usermanagementinfo.php" ?>
<?php include_once "userfn12.php" ?>
<?php

	// Security
	$Security = new cAdvancedSecurity();
	if($Security->CurrentUserLevelID >= 100) {
		die('{}');
	}
	$db =& DbHelper();

/*
	$sql = "SELECT * FROM cdr";
	echo $db->ExecuteHtml($sql, array("fieldcaption" => TRUE, "tablename" => array("calldate", "recordingfile"))); // Execute a SQL and show as HTML table
	exit;
*/
	if (constant("WAV_FOLDER")) {
		$folder = WAV_FOLDER;	
	} else {
		$folder = 'E:\vhosts\freepbx';
	}

//
	$sSqlWrk = "SELECT * FROM `cdr` WHERE `uniqueid` = '".$_GET["id"]."'";

//	$rswrk = $db->Execute($sSqlWrk);
//	print_r($mrswrk->fields('calldate'));exit;

	$rswrk = $db->ExecuteRow($sSqlWrk);

//	print_r($rswrk['recordingfile']);exit;
//

	$path = '/'.date('Y/m/d',strtotime($rswrk['calldate'])).'/';
	$location = $folder.$path.$rswrk['recordingfile'];

//	echo $location;exit;
	if (file_exists($location)) {
		smartReadFile($location, $rswrk['recordingfile']);
	} else {
		echo "404 Not Found";
		exit;
	}
?>
