<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg12.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql12.php") ?>
<?php include_once "phpfn12.php" ?>
<?php include_once "cdrinfo.php" ?>
<?php include_once "userfn12.php" ?>
<?php

//
// Page class
//

$cdr_search = NULL; // Initialize page object first

class ccdr_search extends ccdr {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{274CC91E-1C95-40BB-9BB8-39D2A070EA8E}";

	// Table name
	var $TableName = 'cdr';

	// Page object name
	var $PageObjName = 'cdr_search';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}
    var $AuditTrailOnAdd = FALSE;
    var $AuditTrailOnEdit = FALSE;
    var $AuditTrailOnDelete = FALSE;
    var $AuditTrailOnView = FALSE;
    var $AuditTrailOnViewData = FALSE;
    var $AuditTrailOnSearch = FALSE;

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Methods to clear message
	function ClearMessage() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
	}

	function ClearFailureMessage() {
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
	}

	function ClearSuccessMessage() {
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
	}

	function ClearWarningMessage() {
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	function ClearMessages() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $TokenTimeout = 0;
	var $CheckToken = EW_CHECK_TOKEN;
	var $CheckTokenFn = "ew_CheckToken";
	var $CreateTokenFn = "ew_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ew_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EW_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EW_TOKEN_NAME], $this->TokenTimeout);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		$GLOBALS["Page"] = &$this;
		$this->TokenTimeout = ew_SessionTimeoutTime();

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (cdr)
		if (!isset($GLOBALS["cdr"]) || get_class($GLOBALS["cdr"]) == "ccdr") {
			$GLOBALS["cdr"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["cdr"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'cdr', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect($this->DBID);
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsCustomExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		if (!$Security->CanSearch()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage(ew_DeniedMsg()); // Set no permission
			if ($Security->CanList())
				$this->Page_Terminate(ew_GetUrl("cdrlist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $Language->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Create Token
		$this->CreateToken();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $gsExportFile, $gTmpImages;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		global $EW_EXPORT, $cdr;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($cdr);
				$doc->Text = $sContent;
				if ($this->Export == "email")
					echo $this->ExportEmail($doc->Text);
				else
					$doc->Export();
				ew_DeleteTmpImages(); // Delete temp images
				exit();
			}
		}
		$this->Page_Redirecting($url);

		 // Close connection
		ew_CloseConn();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $FormClassName = "form-horizontal ewForm ewSearchForm";
	var $IsModal = FALSE;
	var $SearchLabelClass = "col-sm-3 control-label ewLabel";
	var $SearchRightColumnClass = "col-sm-9";

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsSearchError;
		global $gbSkipHeaderFooter;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Check modal
		$this->IsModal = (@$_GET["modal"] == "1" || @$_POST["modal"] == "1");
		if ($this->IsModal)
			$gbSkipHeaderFooter = TRUE;
		if ($this->IsPageRequest()) { // Validate request

			// Get action
			$this->CurrentAction = $objForm->GetValue("a_search");
			switch ($this->CurrentAction) {
				case "S": // Get search criteria

					// Build search string for advanced search, remove blank field
					$this->LoadSearchValues(); // Get search values
					if ($this->ValidateSearch()) {
						$sSrchStr = $this->BuildAdvancedSearch();
					} else {
						$sSrchStr = "";
						$this->setFailureMessage($gsSearchError);
					}
					if ($sSrchStr <> "") {
						$sSrchStr = $this->UrlParm($sSrchStr);
						$sSrchStr = "cdrlist.php" . "?" . $sSrchStr;
						if ($this->IsModal) {
							$row = array();
							$row["url"] = $sSrchStr;
							echo ew_ArrayToJson(array($row));
							$this->Page_Terminate();
							exit();
						} else {
							$this->Page_Terminate($sSrchStr); // Go to list page
						}
					}
			}
		}

		// Restore search settings from Session
		if ($gsSearchError == "")
			$this->LoadAdvancedSearch();

		// Render row for search
		$this->RowType = EW_ROWTYPE_SEARCH;
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Build advanced search
	function BuildAdvancedSearch() {
		$sSrchUrl = "";
		$this->BuildSearchUrl($sSrchUrl, $this->calldate); // calldate
		$this->BuildSearchUrl($sSrchUrl, $this->uniqueid); // uniqueid
		$this->BuildSearchUrl($sSrchUrl, $this->cnam); // cnam
		$this->BuildSearchUrl($sSrchUrl, $this->cnum); // cnum
		$this->BuildSearchUrl($sSrchUrl, $this->dst); // dst
		$this->BuildSearchUrl($sSrchUrl, $this->duration); // duration
		$this->BuildSearchUrl($sSrchUrl, $this->billsec); // billsec
		$this->BuildSearchUrl($sSrchUrl, $this->disposition); // disposition
		$this->BuildSearchUrl($sSrchUrl, $this->outbound_cnum); // outbound_cnum
		$this->BuildSearchUrl($sSrchUrl, $this->play); // play
		$this->BuildSearchUrl($sSrchUrl, $this->recordingfile); // recordingfile
		$this->BuildSearchUrl($sSrchUrl, $this->recording_name); // recording_name
		$this->BuildSearchUrl($sSrchUrl, $this->clid); // clid
		$this->BuildSearchUrl($sSrchUrl, $this->src); // src
		$this->BuildSearchUrl($sSrchUrl, $this->dcontext); // dcontext
		$this->BuildSearchUrl($sSrchUrl, $this->channel); // channel
		$this->BuildSearchUrl($sSrchUrl, $this->dstchannel); // dstchannel
		$this->BuildSearchUrl($sSrchUrl, $this->lastapp); // lastapp
		$this->BuildSearchUrl($sSrchUrl, $this->lastdata); // lastdata
		$this->BuildSearchUrl($sSrchUrl, $this->amaflags); // amaflags
		$this->BuildSearchUrl($sSrchUrl, $this->accountcode); // accountcode
		$this->BuildSearchUrl($sSrchUrl, $this->userfield); // userfield
		$this->BuildSearchUrl($sSrchUrl, $this->did); // did
		$this->BuildSearchUrl($sSrchUrl, $this->outbound_cnam); // outbound_cnam
		$this->BuildSearchUrl($sSrchUrl, $this->dst_cnam); // dst_cnam
		$this->BuildSearchUrl($sSrchUrl, $this->linkedid); // linkedid
		$this->BuildSearchUrl($sSrchUrl, $this->peeraccount); // peeraccount
		$this->BuildSearchUrl($sSrchUrl, $this->sequence); // sequence
		if ($sSrchUrl <> "") $sSrchUrl .= "&";
		$sSrchUrl .= "cmd=search";
		return $sSrchUrl;
	}

	// Build search URL
	function BuildSearchUrl(&$Url, &$Fld, $OprOnly=FALSE) {
		global $objForm;
		$sWrk = "";
		$FldParm = substr($Fld->FldVar, 2);
		$FldVal = $objForm->GetValue("x_$FldParm");
		$FldOpr = $objForm->GetValue("z_$FldParm");
		$FldCond = $objForm->GetValue("v_$FldParm");
		$FldVal2 = $objForm->GetValue("y_$FldParm");
		$FldOpr2 = $objForm->GetValue("w_$FldParm");
		$FldVal = ew_StripSlashes($FldVal);
		if (is_array($FldVal)) $FldVal = implode(",", $FldVal);
		$FldVal2 = ew_StripSlashes($FldVal2);
		if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
		$FldOpr = strtoupper(trim($FldOpr));
		$lFldDataType = ($Fld->FldIsVirtual) ? EW_DATATYPE_STRING : $Fld->FldDataType;
		if ($FldOpr == "BETWEEN") {
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal) && $this->SearchValueIsNumeric($Fld, $FldVal2));
			if ($FldVal <> "" && $FldVal2 <> "" && $IsValidValue) {
				$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
					"&y_" . $FldParm . "=" . urlencode($FldVal2) .
					"&z_" . $FldParm . "=" . urlencode($FldOpr);
			}
		} else {
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal));
			if ($FldVal <> "" && $IsValidValue && ew_IsValidOpr($FldOpr, $lFldDataType)) {
				$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
					"&z_" . $FldParm . "=" . urlencode($FldOpr);
			} elseif ($FldOpr == "IS NULL" || $FldOpr == "IS NOT NULL" || ($FldOpr <> "" && $OprOnly && ew_IsValidOpr($FldOpr, $lFldDataType))) {
				$sWrk = "z_" . $FldParm . "=" . urlencode($FldOpr);
			}
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal2));
			if ($FldVal2 <> "" && $IsValidValue && ew_IsValidOpr($FldOpr2, $lFldDataType)) {
				if ($sWrk <> "") $sWrk .= "&v_" . $FldParm . "=" . urlencode($FldCond) . "&";
				$sWrk .= "y_" . $FldParm . "=" . urlencode($FldVal2) .
					"&w_" . $FldParm . "=" . urlencode($FldOpr2);
			} elseif ($FldOpr2 == "IS NULL" || $FldOpr2 == "IS NOT NULL" || ($FldOpr2 <> "" && $OprOnly && ew_IsValidOpr($FldOpr2, $lFldDataType))) {
				if ($sWrk <> "") $sWrk .= "&v_" . $FldParm . "=" . urlencode($FldCond) . "&";
				$sWrk .= "w_" . $FldParm . "=" . urlencode($FldOpr2);
			}
		}
		if ($sWrk <> "") {
			if ($Url <> "") $Url .= "&";
			$Url .= $sWrk;
		}
	}

	function SearchValueIsNumeric($Fld, $Value) {
		if (ew_IsFloatFormat($Fld->FldType)) $Value = ew_StrToFloat($Value);
		return is_numeric($Value);
	}

	// Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// calldate

		$this->calldate->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_calldate"));
		$this->calldate->AdvancedSearch->SearchOperator = $objForm->GetValue("z_calldate");
		$this->calldate->AdvancedSearch->SearchCondition = $objForm->GetValue("v_calldate");
		$this->calldate->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_calldate"));
		$this->calldate->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_calldate");

		// uniqueid
		$this->uniqueid->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_uniqueid"));
		$this->uniqueid->AdvancedSearch->SearchOperator = $objForm->GetValue("z_uniqueid");
		$this->uniqueid->AdvancedSearch->SearchCondition = $objForm->GetValue("v_uniqueid");
		$this->uniqueid->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_uniqueid"));
		$this->uniqueid->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_uniqueid");

		// cnam
		$this->cnam->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_cnam"));
		$this->cnam->AdvancedSearch->SearchOperator = $objForm->GetValue("z_cnam");
		$this->cnam->AdvancedSearch->SearchCondition = $objForm->GetValue("v_cnam");
		$this->cnam->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_cnam"));
		$this->cnam->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_cnam");

		// cnum
		$this->cnum->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_cnum"));
		$this->cnum->AdvancedSearch->SearchOperator = $objForm->GetValue("z_cnum");
		$this->cnum->AdvancedSearch->SearchCondition = $objForm->GetValue("v_cnum");
		$this->cnum->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_cnum"));
		$this->cnum->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_cnum");

		// dst
		$this->dst->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_dst"));
		$this->dst->AdvancedSearch->SearchOperator = $objForm->GetValue("z_dst");
		$this->dst->AdvancedSearch->SearchCondition = $objForm->GetValue("v_dst");
		$this->dst->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_dst"));
		$this->dst->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_dst");

		// duration
		$this->duration->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_duration"));
		$this->duration->AdvancedSearch->SearchOperator = $objForm->GetValue("z_duration");
		$this->duration->AdvancedSearch->SearchCondition = $objForm->GetValue("v_duration");
		$this->duration->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_duration"));
		$this->duration->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_duration");

		// billsec
		$this->billsec->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_billsec"));
		$this->billsec->AdvancedSearch->SearchOperator = $objForm->GetValue("z_billsec");
		$this->billsec->AdvancedSearch->SearchCondition = $objForm->GetValue("v_billsec");
		$this->billsec->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_billsec"));
		$this->billsec->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_billsec");

		// disposition
		$this->disposition->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_disposition"));
		$this->disposition->AdvancedSearch->SearchOperator = $objForm->GetValue("z_disposition");
		$this->disposition->AdvancedSearch->SearchCondition = $objForm->GetValue("v_disposition");
		$this->disposition->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_disposition"));
		$this->disposition->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_disposition");

		// outbound_cnum
		$this->outbound_cnum->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_outbound_cnum"));
		$this->outbound_cnum->AdvancedSearch->SearchOperator = $objForm->GetValue("z_outbound_cnum");
		$this->outbound_cnum->AdvancedSearch->SearchCondition = $objForm->GetValue("v_outbound_cnum");
		$this->outbound_cnum->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_outbound_cnum"));
		$this->outbound_cnum->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_outbound_cnum");

		// play
		$this->play->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_play"));
		$this->play->AdvancedSearch->SearchOperator = $objForm->GetValue("z_play");
		$this->play->AdvancedSearch->SearchCondition = $objForm->GetValue("v_play");
		$this->play->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_play"));
		$this->play->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_play");

		// recordingfile
		$this->recordingfile->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_recordingfile"));
		$this->recordingfile->AdvancedSearch->SearchOperator = $objForm->GetValue("z_recordingfile");
		$this->recordingfile->AdvancedSearch->SearchCondition = $objForm->GetValue("v_recordingfile");
		$this->recordingfile->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_recordingfile"));
		$this->recordingfile->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_recordingfile");

		// recording_name
		$this->recording_name->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_recording_name"));
		$this->recording_name->AdvancedSearch->SearchOperator = $objForm->GetValue("z_recording_name");
		$this->recording_name->AdvancedSearch->SearchCondition = $objForm->GetValue("v_recording_name");
		$this->recording_name->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_recording_name"));
		$this->recording_name->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_recording_name");

		// clid
		$this->clid->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_clid"));
		$this->clid->AdvancedSearch->SearchOperator = $objForm->GetValue("z_clid");
		$this->clid->AdvancedSearch->SearchCondition = $objForm->GetValue("v_clid");
		$this->clid->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_clid"));
		$this->clid->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_clid");

		// src
		$this->src->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_src"));
		$this->src->AdvancedSearch->SearchOperator = $objForm->GetValue("z_src");
		$this->src->AdvancedSearch->SearchCondition = $objForm->GetValue("v_src");
		$this->src->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_src"));
		$this->src->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_src");

		// dcontext
		$this->dcontext->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_dcontext"));
		$this->dcontext->AdvancedSearch->SearchOperator = $objForm->GetValue("z_dcontext");
		$this->dcontext->AdvancedSearch->SearchCondition = $objForm->GetValue("v_dcontext");
		$this->dcontext->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_dcontext"));
		$this->dcontext->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_dcontext");

		// channel
		$this->channel->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_channel"));
		$this->channel->AdvancedSearch->SearchOperator = $objForm->GetValue("z_channel");
		$this->channel->AdvancedSearch->SearchCondition = $objForm->GetValue("v_channel");
		$this->channel->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_channel"));
		$this->channel->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_channel");

		// dstchannel
		$this->dstchannel->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_dstchannel"));
		$this->dstchannel->AdvancedSearch->SearchOperator = $objForm->GetValue("z_dstchannel");
		$this->dstchannel->AdvancedSearch->SearchCondition = $objForm->GetValue("v_dstchannel");
		$this->dstchannel->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_dstchannel"));
		$this->dstchannel->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_dstchannel");

		// lastapp
		$this->lastapp->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_lastapp"));
		$this->lastapp->AdvancedSearch->SearchOperator = $objForm->GetValue("z_lastapp");
		$this->lastapp->AdvancedSearch->SearchCondition = $objForm->GetValue("v_lastapp");
		$this->lastapp->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_lastapp"));
		$this->lastapp->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_lastapp");

		// lastdata
		$this->lastdata->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_lastdata"));
		$this->lastdata->AdvancedSearch->SearchOperator = $objForm->GetValue("z_lastdata");
		$this->lastdata->AdvancedSearch->SearchCondition = $objForm->GetValue("v_lastdata");
		$this->lastdata->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_lastdata"));
		$this->lastdata->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_lastdata");

		// amaflags
		$this->amaflags->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_amaflags"));
		$this->amaflags->AdvancedSearch->SearchOperator = $objForm->GetValue("z_amaflags");
		$this->amaflags->AdvancedSearch->SearchCondition = $objForm->GetValue("v_amaflags");
		$this->amaflags->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_amaflags"));
		$this->amaflags->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_amaflags");

		// accountcode
		$this->accountcode->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_accountcode"));
		$this->accountcode->AdvancedSearch->SearchOperator = $objForm->GetValue("z_accountcode");
		$this->accountcode->AdvancedSearch->SearchCondition = $objForm->GetValue("v_accountcode");
		$this->accountcode->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_accountcode"));
		$this->accountcode->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_accountcode");

		// userfield
		$this->userfield->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_userfield"));
		$this->userfield->AdvancedSearch->SearchOperator = $objForm->GetValue("z_userfield");
		$this->userfield->AdvancedSearch->SearchCondition = $objForm->GetValue("v_userfield");
		$this->userfield->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_userfield"));
		$this->userfield->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_userfield");

		// did
		$this->did->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_did"));
		$this->did->AdvancedSearch->SearchOperator = $objForm->GetValue("z_did");
		$this->did->AdvancedSearch->SearchCondition = $objForm->GetValue("v_did");
		$this->did->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_did"));
		$this->did->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_did");

		// outbound_cnam
		$this->outbound_cnam->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_outbound_cnam"));
		$this->outbound_cnam->AdvancedSearch->SearchOperator = $objForm->GetValue("z_outbound_cnam");
		$this->outbound_cnam->AdvancedSearch->SearchCondition = $objForm->GetValue("v_outbound_cnam");
		$this->outbound_cnam->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_outbound_cnam"));
		$this->outbound_cnam->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_outbound_cnam");

		// dst_cnam
		$this->dst_cnam->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_dst_cnam"));
		$this->dst_cnam->AdvancedSearch->SearchOperator = $objForm->GetValue("z_dst_cnam");
		$this->dst_cnam->AdvancedSearch->SearchCondition = $objForm->GetValue("v_dst_cnam");
		$this->dst_cnam->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_dst_cnam"));
		$this->dst_cnam->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_dst_cnam");

		// linkedid
		$this->linkedid->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_linkedid"));
		$this->linkedid->AdvancedSearch->SearchOperator = $objForm->GetValue("z_linkedid");
		$this->linkedid->AdvancedSearch->SearchCondition = $objForm->GetValue("v_linkedid");
		$this->linkedid->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_linkedid"));
		$this->linkedid->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_linkedid");

		// peeraccount
		$this->peeraccount->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_peeraccount"));
		$this->peeraccount->AdvancedSearch->SearchOperator = $objForm->GetValue("z_peeraccount");
		$this->peeraccount->AdvancedSearch->SearchCondition = $objForm->GetValue("v_peeraccount");
		$this->peeraccount->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_peeraccount"));
		$this->peeraccount->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_peeraccount");

		// sequence
		$this->sequence->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_sequence"));
		$this->sequence->AdvancedSearch->SearchOperator = $objForm->GetValue("z_sequence");
		$this->sequence->AdvancedSearch->SearchCondition = $objForm->GetValue("v_sequence");
		$this->sequence->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_sequence"));
		$this->sequence->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_sequence");
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// calldate
		// uniqueid
		// cnam
		// cnum
		// dst
		// duration
		// billsec
		// disposition
		// outbound_cnum
		// play
		// recordingfile
		// recording_name
		// clid
		// src
		// dcontext
		// channel
		// dstchannel
		// lastapp
		// lastdata
		// amaflags
		// accountcode
		// userfield
		// did
		// outbound_cnam
		// dst_cnam
		// linkedid
		// peeraccount
		// sequence

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// calldate
		$this->calldate->ViewValue = $this->calldate->CurrentValue;
		$this->calldate->ViewValue = ew_FormatDateTime($this->calldate->ViewValue, 9);
		$this->calldate->ViewCustomAttributes = "style='direction:ltr;'";

		// uniqueid
		$this->uniqueid->ViewValue = $this->uniqueid->CurrentValue;
		$this->uniqueid->ViewCustomAttributes = "";

		// cnam
		$this->cnam->ViewValue = $this->cnam->CurrentValue;
		$this->cnam->ViewCustomAttributes = "";

		// cnum
		$this->cnum->ViewValue = $this->cnum->CurrentValue;
		$this->cnum->ViewCustomAttributes = "";

		// dst
		$this->dst->ViewValue = $this->dst->CurrentValue;
		$this->dst->ViewCustomAttributes = "";

		// duration
		$this->duration->ViewValue = $this->duration->CurrentValue;
		$this->duration->ViewCustomAttributes = "";

		// billsec
		$this->billsec->ViewValue = $this->billsec->CurrentValue;
		$this->billsec->ViewCustomAttributes = "";

		// disposition
		$this->disposition->ViewValue = $this->disposition->CurrentValue;
		$this->disposition->ViewCustomAttributes = "";

		// outbound_cnum
		$this->outbound_cnum->ViewValue = $this->outbound_cnum->CurrentValue;
		$this->outbound_cnum->ViewCustomAttributes = "";

		// play
		$this->play->ViewValue = $this->play->CurrentValue;
		$this->play->ViewCustomAttributes = "";

		// recordingfile
		$this->recordingfile->ViewValue = $this->recordingfile->CurrentValue;
		$this->recordingfile->ViewCustomAttributes = "";

		// recording_name
		$this->recording_name->ViewValue = $this->recording_name->CurrentValue;
		$this->recording_name->ViewCustomAttributes = "";

		// clid
		$this->clid->ViewValue = $this->clid->CurrentValue;
		$this->clid->ViewCustomAttributes = "";

		// src
		$this->src->ViewValue = $this->src->CurrentValue;
		$this->src->ViewCustomAttributes = "";

		// dcontext
		$this->dcontext->ViewValue = $this->dcontext->CurrentValue;
		$this->dcontext->ViewCustomAttributes = "";

		// channel
		$this->channel->ViewValue = $this->channel->CurrentValue;
		$this->channel->ViewCustomAttributes = "";

		// dstchannel
		$this->dstchannel->ViewValue = $this->dstchannel->CurrentValue;
		$this->dstchannel->ViewCustomAttributes = "";

		// lastapp
		$this->lastapp->ViewValue = $this->lastapp->CurrentValue;
		$this->lastapp->ViewCustomAttributes = "";

		// lastdata
		$this->lastdata->ViewValue = $this->lastdata->CurrentValue;
		$this->lastdata->ViewCustomAttributes = "";

		// amaflags
		$this->amaflags->ViewValue = $this->amaflags->CurrentValue;
		$this->amaflags->ViewCustomAttributes = "";

		// accountcode
		$this->accountcode->ViewValue = $this->accountcode->CurrentValue;
		$this->accountcode->ViewCustomAttributes = "";

		// userfield
		$this->userfield->ViewValue = $this->userfield->CurrentValue;
		$this->userfield->ViewCustomAttributes = "";

		// did
		$this->did->ViewValue = $this->did->CurrentValue;
		$this->did->ViewCustomAttributes = "";

		// outbound_cnam
		$this->outbound_cnam->ViewValue = $this->outbound_cnam->CurrentValue;
		$this->outbound_cnam->ViewCustomAttributes = "";

		// dst_cnam
		$this->dst_cnam->ViewValue = $this->dst_cnam->CurrentValue;
		$this->dst_cnam->ViewCustomAttributes = "";

		// linkedid
		$this->linkedid->ViewValue = $this->linkedid->CurrentValue;
		$this->linkedid->ViewCustomAttributes = "";

		// peeraccount
		$this->peeraccount->ViewValue = $this->peeraccount->CurrentValue;
		$this->peeraccount->ViewCustomAttributes = "";

		// sequence
		$this->sequence->ViewValue = $this->sequence->CurrentValue;
		$this->sequence->ViewCustomAttributes = "";

			// calldate
			$this->calldate->LinkCustomAttributes = "";
			$this->calldate->HrefValue = "";
			$this->calldate->TooltipValue = "";

			// uniqueid
			$this->uniqueid->LinkCustomAttributes = "";
			$this->uniqueid->HrefValue = "";
			$this->uniqueid->TooltipValue = "";

			// cnam
			$this->cnam->LinkCustomAttributes = "";
			$this->cnam->HrefValue = "";
			$this->cnam->TooltipValue = "";

			// cnum
			$this->cnum->LinkCustomAttributes = "";
			$this->cnum->HrefValue = "";
			$this->cnum->TooltipValue = "";

			// dst
			$this->dst->LinkCustomAttributes = "";
			$this->dst->HrefValue = "";
			$this->dst->TooltipValue = "";

			// duration
			$this->duration->LinkCustomAttributes = "";
			$this->duration->HrefValue = "";
			$this->duration->TooltipValue = "";

			// billsec
			$this->billsec->LinkCustomAttributes = "";
			$this->billsec->HrefValue = "";
			$this->billsec->TooltipValue = "";

			// disposition
			$this->disposition->LinkCustomAttributes = "";
			$this->disposition->HrefValue = "";
			$this->disposition->TooltipValue = "";

			// outbound_cnum
			$this->outbound_cnum->LinkCustomAttributes = "";
			$this->outbound_cnum->HrefValue = "";
			$this->outbound_cnum->TooltipValue = "";

			// play
			$this->play->LinkCustomAttributes = "";
			$this->play->HrefValue = "";
			$this->play->TooltipValue = "";

			// recordingfile
			$this->recordingfile->LinkCustomAttributes = "";
			if (!ew_Empty($this->recordingfile->CurrentValue)) {
				$this->recordingfile->HrefValue = $this->recordingfile->CurrentValue; // Add prefix/suffix
				$this->recordingfile->LinkAttrs["target"] = ""; // Add target
				if ($this->Export <> "") $this->recordingfile->HrefValue = ew_ConvertFullUrl($this->recordingfile->HrefValue);
			} else {
				$this->recordingfile->HrefValue = "";
			}
			$this->recordingfile->TooltipValue = "";

			// recording_name
			$this->recording_name->LinkCustomAttributes = "";
			$this->recording_name->HrefValue = "";
			$this->recording_name->TooltipValue = "";

			// clid
			$this->clid->LinkCustomAttributes = "";
			$this->clid->HrefValue = "";
			$this->clid->TooltipValue = "";

			// src
			$this->src->LinkCustomAttributes = "";
			$this->src->HrefValue = "";
			$this->src->TooltipValue = "";

			// dcontext
			$this->dcontext->LinkCustomAttributes = "";
			$this->dcontext->HrefValue = "";
			$this->dcontext->TooltipValue = "";

			// channel
			$this->channel->LinkCustomAttributes = "";
			$this->channel->HrefValue = "";
			$this->channel->TooltipValue = "";

			// dstchannel
			$this->dstchannel->LinkCustomAttributes = "";
			$this->dstchannel->HrefValue = "";
			$this->dstchannel->TooltipValue = "";

			// lastapp
			$this->lastapp->LinkCustomAttributes = "";
			$this->lastapp->HrefValue = "";
			$this->lastapp->TooltipValue = "";

			// lastdata
			$this->lastdata->LinkCustomAttributes = "";
			$this->lastdata->HrefValue = "";
			$this->lastdata->TooltipValue = "";

			// amaflags
			$this->amaflags->LinkCustomAttributes = "";
			$this->amaflags->HrefValue = "";
			$this->amaflags->TooltipValue = "";

			// accountcode
			$this->accountcode->LinkCustomAttributes = "";
			$this->accountcode->HrefValue = "";
			$this->accountcode->TooltipValue = "";

			// userfield
			$this->userfield->LinkCustomAttributes = "";
			$this->userfield->HrefValue = "";
			$this->userfield->TooltipValue = "";

			// did
			$this->did->LinkCustomAttributes = "";
			$this->did->HrefValue = "";
			$this->did->TooltipValue = "";

			// outbound_cnam
			$this->outbound_cnam->LinkCustomAttributes = "";
			$this->outbound_cnam->HrefValue = "";
			$this->outbound_cnam->TooltipValue = "";

			// dst_cnam
			$this->dst_cnam->LinkCustomAttributes = "";
			$this->dst_cnam->HrefValue = "";
			$this->dst_cnam->TooltipValue = "";

			// linkedid
			$this->linkedid->LinkCustomAttributes = "";
			$this->linkedid->HrefValue = "";
			$this->linkedid->TooltipValue = "";

			// peeraccount
			$this->peeraccount->LinkCustomAttributes = "";
			$this->peeraccount->HrefValue = "";
			$this->peeraccount->TooltipValue = "";

			// sequence
			$this->sequence->LinkCustomAttributes = "";
			$this->sequence->HrefValue = "";
			$this->sequence->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// calldate
			$this->calldate->EditAttrs["class"] = "form-control";
			$this->calldate->EditCustomAttributes = "style='direction:ltr;text-align:center;'";
			$this->calldate->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->calldate->AdvancedSearch->SearchValue, 9), 9));
			$this->calldate->PlaceHolder = ew_RemoveHtml($this->calldate->FldCaption());
			$this->calldate->EditAttrs["class"] = "form-control";
			$this->calldate->EditCustomAttributes = "style='direction:ltr;text-align:center;'";
			$this->calldate->EditValue2 = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->calldate->AdvancedSearch->SearchValue2, 9), 9));
			$this->calldate->PlaceHolder = ew_RemoveHtml($this->calldate->FldCaption());

			// uniqueid
			$this->uniqueid->EditAttrs["class"] = "form-control";
			$this->uniqueid->EditCustomAttributes = "";
			$this->uniqueid->EditValue = ew_HtmlEncode($this->uniqueid->AdvancedSearch->SearchValue);
			$this->uniqueid->PlaceHolder = ew_RemoveHtml($this->uniqueid->FldCaption());
			$this->uniqueid->EditAttrs["class"] = "form-control";
			$this->uniqueid->EditCustomAttributes = "";
			$this->uniqueid->EditValue2 = ew_HtmlEncode($this->uniqueid->AdvancedSearch->SearchValue2);
			$this->uniqueid->PlaceHolder = ew_RemoveHtml($this->uniqueid->FldCaption());

			// cnam
			$this->cnam->EditAttrs["class"] = "form-control";
			$this->cnam->EditCustomAttributes = "";
			$this->cnam->EditValue = ew_HtmlEncode($this->cnam->AdvancedSearch->SearchValue);
			$this->cnam->PlaceHolder = ew_RemoveHtml($this->cnam->FldCaption());
			$this->cnam->EditAttrs["class"] = "form-control";
			$this->cnam->EditCustomAttributes = "";
			$this->cnam->EditValue2 = ew_HtmlEncode($this->cnam->AdvancedSearch->SearchValue2);
			$this->cnam->PlaceHolder = ew_RemoveHtml($this->cnam->FldCaption());

			// cnum
			$this->cnum->EditAttrs["class"] = "form-control";
			$this->cnum->EditCustomAttributes = "";
			$this->cnum->EditValue = ew_HtmlEncode($this->cnum->AdvancedSearch->SearchValue);
			$this->cnum->PlaceHolder = ew_RemoveHtml($this->cnum->FldCaption());
			$this->cnum->EditAttrs["class"] = "form-control";
			$this->cnum->EditCustomAttributes = "";
			$this->cnum->EditValue2 = ew_HtmlEncode($this->cnum->AdvancedSearch->SearchValue2);
			$this->cnum->PlaceHolder = ew_RemoveHtml($this->cnum->FldCaption());

			// dst
			$this->dst->EditAttrs["class"] = "form-control";
			$this->dst->EditCustomAttributes = "";
			$this->dst->EditValue = ew_HtmlEncode($this->dst->AdvancedSearch->SearchValue);
			$this->dst->PlaceHolder = ew_RemoveHtml($this->dst->FldCaption());
			$this->dst->EditAttrs["class"] = "form-control";
			$this->dst->EditCustomAttributes = "";
			$this->dst->EditValue2 = ew_HtmlEncode($this->dst->AdvancedSearch->SearchValue2);
			$this->dst->PlaceHolder = ew_RemoveHtml($this->dst->FldCaption());

			// duration
			$this->duration->EditAttrs["class"] = "form-control";
			$this->duration->EditCustomAttributes = "";
			$this->duration->EditValue = ew_HtmlEncode($this->duration->AdvancedSearch->SearchValue);
			$this->duration->PlaceHolder = ew_RemoveHtml($this->duration->FldCaption());
			$this->duration->EditAttrs["class"] = "form-control";
			$this->duration->EditCustomAttributes = "";
			$this->duration->EditValue2 = ew_HtmlEncode($this->duration->AdvancedSearch->SearchValue2);
			$this->duration->PlaceHolder = ew_RemoveHtml($this->duration->FldCaption());

			// billsec
			$this->billsec->EditAttrs["class"] = "form-control";
			$this->billsec->EditCustomAttributes = "";
			$this->billsec->EditValue = ew_HtmlEncode($this->billsec->AdvancedSearch->SearchValue);
			$this->billsec->PlaceHolder = ew_RemoveHtml($this->billsec->FldCaption());
			$this->billsec->EditAttrs["class"] = "form-control";
			$this->billsec->EditCustomAttributes = "";
			$this->billsec->EditValue2 = ew_HtmlEncode($this->billsec->AdvancedSearch->SearchValue2);
			$this->billsec->PlaceHolder = ew_RemoveHtml($this->billsec->FldCaption());

			// disposition
			$this->disposition->EditAttrs["class"] = "form-control";
			$this->disposition->EditCustomAttributes = "";
			$this->disposition->EditValue = ew_HtmlEncode($this->disposition->AdvancedSearch->SearchValue);
			$this->disposition->PlaceHolder = ew_RemoveHtml($this->disposition->FldCaption());
			$this->disposition->EditAttrs["class"] = "form-control";
			$this->disposition->EditCustomAttributes = "";
			$this->disposition->EditValue2 = ew_HtmlEncode($this->disposition->AdvancedSearch->SearchValue2);
			$this->disposition->PlaceHolder = ew_RemoveHtml($this->disposition->FldCaption());

			// outbound_cnum
			$this->outbound_cnum->EditAttrs["class"] = "form-control";
			$this->outbound_cnum->EditCustomAttributes = "";
			$this->outbound_cnum->EditValue = ew_HtmlEncode($this->outbound_cnum->AdvancedSearch->SearchValue);
			$this->outbound_cnum->PlaceHolder = ew_RemoveHtml($this->outbound_cnum->FldCaption());
			$this->outbound_cnum->EditAttrs["class"] = "form-control";
			$this->outbound_cnum->EditCustomAttributes = "";
			$this->outbound_cnum->EditValue2 = ew_HtmlEncode($this->outbound_cnum->AdvancedSearch->SearchValue2);
			$this->outbound_cnum->PlaceHolder = ew_RemoveHtml($this->outbound_cnum->FldCaption());

			// play
			$this->play->EditAttrs["class"] = "form-control";
			$this->play->EditCustomAttributes = "";
			$this->play->EditValue = ew_HtmlEncode($this->play->AdvancedSearch->SearchValue);
			$this->play->PlaceHolder = ew_RemoveHtml($this->play->FldCaption());
			$this->play->EditAttrs["class"] = "form-control";
			$this->play->EditCustomAttributes = "";
			$this->play->EditValue2 = ew_HtmlEncode($this->play->AdvancedSearch->SearchValue2);
			$this->play->PlaceHolder = ew_RemoveHtml($this->play->FldCaption());

			// recordingfile
			$this->recordingfile->EditAttrs["class"] = "form-control";
			$this->recordingfile->EditCustomAttributes = "";
			$this->recordingfile->EditValue = ew_HtmlEncode($this->recordingfile->AdvancedSearch->SearchValue);
			$this->recordingfile->PlaceHolder = ew_RemoveHtml($this->recordingfile->FldCaption());
			$this->recordingfile->EditAttrs["class"] = "form-control";
			$this->recordingfile->EditCustomAttributes = "";
			$this->recordingfile->EditValue2 = ew_HtmlEncode($this->recordingfile->AdvancedSearch->SearchValue2);
			$this->recordingfile->PlaceHolder = ew_RemoveHtml($this->recordingfile->FldCaption());

			// recording_name
			$this->recording_name->EditAttrs["class"] = "form-control";
			$this->recording_name->EditCustomAttributes = "";
			$this->recording_name->EditValue = ew_HtmlEncode($this->recording_name->AdvancedSearch->SearchValue);
			$this->recording_name->PlaceHolder = ew_RemoveHtml($this->recording_name->FldCaption());
			$this->recording_name->EditAttrs["class"] = "form-control";
			$this->recording_name->EditCustomAttributes = "";
			$this->recording_name->EditValue2 = ew_HtmlEncode($this->recording_name->AdvancedSearch->SearchValue2);
			$this->recording_name->PlaceHolder = ew_RemoveHtml($this->recording_name->FldCaption());

			// clid
			$this->clid->EditAttrs["class"] = "form-control";
			$this->clid->EditCustomAttributes = "";
			$this->clid->EditValue = ew_HtmlEncode($this->clid->AdvancedSearch->SearchValue);
			$this->clid->PlaceHolder = ew_RemoveHtml($this->clid->FldCaption());
			$this->clid->EditAttrs["class"] = "form-control";
			$this->clid->EditCustomAttributes = "";
			$this->clid->EditValue2 = ew_HtmlEncode($this->clid->AdvancedSearch->SearchValue2);
			$this->clid->PlaceHolder = ew_RemoveHtml($this->clid->FldCaption());

			// src
			$this->src->EditAttrs["class"] = "form-control";
			$this->src->EditCustomAttributes = "";
			$this->src->EditValue = ew_HtmlEncode($this->src->AdvancedSearch->SearchValue);
			$this->src->PlaceHolder = ew_RemoveHtml($this->src->FldCaption());
			$this->src->EditAttrs["class"] = "form-control";
			$this->src->EditCustomAttributes = "";
			$this->src->EditValue2 = ew_HtmlEncode($this->src->AdvancedSearch->SearchValue2);
			$this->src->PlaceHolder = ew_RemoveHtml($this->src->FldCaption());

			// dcontext
			$this->dcontext->EditAttrs["class"] = "form-control";
			$this->dcontext->EditCustomAttributes = "";
			$this->dcontext->EditValue = ew_HtmlEncode($this->dcontext->AdvancedSearch->SearchValue);
			$this->dcontext->PlaceHolder = ew_RemoveHtml($this->dcontext->FldCaption());
			$this->dcontext->EditAttrs["class"] = "form-control";
			$this->dcontext->EditCustomAttributes = "";
			$this->dcontext->EditValue2 = ew_HtmlEncode($this->dcontext->AdvancedSearch->SearchValue2);
			$this->dcontext->PlaceHolder = ew_RemoveHtml($this->dcontext->FldCaption());

			// channel
			$this->channel->EditAttrs["class"] = "form-control";
			$this->channel->EditCustomAttributes = "";
			$this->channel->EditValue = ew_HtmlEncode($this->channel->AdvancedSearch->SearchValue);
			$this->channel->PlaceHolder = ew_RemoveHtml($this->channel->FldCaption());
			$this->channel->EditAttrs["class"] = "form-control";
			$this->channel->EditCustomAttributes = "";
			$this->channel->EditValue2 = ew_HtmlEncode($this->channel->AdvancedSearch->SearchValue2);
			$this->channel->PlaceHolder = ew_RemoveHtml($this->channel->FldCaption());

			// dstchannel
			$this->dstchannel->EditAttrs["class"] = "form-control";
			$this->dstchannel->EditCustomAttributes = "";
			$this->dstchannel->EditValue = ew_HtmlEncode($this->dstchannel->AdvancedSearch->SearchValue);
			$this->dstchannel->PlaceHolder = ew_RemoveHtml($this->dstchannel->FldCaption());
			$this->dstchannel->EditAttrs["class"] = "form-control";
			$this->dstchannel->EditCustomAttributes = "";
			$this->dstchannel->EditValue2 = ew_HtmlEncode($this->dstchannel->AdvancedSearch->SearchValue2);
			$this->dstchannel->PlaceHolder = ew_RemoveHtml($this->dstchannel->FldCaption());

			// lastapp
			$this->lastapp->EditAttrs["class"] = "form-control";
			$this->lastapp->EditCustomAttributes = "";
			$this->lastapp->EditValue = ew_HtmlEncode($this->lastapp->AdvancedSearch->SearchValue);
			$this->lastapp->PlaceHolder = ew_RemoveHtml($this->lastapp->FldCaption());
			$this->lastapp->EditAttrs["class"] = "form-control";
			$this->lastapp->EditCustomAttributes = "";
			$this->lastapp->EditValue2 = ew_HtmlEncode($this->lastapp->AdvancedSearch->SearchValue2);
			$this->lastapp->PlaceHolder = ew_RemoveHtml($this->lastapp->FldCaption());

			// lastdata
			$this->lastdata->EditAttrs["class"] = "form-control";
			$this->lastdata->EditCustomAttributes = "";
			$this->lastdata->EditValue = ew_HtmlEncode($this->lastdata->AdvancedSearch->SearchValue);
			$this->lastdata->PlaceHolder = ew_RemoveHtml($this->lastdata->FldCaption());
			$this->lastdata->EditAttrs["class"] = "form-control";
			$this->lastdata->EditCustomAttributes = "";
			$this->lastdata->EditValue2 = ew_HtmlEncode($this->lastdata->AdvancedSearch->SearchValue2);
			$this->lastdata->PlaceHolder = ew_RemoveHtml($this->lastdata->FldCaption());

			// amaflags
			$this->amaflags->EditAttrs["class"] = "form-control";
			$this->amaflags->EditCustomAttributes = "";
			$this->amaflags->EditValue = ew_HtmlEncode($this->amaflags->AdvancedSearch->SearchValue);
			$this->amaflags->PlaceHolder = ew_RemoveHtml($this->amaflags->FldCaption());
			$this->amaflags->EditAttrs["class"] = "form-control";
			$this->amaflags->EditCustomAttributes = "";
			$this->amaflags->EditValue2 = ew_HtmlEncode($this->amaflags->AdvancedSearch->SearchValue2);
			$this->amaflags->PlaceHolder = ew_RemoveHtml($this->amaflags->FldCaption());

			// accountcode
			$this->accountcode->EditAttrs["class"] = "form-control";
			$this->accountcode->EditCustomAttributes = "";
			$this->accountcode->EditValue = ew_HtmlEncode($this->accountcode->AdvancedSearch->SearchValue);
			$this->accountcode->PlaceHolder = ew_RemoveHtml($this->accountcode->FldCaption());
			$this->accountcode->EditAttrs["class"] = "form-control";
			$this->accountcode->EditCustomAttributes = "";
			$this->accountcode->EditValue2 = ew_HtmlEncode($this->accountcode->AdvancedSearch->SearchValue2);
			$this->accountcode->PlaceHolder = ew_RemoveHtml($this->accountcode->FldCaption());

			// userfield
			$this->userfield->EditAttrs["class"] = "form-control";
			$this->userfield->EditCustomAttributes = "";
			$this->userfield->EditValue = ew_HtmlEncode($this->userfield->AdvancedSearch->SearchValue);
			$this->userfield->PlaceHolder = ew_RemoveHtml($this->userfield->FldCaption());
			$this->userfield->EditAttrs["class"] = "form-control";
			$this->userfield->EditCustomAttributes = "";
			$this->userfield->EditValue2 = ew_HtmlEncode($this->userfield->AdvancedSearch->SearchValue2);
			$this->userfield->PlaceHolder = ew_RemoveHtml($this->userfield->FldCaption());

			// did
			$this->did->EditAttrs["class"] = "form-control";
			$this->did->EditCustomAttributes = "";
			$this->did->EditValue = ew_HtmlEncode($this->did->AdvancedSearch->SearchValue);
			$this->did->PlaceHolder = ew_RemoveHtml($this->did->FldCaption());
			$this->did->EditAttrs["class"] = "form-control";
			$this->did->EditCustomAttributes = "";
			$this->did->EditValue2 = ew_HtmlEncode($this->did->AdvancedSearch->SearchValue2);
			$this->did->PlaceHolder = ew_RemoveHtml($this->did->FldCaption());

			// outbound_cnam
			$this->outbound_cnam->EditAttrs["class"] = "form-control";
			$this->outbound_cnam->EditCustomAttributes = "";
			$this->outbound_cnam->EditValue = ew_HtmlEncode($this->outbound_cnam->AdvancedSearch->SearchValue);
			$this->outbound_cnam->PlaceHolder = ew_RemoveHtml($this->outbound_cnam->FldCaption());
			$this->outbound_cnam->EditAttrs["class"] = "form-control";
			$this->outbound_cnam->EditCustomAttributes = "";
			$this->outbound_cnam->EditValue2 = ew_HtmlEncode($this->outbound_cnam->AdvancedSearch->SearchValue2);
			$this->outbound_cnam->PlaceHolder = ew_RemoveHtml($this->outbound_cnam->FldCaption());

			// dst_cnam
			$this->dst_cnam->EditAttrs["class"] = "form-control";
			$this->dst_cnam->EditCustomAttributes = "";
			$this->dst_cnam->EditValue = ew_HtmlEncode($this->dst_cnam->AdvancedSearch->SearchValue);
			$this->dst_cnam->PlaceHolder = ew_RemoveHtml($this->dst_cnam->FldCaption());
			$this->dst_cnam->EditAttrs["class"] = "form-control";
			$this->dst_cnam->EditCustomAttributes = "";
			$this->dst_cnam->EditValue2 = ew_HtmlEncode($this->dst_cnam->AdvancedSearch->SearchValue2);
			$this->dst_cnam->PlaceHolder = ew_RemoveHtml($this->dst_cnam->FldCaption());

			// linkedid
			$this->linkedid->EditAttrs["class"] = "form-control";
			$this->linkedid->EditCustomAttributes = "";
			$this->linkedid->EditValue = ew_HtmlEncode($this->linkedid->AdvancedSearch->SearchValue);
			$this->linkedid->PlaceHolder = ew_RemoveHtml($this->linkedid->FldCaption());
			$this->linkedid->EditAttrs["class"] = "form-control";
			$this->linkedid->EditCustomAttributes = "";
			$this->linkedid->EditValue2 = ew_HtmlEncode($this->linkedid->AdvancedSearch->SearchValue2);
			$this->linkedid->PlaceHolder = ew_RemoveHtml($this->linkedid->FldCaption());

			// peeraccount
			$this->peeraccount->EditAttrs["class"] = "form-control";
			$this->peeraccount->EditCustomAttributes = "";
			$this->peeraccount->EditValue = ew_HtmlEncode($this->peeraccount->AdvancedSearch->SearchValue);
			$this->peeraccount->PlaceHolder = ew_RemoveHtml($this->peeraccount->FldCaption());
			$this->peeraccount->EditAttrs["class"] = "form-control";
			$this->peeraccount->EditCustomAttributes = "";
			$this->peeraccount->EditValue2 = ew_HtmlEncode($this->peeraccount->AdvancedSearch->SearchValue2);
			$this->peeraccount->PlaceHolder = ew_RemoveHtml($this->peeraccount->FldCaption());

			// sequence
			$this->sequence->EditAttrs["class"] = "form-control";
			$this->sequence->EditCustomAttributes = "";
			$this->sequence->EditValue = ew_HtmlEncode($this->sequence->AdvancedSearch->SearchValue);
			$this->sequence->PlaceHolder = ew_RemoveHtml($this->sequence->FldCaption());
			$this->sequence->EditAttrs["class"] = "form-control";
			$this->sequence->EditCustomAttributes = "";
			$this->sequence->EditValue2 = ew_HtmlEncode($this->sequence->AdvancedSearch->SearchValue2);
			$this->sequence->PlaceHolder = ew_RemoveHtml($this->sequence->FldCaption());
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate search
	function ValidateSearch() {
		global $gsSearchError;

		// Initialize
		$gsSearchError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return TRUE;
		if (!ew_CheckDate($this->calldate->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->calldate->FldErrMsg());
		}
		if (!ew_CheckDate($this->calldate->AdvancedSearch->SearchValue2)) {
			ew_AddMessage($gsSearchError, $this->calldate->FldErrMsg());
		}
		if (!ew_CheckInteger($this->duration->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->duration->FldErrMsg());
		}
		if (!ew_CheckInteger($this->duration->AdvancedSearch->SearchValue2)) {
			ew_AddMessage($gsSearchError, $this->duration->FldErrMsg());
		}
		if (!ew_CheckInteger($this->billsec->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->billsec->FldErrMsg());
		}
		if (!ew_CheckInteger($this->billsec->AdvancedSearch->SearchValue2)) {
			ew_AddMessage($gsSearchError, $this->billsec->FldErrMsg());
		}
		if (!ew_CheckInteger($this->amaflags->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->amaflags->FldErrMsg());
		}
		if (!ew_CheckInteger($this->amaflags->AdvancedSearch->SearchValue2)) {
			ew_AddMessage($gsSearchError, $this->amaflags->FldErrMsg());
		}
		if (!ew_CheckInteger($this->sequence->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->sequence->FldErrMsg());
		}
		if (!ew_CheckInteger($this->sequence->AdvancedSearch->SearchValue2)) {
			ew_AddMessage($gsSearchError, $this->sequence->FldErrMsg());
		}

		// Return validate result
		$ValidateSearch = ($gsSearchError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateSearch = $ValidateSearch && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsSearchError, $sFormCustomError);
		}
		return $ValidateSearch;
	}

	// Load advanced search
	function LoadAdvancedSearch() {
		$this->calldate->AdvancedSearch->Load();
		$this->uniqueid->AdvancedSearch->Load();
		$this->cnam->AdvancedSearch->Load();
		$this->cnum->AdvancedSearch->Load();
		$this->dst->AdvancedSearch->Load();
		$this->duration->AdvancedSearch->Load();
		$this->billsec->AdvancedSearch->Load();
		$this->disposition->AdvancedSearch->Load();
		$this->outbound_cnum->AdvancedSearch->Load();
		$this->play->AdvancedSearch->Load();
		$this->recordingfile->AdvancedSearch->Load();
		$this->recording_name->AdvancedSearch->Load();
		$this->clid->AdvancedSearch->Load();
		$this->src->AdvancedSearch->Load();
		$this->dcontext->AdvancedSearch->Load();
		$this->channel->AdvancedSearch->Load();
		$this->dstchannel->AdvancedSearch->Load();
		$this->lastapp->AdvancedSearch->Load();
		$this->lastdata->AdvancedSearch->Load();
		$this->amaflags->AdvancedSearch->Load();
		$this->accountcode->AdvancedSearch->Load();
		$this->userfield->AdvancedSearch->Load();
		$this->did->AdvancedSearch->Load();
		$this->outbound_cnam->AdvancedSearch->Load();
		$this->dst_cnam->AdvancedSearch->Load();
		$this->linkedid->AdvancedSearch->Load();
		$this->peeraccount->AdvancedSearch->Load();
		$this->sequence->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("cdrlist.php"), "", $this->TableVar, TRUE);
		$PageId = "search";
		$Breadcrumb->Add("search", $PageId, $url);
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($cdr_search)) $cdr_search = new ccdr_search();

