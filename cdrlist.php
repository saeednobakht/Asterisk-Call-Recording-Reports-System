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

$cdr_list = NULL; // Initialize page object first

class ccdr_list extends ccdr {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{274CC91E-1C95-40BB-9BB8-39D2A070EA8E}";

	// Table name
	var $TableName = 'cdr';

	// Page object name
	var $PageObjName = 'cdr_list';

	// Grid form hidden field names
	var $FormName = 'fcdrlist';
	var $FormActionName = 'k_action';
	var $FormKeyName = 'k_key';
	var $FormOldKeyName = 'k_oldkey';
	var $FormBlankRowName = 'k_blankrow';
	var $FormKeyCountName = 'key_count';

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

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "cdradd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "cdrdelete.php";
		$this->MultiUpdateUrl = "cdrupdate.php";

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'cdr', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect($this->DBID);

		// List options
		$this->ListOptions = new cListOptions();
		$this->ListOptions->TableVar = $this->TableVar;

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['addedit'] = new cListOptions();
		$this->OtherOptions['addedit']->Tag = "div";
		$this->OtherOptions['addedit']->TagClassName = "ewAddEditOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";

		// Filter options
		$this->FilterOptions = new cListOptions();
		$this->FilterOptions->Tag = "div";
		$this->FilterOptions->TagClassName = "ewFilterOption fcdrlistsrch";

		// List actions
		$this->ListActions = new cListActions();
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
		if (!$Security->CanList()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage(ew_DeniedMsg()); // Set no permission
			$this->Page_Terminate(ew_GetUrl("index.php"));
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

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();

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

		// Setup other options
		$this->SetupOtherOptions();

		// Set up custom action (compatible with old version)
		foreach ($this->CustomActions as $name => $action)
			$this->ListActions->Add($name, $action);

		// Show checkbox column if multiple action
		foreach ($this->ListActions->Items as $listaction) {
			if ($listaction->Select == EW_ACTION_MULTIPLE && $listaction->Allow) {
				$this->ListOptions->Items["checkbox"]->Visible = TRUE;
				break;
			}
		}
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

	// Class variables
	var $ListOptions; // List options
	var $ExportOptions; // Export options
	var $SearchOptions; // Search options
	var $OtherOptions = array(); // Other options
	var $FilterOptions; // Filter options
	var $ListActions; // List actions
	var $SelectedCount = 0;
	var $SelectedIndex = 0;
	var $DisplayRecs = 20;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $DefaultSearchWhere = ""; // Default search WHERE clause
	var $SearchWhere = ""; // Search WHERE clause
	var $RecCnt = 0; // Record count
	var $EditRowCnt;
	var $StartRowCnt = 1;
	var $RowCnt = 0;
	var $Attrs = array(); // Row attributes and cell attributes
	var $RowIndex = 0; // Row index
	var $KeyCount = 0; // Key count
	var $RowAction = ""; // Row action
	var $RowOldKey = ""; // Row old key (for copy)
	var $RecPerRow = 0;
	var $MultiColumnClass;
	var $MultiColumnEditClass = "col-sm-12";
	var $MultiColumnCnt = 12;
	var $MultiColumnEditCnt = 12;
	var $GridCnt = 0;
	var $ColCnt = 0;
	var $DbMasterFilter = ""; // Master filter
	var $DbDetailFilter = ""; // Detail filter
	var $MasterRecordExists;	
	var $MultiSelectKey;
	var $Command;
	var $RestoreSearch = FALSE;
	var $DetailPages;
	var $Recordset;
	var $OldRecordset;

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError, $gsSearchError, $Security;

		// Search filters
		$sSrchAdvanced = ""; // Advanced search filter
		$sSrchBasic = ""; // Basic search filter
		$sFilter = "";

		// Get command
		$this->Command = strtolower(@$_GET["cmd"]);
		if ($this->IsPageRequest()) { // Validate request

			// Process list action first
			if ($this->ProcessListAction()) // Ajax request
				$this->Page_Terminate();

			// Set up records per page
			$this->SetUpDisplayRecs();

			// Handle reset command
			$this->ResetCmd();

			// Set up Breadcrumb
			if ($this->Export == "")
				$this->SetupBreadcrumb();

			// Hide list options
			if ($this->Export <> "") {
				$this->ListOptions->HideAllOptions(array("sequence"));
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			} elseif ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
				$this->ListOptions->HideAllOptions();
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			}

			// Hide options
			if ($this->Export <> "" || $this->CurrentAction <> "") {
				$this->ExportOptions->HideAllOptions();
				$this->FilterOptions->HideAllOptions();
			}

			// Hide other options
			if ($this->Export <> "") {
				foreach ($this->OtherOptions as &$option)
					$option->HideAllOptions();
			}

			// Get default search criteria
			ew_AddFilter($this->DefaultSearchWhere, $this->BasicSearchWhere(TRUE));
			ew_AddFilter($this->DefaultSearchWhere, $this->AdvancedSearchWhere(TRUE));

			// Get basic search values
			$this->LoadBasicSearchValues();

			// Get and validate search values for advanced search
			$this->LoadSearchValues(); // Get search values

			// Restore filter list
			$this->RestoreFilterList();
			if (!$this->ValidateSearch())
				$this->setFailureMessage($gsSearchError);

			// Restore search parms from Session if not searching / reset / export
			if (($this->Export <> "" || $this->Command <> "search" && $this->Command <> "reset" && $this->Command <> "resetall") && $this->CheckSearchParms())
				$this->RestoreSearchParms();

			// Call Recordset SearchValidated event
			$this->Recordset_SearchValidated();

			// Set up sorting order
			$this->SetUpSortOrder();

			// Get basic search criteria
			if ($gsSearchError == "")
				$sSrchBasic = $this->BasicSearchWhere();

			// Get search criteria for advanced search
			if ($gsSearchError == "")
				$sSrchAdvanced = $this->AdvancedSearchWhere();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 20; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Load search default if no existing search criteria
		if (!$this->CheckSearchParms()) {

			// Load basic search from default
			$this->BasicSearch->LoadDefault();
			if ($this->BasicSearch->Keyword != "")
				$sSrchBasic = $this->BasicSearchWhere();

			// Load advanced search from default
			if ($this->LoadAdvancedSearchDefault()) {
				$sSrchAdvanced = $this->AdvancedSearchWhere();
			}
		}

		// Build search criteria
		ew_AddFilter($this->SearchWhere, $sSrchAdvanced);
		ew_AddFilter($this->SearchWhere, $sSrchBasic);

		// Call Recordset_Searching event
		$this->Recordset_Searching($this->SearchWhere);

		// Save search criteria
		if ($this->Command == "search" && !$this->RestoreSearch) {
			$this->setSearchWhere($this->SearchWhere); // Save to Session
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} else {
			$this->SearchWhere = $this->getSearchWhere();
		}

		// Build filter
		$sFilter = "";
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Set up filter in session
		$this->setSessionWhere($sFilter);
		$this->CurrentFilter = "";

		// Export data only
		if ($this->CustomExport == "" && in_array($this->Export, array("html","word","excel","xml","csv","email","pdf"))) {
			$this->ExportData();
			$this->Page_Terminate(); // Terminate response
			exit();
		}

		// Load record count first
		if (!$this->IsAddOrEdit()) {
			$bSelectLimit = $this->UseSelectLimit;
			if ($bSelectLimit) {
				$this->TotalRecs = $this->SelectRecordCount();
			} else {
				if ($this->Recordset = $this->LoadRecordset())
					$this->TotalRecs = $this->Recordset->RecordCount();
			}
		}

		// Search options
		$this->SetupSearchOptions();
	}

