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

$cdr_edit = NULL; // Initialize page object first

class ccdr_edit extends ccdr {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{274CC91E-1C95-40BB-9BB8-39D2A070EA8E}";

	// Table name
	var $TableName = 'cdr';

	// Page object name
	var $PageObjName = 'cdr_edit';

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
			define("EW_PAGE_ID", 'edit', TRUE);

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
		if (!$Security->CanEdit()) {
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

		// Process auto fill
		if (@$_POST["ajax"] == "autofill") {
			$results = $this->GetAutoFill(@$_POST["name"], @$_POST["q"]);
			if ($results) {

				// Clean output buffer
				if (!EW_DEBUG_ENABLED && ob_get_length())
					ob_end_clean();
				echo $results;
				$this->Page_Terminate();
				exit();
			}
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
	var $FormClassName = "form-horizontal ewForm ewEditForm";
	var $DbMasterFilter;
	var $DbDetailFilter;
	var $DisplayRecs = 1;
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
		global $objForm, $Language, $gsFormError;

		// Load current record
		$bLoadCurrentRecord = FALSE;
		$sReturnUrl = "";
		$bMatchRecord = FALSE;

		// Load key from QueryString
		if (@$_GET["uniqueid"] <> "") {
			$this->uniqueid->setQueryStringValue($_GET["uniqueid"]);
			$this->RecKey["uniqueid"] = $this->uniqueid->QueryStringValue;
		} else {
			$bLoadCurrentRecord = TRUE;
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load recordset
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

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Validate form if post back
		if (@$_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$bMatchRecord) {
					if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
						$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
					$this->Page_Terminate("cdrlist.php"); // Return to list page
				} else {
					$this->LoadRowValues($this->Recordset); // Load row values
				}
				break;
			Case "U": // Update
				$sReturnUrl = $this->getReturnUrl();
				if (ew_GetPageName($sReturnUrl) == "cdrlist.php")
					$sReturnUrl = $this->AddMasterUrl($sReturnUrl); // List page, return to list page with correct master key if necessary
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} elseif ($this->getFailureMessage() == $Language->Phrase("NoRecord")) {
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed
				}
		}

		// Render the record
		$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		$this->ResetAttrs();
		$this->RenderRow();
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

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->calldate->FldIsDetailKey) {
			$this->calldate->setFormValue($objForm->GetValue("x_calldate"));
			$this->calldate->CurrentValue = ew_UnFormatDateTime($this->calldate->CurrentValue, 9);
		}
		if (!$this->uniqueid->FldIsDetailKey) {
			$this->uniqueid->setFormValue($objForm->GetValue("x_uniqueid"));
		}
		if (!$this->clid->FldIsDetailKey) {
			$this->clid->setFormValue($objForm->GetValue("x_clid"));
		}
		if (!$this->src->FldIsDetailKey) {
			$this->src->setFormValue($objForm->GetValue("x_src"));
		}
		if (!$this->dst->FldIsDetailKey) {
			$this->dst->setFormValue($objForm->GetValue("x_dst"));
		}
		if (!$this->dcontext->FldIsDetailKey) {
			$this->dcontext->setFormValue($objForm->GetValue("x_dcontext"));
		}
		if (!$this->channel->FldIsDetailKey) {
			$this->channel->setFormValue($objForm->GetValue("x_channel"));
		}
		if (!$this->dstchannel->FldIsDetailKey) {
			$this->dstchannel->setFormValue($objForm->GetValue("x_dstchannel"));
		}
		if (!$this->lastapp->FldIsDetailKey) {
			$this->lastapp->setFormValue($objForm->GetValue("x_lastapp"));
		}
		if (!$this->lastdata->FldIsDetailKey) {
			$this->lastdata->setFormValue($objForm->GetValue("x_lastdata"));
		}
		if (!$this->duration->FldIsDetailKey) {
			$this->duration->setFormValue($objForm->GetValue("x_duration"));
		}
		if (!$this->billsec->FldIsDetailKey) {
			$this->billsec->setFormValue($objForm->GetValue("x_billsec"));
		}
		if (!$this->disposition->FldIsDetailKey) {
			$this->disposition->setFormValue($objForm->GetValue("x_disposition"));
		}
		if (!$this->amaflags->FldIsDetailKey) {
			$this->amaflags->setFormValue($objForm->GetValue("x_amaflags"));
		}
		if (!$this->accountcode->FldIsDetailKey) {
			$this->accountcode->setFormValue($objForm->GetValue("x_accountcode"));
		}
		if (!$this->userfield->FldIsDetailKey) {
			$this->userfield->setFormValue($objForm->GetValue("x_userfield"));
		}
		if (!$this->did->FldIsDetailKey) {
			$this->did->setFormValue($objForm->GetValue("x_did"));
		}
		if (!$this->recordingfile->FldIsDetailKey) {
			$this->recordingfile->setFormValue($objForm->GetValue("x_recordingfile"));
		}
		if (!$this->cnum->FldIsDetailKey) {
			$this->cnum->setFormValue($objForm->GetValue("x_cnum"));
		}
		if (!$this->cnam->FldIsDetailKey) {
			$this->cnam->setFormValue($objForm->GetValue("x_cnam"));
		}
		if (!$this->outbound_cnum->FldIsDetailKey) {
			$this->outbound_cnum->setFormValue($objForm->GetValue("x_outbound_cnum"));
		}
		if (!$this->outbound_cnam->FldIsDetailKey) {
			$this->outbound_cnam->setFormValue($objForm->GetValue("x_outbound_cnam"));
		}
		if (!$this->dst_cnam->FldIsDetailKey) {
			$this->dst_cnam->setFormValue($objForm->GetValue("x_dst_cnam"));
		}
		if (!$this->linkedid->FldIsDetailKey) {
			$this->linkedid->setFormValue($objForm->GetValue("x_linkedid"));
		}
		if (!$this->peeraccount->FldIsDetailKey) {
			$this->peeraccount->setFormValue($objForm->GetValue("x_peeraccount"));
		}
		if (!$this->sequence->FldIsDetailKey) {
			$this->sequence->setFormValue($objForm->GetValue("x_sequence"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->calldate->CurrentValue = $this->calldate->FormValue;
		$this->calldate->CurrentValue = ew_UnFormatDateTime($this->calldate->CurrentValue, 9);
		$this->uniqueid->CurrentValue = $this->uniqueid->FormValue;
		$this->clid->CurrentValue = $this->clid->FormValue;
		$this->src->CurrentValue = $this->src->FormValue;
		$this->dst->CurrentValue = $this->dst->FormValue;
		$this->dcontext->CurrentValue = $this->dcontext->FormValue;
		$this->channel->CurrentValue = $this->channel->FormValue;
		$this->dstchannel->CurrentValue = $this->dstchannel->FormValue;
		$this->lastapp->CurrentValue = $this->lastapp->FormValue;
		$this->lastdata->CurrentValue = $this->lastdata->FormValue;
		$this->duration->CurrentValue = $this->duration->FormValue;
		$this->billsec->CurrentValue = $this->billsec->FormValue;
		$this->disposition->CurrentValue = $this->disposition->FormValue;
		$this->amaflags->CurrentValue = $this->amaflags->FormValue;
		$this->accountcode->CurrentValue = $this->accountcode->FormValue;
		$this->userfield->CurrentValue = $this->userfield->FormValue;
		$this->did->CurrentValue = $this->did->FormValue;
		$this->recordingfile->CurrentValue = $this->recordingfile->FormValue;
		$this->cnum->CurrentValue = $this->cnum->FormValue;
		$this->cnam->CurrentValue = $this->cnam->FormValue;
		$this->outbound_cnum->CurrentValue = $this->outbound_cnum->FormValue;
		$this->outbound_cnam->CurrentValue = $this->outbound_cnam->FormValue;
		$this->dst_cnam->CurrentValue = $this->dst_cnam->FormValue;
		$this->linkedid->CurrentValue = $this->linkedid->FormValue;
		$this->peeraccount->CurrentValue = $this->peeraccount->FormValue;
		$this->sequence->CurrentValue = $this->sequence->FormValue;
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
		$this->calldate->setDbValue($rs->fields('calldate'));
		$this->uniqueid->setDbValue($rs->fields('uniqueid'));
		$this->clid->setDbValue($rs->fields('clid'));
		$this->src->setDbValue($rs->fields('src'));
		$this->dst->setDbValue($rs->fields('dst'));
		$this->dcontext->setDbValue($rs->fields('dcontext'));
		$this->channel->setDbValue($rs->fields('channel'));
		$this->dstchannel->setDbValue($rs->fields('dstchannel'));
		$this->lastapp->setDbValue($rs->fields('lastapp'));
		$this->lastdata->setDbValue($rs->fields('lastdata'));
		$this->duration->setDbValue($rs->fields('duration'));
		$this->billsec->setDbValue($rs->fields('billsec'));
		$this->disposition->setDbValue($rs->fields('disposition'));
		$this->amaflags->setDbValue($rs->fields('amaflags'));
		$this->accountcode->setDbValue($rs->fields('accountcode'));
		$this->userfield->setDbValue($rs->fields('userfield'));
		$this->did->setDbValue($rs->fields('did'));
		$this->recordingfile->setDbValue($rs->fields('recordingfile'));
		$this->cnum->setDbValue($rs->fields('cnum'));
		$this->cnam->setDbValue($rs->fields('cnam'));
		$this->outbound_cnum->setDbValue($rs->fields('outbound_cnum'));
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
		$this->clid->DbValue = $row['clid'];
		$this->src->DbValue = $row['src'];
		$this->dst->DbValue = $row['dst'];
		$this->dcontext->DbValue = $row['dcontext'];
		$this->channel->DbValue = $row['channel'];
		$this->dstchannel->DbValue = $row['dstchannel'];
		$this->lastapp->DbValue = $row['lastapp'];
		$this->lastdata->DbValue = $row['lastdata'];
		$this->duration->DbValue = $row['duration'];
		$this->billsec->DbValue = $row['billsec'];
		$this->disposition->DbValue = $row['disposition'];
		$this->amaflags->DbValue = $row['amaflags'];
		$this->accountcode->DbValue = $row['accountcode'];
		$this->userfield->DbValue = $row['userfield'];
		$this->did->DbValue = $row['did'];
		$this->recordingfile->DbValue = $row['recordingfile'];
		$this->cnum->DbValue = $row['cnum'];
		$this->cnam->DbValue = $row['cnam'];
		$this->outbound_cnum->DbValue = $row['outbound_cnum'];
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
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// calldate
		// uniqueid
		// clid
		// src
		// dst
		// dcontext
		// channel
		// dstchannel
		// lastapp
		// lastdata
		// duration
		// billsec
		// disposition
		// amaflags
		// accountcode
		// userfield
		// did
		// recordingfile
		// cnum
		// cnam
		// outbound_cnum
		// outbound_cnam
		// dst_cnam
		// linkedid
		// peeraccount
		// sequence

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// calldate
		$this->calldate->ViewValue = $this->calldate->CurrentValue;
		$this->calldate->ViewValue = ew_FormatDateTime($this->calldate->ViewValue, 9);
		$this->calldate->ViewCustomAttributes = "";

		// uniqueid
		$this->uniqueid->ViewValue = $this->uniqueid->CurrentValue;
		$this->uniqueid->ViewCustomAttributes = "";

		// clid
		$this->clid->ViewValue = $this->clid->CurrentValue;
		$this->clid->ViewCustomAttributes = "";

		// src
		$this->src->ViewValue = $this->src->CurrentValue;
		$this->src->ViewCustomAttributes = "";

		// dst
		$this->dst->ViewValue = $this->dst->CurrentValue;
		$this->dst->ViewCustomAttributes = "";

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

		// duration
		$this->duration->ViewValue = $this->duration->CurrentValue;
		$this->duration->ViewCustomAttributes = "";

		// billsec
		$this->billsec->ViewValue = $this->billsec->CurrentValue;
		$this->billsec->ViewCustomAttributes = "";

		// disposition
		$this->disposition->ViewValue = $this->disposition->CurrentValue;
		$this->disposition->ViewCustomAttributes = "";

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

		// recordingfile
		$this->recordingfile->ViewValue = $this->recordingfile->CurrentValue;
		$this->recordingfile->ViewCustomAttributes = "";

		// cnum
		$this->cnum->ViewValue = $this->cnum->CurrentValue;
		$this->cnum->ViewCustomAttributes = "";

		// cnam
		$this->cnam->ViewValue = $this->cnam->CurrentValue;
		$this->cnam->ViewCustomAttributes = "";

		// outbound_cnum
		$this->outbound_cnum->ViewValue = $this->outbound_cnum->CurrentValue;
		$this->outbound_cnum->ViewCustomAttributes = "";

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
			if (!ew_Empty($this->uniqueid->CurrentValue)) {
				$this->uniqueid->HrefValue = $this->uniqueid->CurrentValue; // Add prefix/suffix
				$this->uniqueid->LinkAttrs["target"] = ""; // Add target
				if ($this->Export <> "") $this->uniqueid->HrefValue = ew_ConvertFullUrl($this->uniqueid->HrefValue);
			} else {
				$this->uniqueid->HrefValue = "";
			}
			$this->uniqueid->TooltipValue = "";

			// clid
			$this->clid->LinkCustomAttributes = "";
			$this->clid->HrefValue = "";
			$this->clid->TooltipValue = "";

			// src
			$this->src->LinkCustomAttributes = "";
			$this->src->HrefValue = "";
			$this->src->TooltipValue = "";

			// dst
			$this->dst->LinkCustomAttributes = "";
			$this->dst->HrefValue = "";
			$this->dst->TooltipValue = "";

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

			// recordingfile
			$this->recordingfile->LinkCustomAttributes = "";
			$this->recordingfile->HrefValue = "";
			$this->recordingfile->TooltipValue = "";

			// cnum
			$this->cnum->LinkCustomAttributes = "";
			$this->cnum->HrefValue = "";
			$this->cnum->TooltipValue = "";

			// cnam
			$this->cnam->LinkCustomAttributes = "";
			$this->cnam->HrefValue = "";
			$this->cnam->TooltipValue = "";

			// outbound_cnum
			$this->outbound_cnum->LinkCustomAttributes = "";
			$this->outbound_cnum->HrefValue = "";
			$this->outbound_cnum->TooltipValue = "";

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
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// calldate
			$this->calldate->EditAttrs["class"] = "form-control";
			$this->calldate->EditCustomAttributes = "";
			$this->calldate->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->calldate->CurrentValue, 9));
			$this->calldate->PlaceHolder = ew_RemoveHtml($this->calldate->FldCaption());

			// uniqueid
			$this->uniqueid->EditAttrs["class"] = "form-control";
			$this->uniqueid->EditCustomAttributes = "";
			$this->uniqueid->EditValue = $this->uniqueid->CurrentValue;
			$this->uniqueid->ViewCustomAttributes = "";

			// clid
			$this->clid->EditAttrs["class"] = "form-control";
			$this->clid->EditCustomAttributes = "";
			$this->clid->EditValue = ew_HtmlEncode($this->clid->CurrentValue);
			$this->clid->PlaceHolder = ew_RemoveHtml($this->clid->FldCaption());

			// src
			$this->src->EditAttrs["class"] = "form-control";
			$this->src->EditCustomAttributes = "";
			$this->src->EditValue = ew_HtmlEncode($this->src->CurrentValue);
			$this->src->PlaceHolder = ew_RemoveHtml($this->src->FldCaption());

			// dst
			$this->dst->EditAttrs["class"] = "form-control";
			$this->dst->EditCustomAttributes = "";
			$this->dst->EditValue = ew_HtmlEncode($this->dst->CurrentValue);
			$this->dst->PlaceHolder = ew_RemoveHtml($this->dst->FldCaption());

			// dcontext
			$this->dcontext->EditAttrs["class"] = "form-control";
			$this->dcontext->EditCustomAttributes = "";
			$this->dcontext->EditValue = ew_HtmlEncode($this->dcontext->CurrentValue);
			$this->dcontext->PlaceHolder = ew_RemoveHtml($this->dcontext->FldCaption());

			// channel
			$this->channel->EditAttrs["class"] = "form-control";
			$this->channel->EditCustomAttributes = "";
			$this->channel->EditValue = ew_HtmlEncode($this->channel->CurrentValue);
			$this->channel->PlaceHolder = ew_RemoveHtml($this->channel->FldCaption());

			// dstchannel
			$this->dstchannel->EditAttrs["class"] = "form-control";
			$this->dstchannel->EditCustomAttributes = "";
			$this->dstchannel->EditValue = ew_HtmlEncode($this->dstchannel->CurrentValue);
			$this->dstchannel->PlaceHolder = ew_RemoveHtml($this->dstchannel->FldCaption());

			// lastapp
			$this->lastapp->EditAttrs["class"] = "form-control";
			$this->lastapp->EditCustomAttributes = "";
			$this->lastapp->EditValue = ew_HtmlEncode($this->lastapp->CurrentValue);
			$this->lastapp->PlaceHolder = ew_RemoveHtml($this->lastapp->FldCaption());

			// lastdata
			$this->lastdata->EditAttrs["class"] = "form-control";
			$this->lastdata->EditCustomAttributes = "";
			$this->lastdata->EditValue = ew_HtmlEncode($this->lastdata->CurrentValue);
			$this->lastdata->PlaceHolder = ew_RemoveHtml($this->lastdata->FldCaption());

			// duration
			$this->duration->EditAttrs["class"] = "form-control";
			$this->duration->EditCustomAttributes = "";
			$this->duration->EditValue = ew_HtmlEncode($this->duration->CurrentValue);
			$this->duration->PlaceHolder = ew_RemoveHtml($this->duration->FldCaption());

			// billsec
			$this->billsec->EditAttrs["class"] = "form-control";
			$this->billsec->EditCustomAttributes = "";
			$this->billsec->EditValue = ew_HtmlEncode($this->billsec->CurrentValue);
			$this->billsec->PlaceHolder = ew_RemoveHtml($this->billsec->FldCaption());

			// disposition
			$this->disposition->EditAttrs["class"] = "form-control";
			$this->disposition->EditCustomAttributes = "";
			$this->disposition->EditValue = ew_HtmlEncode($this->disposition->CurrentValue);
			$this->disposition->PlaceHolder = ew_RemoveHtml($this->disposition->FldCaption());

			// amaflags
			$this->amaflags->EditAttrs["class"] = "form-control";
			$this->amaflags->EditCustomAttributes = "";
			$this->amaflags->EditValue = ew_HtmlEncode($this->amaflags->CurrentValue);
			$this->amaflags->PlaceHolder = ew_RemoveHtml($this->amaflags->FldCaption());

			// accountcode
			$this->accountcode->EditAttrs["class"] = "form-control";
			$this->accountcode->EditCustomAttributes = "";
			$this->accountcode->EditValue = ew_HtmlEncode($this->accountcode->CurrentValue);
			$this->accountcode->PlaceHolder = ew_RemoveHtml($this->accountcode->FldCaption());

			// userfield
			$this->userfield->EditAttrs["class"] = "form-control";
			$this->userfield->EditCustomAttributes = "";
			$this->userfield->EditValue = ew_HtmlEncode($this->userfield->CurrentValue);
			$this->userfield->PlaceHolder = ew_RemoveHtml($this->userfield->FldCaption());

			// did
			$this->did->EditAttrs["class"] = "form-control";
			$this->did->EditCustomAttributes = "";
			$this->did->EditValue = ew_HtmlEncode($this->did->CurrentValue);
			$this->did->PlaceHolder = ew_RemoveHtml($this->did->FldCaption());

			// recordingfile
			$this->recordingfile->EditAttrs["class"] = "form-control";
			$this->recordingfile->EditCustomAttributes = "";
			$this->recordingfile->EditValue = ew_HtmlEncode($this->recordingfile->CurrentValue);
			$this->recordingfile->PlaceHolder = ew_RemoveHtml($this->recordingfile->FldCaption());

			// cnum
			$this->cnum->EditAttrs["class"] = "form-control";
			$this->cnum->EditCustomAttributes = "";
			$this->cnum->EditValue = ew_HtmlEncode($this->cnum->CurrentValue);
			$this->cnum->PlaceHolder = ew_RemoveHtml($this->cnum->FldCaption());

			// cnam
			$this->cnam->EditAttrs["class"] = "form-control";
			$this->cnam->EditCustomAttributes = "";
			$this->cnam->EditValue = ew_HtmlEncode($this->cnam->CurrentValue);
			$this->cnam->PlaceHolder = ew_RemoveHtml($this->cnam->FldCaption());

			// outbound_cnum
			$this->outbound_cnum->EditAttrs["class"] = "form-control";
			$this->outbound_cnum->EditCustomAttributes = "";
			$this->outbound_cnum->EditValue = ew_HtmlEncode($this->outbound_cnum->CurrentValue);
			$this->outbound_cnum->PlaceHolder = ew_RemoveHtml($this->outbound_cnum->FldCaption());

			// outbound_cnam
			$this->outbound_cnam->EditAttrs["class"] = "form-control";
			$this->outbound_cnam->EditCustomAttributes = "";
			$this->outbound_cnam->EditValue = ew_HtmlEncode($this->outbound_cnam->CurrentValue);
			$this->outbound_cnam->PlaceHolder = ew_RemoveHtml($this->outbound_cnam->FldCaption());

			// dst_cnam
			$this->dst_cnam->EditAttrs["class"] = "form-control";
			$this->dst_cnam->EditCustomAttributes = "";
			$this->dst_cnam->EditValue = ew_HtmlEncode($this->dst_cnam->CurrentValue);
			$this->dst_cnam->PlaceHolder = ew_RemoveHtml($this->dst_cnam->FldCaption());

			// linkedid
			$this->linkedid->EditAttrs["class"] = "form-control";
			$this->linkedid->EditCustomAttributes = "";
			$this->linkedid->EditValue = ew_HtmlEncode($this->linkedid->CurrentValue);
			$this->linkedid->PlaceHolder = ew_RemoveHtml($this->linkedid->FldCaption());

			// peeraccount
			$this->peeraccount->EditAttrs["class"] = "form-control";
			$this->peeraccount->EditCustomAttributes = "";
			$this->peeraccount->EditValue = ew_HtmlEncode($this->peeraccount->CurrentValue);
			$this->peeraccount->PlaceHolder = ew_RemoveHtml($this->peeraccount->FldCaption());

			// sequence
			$this->sequence->EditAttrs["class"] = "form-control";
			$this->sequence->EditCustomAttributes = "";
			$this->sequence->EditValue = ew_HtmlEncode($this->sequence->CurrentValue);
			$this->sequence->PlaceHolder = ew_RemoveHtml($this->sequence->FldCaption());

			// Edit refer script
			// calldate

			$this->calldate->LinkCustomAttributes = "";
			$this->calldate->HrefValue = "";

			// uniqueid
			$this->uniqueid->LinkCustomAttributes = "";
			if (!ew_Empty($this->uniqueid->CurrentValue)) {
				$this->uniqueid->HrefValue = $this->uniqueid->CurrentValue; // Add prefix/suffix
				$this->uniqueid->LinkAttrs["target"] = ""; // Add target
				if ($this->Export <> "") $this->uniqueid->HrefValue = ew_ConvertFullUrl($this->uniqueid->HrefValue);
			} else {
				$this->uniqueid->HrefValue = "";
			}

			// clid
			$this->clid->LinkCustomAttributes = "";
			$this->clid->HrefValue = "";

			// src
			$this->src->LinkCustomAttributes = "";
			$this->src->HrefValue = "";

			// dst
			$this->dst->LinkCustomAttributes = "";
			$this->dst->HrefValue = "";

			// dcontext
			$this->dcontext->LinkCustomAttributes = "";
			$this->dcontext->HrefValue = "";

			// channel
			$this->channel->LinkCustomAttributes = "";
			$this->channel->HrefValue = "";

			// dstchannel
			$this->dstchannel->LinkCustomAttributes = "";
			$this->dstchannel->HrefValue = "";

			// lastapp
			$this->lastapp->LinkCustomAttributes = "";
			$this->lastapp->HrefValue = "";

			// lastdata
			$this->lastdata->LinkCustomAttributes = "";
			$this->lastdata->HrefValue = "";

			// duration
			$this->duration->LinkCustomAttributes = "";
			$this->duration->HrefValue = "";

			// billsec
			$this->billsec->LinkCustomAttributes = "";
			$this->billsec->HrefValue = "";

			// disposition
			$this->disposition->LinkCustomAttributes = "";
			$this->disposition->HrefValue = "";

			// amaflags
			$this->amaflags->LinkCustomAttributes = "";
			$this->amaflags->HrefValue = "";

			// accountcode
			$this->accountcode->LinkCustomAttributes = "";
			$this->accountcode->HrefValue = "";

			// userfield
			$this->userfield->LinkCustomAttributes = "";
			$this->userfield->HrefValue = "";

			// did
			$this->did->LinkCustomAttributes = "";
			$this->did->HrefValue = "";

			// recordingfile
			$this->recordingfile->LinkCustomAttributes = "";
			$this->recordingfile->HrefValue = "";

			// cnum
			$this->cnum->LinkCustomAttributes = "";
			$this->cnum->HrefValue = "";

			// cnam
			$this->cnam->LinkCustomAttributes = "";
			$this->cnam->HrefValue = "";

			// outbound_cnum
			$this->outbound_cnum->LinkCustomAttributes = "";
			$this->outbound_cnum->HrefValue = "";

			// outbound_cnam
			$this->outbound_cnam->LinkCustomAttributes = "";
			$this->outbound_cnam->HrefValue = "";

			// dst_cnam
			$this->dst_cnam->LinkCustomAttributes = "";
			$this->dst_cnam->HrefValue = "";

			// linkedid
			$this->linkedid->LinkCustomAttributes = "";
			$this->linkedid->HrefValue = "";

			// peeraccount
			$this->peeraccount->LinkCustomAttributes = "";
			$this->peeraccount->HrefValue = "";

			// sequence
			$this->sequence->LinkCustomAttributes = "";
			$this->sequence->HrefValue = "";
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

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->calldate->FldIsDetailKey && !is_null($this->calldate->FormValue) && $this->calldate->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->calldate->FldCaption(), $this->calldate->ReqErrMsg));
		}
		if (!ew_CheckDate($this->calldate->FormValue)) {
			ew_AddMessage($gsFormError, $this->calldate->FldErrMsg());
		}
		if (!$this->uniqueid->FldIsDetailKey && !is_null($this->uniqueid->FormValue) && $this->uniqueid->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->uniqueid->FldCaption(), $this->uniqueid->ReqErrMsg));
		}
		if (!$this->clid->FldIsDetailKey && !is_null($this->clid->FormValue) && $this->clid->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->clid->FldCaption(), $this->clid->ReqErrMsg));
		}
		if (!$this->src->FldIsDetailKey && !is_null($this->src->FormValue) && $this->src->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->src->FldCaption(), $this->src->ReqErrMsg));
		}
		if (!$this->dst->FldIsDetailKey && !is_null($this->dst->FormValue) && $this->dst->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->dst->FldCaption(), $this->dst->ReqErrMsg));
		}
		if (!$this->dcontext->FldIsDetailKey && !is_null($this->dcontext->FormValue) && $this->dcontext->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->dcontext->FldCaption(), $this->dcontext->ReqErrMsg));
		}
		if (!$this->channel->FldIsDetailKey && !is_null($this->channel->FormValue) && $this->channel->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->channel->FldCaption(), $this->channel->ReqErrMsg));
		}
		if (!$this->dstchannel->FldIsDetailKey && !is_null($this->dstchannel->FormValue) && $this->dstchannel->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->dstchannel->FldCaption(), $this->dstchannel->ReqErrMsg));
		}
		if (!$this->lastapp->FldIsDetailKey && !is_null($this->lastapp->FormValue) && $this->lastapp->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->lastapp->FldCaption(), $this->lastapp->ReqErrMsg));
		}
		if (!$this->lastdata->FldIsDetailKey && !is_null($this->lastdata->FormValue) && $this->lastdata->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->lastdata->FldCaption(), $this->lastdata->ReqErrMsg));
		}
		if (!$this->duration->FldIsDetailKey && !is_null($this->duration->FormValue) && $this->duration->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->duration->FldCaption(), $this->duration->ReqErrMsg));
		}
		if (!ew_CheckInteger($this->duration->FormValue)) {
			ew_AddMessage($gsFormError, $this->duration->FldErrMsg());
		}
		if (!$this->billsec->FldIsDetailKey && !is_null($this->billsec->FormValue) && $this->billsec->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->billsec->FldCaption(), $this->billsec->ReqErrMsg));
		}
		if (!ew_CheckInteger($this->billsec->FormValue)) {
			ew_AddMessage($gsFormError, $this->billsec->FldErrMsg());
		}
		if (!$this->disposition->FldIsDetailKey && !is_null($this->disposition->FormValue) && $this->disposition->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->disposition->FldCaption(), $this->disposition->ReqErrMsg));
		}
		if (!$this->amaflags->FldIsDetailKey && !is_null($this->amaflags->FormValue) && $this->amaflags->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->amaflags->FldCaption(), $this->amaflags->ReqErrMsg));
		}
		if (!ew_CheckInteger($this->amaflags->FormValue)) {
			ew_AddMessage($gsFormError, $this->amaflags->FldErrMsg());
		}
		if (!$this->accountcode->FldIsDetailKey && !is_null($this->accountcode->FormValue) && $this->accountcode->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->accountcode->FldCaption(), $this->accountcode->ReqErrMsg));
		}
		if (!$this->userfield->FldIsDetailKey && !is_null($this->userfield->FormValue) && $this->userfield->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->userfield->FldCaption(), $this->userfield->ReqErrMsg));
		}
		if (!$this->did->FldIsDetailKey && !is_null($this->did->FormValue) && $this->did->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->did->FldCaption(), $this->did->ReqErrMsg));
		}
		if (!$this->recordingfile->FldIsDetailKey && !is_null($this->recordingfile->FormValue) && $this->recordingfile->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->recordingfile->FldCaption(), $this->recordingfile->ReqErrMsg));
		}
		if (!$this->cnum->FldIsDetailKey && !is_null($this->cnum->FormValue) && $this->cnum->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->cnum->FldCaption(), $this->cnum->ReqErrMsg));
		}
		if (!$this->cnam->FldIsDetailKey && !is_null($this->cnam->FormValue) && $this->cnam->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->cnam->FldCaption(), $this->cnam->ReqErrMsg));
		}
		if (!$this->outbound_cnum->FldIsDetailKey && !is_null($this->outbound_cnum->FormValue) && $this->outbound_cnum->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->outbound_cnum->FldCaption(), $this->outbound_cnum->ReqErrMsg));
		}
		if (!$this->outbound_cnam->FldIsDetailKey && !is_null($this->outbound_cnam->FormValue) && $this->outbound_cnam->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->outbound_cnam->FldCaption(), $this->outbound_cnam->ReqErrMsg));
		}
		if (!$this->dst_cnam->FldIsDetailKey && !is_null($this->dst_cnam->FormValue) && $this->dst_cnam->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->dst_cnam->FldCaption(), $this->dst_cnam->ReqErrMsg));
		}
		if (!$this->linkedid->FldIsDetailKey && !is_null($this->linkedid->FormValue) && $this->linkedid->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->linkedid->FldCaption(), $this->linkedid->ReqErrMsg));
		}
		if (!$this->peeraccount->FldIsDetailKey && !is_null($this->peeraccount->FormValue) && $this->peeraccount->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->peeraccount->FldCaption(), $this->peeraccount->ReqErrMsg));
		}
		if (!$this->sequence->FldIsDetailKey && !is_null($this->sequence->FormValue) && $this->sequence->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->sequence->FldCaption(), $this->sequence->ReqErrMsg));
		}
		if (!ew_CheckInteger($this->sequence->FormValue)) {
			ew_AddMessage($gsFormError, $this->sequence->FldErrMsg());
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Update record based on key values
	function EditRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$conn = &$this->Connection();
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// calldate
			$this->calldate->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->calldate->CurrentValue, 9), ew_CurrentDate(), $this->calldate->ReadOnly);

			// uniqueid
			// clid

			$this->clid->SetDbValueDef($rsnew, $this->clid->CurrentValue, "", $this->clid->ReadOnly);

			// src
			$this->src->SetDbValueDef($rsnew, $this->src->CurrentValue, "", $this->src->ReadOnly);

			// dst
			$this->dst->SetDbValueDef($rsnew, $this->dst->CurrentValue, "", $this->dst->ReadOnly);

			// dcontext
			$this->dcontext->SetDbValueDef($rsnew, $this->dcontext->CurrentValue, "", $this->dcontext->ReadOnly);

			// channel
			$this->channel->SetDbValueDef($rsnew, $this->channel->CurrentValue, "", $this->channel->ReadOnly);

			// dstchannel
			$this->dstchannel->SetDbValueDef($rsnew, $this->dstchannel->CurrentValue, "", $this->dstchannel->ReadOnly);

			// lastapp
			$this->lastapp->SetDbValueDef($rsnew, $this->lastapp->CurrentValue, "", $this->lastapp->ReadOnly);

			// lastdata
			$this->lastdata->SetDbValueDef($rsnew, $this->lastdata->CurrentValue, "", $this->lastdata->ReadOnly);

			// duration
			$this->duration->SetDbValueDef($rsnew, $this->duration->CurrentValue, 0, $this->duration->ReadOnly);

			// billsec
			$this->billsec->SetDbValueDef($rsnew, $this->billsec->CurrentValue, 0, $this->billsec->ReadOnly);

			// disposition
			$this->disposition->SetDbValueDef($rsnew, $this->disposition->CurrentValue, "", $this->disposition->ReadOnly);

			// amaflags
			$this->amaflags->SetDbValueDef($rsnew, $this->amaflags->CurrentValue, 0, $this->amaflags->ReadOnly);

			// accountcode
			$this->accountcode->SetDbValueDef($rsnew, $this->accountcode->CurrentValue, "", $this->accountcode->ReadOnly);

			// userfield
			$this->userfield->SetDbValueDef($rsnew, $this->userfield->CurrentValue, "", $this->userfield->ReadOnly);

			// did
			$this->did->SetDbValueDef($rsnew, $this->did->CurrentValue, "", $this->did->ReadOnly);

			// recordingfile
			$this->recordingfile->SetDbValueDef($rsnew, $this->recordingfile->CurrentValue, "", $this->recordingfile->ReadOnly);

			// cnum
			$this->cnum->SetDbValueDef($rsnew, $this->cnum->CurrentValue, "", $this->cnum->ReadOnly);

			// cnam
			$this->cnam->SetDbValueDef($rsnew, $this->cnam->CurrentValue, "", $this->cnam->ReadOnly);

			// outbound_cnum
			$this->outbound_cnum->SetDbValueDef($rsnew, $this->outbound_cnum->CurrentValue, "", $this->outbound_cnum->ReadOnly);

			// outbound_cnam
			$this->outbound_cnam->SetDbValueDef($rsnew, $this->outbound_cnam->CurrentValue, "", $this->outbound_cnam->ReadOnly);

			// dst_cnam
			$this->dst_cnam->SetDbValueDef($rsnew, $this->dst_cnam->CurrentValue, "", $this->dst_cnam->ReadOnly);

			// linkedid
			$this->linkedid->SetDbValueDef($rsnew, $this->linkedid->CurrentValue, "", $this->linkedid->ReadOnly);

			// peeraccount
			$this->peeraccount->SetDbValueDef($rsnew, $this->peeraccount->CurrentValue, "", $this->peeraccount->ReadOnly);

			// sequence
			$this->sequence->SetDbValueDef($rsnew, $this->sequence->CurrentValue, 0, $this->sequence->ReadOnly);

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
				}
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		$rs->Close();
		return $EditRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("cdrlist.php"), "", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url);
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
if (!isset($cdr_edit)) $cdr_edit = new ccdr_edit();