// Page init
$cdr_search->Page_Init();

// Page main
$cdr_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$cdr_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "search";
<?php if ($cdr_search->IsModal) { ?>
var CurrentAdvancedSearchForm = fcdrsearch = new ew_Form("fcdrsearch", "search");
<?php } else { ?>
var CurrentForm = fcdrsearch = new ew_Form("fcdrsearch", "search");
<?php } ?>

// Form_CustomValidate event
fcdrsearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcdrsearch.ValidateRequired = true;
<?php } else { ?>
fcdrsearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search
// Validate function for search

fcdrsearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	var infix = "";
	elm = this.GetElements("x" + infix + "_calldate");
	if (elm && !ew_CheckDate(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($cdr->calldate->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_duration");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($cdr->duration->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_billsec");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($cdr->billsec->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_recordingfile");
	if (elm && typeof(isMediaElementjs) == "function" && !isMediaElementjs(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($cdr->recordingfile->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_amaflags");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($cdr->amaflags->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_sequence");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($cdr->sequence->FldErrMsg()) ?>");

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$cdr_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $cdr_search->ShowPageHeader(); ?>
<?php
$cdr_search->ShowMessage();
?>
<form name="fcdrsearch" id="fcdrsearch" class="<?php echo $cdr_search->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($cdr_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $cdr_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="cdr">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($cdr_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div>
<?php if ($cdr->calldate->Visible) { // calldate ?>
	<div id="r_calldate" class="form-group">
		<label for="x_calldate" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_calldate"><?php echo $cdr->calldate->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->calldate->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_calldate" id="z_calldate" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="BETWEEN"<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_calldate">
<input type="text" data-table="cdr" data-field="x_calldate" data-format="9" name="x_calldate" id="x_calldate" size="22" placeholder="<?php echo ew_HtmlEncode($cdr->calldate->getPlaceHolder()) ?>" value="<?php echo $cdr->calldate->EditValue ?>"<?php echo $cdr->calldate->EditAttributes() ?>>
<?php if (!$cdr->calldate->ReadOnly && !$cdr->calldate->Disabled && !isset($cdr->calldate->EditAttrs["readonly"]) && !isset($cdr->calldate->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fcdrsearch", "x_calldate", "%Y-%m-%d %H:%M:%S");
</script>
<?php } ?>
</span>
			<span class="ewSearchCond btw0_calldate"><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_calldate" value="AND"<?php if ($cdr->calldate->AdvancedSearch->SearchCondition <> "OR") echo " checked" ?>><?php echo $Language->Phrase("AND") ?></label><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_calldate" value="OR"<?php if ($cdr->calldate->AdvancedSearch->SearchCondition == "OR") echo " checked" ?>><?php echo $Language->Phrase("OR") ?></label>&nbsp;</span>
			<span class="ewSearchCond btw1_calldate">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span class="ewSearchOperator btw0_calldate"><select name="w_calldate" id="w_calldate" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator2 == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator2 == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator2 == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator2 == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator2 == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator2 == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option></select></span>
			<span id="e2_cdr_calldate">
<input type="text" data-table="cdr" data-field="x_calldate" data-format="9" name="y_calldate" id="y_calldate" size="22" placeholder="<?php echo ew_HtmlEncode($cdr->calldate->getPlaceHolder()) ?>" value="<?php echo $cdr->calldate->EditValue2 ?>"<?php echo $cdr->calldate->EditAttributes() ?>>
<?php if (!$cdr->calldate->ReadOnly && !$cdr->calldate->Disabled && !isset($cdr->calldate->EditAttrs["readonly"]) && !isset($cdr->calldate->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fcdrsearch", "y_calldate", "%Y-%m-%d %H:%M:%S");
</script>
<?php } ?>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->uniqueid->Visible) { // uniqueid ?>
	<div id="r_uniqueid" class="form-group">
		<label for="x_uniqueid" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_uniqueid"><?php echo $cdr->uniqueid->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->uniqueid->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_uniqueid" id="z_uniqueid" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_uniqueid">
<input type="text" data-table="cdr" data-field="x_uniqueid" name="x_uniqueid" id="x_uniqueid" size="30" maxlength="32" placeholder="<?php echo ew_HtmlEncode($cdr->uniqueid->getPlaceHolder()) ?>" value="<?php echo $cdr->uniqueid->EditValue ?>"<?php echo $cdr->uniqueid->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw0_uniqueid"><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_uniqueid" value="AND"<?php if ($cdr->uniqueid->AdvancedSearch->SearchCondition <> "OR") echo " checked" ?>><?php echo $Language->Phrase("AND") ?></label><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_uniqueid" value="OR"<?php if ($cdr->uniqueid->AdvancedSearch->SearchCondition == "OR") echo " checked" ?>><?php echo $Language->Phrase("OR") ?></label>&nbsp;</span>
			<span class="ewSearchCond btw1_uniqueid">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span class="ewSearchOperator btw0_uniqueid"><select name="w_uniqueid" id="w_uniqueid" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator2 == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator2 == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator2 == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator2 == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator2 == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator2 == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator2 == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator2 == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator2 == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->uniqueid->AdvancedSearch->SearchOperator2 == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option></select></span>
			<span id="e2_cdr_uniqueid">
<input type="text" data-table="cdr" data-field="x_uniqueid" name="y_uniqueid" id="y_uniqueid" size="30" maxlength="32" placeholder="<?php echo ew_HtmlEncode($cdr->uniqueid->getPlaceHolder()) ?>" value="<?php echo $cdr->uniqueid->EditValue2 ?>"<?php echo $cdr->uniqueid->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->cnam->Visible) { // cnam ?>
	<div id="r_cnam" class="form-group">
		<label for="x_cnam" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_cnam"><?php echo $cdr->cnam->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->cnam->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_cnam" id="z_cnam" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_cnam">
<input type="text" data-table="cdr" data-field="x_cnam" name="x_cnam" id="x_cnam" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->cnam->getPlaceHolder()) ?>" value="<?php echo $cdr->cnam->EditValue ?>"<?php echo $cdr->cnam->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw0_cnam"><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_cnam" value="AND"<?php if ($cdr->cnam->AdvancedSearch->SearchCondition <> "OR") echo " checked" ?>><?php echo $Language->Phrase("AND") ?></label><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_cnam" value="OR"<?php if ($cdr->cnam->AdvancedSearch->SearchCondition == "OR") echo " checked" ?>><?php echo $Language->Phrase("OR") ?></label>&nbsp;</span>
			<span class="ewSearchCond btw1_cnam">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span class="ewSearchOperator btw0_cnam"><select name="w_cnam" id="w_cnam" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option></select></span>
			<span id="e2_cdr_cnam">
<input type="text" data-table="cdr" data-field="x_cnam" name="y_cnam" id="y_cnam" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->cnam->getPlaceHolder()) ?>" value="<?php echo $cdr->cnam->EditValue2 ?>"<?php echo $cdr->cnam->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->cnum->Visible) { // cnum ?>
	<div id="r_cnum" class="form-group">
		<label for="x_cnum" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_cnum"><?php echo $cdr->cnum->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->cnum->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_cnum" id="z_cnum" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_cnum">
<input type="text" data-table="cdr" data-field="x_cnum" name="x_cnum" id="x_cnum" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->cnum->getPlaceHolder()) ?>" value="<?php echo $cdr->cnum->EditValue ?>"<?php echo $cdr->cnum->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw0_cnum"><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_cnum" value="AND"<?php if ($cdr->cnum->AdvancedSearch->SearchCondition <> "OR") echo " checked" ?>><?php echo $Language->Phrase("AND") ?></label><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_cnum" value="OR"<?php if ($cdr->cnum->AdvancedSearch->SearchCondition == "OR") echo " checked" ?>><?php echo $Language->Phrase("OR") ?></label>&nbsp;</span>
			<span class="ewSearchCond btw1_cnum">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span class="ewSearchOperator btw0_cnum"><select name="w_cnum" id="w_cnum" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option></select></span>
			<span id="e2_cdr_cnum">
<input type="text" data-table="cdr" data-field="x_cnum" name="y_cnum" id="y_cnum" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->cnum->getPlaceHolder()) ?>" value="<?php echo $cdr->cnum->EditValue2 ?>"<?php echo $cdr->cnum->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->dst->Visible) { // dst ?>
	<div id="r_dst" class="form-group">
		<label for="x_dst" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_dst"><?php echo $cdr->dst->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->dst->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_dst" id="z_dst" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_dst">
<input type="text" data-table="cdr" data-field="x_dst" name="x_dst" id="x_dst" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->dst->getPlaceHolder()) ?>" value="<?php echo $cdr->dst->EditValue ?>"<?php echo $cdr->dst->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw0_dst"><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_dst" value="AND"<?php if ($cdr->dst->AdvancedSearch->SearchCondition <> "OR") echo " checked" ?>><?php echo $Language->Phrase("AND") ?></label><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_dst" value="OR"<?php if ($cdr->dst->AdvancedSearch->SearchCondition == "OR") echo " checked" ?>><?php echo $Language->Phrase("OR") ?></label>&nbsp;</span>
			<span class="ewSearchCond btw1_dst">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span class="ewSearchOperator btw0_dst"><select name="w_dst" id="w_dst" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option></select></span>
			<span id="e2_cdr_dst">
<input type="text" data-table="cdr" data-field="x_dst" name="y_dst" id="y_dst" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->dst->getPlaceHolder()) ?>" value="<?php echo $cdr->dst->EditValue2 ?>"<?php echo $cdr->dst->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->duration->Visible) { // duration ?>
	<div id="r_duration" class="form-group">
		<label for="x_duration" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_duration"><?php echo $cdr->duration->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->duration->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_duration" id="z_duration" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->duration->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->duration->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->duration->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->duration->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->duration->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->duration->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="BETWEEN"<?php echo ($cdr->duration->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_duration">
<input type="text" data-table="cdr" data-field="x_duration" name="x_duration" id="x_duration" size="30" placeholder="<?php echo ew_HtmlEncode($cdr->duration->getPlaceHolder()) ?>" value="<?php echo $cdr->duration->EditValue ?>"<?php echo $cdr->duration->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_duration" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_duration" class="btw1_duration" style="display: none">
<input type="text" data-table="cdr" data-field="x_duration" name="y_duration" id="y_duration" size="30" placeholder="<?php echo ew_HtmlEncode($cdr->duration->getPlaceHolder()) ?>" value="<?php echo $cdr->duration->EditValue2 ?>"<?php echo $cdr->duration->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->billsec->Visible) { // billsec ?>
	<div id="r_billsec" class="form-group">
		<label for="x_billsec" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_billsec"><?php echo $cdr->billsec->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->billsec->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_billsec" id="z_billsec" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->billsec->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->billsec->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->billsec->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->billsec->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->billsec->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->billsec->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="BETWEEN"<?php echo ($cdr->billsec->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_billsec">
<input type="text" data-table="cdr" data-field="x_billsec" name="x_billsec" id="x_billsec" size="30" placeholder="<?php echo ew_HtmlEncode($cdr->billsec->getPlaceHolder()) ?>" value="<?php echo $cdr->billsec->EditValue ?>"<?php echo $cdr->billsec->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_billsec" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_billsec" class="btw1_billsec" style="display: none">
<input type="text" data-table="cdr" data-field="x_billsec" name="y_billsec" id="y_billsec" size="30" placeholder="<?php echo ew_HtmlEncode($cdr->billsec->getPlaceHolder()) ?>" value="<?php echo $cdr->billsec->EditValue2 ?>"<?php echo $cdr->billsec->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->disposition->Visible) { // disposition ?>
	<div id="r_disposition" class="form-group">
		<label for="x_disposition" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_disposition"><?php echo $cdr->disposition->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->disposition->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_disposition" id="z_disposition" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->disposition->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->disposition->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->disposition->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->disposition->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->disposition->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->disposition->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->disposition->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->disposition->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->disposition->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->disposition->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->disposition->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_disposition">
<input type="text" data-table="cdr" data-field="x_disposition" name="x_disposition" id="x_disposition" size="30" maxlength="45" placeholder="<?php echo ew_HtmlEncode($cdr->disposition->getPlaceHolder()) ?>" value="<?php echo $cdr->disposition->EditValue ?>"<?php echo $cdr->disposition->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_disposition" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_disposition" class="btw1_disposition" style="display: none">
<input type="text" data-table="cdr" data-field="x_disposition" name="y_disposition" id="y_disposition" size="30" maxlength="45" placeholder="<?php echo ew_HtmlEncode($cdr->disposition->getPlaceHolder()) ?>" value="<?php echo $cdr->disposition->EditValue2 ?>"<?php echo $cdr->disposition->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->outbound_cnum->Visible) { // outbound_cnum ?>
	<div id="r_outbound_cnum" class="form-group">
		<label for="x_outbound_cnum" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_outbound_cnum"><?php echo $cdr->outbound_cnum->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->outbound_cnum->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_outbound_cnum" id="z_outbound_cnum" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->outbound_cnum->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->outbound_cnum->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->outbound_cnum->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->outbound_cnum->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->outbound_cnum->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->outbound_cnum->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->outbound_cnum->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->outbound_cnum->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->outbound_cnum->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->outbound_cnum->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->outbound_cnum->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_outbound_cnum">
<input type="text" data-table="cdr" data-field="x_outbound_cnum" name="x_outbound_cnum" id="x_outbound_cnum" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->outbound_cnum->getPlaceHolder()) ?>" value="<?php echo $cdr->outbound_cnum->EditValue ?>"<?php echo $cdr->outbound_cnum->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_outbound_cnum" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_outbound_cnum" class="btw1_outbound_cnum" style="display: none">
<input type="text" data-table="cdr" data-field="x_outbound_cnum" name="y_outbound_cnum" id="y_outbound_cnum" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->outbound_cnum->getPlaceHolder()) ?>" value="<?php echo $cdr->outbound_cnum->EditValue2 ?>"<?php echo $cdr->outbound_cnum->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->play->Visible) { // play ?>
	<div id="r_play" class="form-group">
		<label for="x_play" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_play"><?php echo $cdr->play->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->play->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_play" id="z_play" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->play->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->play->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->play->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->play->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->play->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->play->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->play->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->play->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->play->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->play->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="IS NULL"<?php echo ($cdr->play->AdvancedSearch->SearchOperator == "IS NULL") ? " selected" : "" ?> ><?php echo $Language->Phrase("IS NULL") ?></option><option value="IS NOT NULL"<?php echo ($cdr->play->AdvancedSearch->SearchOperator == "IS NOT NULL") ? " selected" : "" ?> ><?php echo $Language->Phrase("IS NOT NULL") ?></option><option value="BETWEEN"<?php echo ($cdr->play->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_play">
<input type="text" data-table="cdr" data-field="x_play" name="x_play" id="x_play" size="30" maxlength="32" placeholder="<?php echo ew_HtmlEncode($cdr->play->getPlaceHolder()) ?>" value="<?php echo $cdr->play->EditValue ?>"<?php echo $cdr->play->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_play" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_play" class="btw1_play" style="display: none">
<input type="text" data-table="cdr" data-field="x_play" name="y_play" id="y_play" size="30" maxlength="32" placeholder="<?php echo ew_HtmlEncode($cdr->play->getPlaceHolder()) ?>" value="<?php echo $cdr->play->EditValue2 ?>"<?php echo $cdr->play->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->recordingfile->Visible) { // recordingfile ?>
	<div id="r_recordingfile" class="form-group">
		<label for="x_recordingfile" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_recordingfile"><?php echo $cdr->recordingfile->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->recordingfile->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_recordingfile" id="z_recordingfile" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_recordingfile">
<input type="text" data-table="cdr" data-field="x_recordingfile" name="x_recordingfile" id="x_recordingfile" size="60" maxlength="255" placeholder="<?php echo ew_HtmlEncode($cdr->recordingfile->getPlaceHolder()) ?>" value="<?php echo $cdr->recordingfile->EditValue ?>"<?php echo $cdr->recordingfile->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_recordingfile" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_recordingfile" class="btw1_recordingfile" style="display: none">
<input type="text" data-table="cdr" data-field="x_recordingfile" name="y_recordingfile" id="y_recordingfile" size="60" maxlength="255" placeholder="<?php echo ew_HtmlEncode($cdr->recordingfile->getPlaceHolder()) ?>" value="<?php echo $cdr->recordingfile->EditValue2 ?>"<?php echo $cdr->recordingfile->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->recording_name->Visible) { // recording_name ?>
	<div id="r_recording_name" class="form-group">
		<label for="x_recording_name" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_recording_name"><?php echo $cdr->recording_name->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->recording_name->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_recording_name" id="z_recording_name" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="IS NULL"<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator == "IS NULL") ? " selected" : "" ?> ><?php echo $Language->Phrase("IS NULL") ?></option><option value="IS NOT NULL"<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator == "IS NOT NULL") ? " selected" : "" ?> ><?php echo $Language->Phrase("IS NOT NULL") ?></option><option value="BETWEEN"<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_recording_name">
<input type="text" data-table="cdr" data-field="x_recording_name" name="x_recording_name" id="x_recording_name" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($cdr->recording_name->getPlaceHolder()) ?>" value="<?php echo $cdr->recording_name->EditValue ?>"<?php echo $cdr->recording_name->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw0_recording_name"><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_recording_name" value="AND"<?php if ($cdr->recording_name->AdvancedSearch->SearchCondition <> "OR") echo " checked" ?>><?php echo $Language->Phrase("AND") ?></label><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_recording_name" value="OR"<?php if ($cdr->recording_name->AdvancedSearch->SearchCondition == "OR") echo " checked" ?>><?php echo $Language->Phrase("OR") ?></label>&nbsp;</span>
			<span class="ewSearchCond btw1_recording_name">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span class="ewSearchOperator btw0_recording_name"><select name="w_recording_name" id="w_recording_name" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator2 == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator2 == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator2 == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator2 == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator2 == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator2 == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator2 == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator2 == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator2 == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator2 == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="IS NULL"<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator2 == "IS NULL") ? " selected" : "" ?> ><?php echo $Language->Phrase("IS NULL") ?></option><option value="IS NOT NULL"<?php echo ($cdr->recording_name->AdvancedSearch->SearchOperator2 == "IS NOT NULL") ? " selected" : "" ?> ><?php echo $Language->Phrase("IS NOT NULL") ?></option></select></span>
			<span id="e2_cdr_recording_name">
<input type="text" data-table="cdr" data-field="x_recording_name" name="y_recording_name" id="y_recording_name" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($cdr->recording_name->getPlaceHolder()) ?>" value="<?php echo $cdr->recording_name->EditValue2 ?>"<?php echo $cdr->recording_name->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->clid->Visible) { // clid ?>
	<div id="r_clid" class="form-group">
		<label for="x_clid" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_clid"><?php echo $cdr->clid->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->clid->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_clid" id="z_clid" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->clid->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->clid->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->clid->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->clid->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->clid->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->clid->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->clid->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->clid->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->clid->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->clid->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->clid->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_clid">
<input type="text" data-table="cdr" data-field="x_clid" name="x_clid" id="x_clid" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->clid->getPlaceHolder()) ?>" value="<?php echo $cdr->clid->EditValue ?>"<?php echo $cdr->clid->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_clid" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_clid" class="btw1_clid" style="display: none">
<input type="text" data-table="cdr" data-field="x_clid" name="y_clid" id="y_clid" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->clid->getPlaceHolder()) ?>" value="<?php echo $cdr->clid->EditValue2 ?>"<?php echo $cdr->clid->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->src->Visible) { // src ?>
	<div id="r_src" class="form-group">
		<label for="x_src" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_src"><?php echo $cdr->src->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->src->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_src" id="z_src" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->src->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->src->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->src->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->src->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->src->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->src->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->src->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->src->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->src->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->src->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->src->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_src">