	// Set up number of records displayed per page
	function SetUpDisplayRecs() {
		$sWrk = @$_GET[EW_TABLE_REC_PER_PAGE];
		if ($sWrk <> "") {
			if (is_numeric($sWrk)) {
				$this->DisplayRecs = intval($sWrk);
			} else {
				if (strtolower($sWrk) == "all") { // Display all records
					$this->DisplayRecs = -1;
				} else {
					$this->DisplayRecs = 20; // Non-numeric, load default
				}
			}
			$this->setRecordsPerPage($this->DisplayRecs); // Save to Session

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Build filter for all keys
	function BuildKeyFilter() {
		global $objForm;
		$sWrkFilter = "";

		// Update row index and get row key
		$rowindex = 1;
		$objForm->Index = $rowindex;
		$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		while ($sThisKey <> "") {
			if ($this->SetupKeyValues($sThisKey)) {
				$sFilter = $this->KeyFilter();
				if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
				$sWrkFilter .= $sFilter;
			} else {
				$sWrkFilter = "0=1";
				break;
			}

			// Update row index and get row key
			$rowindex++; // Next row
			$objForm->Index = $rowindex;
			$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		}
		return $sWrkFilter;
	}

	// Set up key values
	function SetupKeyValues($key) {
		$arrKeyFlds = explode($GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"], $key);
		if (count($arrKeyFlds) >= 1) {
			$this->uniqueid->setFormValue($arrKeyFlds[0]);
		}
		return TRUE;
	}

	// Get list of filters
	function GetFilterList() {

		// Initialize
		$sFilterList = "";
		$sFilterList = ew_Concat($sFilterList, $this->calldate->AdvancedSearch->ToJSON(), ","); // Field calldate
		$sFilterList = ew_Concat($sFilterList, $this->uniqueid->AdvancedSearch->ToJSON(), ","); // Field uniqueid
		$sFilterList = ew_Concat($sFilterList, $this->cnam->AdvancedSearch->ToJSON(), ","); // Field cnam
		$sFilterList = ew_Concat($sFilterList, $this->cnum->AdvancedSearch->ToJSON(), ","); // Field cnum
		$sFilterList = ew_Concat($sFilterList, $this->dst->AdvancedSearch->ToJSON(), ","); // Field dst
		$sFilterList = ew_Concat($sFilterList, $this->duration->AdvancedSearch->ToJSON(), ","); // Field duration
		$sFilterList = ew_Concat($sFilterList, $this->billsec->AdvancedSearch->ToJSON(), ","); // Field billsec
		$sFilterList = ew_Concat($sFilterList, $this->disposition->AdvancedSearch->ToJSON(), ","); // Field disposition
		$sFilterList = ew_Concat($sFilterList, $this->outbound_cnum->AdvancedSearch->ToJSON(), ","); // Field outbound_cnum
		$sFilterList = ew_Concat($sFilterList, $this->play->AdvancedSearch->ToJSON(), ","); // Field play
		$sFilterList = ew_Concat($sFilterList, $this->recordingfile->AdvancedSearch->ToJSON(), ","); // Field recordingfile
		$sFilterList = ew_Concat($sFilterList, $this->recording_name->AdvancedSearch->ToJSON(), ","); // Field recording_name
		$sFilterList = ew_Concat($sFilterList, $this->clid->AdvancedSearch->ToJSON(), ","); // Field clid
		$sFilterList = ew_Concat($sFilterList, $this->src->AdvancedSearch->ToJSON(), ","); // Field src
		$sFilterList = ew_Concat($sFilterList, $this->dcontext->AdvancedSearch->ToJSON(), ","); // Field dcontext
		$sFilterList = ew_Concat($sFilterList, $this->channel->AdvancedSearch->ToJSON(), ","); // Field channel
		$sFilterList = ew_Concat($sFilterList, $this->dstchannel->AdvancedSearch->ToJSON(), ","); // Field dstchannel
		$sFilterList = ew_Concat($sFilterList, $this->lastapp->AdvancedSearch->ToJSON(), ","); // Field lastapp
		$sFilterList = ew_Concat($sFilterList, $this->lastdata->AdvancedSearch->ToJSON(), ","); // Field lastdata
		$sFilterList = ew_Concat($sFilterList, $this->amaflags->AdvancedSearch->ToJSON(), ","); // Field amaflags
		$sFilterList = ew_Concat($sFilterList, $this->accountcode->AdvancedSearch->ToJSON(), ","); // Field accountcode
		$sFilterList = ew_Concat($sFilterList, $this->userfield->AdvancedSearch->ToJSON(), ","); // Field userfield
		$sFilterList = ew_Concat($sFilterList, $this->did->AdvancedSearch->ToJSON(), ","); // Field did
		$sFilterList = ew_Concat($sFilterList, $this->outbound_cnam->AdvancedSearch->ToJSON(), ","); // Field outbound_cnam
		$sFilterList = ew_Concat($sFilterList, $this->dst_cnam->AdvancedSearch->ToJSON(), ","); // Field dst_cnam
		$sFilterList = ew_Concat($sFilterList, $this->linkedid->AdvancedSearch->ToJSON(), ","); // Field linkedid
		$sFilterList = ew_Concat($sFilterList, $this->peeraccount->AdvancedSearch->ToJSON(), ","); // Field peeraccount
		$sFilterList = ew_Concat($sFilterList, $this->sequence->AdvancedSearch->ToJSON(), ","); // Field sequence
		if ($this->BasicSearch->Keyword <> "") {
			$sWrk = "\"" . EW_TABLE_BASIC_SEARCH . "\":\"" . ew_JsEncode2($this->BasicSearch->Keyword) . "\",\"" . EW_TABLE_BASIC_SEARCH_TYPE . "\":\"" . ew_JsEncode2($this->BasicSearch->Type) . "\"";
			$sFilterList = ew_Concat($sFilterList, $sWrk, ",");
		}

		// Return filter list in json
		return ($sFilterList <> "") ? "{" . $sFilterList . "}" : "null";
	}

	// Restore list of filters
	function RestoreFilterList() {

		// Return if not reset filter
		if (@$_POST["cmd"] <> "resetfilter")
			return FALSE;
		$filter = json_decode(ew_StripSlashes(@$_POST["filter"]), TRUE);
		$this->Command = "search";

		// Field calldate
		$this->calldate->AdvancedSearch->SearchValue = @$filter["x_calldate"];
		$this->calldate->AdvancedSearch->SearchOperator = @$filter["z_calldate"];
		$this->calldate->AdvancedSearch->SearchCondition = @$filter["v_calldate"];
		$this->calldate->AdvancedSearch->SearchValue2 = @$filter["y_calldate"];
		$this->calldate->AdvancedSearch->SearchOperator2 = @$filter["w_calldate"];
		$this->calldate->AdvancedSearch->Save();

		// Field uniqueid
		$this->uniqueid->AdvancedSearch->SearchValue = @$filter["x_uniqueid"];
		$this->uniqueid->AdvancedSearch->SearchOperator = @$filter["z_uniqueid"];
		$this->uniqueid->AdvancedSearch->SearchCondition = @$filter["v_uniqueid"];
		$this->uniqueid->AdvancedSearch->SearchValue2 = @$filter["y_uniqueid"];
		$this->uniqueid->AdvancedSearch->SearchOperator2 = @$filter["w_uniqueid"];
		$this->uniqueid->AdvancedSearch->Save();

		// Field cnam
		$this->cnam->AdvancedSearch->SearchValue = @$filter["x_cnam"];
		$this->cnam->AdvancedSearch->SearchOperator = @$filter["z_cnam"];
		$this->cnam->AdvancedSearch->SearchCondition = @$filter["v_cnam"];
		$this->cnam->AdvancedSearch->SearchValue2 = @$filter["y_cnam"];
		$this->cnam->AdvancedSearch->SearchOperator2 = @$filter["w_cnam"];
		$this->cnam->AdvancedSearch->Save();

		// Field cnum
		$this->cnum->AdvancedSearch->SearchValue = @$filter["x_cnum"];
		$this->cnum->AdvancedSearch->SearchOperator = @$filter["z_cnum"];
		$this->cnum->AdvancedSearch->SearchCondition = @$filter["v_cnum"];
		$this->cnum->AdvancedSearch->SearchValue2 = @$filter["y_cnum"];
		$this->cnum->AdvancedSearch->SearchOperator2 = @$filter["w_cnum"];
		$this->cnum->AdvancedSearch->Save();

		// Field dst
		$this->dst->AdvancedSearch->SearchValue = @$filter["x_dst"];
		$this->dst->AdvancedSearch->SearchOperator = @$filter["z_dst"];
		$this->dst->AdvancedSearch->SearchCondition = @$filter["v_dst"];
		$this->dst->AdvancedSearch->SearchValue2 = @$filter["y_dst"];
		$this->dst->AdvancedSearch->SearchOperator2 = @$filter["w_dst"];
		$this->dst->AdvancedSearch->Save();

		// Field duration
		$this->duration->AdvancedSearch->SearchValue = @$filter["x_duration"];
		$this->duration->AdvancedSearch->SearchOperator = @$filter["z_duration"];
		$this->duration->AdvancedSearch->SearchCondition = @$filter["v_duration"];
		$this->duration->AdvancedSearch->SearchValue2 = @$filter["y_duration"];
		$this->duration->AdvancedSearch->SearchOperator2 = @$filter["w_duration"];
		$this->duration->AdvancedSearch->Save();

		// Field billsec
		$this->billsec->AdvancedSearch->SearchValue = @$filter["x_billsec"];
		$this->billsec->AdvancedSearch->SearchOperator = @$filter["z_billsec"];
		$this->billsec->AdvancedSearch->SearchCondition = @$filter["v_billsec"];
		$this->billsec->AdvancedSearch->SearchValue2 = @$filter["y_billsec"];
		$this->billsec->AdvancedSearch->SearchOperator2 = @$filter["w_billsec"];
		$this->billsec->AdvancedSearch->Save();

		// Field disposition
		$this->disposition->AdvancedSearch->SearchValue = @$filter["x_disposition"];
		$this->disposition->AdvancedSearch->SearchOperator = @$filter["z_disposition"];
		$this->disposition->AdvancedSearch->SearchCondition = @$filter["v_disposition"];
		$this->disposition->AdvancedSearch->SearchValue2 = @$filter["y_disposition"];
		$this->disposition->AdvancedSearch->SearchOperator2 = @$filter["w_disposition"];
		$this->disposition->AdvancedSearch->Save();

		// Field outbound_cnum
		$this->outbound_cnum->AdvancedSearch->SearchValue = @$filter["x_outbound_cnum"];
		$this->outbound_cnum->AdvancedSearch->SearchOperator = @$filter["z_outbound_cnum"];
		$this->outbound_cnum->AdvancedSearch->SearchCondition = @$filter["v_outbound_cnum"];
		$this->outbound_cnum->AdvancedSearch->SearchValue2 = @$filter["y_outbound_cnum"];
		$this->outbound_cnum->AdvancedSearch->SearchOperator2 = @$filter["w_outbound_cnum"];
		$this->outbound_cnum->AdvancedSearch->Save();

		// Field play
		$this->play->AdvancedSearch->SearchValue = @$filter["x_play"];
		$this->play->AdvancedSearch->SearchOperator = @$filter["z_play"];
		$this->play->AdvancedSearch->SearchCondition = @$filter["v_play"];
		$this->play->AdvancedSearch->SearchValue2 = @$filter["y_play"];
		$this->play->AdvancedSearch->SearchOperator2 = @$filter["w_play"];
		$this->play->AdvancedSearch->Save();

		// Field recordingfile
		$this->recordingfile->AdvancedSearch->SearchValue = @$filter["x_recordingfile"];
		$this->recordingfile->AdvancedSearch->SearchOperator = @$filter["z_recordingfile"];
		$this->recordingfile->AdvancedSearch->SearchCondition = @$filter["v_recordingfile"];
		$this->recordingfile->AdvancedSearch->SearchValue2 = @$filter["y_recordingfile"];
		$this->recordingfile->AdvancedSearch->SearchOperator2 = @$filter["w_recordingfile"];
		$this->recordingfile->AdvancedSearch->Save();

		// Field recording_name
		$this->recording_name->AdvancedSearch->SearchValue = @$filter["x_recording_name"];
		$this->recording_name->AdvancedSearch->SearchOperator = @$filter["z_recording_name"];
		$this->recording_name->AdvancedSearch->SearchCondition = @$filter["v_recording_name"];
		$this->recording_name->AdvancedSearch->SearchValue2 = @$filter["y_recording_name"];
		$this->recording_name->AdvancedSearch->SearchOperator2 = @$filter["w_recording_name"];
		$this->recording_name->AdvancedSearch->Save();

		// Field clid
		$this->clid->AdvancedSearch->SearchValue = @$filter["x_clid"];
		$this->clid->AdvancedSearch->SearchOperator = @$filter["z_clid"];
		$this->clid->AdvancedSearch->SearchCondition = @$filter["v_clid"];
		$this->clid->AdvancedSearch->SearchValue2 = @$filter["y_clid"];
		$this->clid->AdvancedSearch->SearchOperator2 = @$filter["w_clid"];
		$this->clid->AdvancedSearch->Save();

		// Field src
		$this->src->AdvancedSearch->SearchValue = @$filter["x_src"];
		$this->src->AdvancedSearch->SearchOperator = @$filter["z_src"];
		$this->src->AdvancedSearch->SearchCondition = @$filter["v_src"];
		$this->src->AdvancedSearch->SearchValue2 = @$filter["y_src"];
		$this->src->AdvancedSearch->SearchOperator2 = @$filter["w_src"];
		$this->src->AdvancedSearch->Save();

		// Field dcontext
		$this->dcontext->AdvancedSearch->SearchValue = @$filter["x_dcontext"];
		$this->dcontext->AdvancedSearch->SearchOperator = @$filter["z_dcontext"];
		$this->dcontext->AdvancedSearch->SearchCondition = @$filter["v_dcontext"];
		$this->dcontext->AdvancedSearch->SearchValue2 = @$filter["y_dcontext"];
		$this->dcontext->AdvancedSearch->SearchOperator2 = @$filter["w_dcontext"];
		$this->dcontext->AdvancedSearch->Save();

		// Field channel
		$this->channel->AdvancedSearch->SearchValue = @$filter["x_channel"];
		$this->channel->AdvancedSearch->SearchOperator = @$filter["z_channel"];
		$this->channel->AdvancedSearch->SearchCondition = @$filter["v_channel"];
		$this->channel->AdvancedSearch->SearchValue2 = @$filter["y_channel"];
		$this->channel->AdvancedSearch->SearchOperator2 = @$filter["w_channel"];
		$this->channel->AdvancedSearch->Save();

		// Field dstchannel
		$this->dstchannel->AdvancedSearch->SearchValue = @$filter["x_dstchannel"];
		$this->dstchannel->AdvancedSearch->SearchOperator = @$filter["z_dstchannel"];
		$this->dstchannel->AdvancedSearch->SearchCondition = @$filter["v_dstchannel"];
		$this->dstchannel->AdvancedSearch->SearchValue2 = @$filter["y_dstchannel"];
		$this->dstchannel->AdvancedSearch->SearchOperator2 = @$filter["w_dstchannel"];
		$this->dstchannel->AdvancedSearch->Save();

		// Field lastapp
		$this->lastapp->AdvancedSearch->SearchValue = @$filter["x_lastapp"];
		$this->lastapp->AdvancedSearch->SearchOperator = @$filter["z_lastapp"];
		$this->lastapp->AdvancedSearch->SearchCondition = @$filter["v_lastapp"];
		$this->lastapp->AdvancedSearch->SearchValue2 = @$filter["y_lastapp"];
		$this->lastapp->AdvancedSearch->SearchOperator2 = @$filter["w_lastapp"];
		$this->lastapp->AdvancedSearch->Save();

		// Field lastdata
		$this->lastdata->AdvancedSearch->SearchValue = @$filter["x_lastdata"];
		$this->lastdata->AdvancedSearch->SearchOperator = @$filter["z_lastdata"];
		$this->lastdata->AdvancedSearch->SearchCondition = @$filter["v_lastdata"];
		$this->lastdata->AdvancedSearch->SearchValue2 = @$filter["y_lastdata"];
		$this->lastdata->AdvancedSearch->SearchOperator2 = @$filter["w_lastdata"];
		$this->lastdata->AdvancedSearch->Save();

		// Field amaflags
		$this->amaflags->AdvancedSearch->SearchValue = @$filter["x_amaflags"];
		$this->amaflags->AdvancedSearch->SearchOperator = @$filter["z_amaflags"];
		$this->amaflags->AdvancedSearch->SearchCondition = @$filter["v_amaflags"];
		$this->amaflags->AdvancedSearch->SearchValue2 = @$filter["y_amaflags"];
		$this->amaflags->AdvancedSearch->SearchOperator2 = @$filter["w_amaflags"];
		$this->amaflags->AdvancedSearch->Save();

		// Field accountcode
		$this->accountcode->AdvancedSearch->SearchValue = @$filter["x_accountcode"];
		$this->accountcode->AdvancedSearch->SearchOperator = @$filter["z_accountcode"];
		$this->accountcode->AdvancedSearch->SearchCondition = @$filter["v_accountcode"];
		$this->accountcode->AdvancedSearch->SearchValue2 = @$filter["y_accountcode"];
		$this->accountcode->AdvancedSearch->SearchOperator2 = @$filter["w_accountcode"];
		$this->accountcode->AdvancedSearch->Save();

		// Field userfield
		$this->userfield->AdvancedSearch->SearchValue = @$filter["x_userfield"];
		$this->userfield->AdvancedSearch->SearchOperator = @$filter["z_userfield"];
		$this->userfield->AdvancedSearch->SearchCondition = @$filter["v_userfield"];
		$this->userfield->AdvancedSearch->SearchValue2 = @$filter["y_userfield"];
		$this->userfield->AdvancedSearch->SearchOperator2 = @$filter["w_userfield"];
		$this->userfield->AdvancedSearch->Save();

		// Field did
		$this->did->AdvancedSearch->SearchValue = @$filter["x_did"];
		$this->did->AdvancedSearch->SearchOperator = @$filter["z_did"];
		$this->did->AdvancedSearch->SearchCondition = @$filter["v_did"];
		$this->did->AdvancedSearch->SearchValue2 = @$filter["y_did"];
		$this->did->AdvancedSearch->SearchOperator2 = @$filter["w_did"];
		$this->did->AdvancedSearch->Save();

		// Field outbound_cnam
		$this->outbound_cnam->AdvancedSearch->SearchValue = @$filter["x_outbound_cnam"];
		$this->outbound_cnam->AdvancedSearch->SearchOperator = @$filter["z_outbound_cnam"];
		$this->outbound_cnam->AdvancedSearch->SearchCondition = @$filter["v_outbound_cnam"];
		$this->outbound_cnam->AdvancedSearch->SearchValue2 = @$filter["y_outbound_cnam"];
		$this->outbound_cnam->AdvancedSearch->SearchOperator2 = @$filter["w_outbound_cnam"];
		$this->outbound_cnam->AdvancedSearch->Save();

		// Field dst_cnam
		$this->dst_cnam->AdvancedSearch->SearchValue = @$filter["x_dst_cnam"];
		$this->dst_cnam->AdvancedSearch->SearchOperator = @$filter["z_dst_cnam"];
		$this->dst_cnam->AdvancedSearch->SearchCondition = @$filter["v_dst_cnam"];
		$this->dst_cnam->AdvancedSearch->SearchValue2 = @$filter["y_dst_cnam"];
		$this->dst_cnam->AdvancedSearch->SearchOperator2 = @$filter["w_dst_cnam"];
		$this->dst_cnam->AdvancedSearch->Save();

		// Field linkedid
		$this->linkedid->AdvancedSearch->SearchValue = @$filter["x_linkedid"];
		$this->linkedid->AdvancedSearch->SearchOperator = @$filter["z_linkedid"];
		$this->linkedid->AdvancedSearch->SearchCondition = @$filter["v_linkedid"];
		$this->linkedid->AdvancedSearch->SearchValue2 = @$filter["y_linkedid"];
		$this->linkedid->AdvancedSearch->SearchOperator2 = @$filter["w_linkedid"];
		$this->linkedid->AdvancedSearch->Save();

		// Field peeraccount
		$this->peeraccount->AdvancedSearch->SearchValue = @$filter["x_peeraccount"];
		$this->peeraccount->AdvancedSearch->SearchOperator = @$filter["z_peeraccount"];
		$this->peeraccount->AdvancedSearch->SearchCondition = @$filter["v_peeraccount"];
		$this->peeraccount->AdvancedSearch->SearchValue2 = @$filter["y_peeraccount"];
		$this->peeraccount->AdvancedSearch->SearchOperator2 = @$filter["w_peeraccount"];
		$this->peeraccount->AdvancedSearch->Save();

		// Field sequence
		$this->sequence->AdvancedSearch->SearchValue = @$filter["x_sequence"];
		$this->sequence->AdvancedSearch->SearchOperator = @$filter["z_sequence"];
		$this->sequence->AdvancedSearch->SearchCondition = @$filter["v_sequence"];
		$this->sequence->AdvancedSearch->SearchValue2 = @$filter["y_sequence"];
		$this->sequence->AdvancedSearch->SearchOperator2 = @$filter["w_sequence"];
		$this->sequence->AdvancedSearch->Save();
		$this->BasicSearch->setKeyword(@$filter[EW_TABLE_BASIC_SEARCH]);
		$this->BasicSearch->setType(@$filter[EW_TABLE_BASIC_SEARCH_TYPE]);
	}

	// Advanced search WHERE clause based on QueryString
	function AdvancedSearchWhere($Default = FALSE) {
		global $Security;
		$sWhere = "";
		$this->BuildSearchSql($sWhere, $this->calldate, $Default, FALSE); // calldate
		$this->BuildSearchSql($sWhere, $this->uniqueid, $Default, FALSE); // uniqueid
		$this->BuildSearchSql($sWhere, $this->cnam, $Default, FALSE); // cnam
		$this->BuildSearchSql($sWhere, $this->cnum, $Default, FALSE); // cnum
		$this->BuildSearchSql($sWhere, $this->dst, $Default, FALSE); // dst
		$this->BuildSearchSql($sWhere, $this->duration, $Default, FALSE); // duration
		$this->BuildSearchSql($sWhere, $this->billsec, $Default, FALSE); // billsec
		$this->BuildSearchSql($sWhere, $this->disposition, $Default, FALSE); // disposition
		$this->BuildSearchSql($sWhere, $this->outbound_cnum, $Default, FALSE); // outbound_cnum
		$this->BuildSearchSql($sWhere, $this->play, $Default, FALSE); // play
		$this->BuildSearchSql($sWhere, $this->recordingfile, $Default, FALSE); // recordingfile
		$this->BuildSearchSql($sWhere, $this->recording_name, $Default, FALSE); // recording_name
		$this->BuildSearchSql($sWhere, $this->clid, $Default, FALSE); // clid
		$this->BuildSearchSql($sWhere, $this->src, $Default, FALSE); // src
		$this->BuildSearchSql($sWhere, $this->dcontext, $Default, FALSE); // dcontext
		$this->BuildSearchSql($sWhere, $this->channel, $Default, FALSE); // channel
		$this->BuildSearchSql($sWhere, $this->dstchannel, $Default, FALSE); // dstchannel
		$this->BuildSearchSql($sWhere, $this->lastapp, $Default, FALSE); // lastapp
		$this->BuildSearchSql($sWhere, $this->lastdata, $Default, FALSE); // lastdata
		$this->BuildSearchSql($sWhere, $this->amaflags, $Default, FALSE); // amaflags
		$this->BuildSearchSql($sWhere, $this->accountcode, $Default, FALSE); // accountcode
		$this->BuildSearchSql($sWhere, $this->userfield, $Default, FALSE); // userfield
		$this->BuildSearchSql($sWhere, $this->did, $Default, FALSE); // did
		$this->BuildSearchSql($sWhere, $this->outbound_cnam, $Default, FALSE); // outbound_cnam
		$this->BuildSearchSql($sWhere, $this->dst_cnam, $Default, FALSE); // dst_cnam
		$this->BuildSearchSql($sWhere, $this->linkedid, $Default, FALSE); // linkedid
		$this->BuildSearchSql($sWhere, $this->peeraccount, $Default, FALSE); // peeraccount
		$this->BuildSearchSql($sWhere, $this->sequence, $Default, FALSE); // sequence

		// Set up search parm
		if (!$Default && $sWhere <> "") {
			$this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->calldate->AdvancedSearch->Save(); // calldate
			$this->uniqueid->AdvancedSearch->Save(); // uniqueid
			$this->cnam->AdvancedSearch->Save(); // cnam
			$this->cnum->AdvancedSearch->Save(); // cnum
			$this->dst->AdvancedSearch->Save(); // dst
			$this->duration->AdvancedSearch->Save(); // duration
			$this->billsec->AdvancedSearch->Save(); // billsec
			$this->disposition->AdvancedSearch->Save(); // disposition
			$this->outbound_cnum->AdvancedSearch->Save(); // outbound_cnum
			$this->play->AdvancedSearch->Save(); // play
			$this->recordingfile->AdvancedSearch->Save(); // recordingfile
			$this->recording_name->AdvancedSearch->Save(); // recording_name
			$this->clid->AdvancedSearch->Save(); // clid
			$this->src->AdvancedSearch->Save(); // src
			$this->dcontext->AdvancedSearch->Save(); // dcontext
			$this->channel->AdvancedSearch->Save(); // channel
			$this->dstchannel->AdvancedSearch->Save(); // dstchannel
			$this->lastapp->AdvancedSearch->Save(); // lastapp
			$this->lastdata->AdvancedSearch->Save(); // lastdata
			$this->amaflags->AdvancedSearch->Save(); // amaflags
			$this->accountcode->AdvancedSearch->Save(); // accountcode
			$this->userfield->AdvancedSearch->Save(); // userfield
			$this->did->AdvancedSearch->Save(); // did
			$this->outbound_cnam->AdvancedSearch->Save(); // outbound_cnam
			$this->dst_cnam->AdvancedSearch->Save(); // dst_cnam
			$this->linkedid->AdvancedSearch->Save(); // linkedid
			$this->peeraccount->AdvancedSearch->Save(); // peeraccount
			$this->sequence->AdvancedSearch->Save(); // sequence
		}
		return $sWhere;
	}

	// Build search SQL
	function BuildSearchSql(&$Where, &$Fld, $Default, $MultiValue) {
		$FldParm = substr($Fld->FldVar, 2);
		$FldVal = ($Default) ? $Fld->AdvancedSearch->SearchValueDefault : $Fld->AdvancedSearch->SearchValue; // @$_GET["x_$FldParm"]
		$FldOpr = ($Default) ? $Fld->AdvancedSearch->SearchOperatorDefault : $Fld->AdvancedSearch->SearchOperator; // @$_GET["z_$FldParm"]
		$FldCond = ($Default) ? $Fld->AdvancedSearch->SearchConditionDefault : $Fld->AdvancedSearch->SearchCondition; // @$_GET["v_$FldParm"]
		$FldVal2 = ($Default) ? $Fld->AdvancedSearch->SearchValue2Default : $Fld->AdvancedSearch->SearchValue2; // @$_GET["y_$FldParm"]
		$FldOpr2 = ($Default) ? $Fld->AdvancedSearch->SearchOperator2Default : $Fld->AdvancedSearch->SearchOperator2; // @$_GET["w_$FldParm"]
		$sWrk = "";

		//$FldVal = ew_StripSlashes($FldVal);
		if (is_array($FldVal)) $FldVal = implode(",", $FldVal);

		//$FldVal2 = ew_StripSlashes($FldVal2);
		if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
		$FldOpr = strtoupper(trim($FldOpr));
		if ($FldOpr == "") $FldOpr = "=";
		$FldOpr2 = strtoupper(trim($FldOpr2));
		if ($FldOpr2 == "") $FldOpr2 = "=";
		if (EW_SEARCH_MULTI_VALUE_OPTION == 1 || $FldOpr <> "LIKE" ||
			($FldOpr2 <> "LIKE" && $FldVal2 <> ""))
			$MultiValue = FALSE;
		if ($MultiValue) {
			$sWrk1 = ($FldVal <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr, $FldVal, $this->DBID) : ""; // Field value 1
			$sWrk2 = ($FldVal2 <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr2, $FldVal2, $this->DBID) : ""; // Field value 2
			$sWrk = $sWrk1; // Build final SQL
			if ($sWrk2 <> "")
				$sWrk = ($sWrk <> "") ? "($sWrk) $FldCond ($sWrk2)" : $sWrk2;
		} else {
			$FldVal = $this->ConvertSearchValue($Fld, $FldVal);
			$FldVal2 = $this->ConvertSearchValue($Fld, $FldVal2);
			$sWrk = ew_GetSearchSql($Fld, $FldVal, $FldOpr, $FldCond, $FldVal2, $FldOpr2, $this->DBID);
		}
		ew_AddFilter($Where, $sWrk);
	}

	// Convert search value
	function ConvertSearchValue(&$Fld, $FldVal) {
		if ($FldVal == EW_NULL_VALUE || $FldVal == EW_NOT_NULL_VALUE)
			return $FldVal;
		$Value = $FldVal;
		if ($Fld->FldDataType == EW_DATATYPE_BOOLEAN) {
			if ($FldVal <> "") $Value = ($FldVal == "1" || strtolower(strval($FldVal)) == "y" || strtolower(strval($FldVal)) == "t") ? $Fld->TrueValue : $Fld->FalseValue;
		} elseif ($Fld->FldDataType == EW_DATATYPE_DATE) {
			if ($FldVal <> "") $Value = ew_UnFormatDateTime($FldVal, $Fld->FldDateTimeFormat);
		}
		return $Value;
	}

	// Return basic search SQL
	function BasicSearchSQL($arKeywords, $type) {
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->uniqueid, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->cnam, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->cnum, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->dst, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->disposition, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->outbound_cnum, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->play, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->recordingfile, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->recording_name, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->clid, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->src, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->dcontext, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->channel, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->dstchannel, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->lastapp, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->lastdata, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->accountcode, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->userfield, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->did, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->outbound_cnam, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->dst_cnam, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->linkedid, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->peeraccount, $arKeywords, $type);
		return $sWhere;
	}

	// Build basic search SQL
	function BuildBasicSearchSql(&$Where, &$Fld, $arKeywords, $type) {
		$sDefCond = ($type == "OR") ? "OR" : "AND";
		$arSQL = array(); // Array for SQL parts
		$arCond = array(); // Array for search conditions
		$cnt = count($arKeywords);
		$j = 0; // Number of SQL parts
		for ($i = 0; $i < $cnt; $i++) {
			$Keyword = $arKeywords[$i];
			$Keyword = trim($Keyword);
			if (EW_BASIC_SEARCH_IGNORE_PATTERN <> "") {
				$Keyword = preg_replace(EW_BASIC_SEARCH_IGNORE_PATTERN, "\\", $Keyword);
				$ar = explode("\\", $Keyword);
			} else {
				$ar = array($Keyword);
			}
			foreach ($ar as $Keyword) {
				if ($Keyword <> "") {
					$sWrk = "";
					if ($Keyword == "OR" && $type == "") {
						if ($j > 0)
							$arCond[$j-1] = "OR";
					} elseif ($Keyword == EW_NULL_VALUE) {
						$sWrk = $Fld->FldExpression . " IS NULL";
					} elseif ($Keyword == EW_NOT_NULL_VALUE) {
						$sWrk = $Fld->FldExpression . " IS NOT NULL";
					} elseif ($Fld->FldIsVirtual && $Fld->FldVirtualSearch) {
						$sWrk = $Fld->FldVirtualExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING, $this->DBID), $this->DBID);
					} elseif ($Fld->FldDataType != EW_DATATYPE_NUMBER || is_numeric($Keyword)) {
						$sWrk = $Fld->FldBasicSearchExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING, $this->DBID), $this->DBID);
					}
					if ($sWrk <> "") {
						$arSQL[$j] = $sWrk;
						$arCond[$j] = $sDefCond;
						$j += 1;
					}
				}
			}
		}
		$cnt = count($arSQL);
		$bQuoted = FALSE;
		$sSql = "";
		if ($cnt > 0) {
			for ($i = 0; $i < $cnt-1; $i++) {
				if ($arCond[$i] == "OR") {
					if (!$bQuoted) $sSql .= "(";
					$bQuoted = TRUE;
				}
				$sSql .= $arSQL[$i];
				if ($bQuoted && $arCond[$i] <> "OR") {
					$sSql .= ")";
					$bQuoted = FALSE;
				}
				$sSql .= " " . $arCond[$i] . " ";
			}
			$sSql .= $arSQL[$cnt-1];
			if ($bQuoted)
				$sSql .= ")";
		}
		if ($sSql <> "") {
			if ($Where <> "") $Where .= " OR ";
			$Where .=  "(" . $sSql . ")";
		}
	}

	// Return basic search WHERE clause based on search keyword and type
	function BasicSearchWhere($Default = FALSE) {
		global $Security;
		$sSearchStr = "";
		$sSearchKeyword = ($Default) ? $this->BasicSearch->KeywordDefault : $this->BasicSearch->Keyword;
		$sSearchType = ($Default) ? $this->BasicSearch->TypeDefault : $this->BasicSearch->Type;
		if ($sSearchKeyword <> "") {
			$sSearch = trim($sSearchKeyword);
			if ($sSearchType <> "=") {
				$ar = array();

				// Match quoted keywords (i.e.: "...")
				if (preg_match_all('/"([^"]*)"/i', $sSearch, $matches, PREG_SET_ORDER)) {
					foreach ($matches as $match) {
						$p = strpos($sSearch, $match[0]);
						$str = substr($sSearch, 0, $p);
						$sSearch = substr($sSearch, $p + strlen($match[0]));
						if (strlen(trim($str)) > 0)
							$ar = array_merge($ar, explode(" ", trim($str)));
						$ar[] = $match[1]; // Save quoted keyword
					}
				}

				// Match individual keywords
				if (strlen(trim($sSearch)) > 0)
					$ar = array_merge($ar, explode(" ", trim($sSearch)));

				// Search keyword in any fields
				if (($sSearchType == "OR" || $sSearchType == "AND") && $this->BasicSearch->BasicSearchAnyFields) {
					foreach ($ar as $sKeyword) {
						if ($sKeyword <> "") {
							if ($sSearchStr <> "") $sSearchStr .= " " . $sSearchType . " ";
							$sSearchStr .= "(" . $this->BasicSearchSQL(array($sKeyword), $sSearchType) . ")";
						}
					}
				} else {
					$sSearchStr = $this->BasicSearchSQL($ar, $sSearchType);
				}
			} else {
				$sSearchStr = $this->BasicSearchSQL(array($sSearch), $sSearchType);
			}
			if (!$Default) $this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->BasicSearch->setKeyword($sSearchKeyword);
			$this->BasicSearch->setType($sSearchType);
		}
		return $sSearchStr;
	}

	// Check if search parm exists
	function CheckSearchParms() {

		// Check basic search
		if ($this->BasicSearch->IssetSession())
			return TRUE;
		if ($this->calldate->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->uniqueid->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->cnam->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->cnum->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->dst->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->duration->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->billsec->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->disposition->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->outbound_cnum->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->play->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->recordingfile->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->recording_name->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->clid->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->src->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->dcontext->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->channel->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->dstchannel->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->lastapp->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->lastdata->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->amaflags->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->accountcode->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->userfield->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->did->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->outbound_cnam->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->dst_cnam->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->linkedid->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->peeraccount->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->sequence->AdvancedSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear basic search parameters
		$this->ResetBasicSearchParms();

		// Clear advanced search parameters
		$this->ResetAdvancedSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all basic search parameters
	function ResetBasicSearchParms() {
		$this->BasicSearch->UnsetSession();
	}

	// Clear all advanced search parameters
	function ResetAdvancedSearchParms() {
		$this->calldate->AdvancedSearch->UnsetSession();
		$this->uniqueid->AdvancedSearch->UnsetSession();
		$this->cnam->AdvancedSearch->UnsetSession();
		$this->cnum->AdvancedSearch->UnsetSession();
		$this->dst->AdvancedSearch->UnsetSession();
		$this->duration->AdvancedSearch->UnsetSession();
		$this->billsec->AdvancedSearch->UnsetSession();
		$this->disposition->AdvancedSearch->UnsetSession();
		$this->outbound_cnum->AdvancedSearch->UnsetSession();
		$this->play->AdvancedSearch->UnsetSession();
		$this->recordingfile->AdvancedSearch->UnsetSession();
		$this->recording_name->AdvancedSearch->UnsetSession();
		$this->clid->AdvancedSearch->UnsetSession();
		$this->src->AdvancedSearch->UnsetSession();
		$this->dcontext->AdvancedSearch->UnsetSession();
		$this->channel->AdvancedSearch->UnsetSession();
		$this->dstchannel->AdvancedSearch->UnsetSession();
		$this->lastapp->AdvancedSearch->UnsetSession();
		$this->lastdata->AdvancedSearch->UnsetSession();
		$this->amaflags->AdvancedSearch->UnsetSession();
		$this->accountcode->AdvancedSearch->UnsetSession();
		$this->userfield->AdvancedSearch->UnsetSession();
		$this->did->AdvancedSearch->UnsetSession();
		$this->outbound_cnam->AdvancedSearch->UnsetSession();
		$this->dst_cnam->AdvancedSearch->UnsetSession();
		$this->linkedid->AdvancedSearch->UnsetSession();
		$this->peeraccount->AdvancedSearch->UnsetSession();
		$this->sequence->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();

		// Restore advanced search values
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

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->calldate); // calldate
			$this->UpdateSort($this->uniqueid); // uniqueid
			$this->UpdateSort($this->cnam); // cnam
			$this->UpdateSort($this->cnum); // cnum
			$this->UpdateSort($this->dst); // dst
			$this->UpdateSort($this->duration); // duration
			$this->UpdateSort($this->billsec); // billsec
			$this->UpdateSort($this->disposition); // disposition
			$this->UpdateSort($this->outbound_cnum); // outbound_cnum
			$this->UpdateSort($this->play); // play
			$this->UpdateSort($this->recordingfile); // recordingfile
			$this->setStartRecordNumber(1); // Reset start position
		}
	}

	// Load sort order parameters
	function LoadSortOrder() {
		$sOrderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
		if ($sOrderBy == "") {
			if ($this->getSqlOrderBy() <> "") {
				$sOrderBy = $this->getSqlOrderBy();
				$this->setSessionOrderBy($sOrderBy);
				$this->calldate->setSort("DESC");
			}
		}
	}

	// Reset command
	// - cmd=reset (Reset search parameters)
	// - cmd=resetall (Reset search and master/detail parameters)
	// - cmd=resetsort (Reset sort parameters)
	function ResetCmd() {

		// Check if reset command
		if (substr($this->Command,0,5) == "reset") {

			// Reset search criteria
			if ($this->Command == "reset" || $this->Command == "resetall")
				$this->ResetSearchParms();

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->calldate->setSort("");
				$this->uniqueid->setSort("");
				$this->cnam->setSort("");
				$this->cnum->setSort("");
				$this->dst->setSort("");
				$this->duration->setSort("");
				$this->billsec->setSort("");
				$this->disposition->setSort("");
				$this->outbound_cnum->setSort("");
				$this->play->setSort("");
				$this->recordingfile->setSort("");
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// Add group option item
		$item = &$this->ListOptions->Add($this->ListOptions->GroupOptionName);
		$item->Body = "";
		$item->OnLeft = TRUE;
		$item->Visible = FALSE;

		// "view"
		$item = &$this->ListOptions->Add("view");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->IsLoggedIn();
		$item->OnLeft = TRUE;

		// List actions
		$item = &$this->ListOptions->Add("listactions");
		$item->CssStyle = "white-space: nowrap;";
		$item->OnLeft = TRUE;
		$item->Visible = FALSE;
		$item->ShowInButtonGroup = FALSE;
		$item->ShowInDropDown = FALSE;

		// "checkbox"
		$item = &$this->ListOptions->Add("checkbox");
		$item->Visible = FALSE;
		$item->OnLeft = TRUE;
		$item->Header = "<input type=\"checkbox\" name=\"key\" id=\"key\" onclick=\"ew_SelectAllKey(this);\">";
		$item->MoveTo(0);
		$item->ShowInDropDown = FALSE;
		$item->ShowInButtonGroup = FALSE;

		// Drop down button for ListOptions
		$this->ListOptions->UseImageAndText = TRUE;
		$this->ListOptions->UseDropDownButton = FALSE;
		$this->ListOptions->DropDownButtonPhrase = $Language->Phrase("ButtonListOptions");
		$this->ListOptions->UseButtonGroup = TRUE;
		if ($this->ListOptions->UseButtonGroup && ew_IsMobile())
			$this->ListOptions->UseDropDownButton = TRUE;
		$this->ListOptions->ButtonClass = "btn-sm"; // Class for button group

		// Call ListOptions_Load event
		$this->ListOptions_Load();
		$this->SetupListOptionsExt();
		$item = &$this->ListOptions->GetItem($this->ListOptions->GroupOptionName);
		$item->Visible = $this->ListOptions->GroupOptionVisible();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();

		// "view"
		$oListOpt = &$this->ListOptions->Items["view"];
		if ($Security->IsLoggedIn())
			$oListOpt->Body = "<a class=\"ewRowLink ewView\" title=\"" . ew_HtmlTitle($Language->Phrase("ViewLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewLink")) . "\" href=\"" . ew_HtmlEncode($this->ViewUrl) . "\">" . $Language->Phrase("ViewLink") . "</a>";
		else
			$oListOpt->Body = "";

		// Set up list action buttons
		$oListOpt = &$this->ListOptions->GetItem("listactions");
		if ($oListOpt && $this->Export == "" && $this->CurrentAction == "") {
			$body = "";
			$links = array();
			foreach ($this->ListActions->Items as $listaction) {
				if ($listaction->Select == EW_ACTION_SINGLE && $listaction->Allow) {
					$action = $listaction->Action;
					$caption = $listaction->Caption;
					$icon = ($listaction->Icon <> "") ? "<span class=\"" . ew_HtmlEncode(str_replace(" ewIcon", "", $listaction->Icon)) . "\" data-caption=\"" . ew_HtmlTitle($caption) . "\"></span> " : "";
					$links[] = "<li><a class=\"ewAction ewListAction\" data-action=\"" . ew_HtmlEncode($action) . "\" data-caption=\"" . ew_HtmlTitle($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({key:" . $this->KeyToJson() . "}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . $listaction->Caption . "</a></li>";
					if (count($links) == 1) // Single button
						$body = "<a class=\"ewAction ewListAction\" data-action=\"" . ew_HtmlEncode($action) . "\" title=\"" . ew_HtmlTitle($caption) . "\" data-caption=\"" . ew_HtmlTitle($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({key:" . $this->KeyToJson() . "}," . $listaction->ToJson(TRUE) . "));return false;\">" . $Language->Phrase("ListActionButton") . "</a>";
				}
			}
			if (count($links) > 1) { // More than one buttons, use dropdown
				$body = "<button class=\"dropdown-toggle btn btn-default btn-sm ewActions\" title=\"" . ew_HtmlTitle($Language->Phrase("ListActionButton")) . "\" data-toggle=\"dropdown\">" . $Language->Phrase("ListActionButton") . "<b class=\"caret\"></b></button>";
				$content = "";
				foreach ($links as $link)
					$content .= "<li>" . $link . "</li>";
				$body .= "<ul class=\"dropdown-menu" . ($oListOpt->OnLeft ? "" : " dropdown-menu-right") . "\">". $content . "</ul>";
				$body = "<div class=\"btn-group\">" . $body . "</div>";
			}
			if (count($links) > 0) {
				$oListOpt->Body = $body;
				$oListOpt->Visible = TRUE;
			}
		}

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->uniqueid->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event);'>";
		$this->RenderListOptionsExt();

		// Call ListOptions_Rendered event
		$this->ListOptions_Rendered();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = $options["action"];

		// Set up options default
		foreach ($options as &$option) {
			$option->UseImageAndText = TRUE;
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$option->ButtonClass = "btn-sm"; // Class for button group
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["addedit"]->DropDownButtonPhrase = $Language->Phrase("ButtonAddEdit");
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");

		// Filter button
		$item = &$this->FilterOptions->Add("savecurrentfilter");
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fcdrlistsrch\" href=\"#\">" . $Language->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fcdrlistsrch\" href=\"#\">" . $Language->Phrase("DeleteFilter") . "</a>";
		$item->Visible = TRUE;
		$this->FilterOptions->UseDropDownButton = TRUE;
		$this->FilterOptions->UseButtonGroup = !$this->FilterOptions->UseDropDownButton;
		$this->FilterOptions->DropDownButtonPhrase = $Language->Phrase("Filters");

		// Add group option item
		$item = &$this->FilterOptions->Add($this->FilterOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
	}

	// Render other options
	function RenderOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
			$option = &$options["action"];

			// Set up list action buttons
			foreach ($this->ListActions->Items as $listaction) {
				if ($listaction->Select == EW_ACTION_MULTIPLE) {
					$item = &$option->Add("custom_" . $listaction->Action);
					$caption = $listaction->Caption;
					$icon = ($listaction->Icon <> "") ? "<span class=\"" . ew_HtmlEncode($listaction->Icon) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\"></span> " : $caption;
					$item->Body = "<a class=\"ewAction ewListAction\" title=\"" . ew_HtmlEncode($caption) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({f:document.fcdrlist}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . "</a>";
					$item->Visible = $listaction->Allow;
				}
			}

			// Hide grid edit and other options
			if ($this->TotalRecs <= 0) {
				$option = &$options["addedit"];
				$item = &$option->GetItem("gridedit");
				if ($item) $item->Visible = FALSE;
				$option = &$options["action"];
				$option->HideAllOptions();
			}
	}

	// Process list action
	function ProcessListAction() {
		global $Language, $Security;
		$userlist = "";
		$user = "";
		$sFilter = $this->GetKeyFilter();
		$UserAction = @$_POST["useraction"];
		if ($sFilter <> "" && $UserAction <> "") {

			// Check permission first
			$ActionCaption = $UserAction;
			if (array_key_exists($UserAction, $this->ListActions->Items)) {
				$ActionCaption = $this->ListActions->Items[$UserAction]->Caption;
				if (!$this->ListActions->Items[$UserAction]->Allow) {
					$errmsg = str_replace('%s', $ActionCaption, $Language->Phrase("CustomActionNotAllowed"));
					if (@$_POST["ajax"] == $UserAction) // Ajax
						echo "<p class=\"text-danger\">" . $errmsg . "</p>";
					else
						$this->setFailureMessage($errmsg);
					return FALSE;
				}
			}
			$this->CurrentFilter = $sFilter;
			$sSql = $this->SQL();
			$conn = &$this->Connection();
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$rs = $conn->Execute($sSql);
			$conn->raiseErrorFn = '';
			$this->CurrentAction = $UserAction;

			// Call row action event
			if ($rs && !$rs->EOF) {
				$conn->BeginTrans();
				$this->SelectedCount = $rs->RecordCount();
				$this->SelectedIndex = 0;
				while (!$rs->EOF) {
					$this->SelectedIndex++;
					$row = $rs->fields;
					$Processed = $this->Row_CustomAction($UserAction, $row);
					if (!$Processed) break;
					$rs->MoveNext();
				}
				if ($Processed) {
					$conn->CommitTrans(); // Commit the changes
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage(str_replace('%s', $ActionCaption, $Language->Phrase("CustomActionCompleted"))); // Set up success message
				} else {
					$conn->RollbackTrans(); // Rollback changes

					// Set up error message
					if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

						// Use the message, do nothing
					} elseif ($this->CancelMessage <> "") {
						$this->setFailureMessage($this->CancelMessage);
						$this->CancelMessage = "";
					} else {
						$this->setFailureMessage(str_replace('%s', $ActionCaption, $Language->Phrase("CustomActionFailed")));
					}
				}
			}
			if ($rs)
				$rs->Close();
			$this->CurrentAction = ""; // Clear action
			if (@$_POST["ajax"] == $UserAction) { // Ajax
				if ($this->getSuccessMessage() <> "") {
					echo "<p class=\"text-success\">" . $this->getSuccessMessage() . "</p>";
					$this->ClearSuccessMessage(); // Clear message
				}
				if ($this->getFailureMessage() <> "") {
					echo "<p class=\"text-danger\">" . $this->getFailureMessage() . "</p>";
					$this->ClearFailureMessage(); // Clear message
				}
				return TRUE;
			}
		}
		return FALSE; // Not ajax request
	}

	// Set up search options
	function SetupSearchOptions() {
		global $Language;
		$this->SearchOptions = new cListOptions();
		$this->SearchOptions->Tag = "div";
		$this->SearchOptions->TagClassName = "ewSearchOption";

		// Search button
		$item = &$this->SearchOptions->Add("searchtoggle");
		$SearchToggleClass = ($this->SearchWhere <> "") ? " active" : "";
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"fcdrlistsrch\">" . $Language->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Show all button
		$item = &$this->SearchOptions->Add("showall");
		$item->Body = "<a class=\"btn btn-default ewShowAll\" title=\"" . $Language->Phrase("ShowAll") . "\" data-caption=\"" . $Language->Phrase("ShowAll") . "\" href=\"" . $this->PageUrl() . "cmd=reset\">" . $Language->Phrase("ShowAllBtn") . "</a>";
		$item->Visible = ($this->SearchWhere <> $this->DefaultSearchWhere && $this->SearchWhere <> "0=101");

		// Advanced search button
		$item = &$this->SearchOptions->Add("advancedsearch");
		$item->Body = "<a class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" href=\"cdrsrch.php\">" . $Language->Phrase("AdvancedSearchBtn") . "</a>";
		$item->Visible = TRUE;

		// Search highlight button
		$item = &$this->SearchOptions->Add("searchhighlight");
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewHighlight active\" title=\"" . $Language->Phrase("Highlight") . "\" data-caption=\"" . $Language->Phrase("Highlight") . "\" data-toggle=\"button\" data-form=\"fcdrlistsrch\" data-name=\"" . $this->HighlightName() . "\">" . $Language->Phrase("HighlightBtn") . "</button>";
		$item->Visible = ($this->SearchWhere <> "" && $this->TotalRecs > 0);

		// Button group for search
		$this->SearchOptions->UseDropDownButton = FALSE;
		$this->SearchOptions->UseImageAndText = TRUE;
		$this->SearchOptions->UseButtonGroup = TRUE;
		$this->SearchOptions->DropDownButtonPhrase = $Language->Phrase("ButtonSearch");

		// Add group option item
		$item = &$this->SearchOptions->Add($this->SearchOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Hide search options
		if ($this->Export <> "" || $this->CurrentAction <> "")
			$this->SearchOptions->HideAllOptions();
	}

	function SetupListOptionsExt() {
		global $Security, $Language;

		// Hide detail items for dropdown if necessary
		$this->ListOptions->HideDetailItemsForDropDown();
	}

	function RenderListOptionsExt() {
		global $Security, $Language;
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

	// Load basic search values
	function LoadBasicSearchValues() {
		$this->BasicSearch->Keyword = @$_GET[EW_TABLE_BASIC_SEARCH];
		if ($this->BasicSearch->Keyword <> "") $this->Command = "search";
		$this->BasicSearch->Type = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
	}

	// Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// calldate

		$this->calldate->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_calldate"]);
		if ($this->calldate->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->calldate->AdvancedSearch->SearchOperator = @$_GET["z_calldate"];
		$this->calldate->AdvancedSearch->SearchCondition = @$_GET["v_calldate"];
		$this->calldate->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_calldate"]);
		if ($this->calldate->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->calldate->AdvancedSearch->SearchOperator2 = @$_GET["w_calldate"];

		// uniqueid
		$this->uniqueid->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_uniqueid"]);
		if ($this->uniqueid->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->uniqueid->AdvancedSearch->SearchOperator = @$_GET["z_uniqueid"];
		$this->uniqueid->AdvancedSearch->SearchCondition = @$_GET["v_uniqueid"];
		$this->uniqueid->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_uniqueid"]);
		if ($this->uniqueid->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->uniqueid->AdvancedSearch->SearchOperator2 = @$_GET["w_uniqueid"];

		// cnam
		$this->cnam->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_cnam"]);
		if ($this->cnam->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->cnam->AdvancedSearch->SearchOperator = @$_GET["z_cnam"];
		$this->cnam->AdvancedSearch->SearchCondition = @$_GET["v_cnam"];
		$this->cnam->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_cnam"]);
		if ($this->cnam->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->cnam->AdvancedSearch->SearchOperator2 = @$_GET["w_cnam"];

		// cnum
		$this->cnum->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_cnum"]);
		if ($this->cnum->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->cnum->AdvancedSearch->SearchOperator = @$_GET["z_cnum"];
		$this->cnum->AdvancedSearch->SearchCondition = @$_GET["v_cnum"];
		$this->cnum->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_cnum"]);
		if ($this->cnum->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->cnum->AdvancedSearch->SearchOperator2 = @$_GET["w_cnum"];

		// dst
		$this->dst->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_dst"]);
		if ($this->dst->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->dst->AdvancedSearch->SearchOperator = @$_GET["z_dst"];
		$this->dst->AdvancedSearch->SearchCondition = @$_GET["v_dst"];
		$this->dst->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_dst"]);
		if ($this->dst->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->dst->AdvancedSearch->SearchOperator2 = @$_GET["w_dst"];

		// duration
		$this->duration->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_duration"]);
		if ($this->duration->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->duration->AdvancedSearch->SearchOperator = @$_GET["z_duration"];
		$this->duration->AdvancedSearch->SearchCondition = @$_GET["v_duration"];
		$this->duration->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_duration"]);
		if ($this->duration->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->duration->AdvancedSearch->SearchOperator2 = @$_GET["w_duration"];

		// billsec
		$this->billsec->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_billsec"]);
		if ($this->billsec->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->billsec->AdvancedSearch->SearchOperator = @$_GET["z_billsec"];
		$this->billsec->AdvancedSearch->SearchCondition = @$_GET["v_billsec"];
		$this->billsec->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_billsec"]);
		if ($this->billsec->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->billsec->AdvancedSearch->SearchOperator2 = @$_GET["w_billsec"];

		// disposition
		$this->disposition->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_disposition"]);
		if ($this->disposition->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->disposition->AdvancedSearch->SearchOperator = @$_GET["z_disposition"];
		$this->disposition->AdvancedSearch->SearchCondition = @$_GET["v_disposition"];
		$this->disposition->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_disposition"]);
		if ($this->disposition->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->disposition->AdvancedSearch->SearchOperator2 = @$_GET["w_disposition"];

		// outbound_cnum
		$this->outbound_cnum->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_outbound_cnum"]);
		if ($this->outbound_cnum->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->outbound_cnum->AdvancedSearch->SearchOperator = @$_GET["z_outbound_cnum"];
		$this->outbound_cnum->AdvancedSearch->SearchCondition = @$_GET["v_outbound_cnum"];
		$this->outbound_cnum->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_outbound_cnum"]);
		if ($this->outbound_cnum->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->outbound_cnum->AdvancedSearch->SearchOperator2 = @$_GET["w_outbound_cnum"];

		// play
		$this->play->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_play"]);
		if ($this->play->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->play->AdvancedSearch->SearchOperator = @$_GET["z_play"];
		$this->play->AdvancedSearch->SearchCondition = @$_GET["v_play"];
		$this->play->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_play"]);
		if ($this->play->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->play->AdvancedSearch->SearchOperator2 = @$_GET["w_play"];

		// recordingfile
		$this->recordingfile->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_recordingfile"]);
		if ($this->recordingfile->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->recordingfile->AdvancedSearch->SearchOperator = @$_GET["z_recordingfile"];
		$this->recordingfile->AdvancedSearch->SearchCondition = @$_GET["v_recordingfile"];
		$this->recordingfile->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_recordingfile"]);
		if ($this->recordingfile->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->recordingfile->AdvancedSearch->SearchOperator2 = @$_GET["w_recordingfile"];

		// recording_name
		$this->recording_name->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_recording_name"]);
		if ($this->recording_name->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->recording_name->AdvancedSearch->SearchOperator = @$_GET["z_recording_name"];
		$this->recording_name->AdvancedSearch->SearchCondition = @$_GET["v_recording_name"];
		$this->recording_name->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_recording_name"]);
		if ($this->recording_name->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->recording_name->AdvancedSearch->SearchOperator2 = @$_GET["w_recording_name"];

		// clid
		$this->clid->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_clid"]);
		if ($this->clid->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->clid->AdvancedSearch->SearchOperator = @$_GET["z_clid"];
		$this->clid->AdvancedSearch->SearchCondition = @$_GET["v_clid"];
		$this->clid->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_clid"]);
		if ($this->clid->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->clid->AdvancedSearch->SearchOperator2 = @$_GET["w_clid"];

		// src
		$this->src->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_src"]);
		if ($this->src->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->src->AdvancedSearch->SearchOperator = @$_GET["z_src"];
		$this->src->AdvancedSearch->SearchCondition = @$_GET["v_src"];
		$this->src->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_src"]);
		if ($this->src->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->src->AdvancedSearch->SearchOperator2 = @$_GET["w_src"];

		// dcontext
		$this->dcontext->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_dcontext"]);
		if ($this->dcontext->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->dcontext->AdvancedSearch->SearchOperator = @$_GET["z_dcontext"];
		$this->dcontext->AdvancedSearch->SearchCondition = @$_GET["v_dcontext"];
		$this->dcontext->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_dcontext"]);
		if ($this->dcontext->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->dcontext->AdvancedSearch->SearchOperator2 = @$_GET["w_dcontext"];

		// channel
		$this->channel->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_channel"]);
		if ($this->channel->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->channel->AdvancedSearch->SearchOperator = @$_GET["z_channel"];
		$this->channel->AdvancedSearch->SearchCondition = @$_GET["v_channel"];
		$this->channel->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_channel"]);
		if ($this->channel->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->channel->AdvancedSearch->SearchOperator2 = @$_GET["w_channel"];

		// dstchannel
		$this->dstchannel->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_dstchannel"]);
		if ($this->dstchannel->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->dstchannel->AdvancedSearch->SearchOperator = @$_GET["z_dstchannel"];
		$this->dstchannel->AdvancedSearch->SearchCondition = @$_GET["v_dstchannel"];
		$this->dstchannel->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_dstchannel"]);
		if ($this->dstchannel->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->dstchannel->AdvancedSearch->SearchOperator2 = @$_GET["w_dstchannel"];

		// lastapp
		$this->lastapp->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_lastapp"]);
		if ($this->lastapp->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->lastapp->AdvancedSearch->SearchOperator = @$_GET["z_lastapp"];
		$this->lastapp->AdvancedSearch->SearchCondition = @$_GET["v_lastapp"];
		$this->lastapp->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_lastapp"]);
		if ($this->lastapp->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->lastapp->AdvancedSearch->SearchOperator2 = @$_GET["w_lastapp"];

		// lastdata
		$this->lastdata->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_lastdata"]);
		if ($this->lastdata->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->lastdata->AdvancedSearch->SearchOperator = @$_GET["z_lastdata"];
		$this->lastdata->AdvancedSearch->SearchCondition = @$_GET["v_lastdata"];
		$this->lastdata->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_lastdata"]);
		if ($this->lastdata->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->lastdata->AdvancedSearch->SearchOperator2 = @$_GET["w_lastdata"];

		// amaflags
		$this->amaflags->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_amaflags"]);
		if ($this->amaflags->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->amaflags->AdvancedSearch->SearchOperator = @$_GET["z_amaflags"];
		$this->amaflags->AdvancedSearch->SearchCondition = @$_GET["v_amaflags"];
		$this->amaflags->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_amaflags"]);
		if ($this->amaflags->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->amaflags->AdvancedSearch->SearchOperator2 = @$_GET["w_amaflags"];

		// accountcode
		$this->accountcode->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_accountcode"]);
		if ($this->accountcode->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->accountcode->AdvancedSearch->SearchOperator = @$_GET["z_accountcode"];
		$this->accountcode->AdvancedSearch->SearchCondition = @$_GET["v_accountcode"];
		$this->accountcode->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_accountcode"]);
		if ($this->accountcode->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->accountcode->AdvancedSearch->SearchOperator2 = @$_GET["w_accountcode"];

		// userfield
		$this->userfield->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_userfield"]);
		if ($this->userfield->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->userfield->AdvancedSearch->SearchOperator = @$_GET["z_userfield"];
		$this->userfield->AdvancedSearch->SearchCondition = @$_GET["v_userfield"];
		$this->userfield->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_userfield"]);
		if ($this->userfield->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->userfield->AdvancedSearch->SearchOperator2 = @$_GET["w_userfield"];

		// did
		$this->did->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_did"]);
		if ($this->did->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->did->AdvancedSearch->SearchOperator = @$_GET["z_did"];
		$this->did->AdvancedSearch->SearchCondition = @$_GET["v_did"];
		$this->did->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_did"]);
		if ($this->did->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->did->AdvancedSearch->SearchOperator2 = @$_GET["w_did"];

		// outbound_cnam
		$this->outbound_cnam->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_outbound_cnam"]);
		if ($this->outbound_cnam->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->outbound_cnam->AdvancedSearch->SearchOperator = @$_GET["z_outbound_cnam"];
		$this->outbound_cnam->AdvancedSearch->SearchCondition = @$_GET["v_outbound_cnam"];
		$this->outbound_cnam->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_outbound_cnam"]);
		if ($this->outbound_cnam->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->outbound_cnam->AdvancedSearch->SearchOperator2 = @$_GET["w_outbound_cnam"];

		// dst_cnam
		$this->dst_cnam->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_dst_cnam"]);
		if ($this->dst_cnam->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->dst_cnam->AdvancedSearch->SearchOperator = @$_GET["z_dst_cnam"];
		$this->dst_cnam->AdvancedSearch->SearchCondition = @$_GET["v_dst_cnam"];
		$this->dst_cnam->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_dst_cnam"]);
		if ($this->dst_cnam->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->dst_cnam->AdvancedSearch->SearchOperator2 = @$_GET["w_dst_cnam"];

		// linkedid
		$this->linkedid->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_linkedid"]);
		if ($this->linkedid->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->linkedid->AdvancedSearch->SearchOperator = @$_GET["z_linkedid"];
		$this->linkedid->AdvancedSearch->SearchCondition = @$_GET["v_linkedid"];
		$this->linkedid->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_linkedid"]);
		if ($this->linkedid->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->linkedid->AdvancedSearch->SearchOperator2 = @$_GET["w_linkedid"];

		// peeraccount
		$this->peeraccount->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_peeraccount"]);
		if ($this->peeraccount->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->peeraccount->AdvancedSearch->SearchOperator = @$_GET["z_peeraccount"];
		$this->peeraccount->AdvancedSearch->SearchCondition = @$_GET["v_peeraccount"];
		$this->peeraccount->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_peeraccount"]);
		if ($this->peeraccount->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->peeraccount->AdvancedSearch->SearchOperator2 = @$_GET["w_peeraccount"];

		// sequence
		$this->sequence->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_sequence"]);
		if ($this->sequence->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->sequence->AdvancedSearch->SearchOperator = @$_GET["z_sequence"];
		$this->sequence->AdvancedSearch->SearchCondition = @$_GET["v_sequence"];
		$this->sequence->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_sequence"]);
		if ($this->sequence->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->sequence->AdvancedSearch->SearchOperator2 = @$_GET["w_sequence"];
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("uniqueid")) <> "")
			$this->uniqueid->CurrentValue = $this->getKey("uniqueid"); // uniqueid
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$conn = &$this->Connection();
			$this->OldRecordset = ew_LoadRecordset($sSql, $conn);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

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
			if ($this->Export == "")
				$this->uniqueid->ViewValue = ew_Highlight($this->HighlightName(), $this->uniqueid->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->uniqueid->AdvancedSearch->getValue("x"), $this->uniqueid->AdvancedSearch->getValue("y"));

			// cnam
			$this->cnam->LinkCustomAttributes = "";
			$this->cnam->HrefValue = "";
			$this->cnam->TooltipValue = "";
			if ($this->Export == "")
				$this->cnam->ViewValue = ew_Highlight($this->HighlightName(), $this->cnam->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->cnam->AdvancedSearch->getValue("x"), $this->cnam->AdvancedSearch->getValue("y"));

			// cnum
			$this->cnum->LinkCustomAttributes = "";
			$this->cnum->HrefValue = "";
			$this->cnum->TooltipValue = "";
			if ($this->Export == "")
				$this->cnum->ViewValue = ew_Highlight($this->HighlightName(), $this->cnum->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->cnum->AdvancedSearch->getValue("x"), $this->cnum->AdvancedSearch->getValue("y"));

			// dst
			$this->dst->LinkCustomAttributes = "";
			$this->dst->HrefValue = "";
			$this->dst->TooltipValue = "";
			if ($this->Export == "")
				$this->dst->ViewValue = ew_Highlight($this->HighlightName(), $this->dst->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->dst->AdvancedSearch->getValue("x"), $this->dst->AdvancedSearch->getValue("y"));

			// duration
			$this->duration->LinkCustomAttributes = "";
			$this->duration->HrefValue = "";
			$this->duration->TooltipValue = "";
			if ($this->Export == "")
				$this->duration->ViewValue = ew_Highlight($this->HighlightName(), $this->duration->ViewValue, "", "", $this->duration->AdvancedSearch->getValue("x"), "");

			// billsec
			$this->billsec->LinkCustomAttributes = "";
			$this->billsec->HrefValue = "";
			$this->billsec->TooltipValue = "";
			if ($this->Export == "")
				$this->billsec->ViewValue = ew_Highlight($this->HighlightName(), $this->billsec->ViewValue, "", "", $this->billsec->AdvancedSearch->getValue("x"), "");

			// disposition
			$this->disposition->LinkCustomAttributes = "";
			$this->disposition->HrefValue = "";
			$this->disposition->TooltipValue = "";
			if ($this->Export == "")
				$this->disposition->ViewValue = ew_Highlight($this->HighlightName(), $this->disposition->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->disposition->AdvancedSearch->getValue("x"), "");

			// outbound_cnum
			$this->outbound_cnum->LinkCustomAttributes = "";
			$this->outbound_cnum->HrefValue = "";
			$this->outbound_cnum->TooltipValue = "";
			if ($this->Export == "")
				$this->outbound_cnum->ViewValue = ew_Highlight($this->HighlightName(), $this->outbound_cnum->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->outbound_cnum->AdvancedSearch->getValue("x"), "");

			// play
			$this->play->LinkCustomAttributes = "";
			$this->play->HrefValue = "";
			$this->play->TooltipValue = "";
			if ($this->Export == "")
				$this->play->ViewValue = ew_Highlight($this->HighlightName(), $this->play->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->play->AdvancedSearch->getValue("x"), "");

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
			if ($this->Export == "")
				$this->recordingfile->ViewValue = ew_Highlight($this->HighlightName(), $this->recordingfile->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->recordingfile->AdvancedSearch->getValue("x"), "");
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
		$item->Visible = FALSE;

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$url = "";
		$item->Body = "<button id=\"emf_cdr\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_cdr',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fcdrlist,sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
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
	}

	// Export data in HTML/CSV/Word/Excel/XML/Email/PDF format
	function ExportData() {
		$utf8 = (strtolower(EW_CHARSET) == "utf-8");
		$bSelectLimit = $this->UseSelectLimit;

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

		// Export all
		if ($this->ExportAll) {
			set_time_limit(EW_EXPORT_ALL_TIME_LIMIT);
			$this->DisplayRecs = $this->TotalRecs;
			$this->StopRec = $this->TotalRecs;
		} else { // Export one page only
			$this->SetUpStartRec(); // Set up start record position

			// Set the last record to display
			if ($this->DisplayRecs <= 0) {
				$this->StopRec = $this->TotalRecs;
			} else {
				$this->StopRec = $this->StartRec + $this->DisplayRecs - 1;
			}
		}
		if ($bSelectLimit)
			$rs = $this->LoadRecordset($this->StartRec-1, $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs);
		if (!$rs) {
			header("Content-Type:"); // Remove header
			header("Content-Disposition:");
			$this->ShowMessage();
			return;
		}
		$this->ExportDoc = ew_ExportDocument($this, "h");
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
		$this->ExportDocument($Doc, $rs, $this->StartRec, $this->StopRec, "");
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

		// Build QueryString for search
		if ($this->BasicSearch->getKeyword() <> "") {
			$sQry .= "&" . EW_TABLE_BASIC_SEARCH . "=" . urlencode($this->BasicSearch->getKeyword()) . "&" . EW_TABLE_BASIC_SEARCH_TYPE . "=" . urlencode($this->BasicSearch->getType());
		}
		$this->AddSearchQueryString($sQry, $this->calldate); // calldate
		$this->AddSearchQueryString($sQry, $this->uniqueid); // uniqueid
		$this->AddSearchQueryString($sQry, $this->cnam); // cnam
		$this->AddSearchQueryString($sQry, $this->cnum); // cnum
		$this->AddSearchQueryString($sQry, $this->dst); // dst
		$this->AddSearchQueryString($sQry, $this->duration); // duration
		$this->AddSearchQueryString($sQry, $this->billsec); // billsec
		$this->AddSearchQueryString($sQry, $this->disposition); // disposition
		$this->AddSearchQueryString($sQry, $this->outbound_cnum); // outbound_cnum
		$this->AddSearchQueryString($sQry, $this->play); // play
		$this->AddSearchQueryString($sQry, $this->recordingfile); // recordingfile
		$this->AddSearchQueryString($sQry, $this->recording_name); // recording_name
		$this->AddSearchQueryString($sQry, $this->clid); // clid
		$this->AddSearchQueryString($sQry, $this->src); // src
		$this->AddSearchQueryString($sQry, $this->dcontext); // dcontext
		$this->AddSearchQueryString($sQry, $this->channel); // channel
		$this->AddSearchQueryString($sQry, $this->dstchannel); // dstchannel
		$this->AddSearchQueryString($sQry, $this->lastapp); // lastapp
		$this->AddSearchQueryString($sQry, $this->lastdata); // lastdata
		$this->AddSearchQueryString($sQry, $this->amaflags); // amaflags
		$this->AddSearchQueryString($sQry, $this->accountcode); // accountcode
		$this->AddSearchQueryString($sQry, $this->userfield); // userfield
		$this->AddSearchQueryString($sQry, $this->did); // did
		$this->AddSearchQueryString($sQry, $this->outbound_cnam); // outbound_cnam
		$this->AddSearchQueryString($sQry, $this->dst_cnam); // dst_cnam
		$this->AddSearchQueryString($sQry, $this->linkedid); // linkedid
		$this->AddSearchQueryString($sQry, $this->peeraccount); // peeraccount
		$this->AddSearchQueryString($sQry, $this->sequence); // sequence

		// Build QueryString for pager
		$sQry .= "&" . EW_TABLE_REC_PER_PAGE . "=" . urlencode($this->getRecordsPerPage()) . "&" . EW_TABLE_START_REC . "=" . urlencode($this->getStartRecordNumber());
		return $sQry;
	}

	// Add search QueryString
	function AddSearchQueryString(&$Qry, &$Fld) {
		$FldSearchValue = $Fld->AdvancedSearch->getValue("x");
		$FldParm = substr($Fld->FldVar,2);
		if (strval($FldSearchValue) <> "") {
			$Qry .= "&x_" . $FldParm . "=" . urlencode($FldSearchValue) .
				"&z_" . $FldParm . "=" . urlencode($Fld->AdvancedSearch->getValue("z"));
		}
		$FldSearchValue2 = $Fld->AdvancedSearch->getValue("y");
		if (strval($FldSearchValue2) <> "") {
			$Qry .= "&v_" . $FldParm . "=" . urlencode($Fld->AdvancedSearch->getValue("v")) .
				"&y_" . $FldParm . "=" . urlencode($FldSearchValue2) .
				"&w_" . $FldParm . "=" . urlencode($Fld->AdvancedSearch->getValue("w"));
		}
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", $this->TableVar, $url, "", $this->TableVar, TRUE);
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

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}

	// ListOptions Load event
	function ListOptions_Load() {

		// Example:
		//$opt = &$this->ListOptions->Add("new");
		//$opt->Header = "xxx";
		//$opt->OnLeft = TRUE; // Link on left
		//$opt->MoveTo(0); // Move to first column

	}

	// ListOptions Rendered event
	function ListOptions_Rendered() {

		// Example: 
		//$this->ListOptions->Items["new"]->Body = "xxx";

	}

	// Row Custom Action event
	function Row_CustomAction($action, $row) {

		// Return FALSE to abort
		return TRUE;
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
if (!isset($cdr_list)) $cdr_list = new ccdr_list();

// Page init
$cdr_list->Page_Init();

// Page main
$cdr_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$cdr_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($cdr->Export == "") { ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "list";
var CurrentForm = fcdrlist = new ew_Form("fcdrlist", "list");
fcdrlist.FormKeyCountName = '<?php echo $cdr_list->FormKeyCountName ?>';

// Form_CustomValidate event
fcdrlist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcdrlist.ValidateRequired = true;
<?php } else { ?>
fcdrlist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

var CurrentSearchForm = fcdrlistsrch = new ew_Form("fcdrlistsrch");

// Validate function for search
fcdrlistsrch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	var infix = "";
	elm = this.GetElements("x" + infix + "_calldate");
	if (elm && !ew_CheckDate(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($cdr->calldate->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_recordingfile");
	if (elm && typeof(isMediaElementjs) == "function" && !isMediaElementjs(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($cdr->recordingfile->FldErrMsg()) ?>");

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate event
fcdrlistsrch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcdrlistsrch.ValidateRequired = true; // Use JavaScript validation
<?php } else { ?>
fcdrlistsrch.ValidateRequired = false; // No JavaScript validation
<?php } ?>

// Dynamic selection lists
// Init search panel as collapsed

if (fcdrlistsrch) fcdrlistsrch.InitSearchPanel = true;
</script>
<style type="text/css">
.ewTablePreviewRow { /* main table preview row color */
	background-color: #FFFFFF; /* preview row color */
}
.ewTablePreviewRow .ewGrid {
	display: table;
}
.ewTablePreviewRow .ewGrid .ewTable {
	width: 100%;
}
</style>
<div id="ewPreview" class="hide"><ul class="nav nav-tabs"></ul><div class="tab-content"><div class="tab-pane fade"></div></div></div>
<script type="text/javascript" src="phpjs/ewpreview.min.js"></script>
<script type="text/javascript">
var EW_PREVIEW_PLACEMENT = EW_CSS_FLIP ? "left" : "right";
var EW_PREVIEW_SINGLE_ROW = false;
var EW_PREVIEW_OVERLAY = false;
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
<?php if ($cdr_list->TotalRecs > 0 && $cdr_list->ExportOptions->Visible()) { ?>
<?php $cdr_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($cdr_list->SearchOptions->Visible()) { ?>
<?php $cdr_list->SearchOptions->Render("body") ?>
<?php } ?>
<?php if ($cdr_list->FilterOptions->Visible()) { ?>
<?php $cdr_list->FilterOptions->Render("body") ?>
<?php } ?>
<?php if ($cdr->Export == "") { ?>
<?php echo $Language->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php
	$bSelectLimit = $cdr_list->UseSelectLimit;
	if ($bSelectLimit) {
		if ($cdr_list->TotalRecs <= 0)
			$cdr_list->TotalRecs = $cdr->SelectRecordCount();
	} else {
		if (!$cdr_list->Recordset && ($cdr_list->Recordset = $cdr_list->LoadRecordset()))
			$cdr_list->TotalRecs = $cdr_list->Recordset->RecordCount();
	}
	$cdr_list->StartRec = 1;
	if ($cdr_list->DisplayRecs <= 0 || ($cdr->Export <> "" && $cdr->ExportAll)) // Display all records
		$cdr_list->DisplayRecs = $cdr_list->TotalRecs;
	if (!($cdr->Export <> "" && $cdr->ExportAll))
		$cdr_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$cdr_list->Recordset = $cdr_list->LoadRecordset($cdr_list->StartRec-1, $cdr_list->DisplayRecs);

	// Set no record found message
	if ($cdr->CurrentAction == "" && $cdr_list->TotalRecs == 0) {
		if ($cdr_list->SearchWhere == "0=101")
			$cdr_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$cdr_list->setWarningMessage($Language->Phrase("NoRecord"));
	}

	// Audit trail on search
	if ($cdr_list->AuditTrailOnSearch && $cdr_list->Command == "search" && !$cdr_list->RestoreSearch) {
		$searchparm = ew_ServerVar("QUERY_STRING");
		$searchsql = $cdr_list->getSessionWhere();
		$cdr_list->WriteAuditTrailOnSearch($searchparm, $searchsql);
	}
$cdr_list->RenderOtherOptions();
?>
<?php if ($Security->IsLoggedIn()) { ?>
<?php if ($cdr->Export == "" && $cdr->CurrentAction == "") { ?>
<form name="fcdrlistsrch" id="fcdrlistsrch" class="form-inline ewForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($cdr_list->SearchWhere <> "") ? " in" : ""; ?>
<div id="fcdrlistsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="cdr">
	<div class="ewBasicSearch">
<?php
if ($gsSearchError == "")
	$cdr_list->LoadAdvancedSearch(); // Load advanced search

// Render for search
$cdr->RowType = EW_ROWTYPE_SEARCH;

// Render row
$cdr->ResetAttrs();
$cdr_list->RenderRow();
?>
<div id="xsr_1" class="ewRow">
<?php if ($cdr->calldate->Visible) { // calldate ?>
	<div id="xsc_calldate" class="ewCell form-group">
		<label for="x_calldate" class="ewSearchCaption ewLabel"><?php echo $cdr->calldate->FldCaption() ?></label>
		<span class="ewSearchOperator"><select name="z_calldate" id="z_calldate" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="BETWEEN"<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
		<span class="ewSearchField">
<input type="text" data-table="cdr" data-field="x_calldate" data-format="9" name="x_calldate" id="x_calldate" size="22" placeholder="<?php echo ew_HtmlEncode($cdr->calldate->getPlaceHolder()) ?>" value="<?php echo $cdr->calldate->EditValue ?>"<?php echo $cdr->calldate->EditAttributes() ?>>
<?php if (!$cdr->calldate->ReadOnly && !$cdr->calldate->Disabled && !isset($cdr->calldate->EditAttrs["readonly"]) && !isset($cdr->calldate->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fcdrlistsrch", "x_calldate", "%Y-%m-%d %H:%M:%S");
</script>
<?php } ?>
</span>
		<span class="ewSearchCond btw0_calldate"><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_calldate" value="AND"<?php if ($cdr->calldate->AdvancedSearch->SearchCondition <> "OR") echo " checked" ?>><?php echo $Language->Phrase("AND") ?></label><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_calldate" value="OR"<?php if ($cdr->calldate->AdvancedSearch->SearchCondition == "OR") echo " checked" ?>><?php echo $Language->Phrase("OR") ?></label>&nbsp;</span>
		<span class="ewSearchCond btw1_calldate">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
		<span class="ewSearchOperator btw0_calldate"><select name="w_calldate" id="w_calldate" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator2 == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator2 == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator2 == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator2 == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator2 == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->calldate->AdvancedSearch->SearchOperator2 == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option></select></span>
		<span class="ewSearchField">
<input type="text" data-table="cdr" data-field="x_calldate" data-format="9" name="y_calldate" id="y_calldate" size="22" placeholder="<?php echo ew_HtmlEncode($cdr->calldate->getPlaceHolder()) ?>" value="<?php echo $cdr->calldate->EditValue2 ?>"<?php echo $cdr->calldate->EditAttributes() ?>>
<?php if (!$cdr->calldate->ReadOnly && !$cdr->calldate->Disabled && !isset($cdr->calldate->EditAttrs["readonly"]) && !isset($cdr->calldate->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fcdrlistsrch", "y_calldate", "%Y-%m-%d %H:%M:%S");
</script>
<?php } ?>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_2" class="ewRow">
<?php if ($cdr->cnam->Visible) { // cnam ?>
	<div id="xsc_cnam" class="ewCell form-group">
		<label for="x_cnam" class="ewSearchCaption ewLabel"><?php echo $cdr->cnam->FldCaption() ?></label>
		<span class="ewSearchOperator"><select name="z_cnam" id="z_cnam" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
		<span class="ewSearchField">
<input type="text" data-table="cdr" data-field="x_cnam" name="x_cnam" id="x_cnam" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->cnam->getPlaceHolder()) ?>" value="<?php echo $cdr->cnam->EditValue ?>"<?php echo $cdr->cnam->EditAttributes() ?>>
</span>
		<span class="ewSearchCond btw0_cnam"><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_cnam" value="AND"<?php if ($cdr->cnam->AdvancedSearch->SearchCondition <> "OR") echo " checked" ?>><?php echo $Language->Phrase("AND") ?></label><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_cnam" value="OR"<?php if ($cdr->cnam->AdvancedSearch->SearchCondition == "OR") echo " checked" ?>><?php echo $Language->Phrase("OR") ?></label>&nbsp;</span>
		<span class="ewSearchCond btw1_cnam">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
		<span class="ewSearchOperator btw0_cnam"><select name="w_cnam" id="w_cnam" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->cnam->AdvancedSearch->SearchOperator2 == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option></select></span>
		<span class="ewSearchField">
<input type="text" data-table="cdr" data-field="x_cnam" name="y_cnam" id="y_cnam" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->cnam->getPlaceHolder()) ?>" value="<?php echo $cdr->cnam->EditValue2 ?>"<?php echo $cdr->cnam->EditAttributes() ?>>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_3" class="ewRow">
<?php if ($cdr->cnum->Visible) { // cnum ?>
	<div id="xsc_cnum" class="ewCell form-group">
		<label for="x_cnum" class="ewSearchCaption ewLabel"><?php echo $cdr->cnum->FldCaption() ?></label>
		<span class="ewSearchOperator"><select name="z_cnum" id="z_cnum" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
		<span class="ewSearchField">
<input type="text" data-table="cdr" data-field="x_cnum" name="x_cnum" id="x_cnum" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->cnum->getPlaceHolder()) ?>" value="<?php echo $cdr->cnum->EditValue ?>"<?php echo $cdr->cnum->EditAttributes() ?>>
</span>
		<span class="ewSearchCond btw0_cnum"><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_cnum" value="AND"<?php if ($cdr->cnum->AdvancedSearch->SearchCondition <> "OR") echo " checked" ?>><?php echo $Language->Phrase("AND") ?></label><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_cnum" value="OR"<?php if ($cdr->cnum->AdvancedSearch->SearchCondition == "OR") echo " checked" ?>><?php echo $Language->Phrase("OR") ?></label>&nbsp;</span>
		<span class="ewSearchCond btw1_cnum">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
		<span class="ewSearchOperator btw0_cnum"><select name="w_cnum" id="w_cnum" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->cnum->AdvancedSearch->SearchOperator2 == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option></select></span>
		<span class="ewSearchField">
<input type="text" data-table="cdr" data-field="x_cnum" name="y_cnum" id="y_cnum" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->cnum->getPlaceHolder()) ?>" value="<?php echo $cdr->cnum->EditValue2 ?>"<?php echo $cdr->cnum->EditAttributes() ?>>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_4" class="ewRow">
<?php if ($cdr->dst->Visible) { // dst ?>
	<div id="xsc_dst" class="ewCell form-group">
		<label for="x_dst" class="ewSearchCaption ewLabel"><?php echo $cdr->dst->FldCaption() ?></label>
		<span class="ewSearchOperator"><select name="z_dst" id="z_dst" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
		<span class="ewSearchField">
<input type="text" data-table="cdr" data-field="x_dst" name="x_dst" id="x_dst" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->dst->getPlaceHolder()) ?>" value="<?php echo $cdr->dst->EditValue ?>"<?php echo $cdr->dst->EditAttributes() ?>>
</span>
		<span class="ewSearchCond btw0_dst"><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_dst" value="AND"<?php if ($cdr->dst->AdvancedSearch->SearchCondition <> "OR") echo " checked" ?>><?php echo $Language->Phrase("AND") ?></label><label class="radio-inline ewRadio" style="white-space: nowrap;"><input type="radio" name="v_dst" value="OR"<?php if ($cdr->dst->AdvancedSearch->SearchCondition == "OR") echo " checked" ?>><?php echo $Language->Phrase("OR") ?></label>&nbsp;</span>
		<span class="ewSearchCond btw1_dst">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
		<span class="ewSearchOperator btw0_dst"><select name="w_dst" id="w_dst" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->dst->AdvancedSearch->SearchOperator2 == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option></select></span>
		<span class="ewSearchField">
<input type="text" data-table="cdr" data-field="x_dst" name="y_dst" id="y_dst" size="30" maxlength="80" placeholder="<?php echo ew_HtmlEncode($cdr->dst->getPlaceHolder()) ?>" value="<?php echo $cdr->dst->EditValue2 ?>"<?php echo $cdr->dst->EditAttributes() ?>>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_5" class="ewRow">
<?php if ($cdr->recordingfile->Visible) { // recordingfile ?>
	<div id="xsc_recordingfile" class="ewCell form-group">
		<label for="x_recordingfile" class="ewSearchCaption ewLabel"><?php echo $cdr->recordingfile->FldCaption() ?></label>
		<span class="ewSearchOperator"><select name="z_recordingfile" id="z_recordingfile" class="form-control" onchange="ewForms(this).SrchOprChanged(this);"><option value="="<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == "=") ? " selected" : "" ?> ><?php echo $Language->Phrase("EQUAL") ?></option><option value="<>"<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == "<>") ? " selected" : "" ?> ><?php echo $Language->Phrase("<>") ?></option><option value="<"<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == "<") ? " selected" : "" ?> ><?php echo $Language->Phrase("<") ?></option><option value="<="<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == "<=") ? " selected" : "" ?> ><?php echo $Language->Phrase("<=") ?></option><option value=">"<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == ">") ? " selected" : "" ?> ><?php echo $Language->Phrase(">") ?></option><option value=">="<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == ">=") ? " selected" : "" ?> ><?php echo $Language->Phrase(">=") ?></option><option value="LIKE"<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == "LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("LIKE") ?></option><option value="NOT LIKE"<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == "NOT LIKE") ? " selected" : "" ?> ><?php echo $Language->Phrase("NOT LIKE") ?></option><option value="STARTS WITH"<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == "STARTS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("STARTS WITH") ?></option><option value="ENDS WITH"<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == "ENDS WITH") ? " selected" : "" ?> ><?php echo $Language->Phrase("ENDS WITH") ?></option><option value="BETWEEN"<?php echo ($cdr->recordingfile->AdvancedSearch->SearchOperator == "BETWEEN") ? " selected" : "" ?> ><?php echo $Language->Phrase("BETWEEN") ?></option></select></span>
		<span class="ewSearchField">
<input type="text" data-table="cdr" data-field="x_recordingfile" name="x_recordingfile" id="x_recordingfile" size="60" maxlength="255" placeholder="<?php echo ew_HtmlEncode($cdr->recordingfile->getPlaceHolder()) ?>" value="<?php echo $cdr->recordingfile->EditValue ?>"<?php echo $cdr->recordingfile->EditAttributes() ?>>
</span>
		<span class="ewSearchCond btw1_recordingfile" style="display: none">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
		<span class="ewSearchField btw1_recordingfile" style="display: none">
<input type="text" data-table="cdr" data-field="x_recordingfile" name="y_recordingfile" id="y_recordingfile" size="60" maxlength="255" placeholder="<?php echo ew_HtmlEncode($cdr->recordingfile->getPlaceHolder()) ?>" value="<?php echo $cdr->recordingfile->EditValue2 ?>"<?php echo $cdr->recordingfile->EditAttributes() ?>>
</span>
	</div>
<?php } ?>
</div>
<div id="xsr_6" class="ewRow">
	<div class="ewQuickSearch input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control" value="<?php echo ew_HtmlEncode($cdr_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<input type="hidden" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="<?php echo ew_HtmlEncode($cdr_list->BasicSearch->getType()) ?>">
	<div class="input-group-btn">
		<button type="button" data-toggle="dropdown" class="btn btn-default"><span id="searchtype"><?php echo $cdr_list->BasicSearch->getTypeNameShort() ?></span><span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li<?php if ($cdr_list->BasicSearch->getType() == "") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this)"><?php echo $Language->Phrase("QuickSearchAuto") ?></a></li>
			<li<?php if ($cdr_list->BasicSearch->getType() == "=") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'=')"><?php echo $Language->Phrase("QuickSearchExact") ?></a></li>
			<li<?php if ($cdr_list->BasicSearch->getType() == "AND") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'AND')"><?php echo $Language->Phrase("QuickSearchAll") ?></a></li>
			<li<?php if ($cdr_list->BasicSearch->getType() == "OR") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'OR')"><?php echo $Language->Phrase("QuickSearchAny") ?></a></li>
		</ul>
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?></button>
	</div>
	</div>
</div>
	</div>
</div>
</form>
<?php } ?>
<?php } ?>
<?php $cdr_list->ShowPageHeader(); ?>
<?php
$cdr_list->ShowMessage();
?>
<?php if ($cdr_list->TotalRecs > 0 || $cdr->CurrentAction <> "") { ?>
<div class="panel panel-default ewGrid">
<?php if ($cdr->Export == "") { ?>
<div class="panel-heading ewGridUpperPanel">
<?php if ($cdr->CurrentAction <> "gridadd" && $cdr->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="form-inline ewForm ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($cdr_list->Pager)) $cdr_list->Pager = new cPrevNextPager($cdr_list->StartRec, $cdr_list->DisplayRecs, $cdr_list->TotalRecs) ?>
<?php if ($cdr_list->Pager->RecordCount > 0) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($cdr_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $cdr_list->PageUrl() ?>start=<?php echo $cdr_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($cdr_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $cdr_list->PageUrl() ?>start=<?php echo $cdr_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $cdr_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($cdr_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $cdr_list->PageUrl() ?>start=<?php echo $cdr_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($cdr_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $cdr_list->PageUrl() ?>start=<?php echo $cdr_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $cdr_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $cdr_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $cdr_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $cdr_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
<?php if ($cdr_list->TotalRecs > 0) { ?>
<div class="ewPager">
<input type="hidden" name="t" value="cdr">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="form-control input-sm" onchange="this.form.submit();">
<option value="5"<?php if ($cdr_list->DisplayRecs == 5) { ?> selected<?php } ?>>5</option>
<option value="10"<?php if ($cdr_list->DisplayRecs == 10) { ?> selected<?php } ?>>10</option>
<option value="15"<?php if ($cdr_list->DisplayRecs == 15) { ?> selected<?php } ?>>15</option>
<option value="20"<?php if ($cdr_list->DisplayRecs == 20) { ?> selected<?php } ?>>20</option>
<option value="30"<?php if ($cdr_list->DisplayRecs == 30) { ?> selected<?php } ?>>30</option>
<option value="40"<?php if ($cdr_list->DisplayRecs == 40) { ?> selected<?php } ?>>40</option>
<option value="50"<?php if ($cdr_list->DisplayRecs == 50) { ?> selected<?php } ?>>50</option>
<option value="100"<?php if ($cdr_list->DisplayRecs == 100) { ?> selected<?php } ?>>100</option>
<option value="ALL"<?php if ($cdr->getRecordsPerPage() == -1) { ?> selected<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($cdr_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
<form name="fcdrlist" id="fcdrlist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($cdr_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $cdr_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="cdr">
<div id="gmp_cdr" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php if ($cdr_list->TotalRecs > 0) { ?>
<table id="tbl_cdrlist" class="table ewTable">
<?php echo $cdr->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Header row
$cdr_list->RowType = EW_ROWTYPE_HEADER;

// Render list options
$cdr_list->RenderListOptions();

// Render list options (header, left)
$cdr_list->ListOptions->Render("header", "left");
?>
<?php if ($cdr->calldate->Visible) { // calldate ?>
	<?php if ($cdr->SortUrl($cdr->calldate) == "") { ?>
		<th data-name="calldate"><div id="elh_cdr_calldate" class="cdr_calldate"><div class="ewTableHeaderCaption"><?php echo $cdr->calldate->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="calldate"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $cdr->SortUrl($cdr->calldate) ?>',1);"><div id="elh_cdr_calldate" class="cdr_calldate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $cdr->calldate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($cdr->calldate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($cdr->calldate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($cdr->uniqueid->Visible) { // uniqueid ?>
	<?php if ($cdr->SortUrl($cdr->uniqueid) == "") { ?>
		<th data-name="uniqueid"><div id="elh_cdr_uniqueid" class="cdr_uniqueid"><div class="ewTableHeaderCaption"><?php echo $cdr->uniqueid->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="uniqueid"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $cdr->SortUrl($cdr->uniqueid) ?>',1);"><div id="elh_cdr_uniqueid" class="cdr_uniqueid">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $cdr->uniqueid->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($cdr->uniqueid->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($cdr->uniqueid->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($cdr->cnam->Visible) { // cnam ?>
	<?php if ($cdr->SortUrl($cdr->cnam) == "") { ?>
		<th data-name="cnam"><div id="elh_cdr_cnam" class="cdr_cnam"><div class="ewTableHeaderCaption"><?php echo $cdr->cnam->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="cnam"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $cdr->SortUrl($cdr->cnam) ?>',1);"><div id="elh_cdr_cnam" class="cdr_cnam">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $cdr->cnam->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($cdr->cnam->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($cdr->cnam->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($cdr->cnum->Visible) { // cnum ?>
	<?php if ($cdr->SortUrl($cdr->cnum) == "") { ?>
		<th data-name="cnum"><div id="elh_cdr_cnum" class="cdr_cnum"><div class="ewTableHeaderCaption"><?php echo $cdr->cnum->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="cnum"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $cdr->SortUrl($cdr->cnum) ?>',1);"><div id="elh_cdr_cnum" class="cdr_cnum">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $cdr->cnum->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($cdr->cnum->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($cdr->cnum->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($cdr->dst->Visible) { // dst ?>
	<?php if ($cdr->SortUrl($cdr->dst) == "") { ?>
		<th data-name="dst"><div id="elh_cdr_dst" class="cdr_dst"><div class="ewTableHeaderCaption"><?php echo $cdr->dst->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="dst"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $cdr->SortUrl($cdr->dst) ?>',1);"><div id="elh_cdr_dst" class="cdr_dst">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $cdr->dst->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($cdr->dst->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($cdr->dst->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($cdr->duration->Visible) { // duration ?>
	<?php if ($cdr->SortUrl($cdr->duration) == "") { ?>
		<th data-name="duration"><div id="elh_cdr_duration" class="cdr_duration"><div class="ewTableHeaderCaption"><?php echo $cdr->duration->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="duration"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $cdr->SortUrl($cdr->duration) ?>',1);"><div id="elh_cdr_duration" class="cdr_duration">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $cdr->duration->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($cdr->duration->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($cdr->duration->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($cdr->billsec->Visible) { // billsec ?>
	<?php if ($cdr->SortUrl($cdr->billsec) == "") { ?>
		<th data-name="billsec"><div id="elh_cdr_billsec" class="cdr_billsec"><div class="ewTableHeaderCaption"><?php echo $cdr->billsec->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="billsec"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $cdr->SortUrl($cdr->billsec) ?>',1);"><div id="elh_cdr_billsec" class="cdr_billsec">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $cdr->billsec->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($cdr->billsec->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($cdr->billsec->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($cdr->disposition->Visible) { // disposition ?>
	<?php if ($cdr->SortUrl($cdr->disposition) == "") { ?>
		<th data-name="disposition"><div id="elh_cdr_disposition" class="cdr_disposition"><div class="ewTableHeaderCaption"><?php echo $cdr->disposition->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="disposition"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $cdr->SortUrl($cdr->disposition) ?>',1);"><div id="elh_cdr_disposition" class="cdr_disposition">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $cdr->disposition->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($cdr->disposition->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($cdr->disposition->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($cdr->outbound_cnum->Visible) { // outbound_cnum ?>
	<?php if ($cdr->SortUrl($cdr->outbound_cnum) == "") { ?>
		<th data-name="outbound_cnum"><div id="elh_cdr_outbound_cnum" class="cdr_outbound_cnum"><div class="ewTableHeaderCaption"><?php echo $cdr->outbound_cnum->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="outbound_cnum"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $cdr->SortUrl($cdr->outbound_cnum) ?>',1);"><div id="elh_cdr_outbound_cnum" class="cdr_outbound_cnum">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $cdr->outbound_cnum->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($cdr->outbound_cnum->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($cdr->outbound_cnum->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($cdr->play->Visible) { // play ?>
	<?php if ($cdr->SortUrl($cdr->play) == "") { ?>
		<th data-name="play"><div id="elh_cdr_play" class="cdr_play"><div class="ewTableHeaderCaption"><?php echo $cdr->play->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="play"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $cdr->SortUrl($cdr->play) ?>',1);"><div id="elh_cdr_play" class="cdr_play">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $cdr->play->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($cdr->play->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($cdr->play->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($cdr->recordingfile->Visible) { // recordingfile ?>
	<?php if ($cdr->SortUrl($cdr->recordingfile) == "") { ?>
		<th data-name="recordingfile"><div id="elh_cdr_recordingfile" class="cdr_recordingfile"><div class="ewTableHeaderCaption"><?php echo $cdr->recordingfile->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="recordingfile"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $cdr->SortUrl($cdr->recordingfile) ?>',1);"><div id="elh_cdr_recordingfile" class="cdr_recordingfile">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $cdr->recordingfile->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($cdr->recordingfile->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($cdr->recordingfile->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$cdr_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($cdr->ExportAll && $cdr->Export <> "") {
	$cdr_list->StopRec = $cdr_list->TotalRecs;
} else {

	// Set the last record to display
	if ($cdr_list->TotalRecs > $cdr_list->StartRec + $cdr_list->DisplayRecs - 1)
		$cdr_list->StopRec = $cdr_list->StartRec + $cdr_list->DisplayRecs - 1;
	else
		$cdr_list->StopRec = $cdr_list->TotalRecs;
}
$cdr_list->RecCnt = $cdr_list->StartRec - 1;
if ($cdr_list->Recordset && !$cdr_list->Recordset->EOF) {
	$cdr_list->Recordset->MoveFirst();
	$bSelectLimit = $cdr_list->UseSelectLimit;
	if (!$bSelectLimit && $cdr_list->StartRec > 1)
		$cdr_list->Recordset->Move($cdr_list->StartRec - 1);
} elseif (!$cdr->AllowAddDeleteRow && $cdr_list->StopRec == 0) {
	$cdr_list->StopRec = $cdr->GridAddRowCount;
}

// Initialize aggregate
$cdr->RowType = EW_ROWTYPE_AGGREGATEINIT;
$cdr->ResetAttrs();
$cdr_list->RenderRow();
while ($cdr_list->RecCnt < $cdr_list->StopRec) {
	$cdr_list->RecCnt++;
	if (intval($cdr_list->RecCnt) >= intval($cdr_list->StartRec)) {
		$cdr_list->RowCnt++;

		// Set up key count
		$cdr_list->KeyCount = $cdr_list->RowIndex;

		// Init row class and style
		$cdr->ResetAttrs();
		$cdr->CssClass = "";
		if ($cdr->CurrentAction == "gridadd") {
		} else {
			$cdr_list->LoadRowValues($cdr_list->Recordset); // Load row values
		}
		$cdr->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$cdr->RowAttrs = array_merge($cdr->RowAttrs, array('data-rowindex'=>$cdr_list->RowCnt, 'id'=>'r' . $cdr_list->RowCnt . '_cdr', 'data-rowtype'=>$cdr->RowType));

		// Render row
		$cdr_list->RenderRow();

		// Render list options
		$cdr_list->RenderListOptions();
?>
	<tr<?php echo $cdr->RowAttributes() ?>>
<?php

// Render list options (body, left)
$cdr_list->ListOptions->Render("body", "left", $cdr_list->RowCnt);
?>
	<?php if ($cdr->calldate->Visible) { // calldate ?>
		<td data-name="calldate"<?php echo $cdr->calldate->CellAttributes() ?>>
<span id="el<?php echo $cdr_list->RowCnt ?>_cdr_calldate" class="cdr_calldate">
<span<?php echo $cdr->calldate->ViewAttributes() ?>>
<?php echo $cdr->calldate->ListViewValue() ?></span>
</span>
<a id="<?php echo $cdr_list->PageObjName . "_row_" . $cdr_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($cdr->uniqueid->Visible) { // uniqueid ?>
		<td data-name="uniqueid"<?php echo $cdr->uniqueid->CellAttributes() ?>>
<span id="el<?php echo $cdr_list->RowCnt ?>_cdr_uniqueid" class="cdr_uniqueid">
<span<?php echo $cdr->uniqueid->ViewAttributes() ?>>
<?php echo $cdr->uniqueid->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($cdr->cnam->Visible) { // cnam ?>
		<td data-name="cnam"<?php echo $cdr->cnam->CellAttributes() ?>>
<span id="el<?php echo $cdr_list->RowCnt ?>_cdr_cnam" class="cdr_cnam">
<span<?php echo $cdr->cnam->ViewAttributes() ?>>
<?php echo $cdr->cnam->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($cdr->cnum->Visible) { // cnum ?>
		<td data-name="cnum"<?php echo $cdr->cnum->CellAttributes() ?>>
<span id="el<?php echo $cdr_list->RowCnt ?>_cdr_cnum" class="cdr_cnum">
<span<?php echo $cdr->cnum->ViewAttributes() ?>>
<?php echo $cdr->cnum->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($cdr->dst->Visible) { // dst ?>
		<td data-name="dst"<?php echo $cdr->dst->CellAttributes() ?>>
<span id="el<?php echo $cdr_list->RowCnt ?>_cdr_dst" class="cdr_dst">
<span<?php echo $cdr->dst->ViewAttributes() ?>>
<?php echo $cdr->dst->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($cdr->duration->Visible) { // duration ?>
		<td data-name="duration"<?php echo $cdr->duration->CellAttributes() ?>>
<span id="el<?php echo $cdr_list->RowCnt ?>_cdr_duration" class="cdr_duration">
<span<?php echo $cdr->duration->ViewAttributes() ?>>
<?php echo $cdr->duration->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($cdr->billsec->Visible) { // billsec ?>
		<td data-name="billsec"<?php echo $cdr->billsec->CellAttributes() ?>>
<span id="el<?php echo $cdr_list->RowCnt ?>_cdr_billsec" class="cdr_billsec">
<span<?php echo $cdr->billsec->ViewAttributes() ?>>
<?php echo $cdr->billsec->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($cdr->disposition->Visible) { // disposition ?>
		<td data-name="disposition"<?php echo $cdr->disposition->CellAttributes() ?>>
<span id="el<?php echo $cdr_list->RowCnt ?>_cdr_disposition" class="cdr_disposition">
<span<?php echo $cdr->disposition->ViewAttributes() ?>>
<?php echo $cdr->disposition->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($cdr->outbound_cnum->Visible) { // outbound_cnum ?>
		<td data-name="outbound_cnum"<?php echo $cdr->outbound_cnum->CellAttributes() ?>>
<span id="el<?php echo $cdr_list->RowCnt ?>_cdr_outbound_cnum" class="cdr_outbound_cnum">
<span<?php echo $cdr->outbound_cnum->ViewAttributes() ?>>
<?php echo $cdr->outbound_cnum->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($cdr->play->Visible) { // play ?>
		<td data-name="play"<?php echo $cdr->play->CellAttributes() ?>>
<span id="el<?php echo $cdr_list->RowCnt ?>_cdr_play" class="cdr_play">
<span<?php echo $cdr->play->ViewAttributes() ?>>
<?php echo $cdr->play->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($cdr->recordingfile->Visible) { // recordingfile ?>
		<td data-name="recordingfile"<?php echo $cdr->recordingfile->CellAttributes() ?>>
<span id="el<?php echo $cdr_list->RowCnt ?>_cdr_recordingfile" class="cdr_recordingfile">
<span<?php echo $cdr->recordingfile->ViewAttributes() ?>>
<?php if ((!ew_EmptyStr($cdr->recordingfile->ListViewValue())) && $cdr->recordingfile->LinkAttributes() <> "") { ?>
<a<?php echo $cdr->recordingfile->LinkAttributes() ?>><?php echo $cdr->recordingfile->ListViewValue() ?></a>
<?php } else { ?>
<?php echo $cdr->recordingfile->ListViewValue() ?>
<?php } ?>
</span>
</span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$cdr_list->ListOptions->Render("body", "right", $cdr_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($cdr->CurrentAction <> "gridadd")
		$cdr_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($cdr->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($cdr_list->Recordset)
	$cdr_list->Recordset->Close();
?>
<?php if ($cdr->Export == "") { ?>
<div class="panel-footer ewGridLowerPanel">
<?php if ($cdr->CurrentAction <> "gridadd" && $cdr->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($cdr_list->Pager)) $cdr_list->Pager = new cPrevNextPager($cdr_list->StartRec, $cdr_list->DisplayRecs, $cdr_list->TotalRecs) ?>
<?php if ($cdr_list->Pager->RecordCount > 0) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($cdr_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $cdr_list->PageUrl() ?>start=<?php echo $cdr_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($cdr_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $cdr_list->PageUrl() ?>start=<?php echo $cdr_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $cdr_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($cdr_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $cdr_list->PageUrl() ?>start=<?php echo $cdr_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($cdr_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $cdr_list->PageUrl() ?>start=<?php echo $cdr_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $cdr_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $cdr_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $cdr_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $cdr_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
<?php if ($cdr_list->TotalRecs > 0) { ?>
<div class="ewPager">
<input type="hidden" name="t" value="cdr">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="form-control input-sm" onchange="this.form.submit();">
<option value="5"<?php if ($cdr_list->DisplayRecs == 5) { ?> selected<?php } ?>>5</option>
<option value="10"<?php if ($cdr_list->DisplayRecs == 10) { ?> selected<?php } ?>>10</option>
<option value="15"<?php if ($cdr_list->DisplayRecs == 15) { ?> selected<?php } ?>>15</option>
<option value="20"<?php if ($cdr_list->DisplayRecs == 20) { ?> selected<?php } ?>>20</option>
<option value="30"<?php if ($cdr_list->DisplayRecs == 30) { ?> selected<?php } ?>>30</option>
<option value="40"<?php if ($cdr_list->DisplayRecs == 40) { ?> selected<?php } ?>>40</option>
<option value="50"<?php if ($cdr_list->DisplayRecs == 50) { ?> selected<?php } ?>>50</option>
<option value="100"<?php if ($cdr_list->DisplayRecs == 100) { ?> selected<?php } ?>>100</option>
<option value="ALL"<?php if ($cdr->getRecordsPerPage() == -1) { ?> selected<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($cdr_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
<?php if ($cdr_list->TotalRecs == 0 && $cdr->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($cdr_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($cdr->Export == "") { ?>
<script type="text/javascript">
fcdrlistsrch.Init();
fcdrlistsrch.FilterList = <?php echo $cdr_list->GetFilterList() ?>;
fcdrlist.Init();
</script>
<?php } ?>
<?php
$cdr_list->ShowPageFooter();
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
$cdr_list->Page_Terminate();
?>