// Page init
$cdr_edit->Page_Init();

// Page main
$cdr_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$cdr_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "edit";
var CurrentForm = fcdredit = new ew_Form("fcdredit", "edit");

// Validate form
fcdredit.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_calldate");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->calldate->FldCaption(), $cdr->calldate->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_calldate");
			if (elm && !ew_CheckDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($cdr->calldate->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_uniqueid");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->uniqueid->FldCaption(), $cdr->uniqueid->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_clid");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->clid->FldCaption(), $cdr->clid->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_src");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->src->FldCaption(), $cdr->src->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_dst");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->dst->FldCaption(), $cdr->dst->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_dcontext");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->dcontext->FldCaption(), $cdr->dcontext->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_channel");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->channel->FldCaption(), $cdr->channel->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_dstchannel");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->dstchannel->FldCaption(), $cdr->dstchannel->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_lastapp");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->lastapp->FldCaption(), $cdr->lastapp->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_lastdata");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->lastdata->FldCaption(), $cdr->lastdata->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_duration");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->duration->FldCaption(), $cdr->duration->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_duration");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($cdr->duration->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_billsec");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->billsec->FldCaption(), $cdr->billsec->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_billsec");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($cdr->billsec->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_disposition");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->disposition->FldCaption(), $cdr->disposition->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_amaflags");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->amaflags->FldCaption(), $cdr->amaflags->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_amaflags");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($cdr->amaflags->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_accountcode");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->accountcode->FldCaption(), $cdr->accountcode->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_userfield");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->userfield->FldCaption(), $cdr->userfield->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_did");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->did->FldCaption(), $cdr->did->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_recordingfile");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->recordingfile->FldCaption(), $cdr->recordingfile->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_recordingfile");
			if (elm && typeof(isMediaElementjs) == "function" && !isMediaElementjs(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($cdr->recordingfile->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_cnum");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->cnum->FldCaption(), $cdr->cnum->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_cnam");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->cnam->FldCaption(), $cdr->cnam->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_outbound_cnum");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->outbound_cnum->FldCaption(), $cdr->outbound_cnum->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_outbound_cnam");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->outbound_cnam->FldCaption(), $cdr->outbound_cnam->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_dst_cnam");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->dst_cnam->FldCaption(), $cdr->dst_cnam->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_linkedid");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->linkedid->FldCaption(), $cdr->linkedid->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_peeraccount");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->peeraccount->FldCaption(), $cdr->peeraccount->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_sequence");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $cdr->sequence->FldCaption(), $cdr->sequence->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_sequence");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($cdr->sequence->FldErrMsg()) ?>");

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
fcdredit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcdredit.ValidateRequired = true;
<?php } else { ?>
fcdredit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $cdr_edit->ShowPageHeader(); ?>
<?php
$cdr_edit->ShowMessage();
?>
<form name="ewPagerForm" class="form-horizontal ewForm ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($cdr_edit->Pager)) $cdr_edit->Pager = new cPrevNextPager($cdr_edit->StartRec, $cdr_edit->DisplayRecs, $cdr_edit->TotalRecs) ?>
<?php if ($cdr_edit->Pager->RecordCount > 0) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($cdr_edit->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $cdr_edit->PageUrl() ?>start=<?php echo $cdr_edit->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($cdr_edit->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $cdr_edit->PageUrl() ?>start=<?php echo $cdr_edit->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $cdr_edit->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($cdr_edit->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $cdr_edit->PageUrl() ?>start=<?php echo $cdr_edit->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($cdr_edit->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $cdr_edit->PageUrl() ?>start=<?php echo $cdr_edit->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $cdr_edit->Pager->PageCount ?></span>
</div>
<?php } ?>
<div class="clearfix"></div>
</form>
<form name="fcdredit" id="fcdredit" class="<?php echo $cdr_edit->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($cdr_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $cdr_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="cdr">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<div>
<?php if ($cdr->calldate->Visible) { // calldate ?>
	<div id="r_calldate" class="form-group">
		<label id="elh_cdr_calldate" for="x_calldate" class="col-sm-2 control-label ewLabel"><?php echo $cdr->calldate->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->calldate->CellAttributes() ?>>
<span id="el_cdr_calldate">
<input type="text" data-table="cdr" data-field="x_calldate" data-format="9" name="x_calldate" id="x_calldate" placeholder="<?php echo ew_HtmlEncode($cdr->calldate->getPlaceHolder()) ?>" value="<?php echo $cdr->calldate->EditValue ?>"<?php echo $cdr->calldate->EditAttributes() ?>>
</span>
<?php echo $cdr->calldate->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->uniqueid->Visible) { // uniqueid ?>
	<div id="r_uniqueid" class="form-group">
		<label id="elh_cdr_uniqueid" for="x_uniqueid" class="col-sm-2 control-label ewLabel"><?php echo $cdr->uniqueid->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->uniqueid->CellAttributes() ?>>
<span id="el_cdr_uniqueid">
<span<?php echo $cdr->uniqueid->ViewAttributes() ?>>
<?php if ((!ew_EmptyStr($cdr->uniqueid->EditValue)) && $cdr->uniqueid->LinkAttributes() <> "") { ?>
<a<?php echo $cdr->uniqueid->LinkAttributes() ?>><p class="form-control-static"><?php echo $cdr->uniqueid->EditValue ?></p></a>
<?php } else { ?>
<p class="form-control-static"><?php echo $cdr->uniqueid->EditValue ?></p>
<?php } ?>
</span>
</span>
<input type="hidden" data-table="cdr" data-field="x_uniqueid" name="x_uniqueid" id="x_uniqueid" value="<?php echo ew_HtmlEncode($cdr->uniqueid->CurrentValue) ?>">
<?php echo $cdr->uniqueid->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->clid->Visible) { // clid ?>
	<div id="r_clid" class="form-group">
		<label id="elh_cdr_clid" for="x_clid" class="col-sm-2 control-label ewLabel"><?php echo $cdr->clid->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->clid->CellAttributes() ?>>
<span id="el_cdr_clid">
<input type="text" data-table="cdr" data-field="x_clid" name="x_clid" id="x_clid" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->clid->getPlaceHolder()) ?>" value="<?php echo $cdr->clid->EditValue ?>"<?php echo $cdr->clid->EditAttributes() ?>>
</span>
<?php echo $cdr->clid->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->src->Visible) { // src ?>
	<div id="r_src" class="form-group">
		<label id="elh_cdr_src" for="x_src" class="col-sm-2 control-label ewLabel"><?php echo $cdr->src->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->src->CellAttributes() ?>>
<span id="el_cdr_src">
<input type="text" data-table="cdr" data-field="x_src" name="x_src" id="x_src" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->src->getPlaceHolder()) ?>" value="<?php echo $cdr->src->EditValue ?>"<?php echo $cdr->src->EditAttributes() ?>>
</span>
<?php echo $cdr->src->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->dst->Visible) { // dst ?>
	<div id="r_dst" class="form-group">
		<label id="elh_cdr_dst" for="x_dst" class="col-sm-2 control-label ewLabel"><?php echo $cdr->dst->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->dst->CellAttributes() ?>>
<span id="el_cdr_dst">
<input type="text" data-table="cdr" data-field="x_dst" name="x_dst" id="x_dst" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->dst->getPlaceHolder()) ?>" value="<?php echo $cdr->dst->EditValue ?>"<?php echo $cdr->dst->EditAttributes() ?>>
</span>
<?php echo $cdr->dst->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->dcontext->Visible) { // dcontext ?>
	<div id="r_dcontext" class="form-group">
		<label id="elh_cdr_dcontext" for="x_dcontext" class="col-sm-2 control-label ewLabel"><?php echo $cdr->dcontext->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->dcontext->CellAttributes() ?>>
<span id="el_cdr_dcontext">
<input type="text" data-table="cdr" data-field="x_dcontext" name="x_dcontext" id="x_dcontext" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->dcontext->getPlaceHolder()) ?>" value="<?php echo $cdr->dcontext->EditValue ?>"<?php echo $cdr->dcontext->EditAttributes() ?>>
</span>
<?php echo $cdr->dcontext->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->channel->Visible) { // channel ?>
	<div id="r_channel" class="form-group">
		<label id="elh_cdr_channel" for="x_channel" class="col-sm-2 control-label ewLabel"><?php echo $cdr->channel->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->channel->CellAttributes() ?>>
<span id="el_cdr_channel">
<input type="text" data-table="cdr" data-field="x_channel" name="x_channel" id="x_channel" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->channel->getPlaceHolder()) ?>" value="<?php echo $cdr->channel->EditValue ?>"<?php echo $cdr->channel->EditAttributes() ?>>
</span>
<?php echo $cdr->channel->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->dstchannel->Visible) { // dstchannel ?>
	<div id="r_dstchannel" class="form-group">
		<label id="elh_cdr_dstchannel" for="x_dstchannel" class="col-sm-2 control-label ewLabel"><?php echo $cdr->dstchannel->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->dstchannel->CellAttributes() ?>>
<span id="el_cdr_dstchannel">
<input type="text" data-table="cdr" data-field="x_dstchannel" name="x_dstchannel" id="x_dstchannel" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->dstchannel->getPlaceHolder()) ?>" value="<?php echo $cdr->dstchannel->EditValue ?>"<?php echo $cdr->dstchannel->EditAttributes() ?>>
</span>
<?php echo $cdr->dstchannel->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->lastapp->Visible) { // lastapp ?>
	<div id="r_lastapp" class="form-group">
		<label id="elh_cdr_lastapp" for="x_lastapp" class="col-sm-2 control-label ewLabel"><?php echo $cdr->lastapp->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->lastapp->CellAttributes() ?>>
<span id="el_cdr_lastapp">
<input type="text" data-table="cdr" data-field="x_lastapp" name="x_lastapp" id="x_lastapp" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->lastapp->getPlaceHolder()) ?>" value="<?php echo $cdr->lastapp->EditValue ?>"<?php echo $cdr->lastapp->EditAttributes() ?>>
</span>
<?php echo $cdr->lastapp->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->lastdata->Visible) { // lastdata ?>
	<div id="r_lastdata" class="form-group">
		<label id="elh_cdr_lastdata" for="x_lastdata" class="col-sm-2 control-label ewLabel"><?php echo $cdr->lastdata->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->lastdata->CellAttributes() ?>>
<span id="el_cdr_lastdata">
<input type="text" data-table="cdr" data-field="x_lastdata" name="x_lastdata" id="x_lastdata" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->lastdata->getPlaceHolder()) ?>" value="<?php echo $cdr->lastdata->EditValue ?>"<?php echo $cdr->lastdata->EditAttributes() ?>>
</span>
<?php echo $cdr->lastdata->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->duration->Visible) { // duration ?>
	<div id="r_duration" class="form-group">
		<label id="elh_cdr_duration" for="x_duration" class="col-sm-2 control-label ewLabel"><?php echo $cdr->duration->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->duration->CellAttributes() ?>>
<span id="el_cdr_duration">
<input type="text" data-table="cdr" data-field="x_duration" name="x_duration" id="x_duration" size="30" placeholder="<?php echo ew_HtmlEncode($cdr->duration->getPlaceHolder()) ?>" value="<?php echo $cdr->duration->EditValue ?>"<?php echo $cdr->duration->EditAttributes() ?>>
</span>
<?php echo $cdr->duration->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->billsec->Visible) { // billsec ?>
	<div id="r_billsec" class="form-group">
		<label id="elh_cdr_billsec" for="x_billsec" class="col-sm-2 control-label ewLabel"><?php echo $cdr->billsec->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->billsec->CellAttributes() ?>>
<span id="el_cdr_billsec">
<input type="text" data-table="cdr" data-field="x_billsec" name="x_billsec" id="x_billsec" size="30" placeholder="<?php echo ew_HtmlEncode($cdr->billsec->getPlaceHolder()) ?>" value="<?php echo $cdr->billsec->EditValue ?>"<?php echo $cdr->billsec->EditAttributes() ?>>
</span>
<?php echo $cdr->billsec->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->disposition->Visible) { // disposition ?>
	<div id="r_disposition" class="form-group">
		<label id="elh_cdr_disposition" for="x_disposition" class="col-sm-2 control-label ewLabel"><?php echo $cdr->disposition->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->disposition->CellAttributes() ?>>
<span id="el_cdr_disposition">
<input type="text" data-table="cdr" data-field="x_disposition" name="x_disposition" id="x_disposition" size="30" maxlength="45" placeholder="<?php echo ew_HtmlEncode($cdr->disposition->getPlaceHolder()) ?>" value="<?php echo $cdr->disposition->EditValue ?>"<?php echo $cdr->disposition->EditAttributes() ?>>
</span>
<?php echo $cdr->disposition->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->amaflags->Visible) { // amaflags ?>
	<div id="r_amaflags" class="form-group">
		<label id="elh_cdr_amaflags" for="x_amaflags" class="col-sm-2 control-label ewLabel"><?php echo $cdr->amaflags->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->amaflags->CellAttributes() ?>>
<span id="el_cdr_amaflags">
<input type="text" data-table="cdr" data-field="x_amaflags" name="x_amaflags" id="x_amaflags" size="30" placeholder="<?php echo ew_HtmlEncode($cdr->amaflags->getPlaceHolder()) ?>" value="<?php echo $cdr->amaflags->EditValue ?>"<?php echo $cdr->amaflags->EditAttributes() ?>>
</span>
<?php echo $cdr->amaflags->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->accountcode->Visible) { // accountcode ?>
	<div id="r_accountcode" class="form-group">
		<label id="elh_cdr_accountcode" for="x_accountcode" class="col-sm-2 control-label ewLabel"><?php echo $cdr->accountcode->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->accountcode->CellAttributes() ?>>
<span id="el_cdr_accountcode">
<input type="text" data-table="cdr" data-field="x_accountcode" name="x_accountcode" id="x_accountcode" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($cdr->accountcode->getPlaceHolder()) ?>" value="<?php echo $cdr->accountcode->EditValue ?>"<?php echo $cdr->accountcode->EditAttributes() ?>>
</span>
<?php echo $cdr->accountcode->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->userfield->Visible) { // userfield ?>
	<div id="r_userfield" class="form-group">
		<label id="elh_cdr_userfield" for="x_userfield" class="col-sm-2 control-label ewLabel"><?php echo $cdr->userfield->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->userfield->CellAttributes() ?>>
<span id="el_cdr_userfield">
<input type="text" data-table="cdr" data-field="x_userfield" name="x_userfield" id="x_userfield" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($cdr->userfield->getPlaceHolder()) ?>" value="<?php echo $cdr->userfield->EditValue ?>"<?php echo $cdr->userfield->EditAttributes() ?>>
</span>
<?php echo $cdr->userfield->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->did->Visible) { // did ?>
	<div id="r_did" class="form-group">
		<label id="elh_cdr_did" for="x_did" class="col-sm-2 control-label ewLabel"><?php echo $cdr->did->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->did->CellAttributes() ?>>
<span id="el_cdr_did">
<input type="text" data-table="cdr" data-field="x_did" name="x_did" id="x_did" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($cdr->did->getPlaceHolder()) ?>" value="<?php echo $cdr->did->EditValue ?>"<?php echo $cdr->did->EditAttributes() ?>>
</span>
<?php echo $cdr->did->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->recordingfile->Visible) { // recordingfile ?>
	<div id="r_recordingfile" class="form-group">
		<label id="elh_cdr_recordingfile" for="x_recordingfile" class="col-sm-2 control-label ewLabel"><?php echo $cdr->recordingfile->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->recordingfile->CellAttributes() ?>>
<span id="el_cdr_recordingfile">
<input type="text" data-table="cdr" data-field="x_recordingfile" name="x_recordingfile" id="x_recordingfile" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($cdr->recordingfile->getPlaceHolder()) ?>" value="<?php echo $cdr->recordingfile->EditValue ?>"<?php echo $cdr->recordingfile->EditAttributes() ?>>
</span>
<?php echo $cdr->recordingfile->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->cnum->Visible) { // cnum ?>
	<div id="r_cnum" class="form-group">
		<label id="elh_cdr_cnum" for="x_cnum" class="col-sm-2 control-label ewLabel"><?php echo $cdr->cnum->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->cnum->CellAttributes() ?>>
<span id="el_cdr_cnum">
<input type="text" data-table="cdr" data-field="x_cnum" name="x_cnum" id="x_cnum" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->cnum->getPlaceHolder()) ?>" value="<?php echo $cdr->cnum->EditValue ?>"<?php echo $cdr->cnum->EditAttributes() ?>>
</span>
<?php echo $cdr->cnum->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->cnam->Visible) { // cnam ?>
	<div id="r_cnam" class="form-group">
		<label id="elh_cdr_cnam" for="x_cnam" class="col-sm-2 control-label ewLabel"><?php echo $cdr->cnam->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->cnam->CellAttributes() ?>>
<span id="el_cdr_cnam">
<input type="text" data-table="cdr" data-field="x_cnam" name="x_cnam" id="x_cnam" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->cnam->getPlaceHolder()) ?>" value="<?php echo $cdr->cnam->EditValue ?>"<?php echo $cdr->cnam->EditAttributes() ?>>
</span>
<?php echo $cdr->cnam->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->outbound_cnum->Visible) { // outbound_cnum ?>
	<div id="r_outbound_cnum" class="form-group">
		<label id="elh_cdr_outbound_cnum" for="x_outbound_cnum" class="col-sm-2 control-label ewLabel"><?php echo $cdr->outbound_cnum->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->outbound_cnum->CellAttributes() ?>>
<span id="el_cdr_outbound_cnum">
<input type="text" data-table="cdr" data-field="x_outbound_cnum" name="x_outbound_cnum" id="x_outbound_cnum" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->outbound_cnum->getPlaceHolder()) ?>" value="<?php echo $cdr->outbound_cnum->EditValue ?>"<?php echo $cdr->outbound_cnum->EditAttributes() ?>>
</span>
<?php echo $cdr->outbound_cnum->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->outbound_cnam->Visible) { // outbound_cnam ?>
	<div id="r_outbound_cnam" class="form-group">
		<label id="elh_cdr_outbound_cnam" for="x_outbound_cnam" class="col-sm-2 control-label ewLabel"><?php echo $cdr->outbound_cnam->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->outbound_cnam->CellAttributes() ?>>
<span id="el_cdr_outbound_cnam">
<input type="text" data-table="cdr" data-field="x_outbound_cnam" name="x_outbound_cnam" id="x_outbound_cnam" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->outbound_cnam->getPlaceHolder()) ?>" value="<?php echo $cdr->outbound_cnam->EditValue ?>"<?php echo $cdr->outbound_cnam->EditAttributes() ?>>
</span>
<?php echo $cdr->outbound_cnam->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->dst_cnam->Visible) { // dst_cnam ?>
	<div id="r_dst_cnam" class="form-group">
		<label id="elh_cdr_dst_cnam" for="x_dst_cnam" class="col-sm-2 control-label ewLabel"><?php echo $cdr->dst_cnam->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->dst_cnam->CellAttributes() ?>>
<span id="el_cdr_dst_cnam">
<input type="text" data-table="cdr" data-field="x_dst_cnam" name="x_dst_cnam" id="x_dst_cnam" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->dst_cnam->getPlaceHolder()) ?>" value="<?php echo $cdr->dst_cnam->EditValue ?>"<?php echo $cdr->dst_cnam->EditAttributes() ?>>
</span>
<?php echo $cdr->dst_cnam->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->linkedid->Visible) { // linkedid ?>
	<div id="r_linkedid" class="form-group">
		<label id="elh_cdr_linkedid" for="x_linkedid" class="col-sm-2 control-label ewLabel"><?php echo $cdr->linkedid->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->linkedid->CellAttributes() ?>>
<span id="el_cdr_linkedid">
<input type="text" data-table="cdr" data-field="x_linkedid" name="x_linkedid" id="x_linkedid" size="30" maxlength="32" placeholder="<?php echo ew_HtmlEncode($cdr->linkedid->getPlaceHolder()) ?>" value="<?php echo $cdr->linkedid->EditValue ?>"<?php echo $cdr->linkedid->EditAttributes() ?>>
</span>
<?php echo $cdr->linkedid->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->peeraccount->Visible) { // peeraccount ?>
	<div id="r_peeraccount" class="form-group">
		<label id="elh_cdr_peeraccount" for="x_peeraccount" class="col-sm-2 control-label ewLabel"><?php echo $cdr->peeraccount->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->peeraccount->CellAttributes() ?>>
<span id="el_cdr_peeraccount">
<input type="text" data-table="cdr" data-field="x_peeraccount" name="x_peeraccount" id="x_peeraccount" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->peeraccount->getPlaceHolder()) ?>" value="<?php echo $cdr->peeraccount->EditValue ?>"<?php echo $cdr->peeraccount->EditAttributes() ?>>
</span>
<?php echo $cdr->peeraccount->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($cdr->sequence->Visible) { // sequence ?>
	<div id="r_sequence" class="form-group">
		<label id="elh_cdr_sequence" for="x_sequence" class="col-sm-2 control-label ewLabel"><?php echo $cdr->sequence->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $cdr->sequence->CellAttributes() ?>>
<span id="el_cdr_sequence">
<input type="text" data-table="cdr" data-field="x_sequence" name="x_sequence" id="x_sequence" size="30" placeholder="<?php echo ew_HtmlEncode($cdr->sequence->getPlaceHolder()) ?>" value="<?php echo $cdr->sequence->EditValue ?>"<?php echo $cdr->sequence->EditAttributes() ?>>
</span>
<?php echo $cdr->sequence->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("SaveBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $cdr_edit->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
	</div>
</div>
<?php if (!isset($cdr_edit->Pager)) $cdr_edit->Pager = new cPrevNextPager($cdr_edit->StartRec, $cdr_edit->DisplayRecs, $cdr_edit->TotalRecs) ?>
<?php if ($cdr_edit->Pager->RecordCount > 0) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($cdr_edit->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $cdr_edit->PageUrl() ?>start=<?php echo $cdr_edit->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($cdr_edit->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $cdr_edit->PageUrl() ?>start=<?php echo $cdr_edit->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $cdr_edit->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($cdr_edit->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $cdr_edit->PageUrl() ?>start=<?php echo $cdr_edit->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($cdr_edit->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $cdr_edit->PageUrl() ?>start=<?php echo $cdr_edit->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $cdr_edit->Pager->PageCount ?></span>
</div>
<?php } ?>
<div class="clearfix"></div>
</form>
<script type="text/javascript">
fcdredit.Init();
</script>
<?php
$cdr_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$cdr_edit->Page_Terminate();
?>