<input type="text" data-table="cdr" data-field="x_src" name="x_src" id="x_src" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->src->getPlaceHolder()) ?>" value="<?php echo $cdr->src->EditValue ?>"<?php echo $cdr->src->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_src" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_src" class="btw1_src" style="display: none">
<input type="text" data-table="cdr" data-field="x_src" name="y_src" id="y_src" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->src->getPlaceHolder()) ?>" value="<?php echo $cdr->src->EditValue2 ?>"<?php echo $cdr->src->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->dcontext->Visible) { // dcontext ?>
	<div id="r_dcontext" class="form-group">
		<label for="x_dcontext" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_dcontext"><?php echo $cdr->dcontext->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->dcontext->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_dcontext" id="z_dcontext" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->dcontext->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->dcontext->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->dcontext->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->dcontext->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->dcontext->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->dcontext->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->dcontext->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->dcontext->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->dcontext->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->dcontext->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->dcontext->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_dcontext">
<input type="text" data-table="cdr" data-field="x_dcontext" name="x_dcontext" id="x_dcontext" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->dcontext->getPlaceHolder()) ?>" value="<?php echo $cdr->dcontext->EditValue ?>"<?php echo $cdr->dcontext->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_dcontext" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_dcontext" class="btw1_dcontext" style="display: none">
<input type="text" data-table="cdr" data-field="x_dcontext" name="y_dcontext" id="y_dcontext" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->dcontext->getPlaceHolder()) ?>" value="<?php echo $cdr->dcontext->EditValue2 ?>"<?php echo $cdr->dcontext->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->channel->Visible) { // channel ?>
	<div id="r_channel" class="form-group">
		<label for="x_channel" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_channel"><?php echo $cdr->channel->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->channel->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_channel" id="z_channel" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->channel->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->channel->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->channel->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->channel->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->channel->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->channel->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->channel->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->channel->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->channel->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->channel->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->channel->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_channel">
