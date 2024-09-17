<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg12.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql12.php") ?>
<?php include_once "phpfn12.php" ?>
<?php include_once "userfn12.php" ?>
<?php
ew_Header(FALSE);
$session = new cewsession;
$session->Page_Main();

//
// Page class for session
//
class cewsession {

	// Page ID
	var $PageID = "session";

	// Project ID
	var $ProjectID = "{274CC91E-1C95-40BB-9BB8-39D2A070EA8E}";

	// Page object name
	var $PageObjName = "session";

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		return ew_CurrentPage() . "?";
	}

	// Main
	// - Uncomment ** for database connectivity / Page_Loading / Page_Unloaded server event
	function Page_Main() {
		global $conn;
		$GLOBALS["Page"] = &$this;

		//**$conn = ew_Connect();
		// Global Page Loading event (in userfn*.php)
		//**Page_Loading();

		if (ob_get_length())
			ob_end_clean();
		$time = time();
		$_SESSION["EW_LAST_REFRESH_TIME"] = $time;
		echo ew_Encrypt($time);

		// Global Page Unloaded event (in userfn*.php)
		//**Page_Unloaded();
		 // Close connection
		//**ew_CloseConn();

	}
}
?>
