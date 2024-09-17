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

$cdr_view = NULL; // Initialize page object first

class ccdr_view extends ccdr {

	// Page ID
	var $PageID = 'view';

	// Project ID
	var $ProjectID = "{274CC91E-1C95-40BB-9BB8-39D2A070EA8E}";

	// Table name
	var $TableName = 'cdr';

	// Page object name
	var $PageObjName = 'cdr_view';

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

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Custom export
	var $ExportExcelCustom = FALSE;
	var $ExportWordCustom = FALSE;
	var $ExportPdfCustom = FALSE;
	var $ExportEmailCustom = FALSE;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiUpdateUrl;
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
		$KeyUrl = "";
		if (@$_GET["uniqueid"] <> "") {
			$this->RecKey["uniqueid"] = $_GET["uniqueid"];
			$KeyUrl .= "&amp;uniqueid=" . urlencode($this->RecKey["uniqueid"]);
		}
		$this->ExportPrintUrl = $this->PageUrl() . "export=print" . $KeyUrl;
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html" . $KeyUrl;
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel" . $KeyUrl;
		$this->ExportWordUrl = $this->PageUrl() . "export=word" . $KeyUrl;
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml" . $KeyUrl;
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv" . $KeyUrl;
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf" . $KeyUrl;

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'view', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'cdr', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect($this->DBID);

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
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
		if (!$Security->CanView()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage(ew_DeniedMsg()); // Set no permission
			if ($Security->CanList())
				$this->Page_Terminate(ew_GetUrl("cdrlist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Get export parameters
		$custom = "";
		if (@$_GET["export"] <> "") {
			$this->Export = $_GET["export"];
			$custom = @$_GET["custom"];
		} elseif (@$_POST["export"] <> "") {
			$this->Export = $_POST["export"];
			$custom = @$_POST["custom"];
		} elseif (ew_IsHttpPost()) {
			if (@$_POST["exporttype"] <> "")
				$this->Export = $_POST["exporttype"];
			$custom = @$_POST["custom"];
		} else {
			$this->setExportReturnUrl(ew_CurrentUrl());
		}
		$gsExportFile = $this->TableVar; // Get export file, used in header
		if (@$_GET["uniqueid"] <> "") {
			if ($gsExportFile <> "") $gsExportFile .= "_";
			$gsExportFile .= ew_StripSlashes($_GET["uniqueid"]);
		}

		// Get custom export parameters
		if ($this->Export <> "" && $custom <> "") {
			$this->CustomExport = $this->Export;
			$this->Export = "print";
		}
		$gsCustomExport = $this->CustomExport;
		$gsExport = $this->Export; // Get export parameter, used in header

		// Update Export URLs
		if (defined("EW_USE_PHPEXCEL"))
			$this->ExportExcelCustom = FALSE;
		if ($this->ExportExcelCustom)
			$this->ExportExcelUrl .= "&amp;custom=1";
		if (defined("EW_USE_PHPWORD"))
			$this->ExportWordCustom = FALSE;
		if ($this->ExportWordCustom)
			$this->ExportWordUrl .= "&amp;custom=1";
		if ($this->ExportPdfCustom)
			$this->ExportPdfUrl .= "&amp;custom=1";
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Setup export options
		$this->SetupExportOptions();

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
	var $ExportOptions; // Export options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 1;
	var $DbMasterFilter;
	var $DbDetailFilter;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $RecCnt;
	var $RecKey = array();
	var $Recordset;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Load current record
		$bLoadCurrentRecord = FALSE;
		$sReturnUrl = "";
		$bMatchRecord = FALSE;

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET["uniqueid"] <> "") {
				$this->uniqueid->setQueryStringValue($_GET["uniqueid"]);
				$this->RecKey["uniqueid"] = $this->uniqueid->QueryStringValue;
			} elseif (@$_POST["uniqueid"] <> "") {
				$this->uniqueid->setFormValue($_POST["uniqueid"]);
				$this->RecKey["uniqueid"] = $this->uniqueid->FormValue;
			} else {
				$bLoadCurrentRecord = TRUE;
			}

			// Get action
			$this->CurrentAction = "I"; // Display form
			switch ($this->CurrentAction) {
				case "I": // Get a record to display
					$this->StartRec = 1; // Initialize start position
					if ($this->Recordset = $this->LoadRecordset()) // Load records
						$this->TotalRecs = $this->Recordset->RecordCount(); // Get record count
					if ($this->TotalRecs <= 0) { // No record found
						if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
						$this->Page_Terminate("cdrlist.php"); // Return to list page
					} elseif ($bLoadCurrentRecord) { // Load current record position
						$this->SetUpStartRec(); // Set up start record position

						// Point to current record
						if (intval($this->StartRec) <= intval($this->TotalRecs)) {
							$bMatchRecord = TRUE;
							$this->Recordset->Move($this->StartRec-1);
						}
					} else { // Match key values
						while (!$this->Recordset->EOF) {
							if (strval($this->uniqueid->CurrentValue) == strval($this->Recordset->fields('uniqueid'))) {
								$this->setStartRecordNumber($this->StartRec); // Save record position
								$bMatchRecord = TRUE;
								break;
							} else {
								$this->StartRec++;
								$this->Recordset->MoveNext();
							}
						}
					}
					if (!$bMatchRecord) {
						if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
						$sReturnUrl = "cdrlist.php"; // No matching record, return to list
					} else {
						$this->LoadRowValues($this->Recordset); // Load row values
					}
			}

			// Export data only
			if ($this->CustomExport == "" && in_array($this->Export, array("html","word","excel","xml","csv","email","pdf"))) {
				$this->ExportData();
				$this->Page_Terminate(); // Terminate response
				exit();
			}
		} else {
			$sReturnUrl = "cdrlist.php"; // Not page request, return to list
		}
		if ($sReturnUrl <> "")
			$this->Page_Terminate($sReturnUrl);

		// Render row
		$this->RowType = EW_ROWTYPE_VIEW;
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = &$options["action"];

		// Set up action default
		$option = &$options["action"];
		$option->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
		$option->UseImageAndText = TRUE;
		$option->UseDropDownButton = FALSE;
		$option->UseButtonGroup = TRUE;
		$item = &$option->Add($option->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {

		// Load List page SQL
		$sSql = $this->SelectSQL();
		$conn = &$this->Connection();

		// Load recordset
		$dbtype = ew_GetConnectionType($this->DBID);
		if ($this->UseSelectLimit) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			if ($dbtype == "MSSQL") {
				$rs = $conn->SelectLimit($sSql, $rowcnt, $offset, array("_hasOrderBy" => trim($this->getOrderBy()) || trim($this->getSessionOrderBy())));
			} else {
				$rs = $conn->SelectLimit($sSql, $rowcnt, $offset);
			}
			$conn->raiseErrorFn = '';
		} else {
			$rs = ew_LoadRecordset($sSql, $conn);
		}

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
	}

	// Load row based on key values
	function LoadRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn = &$this->Connection();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql, $conn);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		if ($this->AuditTrailOnView) $this->WriteAuditTrailOnView($row);
		$this->calldate->setDbValue($rs->fields('calldate'));
		$this->uniqueid->setDbValue($rs->fields('uniqueid'));
		$this->cnam->setDbValue($rs->fields('cnam'));
		$this->cnum->setDbValue($rs->fields('cnum'));
		$this->dst->setDbValue($rs->fields('dst'));
		$this->duration->setDbValue($rs->fields('duration'));
		$this->billsec->setDbValue($rs->fields('billsec'));
		$this->disposition->setDbValue($rs->fields('disposition'));
		$this->outbound_cnum->setDbValue($rs->fields('outbound_cnum'));
		$this->play->setDbValue($rs->fields('play'));
		$this->recordingfile->setDbValue($rs->fields('recordingfile'));
		$this->recording_name->setDbValue($rs->fields('recording_name'));
		$this->clid->setDbValue($rs->fields('clid'));
		$this->src->setDbValue($rs->fields('src'));
		$this->dcontext->setDbValue($rs->fields('dcontext'));
		$this->channel->setDbValue($rs->fields('channel'));
		$this->dstchannel->setDbValue($rs->fields('dstchannel'));
		$this->lastapp->setDbValue($rs->fields('lastapp'));
		$this->lastdata->setDbValue($rs->fields('lastdata'));
		$this->amaflags->setDbValue($rs->fields('amaflags'));
		$this->accountcode->setDbValue($rs->fields('accountcode'));
		$this->userfield->setDbValue($rs->fields('userfield'));
		$this->did->setDbValue($rs->fields('did'));
		$this->outbound_cnam->setDbValue($rs->fields('outbound_cnam'));
		$this->dst_cnam->setDbValue($rs->fields('dst_cnam'));
		$this->linkedid->setDbValue($rs->fields('linkedid'));
		$this->peeraccount->setDbValue($rs->fields('peeraccount'));
		$this->sequence->setDbValue($rs->fields('sequence'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->calldate->DbValue = $row['calldate'];
		$this->uniqueid->DbValue = $row['uniqueid'];
		$this->cnam->DbValue = $row['cnam'];
		$this->cnum->DbValue = $row['cnum'];
		$this->dst->DbValue = $row['dst'];
		$this->duration->DbValue = $row['duration'];
		$this->billsec->DbValue = $row['billsec'];
		$this->disposition->DbValue = $row['disposition'];
		$this->outbound_cnum->DbValue = $row['outbound_cnum'];
		$this->play->DbValue = $row['play'];
		$this->recordingfile->DbValue = $row['recordingfile'];
		$this->recording_name->DbValue = $row['recording_name'];
		$this->clid->DbValue = $row['clid'];
		$this->src->DbValue = $row['src'];
		$this->dcontext->DbValue = $row['dcontext'];
		$this->channel->DbValue = $row['channel'];
		$this->dstchannel->DbValue = $row['dstchannel'];
		$this->lastapp->DbValue = $row['lastapp'];
		$this->lastdata->DbValue = $row['lastdata'];
		$this->amaflags->DbValue = $row['amaflags'];
		$this->accountcode->DbValue = $row['accountcode'];
		$this->userfield->DbValue = $row['userfield'];
		$this->did->DbValue = $row['did'];
		$this->outbound_cnam->DbValue = $row['outbound_cnam'];
		$this->dst_cnam->DbValue = $row['dst_cnam'];
		$this->linkedid->DbValue = $row['linkedid'];
		$this->peeraccount->DbValue = $row['peeraccount'];
		$this->sequence->DbValue = $row['sequence'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		$this->AddUrl = $this->GetAddUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();
		$this->ListUrl = $this->GetListUrl();
		$this->SetupOtherOptions();

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
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Set up export options
	function SetupExportOptions() {
		global $Language;

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a href=\"" . $this->ExportPrintUrl . "\" class=\"ewExportLink ewPrint\" title=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\">" . $Language->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = TRUE;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a href=\"" . $this->ExportExcelUrl . "\" class=\"ewExportLink ewExcel\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\">" . $Language->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a href=\"" . $this->ExportWordUrl . "\" class=\"ewExportLink ewWord\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\">" . $Language->Phrase("ExportToWord") . "</a>";
		$item->Visible = TRUE;

		// Export to Html
		$item = &$this->ExportOptions->Add("html");
		$item->Body = "<a href=\"" . $this->ExportHtmlUrl . "\" class=\"ewExportLink ewHtml\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToHtmlText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToHtmlText")) . "\">" . $Language->Phrase("ExportToHtml") . "</a>";
		$item->Visible = TRUE;

		// Export to Xml
		$item = &$this->ExportOptions->Add("xml");
		$item->Body = "<a href=\"" . $this->ExportXmlUrl . "\" class=\"ewExportLink ewXml\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToXmlText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToXmlText")) . "\">" . $Language->Phrase("ExportToXml") . "</a>";
		$item->Visible = TRUE;