<input type="text" data-table="cdr" data-field="x_channel" name="x_channel" id="x_channel" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->channel->getPlaceHolder()) ?>" value="<?php echo $cdr->channel->EditValue ?>"<?php echo $cdr->channel->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_channel" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_channel" class="btw1_channel" style="display: none">
<input type="text" data-table="cdr" data-field="x_channel" name="y_channel" id="y_channel" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->channel->getPlaceHolder()) ?>" value="<?php echo $cdr->channel->EditValue2 ?>"<?php echo $cdr->channel->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->dstchannel->Visible) { // dstchannel ?>
	<div id="r_dstchannel" class="form-group">
		<label for="x_dstchannel" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_dstchannel"><?php echo $cdr->dstchannel->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->dstchannel->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_dstchannel" id="z_dstchannel" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->dstchannel->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->dstchannel->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->dstchannel->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->dstchannel->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->dstchannel->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->dstchannel->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->dstchannel->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->dstchannel->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->dstchannel->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->dstchannel->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->dstchannel->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_dstchannel">
<input type="text" data-table="cdr" data-field="x_dstchannel" name="x_dstchannel" id="x_dstchannel" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->dstchannel->getPlaceHolder()) ?>" value="<?php echo $cdr->dstchannel->EditValue ?>"<?php echo $cdr->dstchannel->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_dstchannel" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_dstchannel" class="btw1_dstchannel" style="display: none">
<input type="text" data-table="cdr" data-field="x_dstchannel" name="y_dstchannel" id="y_dstchannel" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->dstchannel->getPlaceHolder()) ?>" value="<?php echo $cdr->dstchannel->EditValue2 ?>"<?php echo $cdr->dstchannel->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->lastapp->Visible) { // lastapp ?>
	<div id="r_lastapp" class="form-group">
		<label for="x_lastapp" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_lastapp"><?php echo $cdr->lastapp->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->lastapp->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_lastapp" id="z_lastapp" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->lastapp->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->lastapp->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->lastapp->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->lastapp->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->lastapp->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->lastapp->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->lastapp->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->lastapp->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->lastapp->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->lastapp->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->lastapp->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_lastapp">
<input type="text" data-table="cdr" data-field="x_lastapp" name="x_lastapp" id="x_lastapp" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->lastapp->getPlaceHolder()) ?>" value="<?php echo $cdr->lastapp->EditValue ?>"<?php echo $cdr->lastapp->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_lastapp" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_lastapp" class="btw1_lastapp" style="display: none">
<input type="text" data-table="cdr" data-field="x_lastapp" name="y_lastapp" id="y_lastapp" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->lastapp->getPlaceHolder()) ?>" value="<?php echo $cdr->lastapp->EditValue2 ?>"<?php echo $cdr->lastapp->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->lastdata->Visible) { // lastdata ?>
	<div id="r_lastdata" class="form-group">
		<label for="x_lastdata" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_lastdata"><?php echo $cdr->lastdata->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->lastdata->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_lastdata" id="z_lastdata" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->lastdata->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->lastdata->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->lastdata->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->lastdata->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->lastdata->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->lastdata->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->lastdata->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->lastdata->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->lastdata->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->lastdata->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->lastdata->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_lastdata">
<input type="text" data-table="cdr" data-field="x_lastdata" name="x_lastdata" id="x_lastdata" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->lastdata->getPlaceHolder()) ?>" value="<?php echo $cdr->lastdata->EditValue ?>"<?php echo $cdr->lastdata->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_lastdata" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_lastdata" class="btw1_lastdata" style="display: none">
<input type="text" data-table="cdr" data-field="x_lastdata" name="y_lastdata" id="y_lastdata" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->lastdata->getPlaceHolder()) ?>" value="<?php echo $cdr->lastdata->EditValue2 ?>"<?php echo $cdr->lastdata->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->amaflags->Visible) { // amaflags ?>
	<div id="r_amaflags" class="form-group">
		<label for="x_amaflags" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_amaflags"><?php echo $cdr->amaflags->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->amaflags->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_amaflags" id="z_amaflags" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->amaflags->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->amaflags->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->amaflags->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->amaflags->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->amaflags->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->amaflags->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="BETWEEN"<?php echo ($cdr->amaflags->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_amaflags">
<input type="text" data-table="cdr" data-field="x_amaflags" name="x_amaflags" id="x_amaflags" size="30" placeholder="<?php echo ew_HtmlEncode($cdr->amaflags->getPlaceHolder()) ?>" value="<?php echo $cdr->amaflags->EditValue ?>"<?php echo $cdr->amaflags->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_amaflags" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_amaflags" class="btw1_amaflags" style="display: none">
<input type="text" data-table="cdr" data-field="x_amaflags" name="y_amaflags" id="y_amaflags" size="30" placeholder="<?php echo ew_HtmlEncode($cdr->amaflags->getPlaceHolder()) ?>" value="<?php echo $cdr->amaflags->EditValue2 ?>"<?php echo $cdr->amaflags->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->accountcode->Visible) { // accountcode ?>
	<div id="r_accountcode" class="form-group">
		<label for="x_accountcode" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_accountcode"><?php echo $cdr->accountcode->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->accountcode->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_accountcode" id="z_accountcode" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->accountcode->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->accountcode->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->accountcode->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->accountcode->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->accountcode->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->accountcode->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->accountcode->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->accountcode->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->accountcode->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->accountcode->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->accountcode->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_accountcode">
<input type="text" data-table="cdr" data-field="x_accountcode" name="x_accountcode" id="x_accountcode" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($cdr->accountcode->getPlaceHolder()) ?>" value="<?php echo $cdr->accountcode->EditValue ?>"<?php echo $cdr->accountcode->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_accountcode" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_accountcode" class="btw1_accountcode" style="display: none">
<input type="text" data-table="cdr" data-field="x_accountcode" name="y_accountcode" id="y_accountcode" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($cdr->accountcode->getPlaceHolder()) ?>" value="<?php echo $cdr->accountcode->EditValue2 ?>"<?php echo $cdr->accountcode->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->userfield->Visible) { // userfield ?>
	<div id="r_userfield" class="form-group">
		<label for="x_userfield" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_userfield"><?php echo $cdr->userfield->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->userfield->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_userfield" id="z_userfield" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->userfield->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->userfield->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->userfield->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->userfield->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->userfield->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->userfield->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->userfield->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->userfield->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->userfield->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->userfield->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->userfield->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_userfield">
<input type="text" data-table="cdr" data-field="x_userfield" name="x_userfield" id="x_userfield" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($cdr->userfield->getPlaceHolder()) ?>" value="<?php echo $cdr->userfield->EditValue ?>"<?php echo $cdr->userfield->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_userfield" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_userfield" class="btw1_userfield" style="display: none">
<input type="text" data-table="cdr" data-field="x_userfield" name="y_userfield" id="y_userfield" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($cdr->userfield->getPlaceHolder()) ?>" value="<?php echo $cdr->userfield->EditValue2 ?>"<?php echo $cdr->userfield->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->did->Visible) { // did ?>
	<div id="r_did" class="form-group">
		<label for="x_did" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_did"><?php echo $cdr->did->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->did->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_did" id="z_did" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->did->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->did->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->did->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->did->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->did->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->did->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->did->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->did->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->did->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->did->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->did->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_did">
<input type="text" data-table="cdr" data-field="x_did" name="x_did" id="x_did" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($cdr->did->getPlaceHolder()) ?>" value="<?php echo $cdr->did->EditValue ?>"<?php echo $cdr->did->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_did" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_did" class="btw1_did" style="display: none">
<input type="text" data-table="cdr" data-field="x_did" name="y_did" id="y_did" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($cdr->did->getPlaceHolder()) ?>" value="<?php echo $cdr->did->EditValue2 ?>"<?php echo $cdr->did->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->outbound_cnam->Visible) { // outbound_cnam ?>
	<div id="r_outbound_cnam" class="form-group">
		<label for="x_outbound_cnam" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_outbound_cnam"><?php echo $cdr->outbound_cnam->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->outbound_cnam->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_outbound_cnam" id="z_outbound_cnam" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->outbound_cnam->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->outbound_cnam->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->outbound_cnam->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->outbound_cnam->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->outbound_cnam->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->outbound_cnam->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->outbound_cnam->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->outbound_cnam->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->outbound_cnam->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->outbound_cnam->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->outbound_cnam->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_outbound_cnam">
<input type="text" data-table="cdr" data-field="x_outbound_cnam" name="x_outbound_cnam" id="x_outbound_cnam" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->outbound_cnam->getPlaceHolder()) ?>" value="<?php echo $cdr->outbound_cnam->EditValue ?>"<?php echo $cdr->outbound_cnam->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_outbound_cnam" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_outbound_cnam" class="btw1_outbound_cnam" style="display: none">
<input type="text" data-table="cdr" data-field="x_outbound_cnam" name="y_outbound_cnam" id="y_outbound_cnam" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->outbound_cnam->getPlaceHolder()) ?>" value="<?php echo $cdr->outbound_cnam->EditValue2 ?>"<?php echo $cdr->outbound_cnam->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->dst_cnam->Visible) { // dst_cnam ?>
	<div id="r_dst_cnam" class="form-group">
		<label for="x_dst_cnam" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_dst_cnam"><?php echo $cdr->dst_cnam->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->dst_cnam->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_dst_cnam" id="z_dst_cnam" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->dst_cnam->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->dst_cnam->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->dst_cnam->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->dst_cnam->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->dst_cnam->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->dst_cnam->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->dst_cnam->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->dst_cnam->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->dst_cnam->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->dst_cnam->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->dst_cnam->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_dst_cnam">