		// Export to Csv
		$item = &$this->ExportOptions->Add("csv");
		$item->Body = "<a href=\"" . $this->ExportCsvUrl . "\" class=\"ewExportLink ewCsv\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToCsvText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToCsvText")) . "\">" . $Language->Phrase("ExportToCsv") . "</a>";
		$item->Visible = TRUE;

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a href=\"" . $this->ExportPdfUrl . "\" class=\"ewExportLink ewPdf\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\">" . $Language->Phrase("ExportToPDF") . "</a>";
		$item->Visible = TRUE;

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$url = "";
		$item->Body = "<button id=\"emf_cdr\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_cdr',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fcdrview,key:" . ew_ArrayToJsonAttr($this->RecKey) . ",sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
		$item->Visible = TRUE;

		// Drop down button for export
		$this->ExportOptions->UseButtonGroup = TRUE;
		$this->ExportOptions->UseImageAndText = TRUE;
		$this->ExportOptions->UseDropDownButton = FALSE;
		if ($this->ExportOptions->UseButtonGroup && ew_IsMobile())
			$this->ExportOptions->UseDropDownButton = TRUE;
		$this->ExportOptions->DropDownButtonPhrase = $Language->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Hide options for export
		if ($this->Export <> "")
			$this->ExportOptions->HideAllOptions();
	}

	// Export data in HTML/CSV/Word/Excel/XML/Email/PDF format
	function ExportData() {
		$utf8 = (strtolower(EW_CHARSET) == "utf-8");
		$bSelectLimit = FALSE;

		// Load recordset
		if ($bSelectLimit) {
			$this->TotalRecs = $this->SelectRecordCount();
		} else {
			if (!$this->Recordset)
				$this->Recordset = $this->LoadRecordset();
			$rs = &$this->Recordset;
			if ($rs)
				$this->TotalRecs = $rs->RecordCount();
		}
		$this->StartRec = 1;
		$this->SetUpStartRec(); // Set up start record position

		// Set the last record to display
		if ($this->DisplayRecs <= 0) {
			$this->StopRec = $this->TotalRecs;
		} else {
			$this->StopRec = $this->StartRec + $this->DisplayRecs - 1;
		}
		if (!$rs) {
			header("Content-Type:"); // Remove header
			header("Content-Disposition:");
			$this->ShowMessage();
			return;
		}
		$this->ExportDoc = ew_ExportDocument($this, "v");
		$Doc = &$this->ExportDoc;
		if ($bSelectLimit) {
			$this->StartRec = 1;
			$this->StopRec = $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs;
		} else {

			//$this->StartRec = $this->StartRec;
			//$this->StopRec = $this->StopRec;

		}

		// Call Page Exporting server event
		$this->ExportDoc->ExportCustom = !$this->Page_Exporting();
		$ParentTable = "";
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		$Doc->Text .= $sHeader;
		$this->ExportDocument($Doc, $rs, $this->StartRec, $this->StopRec, "view");
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		$Doc->Text .= $sFooter;

		// Close recordset
		$rs->Close();

		// Call Page Exported server event
		$this->Page_Exported();

		// Export header and footer
		$Doc->ExportHeaderAndFooter();

		// Clean output buffer
		if (!EW_DEBUG_ENABLED && ob_get_length())
			ob_end_clean();

		// Write debug message if enabled
		if (EW_DEBUG_ENABLED && $this->Export <> "pdf")
			echo ew_DebugMsg();

		// Output data
		if ($this->Export == "email") {
			echo $this->ExportEmail($Doc->Text);
		} else {
			$Doc->Export();
		}
	}

	// Export email
	function ExportEmail($EmailContent) {
		global $gTmpImages, $Language;
		$sSender = @$_POST["sender"];
		$sRecipient = @$_POST["recipient"];
		$sCc = @$_POST["cc"];
		$sBcc = @$_POST["bcc"];
		$sContentType = @$_POST["contenttype"];

		// Subject
		$sSubject = ew_StripSlashes(@$_POST["subject"]);
		$sEmailSubject = $sSubject;

		// Message
		$sContent = ew_StripSlashes(@$_POST["message"]);
		$sEmailMessage = $sContent;

		// Check sender
		if ($sSender == "") {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterSenderEmail") . "</p>";
		}
		if (!ew_CheckEmail($sSender)) {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterProperSenderEmail") . "</p>";
		}

		// Check recipient
		if (!ew_CheckEmailList($sRecipient, EW_MAX_EMAIL_RECIPIENT)) {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterProperRecipientEmail") . "</p>";
		}

		// Check cc
		if (!ew_CheckEmailList($sCc, EW_MAX_EMAIL_RECIPIENT)) {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterProperCcEmail") . "</p>";
		}

		// Check bcc
		if (!ew_CheckEmailList($sBcc, EW_MAX_EMAIL_RECIPIENT)) {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterProperBccEmail") . "</p>";
		}

		// Check email sent count
		if (!isset($_SESSION[EW_EXPORT_EMAIL_COUNTER]))
			$_SESSION[EW_EXPORT_EMAIL_COUNTER] = 0;
		if (intval($_SESSION[EW_EXPORT_EMAIL_COUNTER]) > EW_MAX_EMAIL_SENT_COUNT) {
			return "<p class=\"text-danger\">" . $Language->Phrase("ExceedMaxEmailExport") . "</p>";
		}

		// Send email
		$Email = new cEmail();
		$Email->Sender = $sSender; // Sender
		$Email->Recipient = $sRecipient; // Recipient
		$Email->Cc = $sCc; // Cc
		$Email->Bcc = $sBcc; // Bcc
		$Email->Subject = $sEmailSubject; // Subject
		$Email->Format = ($sContentType == "url") ? "text" : "html";
		if ($sEmailMessage <> "") {
			$sEmailMessage = ew_RemoveXSS($sEmailMessage);
			$sEmailMessage .= ($sContentType == "url") ? "\r\n\r\n" : "<br><br>";
		}
		if ($sContentType == "url") {
			$sUrl = ew_ConvertFullUrl(ew_CurrentPage() . "?" . $this->ExportQueryString());
			$sEmailMessage .= $sUrl; // Send URL only
		} else {
			foreach ($gTmpImages as $tmpimage)
				$Email->AddEmbeddedImage($tmpimage);
			$sEmailMessage .= ew_CleanEmailContent($EmailContent); // Send HTML
		}
		$Email->Content = $sEmailMessage; // Content
		$EventArgs = array();
		if ($this->Recordset) {
			$this->RecCnt = $this->StartRec - 1;
			$this->Recordset->MoveFirst();
			if ($this->StartRec > 1)
				$this->Recordset->Move($this->StartRec - 1);
			$EventArgs["rs"] = &$this->Recordset;
		}
		$bEmailSent = FALSE;
		if ($this->Email_Sending($Email, $EventArgs))
			$bEmailSent = $Email->Send();

		// Check email sent status
		if ($bEmailSent) {

			// Update email sent count
			$_SESSION[EW_EXPORT_EMAIL_COUNTER]++;

			// Sent email success
			return "<p class=\"text-success\">" . $Language->Phrase("SendEmailSuccess") . "</p>"; // Set up success message
		} else {

			// Sent email failure
			return "<p class=\"text-danger\">" . $Email->SendErrDescription . "</p>";
		}
	}

	// Export QueryString
	function ExportQueryString() {

		// Initialize
		$sQry = "export=html";

		// Add record key QueryString
		$sQry .= "&" . substr($this->KeyUrl("", ""), 1);
		return $sQry;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("cdrlist.php"), "", $this->TableVar, TRUE);
		$PageId = "view";
		$Breadcrumb->Add("view", $PageId, $url);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'cdr';
		$usr = CurrentUserName();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
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

	// Page Exporting event
	// $this->ExportDoc = export document object
	function Page_Exporting() {

		//$this->ExportDoc->Text = "my header"; // Export header
		//return FALSE; // Return FALSE to skip default export and use Row_Export event

		return TRUE; // Return TRUE to use default export and skip Row_Export event
	}

	// Row Export event
	// $this->ExportDoc = export document object
	function Row_Export($rs) {

	    //$this->ExportDoc->Text .= "my content"; // Build HTML with field value: $rs["MyField"] or $this->MyField->ViewValue
	}

	// Page Exported event
	// $this->ExportDoc = export document object
	function Page_Exported() {

		//$this->ExportDoc->Text .= "my footer"; // Export footer
		//echo $this->ExportDoc->Text;

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($cdr_view)) $cdr_view = new ccdr_view();