<input type="text" data-table="cdr" data-field="x_dst_cnam" name="x_dst_cnam" id="x_dst_cnam" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->dst_cnam->getPlaceHolder()) ?>" value="<?php echo $cdr->dst_cnam->EditValue ?>"<?php echo $cdr->dst_cnam->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_dst_cnam" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_dst_cnam" class="btw1_dst_cnam" style="display: none">
<input type="text" data-table="cdr" data-field="x_dst_cnam" name="y_dst_cnam" id="y_dst_cnam" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->dst_cnam->getPlaceHolder()) ?>" value="<?php echo $cdr->dst_cnam->EditValue2 ?>"<?php echo $cdr->dst_cnam->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->linkedid->Visible) { // linkedid ?>
	<div id="r_linkedid" class="form-group">
		<label for="x_linkedid" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_linkedid"><?php echo $cdr->linkedid->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->linkedid->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_linkedid" id="z_linkedid" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->linkedid->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->linkedid->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->linkedid->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->linkedid->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->linkedid->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->linkedid->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->linkedid->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->linkedid->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->linkedid->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->linkedid->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->linkedid->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_linkedid">
<input type="text" data-table="cdr" data-field="x_linkedid" name="x_linkedid" id="x_linkedid" size="30" maxlength="32" placeholder="<?php echo ew_HtmlEncode($cdr->linkedid->getPlaceHolder()) ?>" value="<?php echo $cdr->linkedid->EditValue ?>"<?php echo $cdr->linkedid->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_linkedid" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_linkedid" class="btw1_linkedid" style="display: none">
<input type="text" data-table="cdr" data-field="x_linkedid" name="y_linkedid" id="y_linkedid" size="30" maxlength="32" placeholder="<?php echo ew_HtmlEncode($cdr->linkedid->getPlaceHolder()) ?>" value="<?php echo $cdr->linkedid->EditValue2 ?>"<?php echo $cdr->linkedid->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->peeraccount->Visible) { // peeraccount ?>
	<div id="r_peeraccount" class="form-group">
		<label for="x_peeraccount" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_peeraccount"><?php echo $cdr->peeraccount->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->peeraccount->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_peeraccount" id="z_peeraccount" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->peeraccount->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->peeraccount->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->peeraccount->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->peeraccount->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->peeraccount->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->peeraccount->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->peeraccount->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->peeraccount->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->peeraccount->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->peeraccount->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->peeraccount->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_peeraccount">
<input type="text" data-table="cdr" data-field="x_peeraccount" name="x_peeraccount" id="x_peeraccount" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->peeraccount->getPlaceHolder()) ?>" value="<?php echo $cdr->peeraccount->EditValue ?>"<?php echo $cdr->peeraccount->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_peeraccount" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_peeraccount" class="btw1_peeraccount" style="display: none">
<input type="text" data-table="cdr" data-field="x_peeraccount" name="y_peeraccount" id="y_peeraccount" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->peeraccount->getPlaceHolder()) ?>" value="<?php echo $cdr->peeraccount->EditValue2 ?>"<?php echo $cdr->peeraccount->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
<?php if ($cdr->sequence->Visible) { // sequence ?>
	<div id="r_sequence" class="form-group">
		<label for="x_sequence" class="<?php echo $cdr_search->SearchLabelClass ?>"><span id="elh_cdr_sequence"><?php echo $cdr->sequence->FldCaption() ?></span>	
		</label>
		<div class="<?php echo $cdr_search->SearchRightColumnClass ?>"><div<?php echo $cdr->sequence->CellAttributes() ?>>
		<span class="ewSearchOperator"><select name="z_sequence" id="z_sequence" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->sequence->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->sequence->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->sequence->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->sequence->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->sequence->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->sequence->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="BETWEEN"<?php echo ($cdr->sequence->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
			<span id="el_cdr_sequence">
<input type="text" data-table="cdr" data-field="x_sequence" name="x_sequence" id="x_sequence" size="30" placeholder="<?php echo ew_HtmlEncode($cdr->sequence->getPlaceHolder()) ?>" value="<?php echo $cdr->sequence->EditValue ?>"<?php echo $cdr->sequence->EditAttributes() ?>>
</span>
			<span class="ewSearchCond btw1_sequence" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
			<span id="e2_cdr_sequence" class="btw1_sequence" style="display: none">
<input type="text" data-table="cdr" data-field="x_sequence" name="y_sequence" id="y_sequence" size="30" placeholder="<?php echo ew_HtmlEncode($cdr->sequence->getPlaceHolder()) ?>" value="<?php echo $cdr->sequence->EditValue2 ?>"<?php echo $cdr->sequence->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } ?>
</div>
<?php if (!$cdr_search->IsModal) { ?>
<div class="form-group">
	<div class="col-sm-offset-3 col-sm-9">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("Search") ?></button>
<button class="btn btn-default ewButton" name="btnReset" id="btnReset" type="button" onclick="ew_ClearForm(this.form);"><?php echo $Language->Phrase("Reset") ?></button>
	</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fcdrsearch.Init();
</script>
<?php
$cdr_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$cdr_search->Page_Terminate();
?>