// Page init
$cdr_view->Page_Init();

// Page main
$cdr_view->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$cdr_view->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($cdr->Export == "") { ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "view";
var CurrentForm = fcdrview = new ew_Form("fcdrview", "view");

// Form_CustomValidate event
fcdrview.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcdrview.ValidateRequired = true;
<?php } else { ?>
fcdrview.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($cdr->Export == "") { ?>
<div class="ewToolbar">
<?php if ($cdr->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php $cdr_view->ExportOptions->Render("body") ?>
<?php
	foreach ($cdr_view->OtherOptions as &$option)
		$option->Render("body");
?>
<?php if ($cdr->Export == "") { ?>
<?php echo $Language->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $cdr_view->ShowPageHeader(); ?>
<?php
$cdr_view->ShowMessage();
?>
<?php if ($cdr->Export == "") { ?>
<form name="ewPagerForm" class="form-inline ewForm ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($cdr_view->Pager)) $cdr_view->Pager = new cPrevNextPager($cdr_view->StartRec, $cdr_view->DisplayRecs, $cdr_view->TotalRecs) ?>
<?php if ($cdr_view->Pager->RecordCount > 0) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($cdr_view->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $cdr_view->PageUrl() ?>start=<?php echo $cdr_view->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($cdr_view->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $cdr_view->PageUrl() ?>start=<?php echo $cdr_view->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $cdr_view->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($cdr_view->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $cdr_view->PageUrl() ?>start=<?php echo $cdr_view->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($cdr_view->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $cdr_view->PageUrl() ?>start=<?php echo $cdr_view->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $cdr_view->Pager->PageCount ?></span>
</div>
<?php } ?>
<div class="clearfix"></div>
</form>
<?php } ?>
<form name="fcdrview" id="fcdrview" class="form-inline ewForm ewViewForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($cdr_view->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $cdr_view->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="cdr">
<table class="table table-bordered table-striped ewViewTable">
<?php if ($cdr->calldate->Visible) { // calldate ?>
	<tr id="r_calldate">
		<td><span id="elh_cdr_calldate"><?php echo $cdr->calldate->FldCaption() ?></span></td>
		<td data-name="calldate"<?php echo $cdr->calldate->CellAttributes() ?>>
<span id="el_cdr_calldate">
<span<?php echo $cdr->calldate->ViewAttributes() ?>>
<?php echo $cdr->calldate->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->uniqueid->Visible) { // uniqueid ?>
	<tr id="r_uniqueid">
		<td><span id="elh_cdr_uniqueid"><?php echo $cdr->uniqueid->FldCaption() ?></span></td>
		<td data-name="uniqueid"<?php echo $cdr->uniqueid->CellAttributes() ?>>
<span id="el_cdr_uniqueid">
<span<?php echo $cdr->uniqueid->ViewAttributes() ?>>
<?php echo $cdr->uniqueid->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->cnam->Visible) { // cnam ?>
	<tr id="r_cnam">
		<td><span id="elh_cdr_cnam"><?php echo $cdr->cnam->FldCaption() ?></span></td>
		<td data-name="cnam"<?php echo $cdr->cnam->CellAttributes() ?>>
<span id="el_cdr_cnam">
<span<?php echo $cdr->cnam->ViewAttributes() ?>>
<?php echo $cdr->cnam->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->cnum->Visible) { // cnum ?>
	<tr id="r_cnum">
		<td><span id="elh_cdr_cnum"><?php echo $cdr->cnum->FldCaption() ?></span></td>
		<td data-name="cnum"<?php echo $cdr->cnum->CellAttributes() ?>>
<span id="el_cdr_cnum">
<span<?php echo $cdr->cnum->ViewAttributes() ?>>
<?php echo $cdr->cnum->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->dst->Visible) { // dst ?>
	<tr id="r_dst">
		<td><span id="elh_cdr_dst"><?php echo $cdr->dst->FldCaption() ?></span></td>
		<td data-name="dst"<?php echo $cdr->dst->CellAttributes() ?>>
<span id="el_cdr_dst">
<span<?php echo $cdr->dst->ViewAttributes() ?>>
<?php echo $cdr->dst->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->duration->Visible) { // duration ?>
	<tr id="r_duration">
		<td><span id="elh_cdr_duration"><?php echo $cdr->duration->FldCaption() ?></span></td>
		<td data-name="duration"<?php echo $cdr->duration->CellAttributes() ?>>
<span id="el_cdr_duration">
<span<?php echo $cdr->duration->ViewAttributes() ?>>
<?php echo $cdr->duration->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->billsec->Visible) { // billsec ?>
	<tr id="r_billsec">
		<td><span id="elh_cdr_billsec"><?php echo $cdr->billsec->FldCaption() ?></span></td>
		<td data-name="billsec"<?php echo $cdr->billsec->CellAttributes() ?>>
<span id="el_cdr_billsec">
<span<?php echo $cdr->billsec->ViewAttributes() ?>>
<?php echo $cdr->billsec->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->disposition->Visible) { // disposition ?>
	<tr id="r_disposition">
		<td><span id="elh_cdr_disposition"><?php echo $cdr->disposition->FldCaption() ?></span></td>
		<td data-name="disposition"<?php echo $cdr->disposition->CellAttributes() ?>>
<span id="el_cdr_disposition">
<span<?php echo $cdr->disposition->ViewAttributes() ?>>
<?php echo $cdr->disposition->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->outbound_cnum->Visible) { // outbound_cnum ?>
	<tr id="r_outbound_cnum">
		<td><span id="elh_cdr_outbound_cnum"><?php echo $cdr->outbound_cnum->FldCaption() ?></span></td>
		<td data-name="outbound_cnum"<?php echo $cdr->outbound_cnum->CellAttributes() ?>>
<span id="el_cdr_outbound_cnum">
<span<?php echo $cdr->outbound_cnum->ViewAttributes() ?>>
<?php echo $cdr->outbound_cnum->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->play->Visible) { // play ?>
	<tr id="r_play">
		<td><span id="elh_cdr_play"><?php echo $cdr->play->FldCaption() ?></span></td>
		<td data-name="play"<?php echo $cdr->play->CellAttributes() ?>>
<span id="el_cdr_play">
<span<?php echo $cdr->play->ViewAttributes() ?>>
<?php echo $cdr->play->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->recordingfile->Visible) { // recordingfile ?>
	<tr id="r_recordingfile">
		<td><span id="elh_cdr_recordingfile"><?php echo $cdr->recordingfile->FldCaption() ?></span></td>
		<td data-name="recordingfile"<?php echo $cdr->recordingfile->CellAttributes() ?>>
<span id="el_cdr_recordingfile">
<span<?php echo $cdr->recordingfile->ViewAttributes() ?>>
<?php if ((!ew_EmptyStr($cdr->recordingfile->ViewValue)) && $cdr->recordingfile->LinkAttributes() <> "") { ?>
<a<?php echo $cdr->recordingfile->LinkAttributes() ?>><?php echo $cdr->recordingfile->ViewValue ?></a>
<?php } else { ?>
<?php echo $cdr->recordingfile->ViewValue ?>
<?php } ?>
</span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->recording_name->Visible) { // recording_name ?>
	<tr id="r_recording_name">
		<td><span id="elh_cdr_recording_name"><?php echo $cdr->recording_name->FldCaption() ?></span></td>
		<td data-name="recording_name"<?php echo $cdr->recording_name->CellAttributes() ?>>
<span id="el_cdr_recording_name">
<span<?php echo $cdr->recording_name->ViewAttributes() ?>>
<?php echo $cdr->recording_name->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->clid->Visible) { // clid ?>
	<tr id="r_clid">
		<td><span id="elh_cdr_clid"><?php echo $cdr->clid->FldCaption() ?></span></td>
		<td data-name="clid"<?php echo $cdr->clid->CellAttributes() ?>>
<span id="el_cdr_clid">
<span<?php echo $cdr->clid->ViewAttributes() ?>>
<?php echo $cdr->clid->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->src->Visible) { // src ?>
	<tr id="r_src">
		<td><span id="elh_cdr_src"><?php echo $cdr->src->FldCaption() ?></span></td>
		<td data-name="src"<?php echo $cdr->src->CellAttributes() ?>>
<span id="el_cdr_src">
<span<?php echo $cdr->src->ViewAttributes() ?>>
<?php echo $cdr->src->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->dcontext->Visible) { // dcontext ?>
	<tr id="r_dcontext">
		<td><span id="elh_cdr_dcontext"><?php echo $cdr->dcontext->FldCaption() ?></span></td>
		<td data-name="dcontext"<?php echo $cdr->dcontext->CellAttributes() ?>>
<span id="el_cdr_dcontext">
<span<?php echo $cdr->dcontext->ViewAttributes() ?>>
<?php echo $cdr->dcontext->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->channel->Visible) { // channel ?>
	<tr id="r_channel">
		<td><span id="elh_cdr_channel"><?php echo $cdr->channel->FldCaption() ?></span></td>
		<td data-name="channel"<?php echo $cdr->channel->CellAttributes() ?>>
<span id="el_cdr_channel">
<span<?php echo $cdr->channel->ViewAttributes() ?>>
<?php echo $cdr->channel->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->dstchannel->Visible) { // dstchannel ?>
	<tr id="r_dstchannel">
		<td><span id="elh_cdr_dstchannel"><?php echo $cdr->dstchannel->FldCaption() ?></span></td>
		<td data-name="dstchannel"<?php echo $cdr->dstchannel->CellAttributes() ?>>
<span id="el_cdr_dstchannel">
<span<?php echo $cdr->dstchannel->ViewAttributes() ?>>
<?php echo $cdr->dstchannel->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->lastapp->Visible) { // lastapp ?>
	<tr id="r_lastapp">
		<td><span id="elh_cdr_lastapp"><?php echo $cdr->lastapp->FldCaption() ?></span></td>
		<td data-name="lastapp"<?php echo $cdr->lastapp->CellAttributes() ?>>
<span id="el_cdr_lastapp">
<span<?php echo $cdr->lastapp->ViewAttributes() ?>>
<?php echo $cdr->lastapp->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->lastdata->Visible) { // lastdata ?>
	<tr id="r_lastdata">
		<td><span id="elh_cdr_lastdata"><?php echo $cdr->lastdata->FldCaption() ?></span></td>
		<td data-name="lastdata"<?php echo $cdr->lastdata->CellAttributes() ?>>
<span id="el_cdr_lastdata">
<span<?php echo $cdr->lastdata->ViewAttributes() ?>>
<?php echo $cdr->lastdata->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->amaflags->Visible) { // amaflags ?>
	<tr id="r_amaflags">
		<td><span id="elh_cdr_amaflags"><?php echo $cdr->amaflags->FldCaption() ?></span></td>
		<td data-name="amaflags"<?php echo $cdr->amaflags->CellAttributes() ?>>
<span id="el_cdr_amaflags">
<span<?php echo $cdr->amaflags->ViewAttributes() ?>>
<?php echo $cdr->amaflags->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->accountcode->Visible) { // accountcode ?>
	<tr id="r_accountcode">
		<td><span id="elh_cdr_accountcode"><?php echo $cdr->accountcode->FldCaption() ?></span></td>
		<td data-name="accountcode"<?php echo $cdr->accountcode->CellAttributes() ?>>
<span id="el_cdr_accountcode">
<span<?php echo $cdr->accountcode->ViewAttributes() ?>>
<?php echo $cdr->accountcode->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->userfield->Visible) { // userfield ?>
	<tr id="r_userfield">
		<td><span id="elh_cdr_userfield"><?php echo $cdr->userfield->FldCaption() ?></span></td>
		<td data-name="userfield"<?php echo $cdr->userfield->CellAttributes() ?>>
<span id="el_cdr_userfield">
<span<?php echo $cdr->userfield->ViewAttributes() ?>>
<?php echo $cdr->userfield->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->did->Visible) { // did ?>
	<tr id="r_did">
		<td><span id="elh_cdr_did"><?php echo $cdr->did->FldCaption() ?></span></td>
		<td data-name="did"<?php echo $cdr->did->CellAttributes() ?>>
<span id="el_cdr_did">
<span<?php echo $cdr->did->ViewAttributes() ?>>
<?php echo $cdr->did->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->outbound_cnam->Visible) { // outbound_cnam ?>
	<tr id="r_outbound_cnam">
		<td><span id="elh_cdr_outbound_cnam"><?php echo $cdr->outbound_cnam->FldCaption() ?></span></td>
		<td data-name="outbound_cnam"<?php echo $cdr->outbound_cnam->CellAttributes() ?>>
<span id="el_cdr_outbound_cnam">
<span<?php echo $cdr->outbound_cnam->ViewAttributes() ?>>
<?php echo $cdr->outbound_cnam->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->dst_cnam->Visible) { // dst_cnam ?>
	<tr id="r_dst_cnam">
		<td><span id="elh_cdr_dst_cnam"><?php echo $cdr->dst_cnam->FldCaption() ?></span></td>
		<td data-name="dst_cnam"<?php echo $cdr->dst_cnam->CellAttributes() ?>>
<span id="el_cdr_dst_cnam">
<span<?php echo $cdr->dst_cnam->ViewAttributes() ?>>
<?php echo $cdr->dst_cnam->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->linkedid->Visible) { // linkedid ?>
	<tr id="r_linkedid">
		<td><span id="elh_cdr_linkedid"><?php echo $cdr->linkedid->FldCaption() ?></span></td>
		<td data-name="linkedid"<?php echo $cdr->linkedid->CellAttributes() ?>>
<span id="el_cdr_linkedid">
<span<?php echo $cdr->linkedid->ViewAttributes() ?>>
<?php echo $cdr->linkedid->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->peeraccount->Visible) { // peeraccount ?>
	<tr id="r_peeraccount">
		<td><span id="elh_cdr_peeraccount"><?php echo $cdr->peeraccount->FldCaption() ?></span></td>
		<td data-name="peeraccount"<?php echo $cdr->peeraccount->CellAttributes() ?>>
<span id="el_cdr_peeraccount">
<span<?php echo $cdr->peeraccount->ViewAttributes() ?>>
<?php echo $cdr->peeraccount->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($cdr->sequence->Visible) { // sequence ?>
	<tr id="r_sequence">
		<td><span id="elh_cdr_sequence"><?php echo $cdr->sequence->FldCaption() ?></span></td>
		<td data-name="sequence"<?php echo $cdr->sequence->CellAttributes() ?>>
<span id="el_cdr_sequence">
<span<?php echo $cdr->sequence->ViewAttributes() ?>>
<?php echo $cdr->sequence->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
<?php if ($cdr->Export == "") { ?>
<?php if (!isset($cdr_view->Pager)) $cdr_view->Pager = new cPrevNextPager($cdr_view->StartRec, $cdr_view->DisplayRecs, $cdr_view->TotalRecs) ?>
<?php if ($cdr_view->Pager->RecordCount > 0) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($cdr_view->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $cdr_view->PageUrl() ?>start=<?php echo $cdr_view->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($cdr_view->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $cdr_view->PageUrl() ?>start=<?php echo $cdr_view->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $cdr_view->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($cdr_view->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $cdr_view->PageUrl() ?>start=<?php echo $cdr_view->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($cdr_view->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $cdr_view->PageUrl() ?>start=<?php echo $cdr_view->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $cdr_view->Pager->PageCount ?></span>
</div>
<?php } ?>
<div class="clearfix"></div>
<?php } ?>
</form>
<?php if ($cdr->Export == "") { ?>
<script type="text/javascript">
fcdrview.Init();
</script>
<?php } ?>
<?php
$cdr_view->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($cdr->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$cdr_view->Page_Terminate();
?>
