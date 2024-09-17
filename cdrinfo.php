<?php

// Global variable for table object
$cdr = NULL;

//
// Table class for cdr
//
class ccdr extends cTable {
	var $calldate;
	var $uniqueid;
	var $cnam;
	var $cnum;
	var $dst;
	var $duration;
	var $billsec;
	var $disposition;
	var $outbound_cnum;
	var $play;
	var $recordingfile;
	var $recording_name;
	var $clid;
	var $src;
	var $dcontext;
	var $channel;
	var $dstchannel;
	var $lastapp;
	var $lastdata;
	var $amaflags;
	var $accountcode;
	var $userfield;
	var $did;
	var $outbound_cnam;
	var $dst_cnam;
	var $linkedid;
	var $peeraccount;
	var $sequence;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'cdr';
		$this->TableName = 'cdr';
		$this->TableType = 'TABLE';

		// Update Table
		$this->UpdateTable = "`cdr`";
		$this->DBID = 'DB';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
		$this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
		$this->ExportPageSize = "a4"; // Page size (PDF only)
		$this->ExportExcelPageOrientation = PHPExcel_Worksheet_PageSetup::ORIENTATION_DEFAULT; // Page orientation (PHPExcel only)
		$this->ExportExcelPageSize = PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4; // Page size (PHPExcel only)
		$this->DetailAdd = FALSE; // Allow detail add
		$this->DetailEdit = FALSE; // Allow detail edit
		$this->DetailView = FALSE; // Allow detail view
		$this->ShowMultipleDetails = FALSE; // Show multiple details
		$this->GridAddRowCount = ($this->getRecordsPerPage()) ? $this->getRecordsPerPage() : 5;
		$this->AllowAddDeleteRow = ew_AllowAddDeleteRow(); // Allow add/delete row
		$this->UserIDAllowSecurity = 0; // User ID Allow
		$this->BasicSearch = new cBasicSearch($this->TableVar);
		$this->BasicSearch->TypeDefault = "OR";

		// calldate
		$this->calldate = new cField('cdr', 'cdr', 'x_calldate', 'calldate', '`calldate`', 'DATE_FORMAT(`calldate`, \'%Y-%m-%d\')', 135, 9, FALSE, '`calldate`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->calldate->FldDefaultErrMsg = str_replace("%s", "-", $Language->Phrase("IncorrectDateYMD"));
		$this->fields['calldate'] = &$this->calldate;

		// uniqueid
		$this->uniqueid = new cField('cdr', 'cdr', 'x_uniqueid', 'uniqueid', '`uniqueid`', '`uniqueid`', 200, -1, FALSE, '`uniqueid`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['uniqueid'] = &$this->uniqueid;

		// cnam
		$this->cnam = new cField('cdr', 'cdr', 'x_cnam', 'cnam', '`cnam`', '`cnam`', 200, -1, FALSE, '`cnam`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['cnam'] = &$this->cnam;

		// cnum
		$this->cnum = new cField('cdr', 'cdr', 'x_cnum', 'cnum', '`cnum`', '`cnum`', 200, -1, FALSE, '`cnum`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['cnum'] = &$this->cnum;

		// dst
		$this->dst = new cField('cdr', 'cdr', 'x_dst', 'dst', '`dst`', '`dst`', 200, -1, FALSE, '`dst`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['dst'] = &$this->dst;

		// duration
		$this->duration = new cField('cdr', 'cdr', 'x_duration', 'duration', '`duration`', '`duration`', 3, -1, FALSE, '`duration`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->duration->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['duration'] = &$this->duration;

		// billsec
		$this->billsec = new cField('cdr', 'cdr', 'x_billsec', 'billsec', '`billsec`', '`billsec`', 3, -1, FALSE, '`billsec`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->billsec->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['billsec'] = &$this->billsec;

		// disposition
		$this->disposition = new cField('cdr', 'cdr', 'x_disposition', 'disposition', '`disposition`', '`disposition`', 200, -1, FALSE, '`disposition`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['disposition'] = &$this->disposition;

		// outbound_cnum
		$this->outbound_cnum = new cField('cdr', 'cdr', 'x_outbound_cnum', 'outbound_cnum', '`outbound_cnum`', '`outbound_cnum`', 200, -1, FALSE, '`outbound_cnum`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['outbound_cnum'] = &$this->outbound_cnum;

		// play
		$this->play = new cField('cdr', 'cdr', 'x_play', 'play', 'uniqueid', 'uniqueid', 200, -1, FALSE, 'uniqueid', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->play->FldIsCustom = TRUE; // Custom field
		$this->fields['play'] = &$this->play;

		// recordingfile
		$this->recordingfile = new cField('cdr', 'cdr', 'x_recordingfile', 'recordingfile', '`recordingfile`', '`recordingfile`', 200, -1, FALSE, '`recordingfile`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->recordingfile->FldDefaultErrMsg = $Language->Phrase("IncorrectField");
		$this->fields['recordingfile'] = &$this->recordingfile;

		// recording_name
		$this->recording_name = new cField('cdr', 'cdr', 'x_recording_name', 'recording_name', 'recordingfile', 'recordingfile', 200, -1, FALSE, 'recordingfile', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->recording_name->FldIsCustom = TRUE; // Custom field
		$this->fields['recording_name'] = &$this->recording_name;

		// clid
		$this->clid = new cField('cdr', 'cdr', 'x_clid', 'clid', '`clid`', '`clid`', 200, -1, FALSE, '`clid`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['clid'] = &$this->clid;

		// src
		$this->src = new cField('cdr', 'cdr', 'x_src', 'src', '`src`', '`src`', 200, -1, FALSE, '`src`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['src'] = &$this->src;

		// dcontext
		$this->dcontext = new cField('cdr', 'cdr', 'x_dcontext', 'dcontext', '`dcontext`', '`dcontext`', 200, -1, FALSE, '`dcontext`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['dcontext'] = &$this->dcontext;

		// channel
		$this->channel = new cField('cdr', 'cdr', 'x_channel', 'channel', '`channel`', '`channel`', 200, -1, FALSE, '`channel`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['channel'] = &$this->channel;

		// dstchannel
		$this->dstchannel = new cField('cdr', 'cdr', 'x_dstchannel', 'dstchannel', '`dstchannel`', '`dstchannel`', 200, -1, FALSE, '`dstchannel`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['dstchannel'] = &$this->dstchannel;

		// lastapp
		$this->lastapp = new cField('cdr', 'cdr', 'x_lastapp', 'lastapp', '`lastapp`', '`lastapp`', 200, -1, FALSE, '`lastapp`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['lastapp'] = &$this->lastapp;

		// lastdata
		$this->lastdata = new cField('cdr', 'cdr', 'x_lastdata', 'lastdata', '`lastdata`', '`lastdata`', 200, -1, FALSE, '`lastdata`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['lastdata'] = &$this->lastdata;

		// amaflags
		$this->amaflags = new cField('cdr', 'cdr', 'x_amaflags', 'amaflags', '`amaflags`', '`amaflags`', 3, -1, FALSE, '`amaflags`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->amaflags->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['amaflags'] = &$this->amaflags;

		// accountcode
		$this->accountcode = new cField('cdr', 'cdr', 'x_accountcode', 'accountcode', '`accountcode`', '`accountcode`', 200, -1, FALSE, '`accountcode`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['accountcode'] = &$this->accountcode;

		// userfield
		$this->userfield = new cField('cdr', 'cdr', 'x_userfield', 'userfield', '`userfield`', '`userfield`', 200, -1, FALSE, '`userfield`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['userfield'] = &$this->userfield;

		// did
		$this->did = new cField('cdr', 'cdr', 'x_did', 'did', '`did`', '`did`', 200, -1, FALSE, '`did`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['did'] = &$this->did;

		// outbound_cnam
		$this->outbound_cnam = new cField('cdr', 'cdr', 'x_outbound_cnam', 'outbound_cnam', '`outbound_cnam`', '`outbound_cnam`', 200, -1, FALSE, '`outbound_cnam`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['outbound_cnam'] = &$this->outbound_cnam;

		// dst_cnam
		$this->dst_cnam = new cField('cdr', 'cdr', 'x_dst_cnam', 'dst_cnam', '`dst_cnam`', '`dst_cnam`', 200, -1, FALSE, '`dst_cnam`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['dst_cnam'] = &$this->dst_cnam;

		// linkedid
		$this->linkedid = new cField('cdr', 'cdr', 'x_linkedid', 'linkedid', '`linkedid`', '`linkedid`', 200, -1, FALSE, '`linkedid`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['linkedid'] = &$this->linkedid;

		// peeraccount
		$this->peeraccount = new cField('cdr', 'cdr', 'x_peeraccount', 'peeraccount', '`peeraccount`', '`peeraccount`', 200, -1, FALSE, '`peeraccount`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['peeraccount'] = &$this->peeraccount;

		// sequence
		$this->sequence = new cField('cdr', 'cdr', 'x_sequence', 'sequence', '`sequence`', '`sequence`', 3, -1, FALSE, '`sequence`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->sequence->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['sequence'] = &$this->sequence;
	}

	// Single column sort
	function UpdateSort(&$ofld) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sSortField = $ofld->FldExpression;
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
			$this->setSessionOrderBy($sSortField . " " . $sThisSort); // Save to Session
		} else {
			$ofld->setSort("");
		}
	}

	// Table level SQL
	var $_SqlFrom = "";

	function getSqlFrom() { // From
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`cdr`";
	}

	function SqlFrom() { // For backward compatibility
    	return $this->getSqlFrom();
	}

	function setSqlFrom($v) {
    	$this->_SqlFrom = $v;
	}
	var $_SqlSelect = "";

	function getSqlSelect() { // Select
		return ($this->_SqlSelect <> "") ? $this->_SqlSelect : "SELECT *, uniqueid AS `play`, recordingfile AS `recording_name` FROM " . $this->getSqlFrom();
	}

	function SqlSelect() { // For backward compatibility
    	return $this->getSqlSelect();
	}

	function setSqlSelect($v) {
    	$this->_SqlSelect = $v;
	}
	var $_SqlWhere = "";

	function getSqlWhere() { // Where
		global $Page;
		$sWhere = ($this->_SqlWhere <> "") ? $this->_SqlWhere : "";
		$this->TableFilter = "";
		if ($Page->PageID == 'view' || $Page->PageID == 'edit') {
			if ($this->TableFilter) {
				$this->TableFilter .= ' AND '.$this->KeyFilter();
			} else {
				$this->TableFilter = ($this->getSessionWhere() ? $this->getSessionWhere() : $this->KeyFilter());
			}
			$this->setSessionWhere($this->TableFilter);
		}
		ew_AddFilter($sWhere, $this->TableFilter);
		return $sWhere;
	}

	function SqlWhere() { // For backward compatibility
    	return $this->getSqlWhere();
	}

	function setSqlWhere($v) {
    	$this->_SqlWhere = $v;
	}
	var $_SqlGroupBy = "";

	function getSqlGroupBy() { // Group By
		return ($this->_SqlGroupBy <> "") ? $this->_SqlGroupBy : "";
	}

	function SqlGroupBy() { // For backward compatibility
    	return $this->getSqlGroupBy();
	}

	function setSqlGroupBy($v) {
    	$this->_SqlGroupBy = $v;
	}
	var $_SqlHaving = "";

	function getSqlHaving() { // Having
		return ($this->_SqlHaving <> "") ? $this->_SqlHaving : "";
	}

	function SqlHaving() { // For backward compatibility
    	return $this->getSqlHaving();
	}

	function setSqlHaving($v) {
    	$this->_SqlHaving = $v;
	}
	var $_SqlOrderBy = "";

	function getSqlOrderBy() { // Order By
		return ($this->_SqlOrderBy <> "") ? $this->_SqlOrderBy : "`calldate` DESC";
	}

	function SqlOrderBy() { // For backward compatibility
    	return $this->getSqlOrderBy();
	}

	function setSqlOrderBy($v) {
    	$this->_SqlOrderBy = $v;
	}

	// Apply User ID filters
	function ApplyUserIDFilters($sFilter) {
		return $sFilter;
	}

	// Check if User ID security allows view all
	function UserIDAllow($id = "") {
		$allow = EW_USER_ID_ALLOW;
		switch ($id) {
			case "add":
			case "copy":
			case "gridadd":
			case "register":
			case "addopt":
				return (($allow & 1) == 1);
			case "edit":
			case "gridedit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return (($allow & 4) == 4);
			case "delete":
				return (($allow & 2) == 2);
			case "view":
				return (($allow & 32) == 32);
			case "search":
				return (($allow & 64) == 64);
			default:
				return (($allow & 8) == 8);
		}
	}

	// Get SQL
	function GetSQL($where, $orderby) {
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(),
			$this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(),
			$where, $orderby);
	}

	// Table SQL
	function SQL() {
		$sFilter = $this->CurrentFilter;
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(),
			$this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(),
			$sFilter, $sSort);
	}

	// Table SQL with List page filter
	function SelectSQL() {
		$sFilter = $this->getSessionWhere();
		ew_AddFilter($sFilter, $this->CurrentFilter);
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$this->Recordset_Selecting($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(),
			$this->getSqlHaving(), $this->getSqlOrderBy(), $sFilter, $sSort);
	}

	// Get ORDER BY clause
	function GetOrderBy() {
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql("", "", "", "", $this->getSqlOrderBy(), "", $sSort);
	}

	// Try to get record count
	function TryGetRecordCount($sSql) {
		$cnt = -1;
		if (($this->TableType == 'TABLE' || $this->TableType == 'VIEW' || $this->TableType == 'LINKTABLE') && preg_match("/^SELECT \* FROM/i", $sSql)) {
			$sSql = "SELECT COUNT(*) FROM" . preg_replace('/^SELECT\s([\s\S]+)?\*\sFROM/i', "", $sSql);
			$sOrderBy = $this->GetOrderBy();
			if (substr($sSql, strlen($sOrderBy) * -1) == $sOrderBy)
				$sSql = substr($sSql, 0, strlen($sSql) - strlen($sOrderBy)); // Remove ORDER BY clause
		} else {
			$sSql = "SELECT COUNT(*) FROM (" . $sSql . ") EW_COUNT_TABLE";
		}
		$conn = &$this->Connection();
		if ($rs = $conn->Execute($sSql)) {
			if (!$rs->EOF && $rs->FieldCount() > 0) {
				$cnt = $rs->fields[0];
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// Get record count based on filter (for detail record count in master table pages)
	function LoadRecordCount($sFilter) {
		$origFilter = $this->CurrentFilter;
		$this->CurrentFilter = $sFilter;
		$this->Recordset_Selecting($this->CurrentFilter);

		//$sSql = $this->SQL();
		$sSql = $this->GetSQL($this->CurrentFilter, "");
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $this->LoadRs($this->CurrentFilter)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Get record count (for current List page)
	function SelectRecordCount() {
		$sSql = $this->SelectSQL();
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			$conn = &$this->Connection();
			if ($rs = $conn->Execute($sSql)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// INSERT statement
	function InsertSQL(&$rs) {
		$names = "";
		$values = "";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]) || $this->fields[$name]->FldIsCustom)
				continue;
			$names .= $this->fields[$name]->FldExpression . ",";
			$values .= ew_QuotedValue($value, $this->fields[$name]->FldDataType, $this->DBID) . ",";
		}
		while (substr($names, -1) == ",")
			$names = substr($names, 0, -1);
		while (substr($values, -1) == ",")
			$values = substr($values, 0, -1);
		return "INSERT INTO " . $this->UpdateTable . " ($names) VALUES ($values)";
	}

	// Insert
	function Insert(&$rs) {
		$conn = &$this->Connection();
		return $conn->Execute($this->InsertSQL($rs));
	}

	// UPDATE statement
	function UpdateSQL(&$rs, $where = "", $curfilter = TRUE) {
		$sql = "UPDATE " . $this->UpdateTable . " SET ";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]) || $this->fields[$name]->FldIsCustom)
				continue;
			$sql .= $this->fields[$name]->FldExpression . "=";
			$sql .= ew_QuotedValue($value, $this->fields[$name]->FldDataType, $this->DBID) . ",";
		}
		while (substr($sql, -1) == ",")
			$sql = substr($sql, 0, -1);
		$filter = ($curfilter) ? $this->CurrentFilter : "";
		if (is_array($where))
			$where = $this->ArrayToFilter($where);
		ew_AddFilter($filter, $where);
		if ($filter <> "")	$sql .= " WHERE " . $filter;
		return $sql;
	}

	// Update
	function Update(&$rs, $where = "", $rsold = NULL, $curfilter = TRUE) {
		$conn = &$this->Connection();
		return $conn->Execute($this->UpdateSQL($rs, $where, $curfilter));
	}

	// DELETE statement
	function DeleteSQL(&$rs, $where = "", $curfilter = TRUE) {
		$sql = "DELETE FROM " . $this->UpdateTable . " WHERE ";
		if (is_array($where))
			$where = $this->ArrayToFilter($where);
		if ($rs) {
			if (array_key_exists('uniqueid', $rs))
				ew_AddFilter($where, ew_QuotedName('uniqueid', $this->DBID) . '=' . ew_QuotedValue($rs['uniqueid'], $this->uniqueid->FldDataType, $this->DBID));
		}
		$filter = ($curfilter) ? $this->CurrentFilter : "";
		ew_AddFilter($filter, $where);
		if ($filter <> "")
			$sql .= $filter;
		else
			$sql .= "0=1"; // Avoid delete
		return $sql;
	}

	// Delete
	function Delete(&$rs, $where = "", $curfilter = TRUE) {
		$conn = &$this->Connection();
		return $conn->Execute($this->DeleteSQL($rs, $where, $curfilter));
	}

	// Key filter WHERE clause
	function SqlKeyFilter() {
		return "`uniqueid` = '@uniqueid@'";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		$sKeyFilter = str_replace("@uniqueid@", ew_AdjustSql($this->uniqueid->CurrentValue, $this->DBID), $sKeyFilter); // Replace key value
		return $sKeyFilter;
	}

	// Return page URL
	function getReturnUrl() {
		$name = EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL;

		// Get referer URL automatically
		if (ew_ServerVar("HTTP_REFERER") <> "" && ew_ReferPage() <> ew_CurrentPage() && ew_ReferPage() <> "login.php") // Referer not same page or login page
			$_SESSION[$name] = ew_ServerVar("HTTP_REFERER"); // Save to Session
		if (@$_SESSION[$name] <> "") {
			return $_SESSION[$name];
		} else {
			return "cdrlist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "cdrlist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			$url = $this->KeyUrl("cdrview.php", $this->UrlParm($parm));
		else
			$url = $this->KeyUrl("cdrview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
		return $this->AddMasterUrl($url);
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			$url = "cdradd.php?" . $this->UrlParm($parm);
		else
			$url = "cdradd.php";
		return $this->AddMasterUrl($url);
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		$url = $this->KeyUrl("cdredit.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
		return $this->AddMasterUrl($url);
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		$url = $this->KeyUrl("cdradd.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
		return $this->AddMasterUrl($url);
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("cdrdelete.php", $this->UrlParm());
	}

	// Add master url
	function AddMasterUrl($url) {
		return $url;
	}

	function KeyToJson() {
		$json = "";
		$json .= "uniqueid:" . ew_VarToJson($this->uniqueid->CurrentValue, "string", "'");
		return "{" . $json . "}";
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->uniqueid->CurrentValue)) {
			$sUrl .= "uniqueid=" . urlencode($this->uniqueid->CurrentValue);
		} else {
			return "javascript:ew_Alert(ewLanguage.Phrase('InvalidRecord'));";
		}
		return $sUrl;
	}

	// Sort URL
	function SortUrl(&$fld) {
		if ($this->CurrentAction <> "" || $this->Export <> "" ||
			in_array($fld->FldType, array(128, 204, 205))) { // Unsortable data type
				return "";
		} elseif ($fld->Sortable) {
			$sUrlParm = $this->UrlParm("order=" . urlencode($fld->FldName) . "&amp;ordertype=" . $fld->ReverseSort());
			return ew_CurrentPage() . "?" . $sUrlParm;
		} else {
			return "";
		}
	}

	// Get record keys from $_POST/$_GET/$_SESSION
	function GetRecordKeys() {
		global $EW_COMPOSITE_KEY_SEPARATOR;
		$arKeys = array();
		$arKey = array();
		if (isset($_POST["key_m"])) {
			$arKeys = ew_StripSlashes($_POST["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET["key_m"])) {
			$arKeys = ew_StripSlashes($_GET["key_m"]);
			$cnt = count($arKeys);
		} elseif (!empty($_GET) || !empty($_POST)) {
			$isPost = ew_IsHttpPost();
			if ($isPost && isset($_POST["uniqueid"]))
				$arKeys[] = ew_StripSlashes($_POST["uniqueid"]);
			elseif (isset($_GET["uniqueid"]))
				$arKeys[] = ew_StripSlashes($_GET["uniqueid"]);
			else
				$arKeys = NULL; // Do not setup

			//return $arKeys; // Do not return yet, so the values will also be checked by the following code
		}

		// Check keys
		$ar = array();
		if (is_array($arKeys)) {
			foreach ($arKeys as $key) {
				$ar[] = $key;
			}
		}
		return $ar;
	}

	// Get key filter
	function GetKeyFilter() {
		$arKeys = $this->GetRecordKeys();
		$sKeyFilter = "";
		foreach ($arKeys as $key) {
			if ($sKeyFilter <> "") $sKeyFilter .= " OR ";
			$this->uniqueid->CurrentValue = $key;
			$sKeyFilter .= "(" . $this->KeyFilter() . ")";
		}
		return $sKeyFilter;
	}

	// Load rows based on filter
	function &LoadRs($sFilter) {

		// Set up filter (SQL WHERE clause) and get return SQL
		//$this->CurrentFilter = $sFilter;
		//$sSql = $this->SQL();

		$sSql = $this->GetSQL($sFilter, "");
		$conn = &$this->Connection();
		$rs = $conn->Execute($sSql);
		return $rs;
	}

	// Load row values from recordset
	function LoadListRowValues(&$rs) {
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

	// Render list row values
	function RenderListRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
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

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Render edit row values
	function RenderEditRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

		// calldate
		$this->calldate->EditAttrs["class"] = "form-control";
		$this->calldate->EditCustomAttributes = "style='direction:ltr;text-align:center;'";
		$this->calldate->EditValue = ew_FormatDateTime($this->calldate->CurrentValue, 9);
		$this->calldate->PlaceHolder = ew_RemoveHtml($this->calldate->FldCaption());

		// uniqueid
		$this->uniqueid->EditAttrs["class"] = "form-control";
		$this->uniqueid->EditCustomAttributes = "";
		$this->uniqueid->EditValue = $this->uniqueid->CurrentValue;
		$this->uniqueid->ViewCustomAttributes = "";

		// cnam
		$this->cnam->EditAttrs["class"] = "form-control";
		$this->cnam->EditCustomAttributes = "";
		$this->cnam->EditValue = $this->cnam->CurrentValue;
		$this->cnam->PlaceHolder = ew_RemoveHtml($this->cnam->FldCaption());

		// cnum
		$this->cnum->EditAttrs["class"] = "form-control";
		$this->cnum->EditCustomAttributes = "";
		$this->cnum->EditValue = $this->cnum->CurrentValue;
		$this->cnum->PlaceHolder = ew_RemoveHtml($this->cnum->FldCaption());

		// dst
		$this->dst->EditAttrs["class"] = "form-control";
		$this->dst->EditCustomAttributes = "";
		$this->dst->EditValue = $this->dst->CurrentValue;
		$this->dst->PlaceHolder = ew_RemoveHtml($this->dst->FldCaption());

		// duration
		$this->duration->EditAttrs["class"] = "form-control";
		$this->duration->EditCustomAttributes = "";
		$this->duration->EditValue = $this->duration->CurrentValue;
		$this->duration->PlaceHolder = ew_RemoveHtml($this->duration->FldCaption());

		// billsec
		$this->billsec->EditAttrs["class"] = "form-control";
		$this->billsec->EditCustomAttributes = "";
		$this->billsec->EditValue = $this->billsec->CurrentValue;
		$this->billsec->PlaceHolder = ew_RemoveHtml($this->billsec->FldCaption());

		// disposition
		$this->disposition->EditAttrs["class"] = "form-control";
		$this->disposition->EditCustomAttributes = "";
		$this->disposition->EditValue = $this->disposition->CurrentValue;
		$this->disposition->PlaceHolder = ew_RemoveHtml($this->disposition->FldCaption());

		// outbound_cnum
		$this->outbound_cnum->EditAttrs["class"] = "form-control";
		$this->outbound_cnum->EditCustomAttributes = "";
		$this->outbound_cnum->EditValue = $this->outbound_cnum->CurrentValue;
		$this->outbound_cnum->PlaceHolder = ew_RemoveHtml($this->outbound_cnum->FldCaption());

		// play
		$this->play->EditAttrs["class"] = "form-control";
		$this->play->EditCustomAttributes = "";
		$this->play->EditValue = $this->play->CurrentValue;
		$this->play->PlaceHolder = ew_RemoveHtml($this->play->FldCaption());

		// recordingfile
		$this->recordingfile->EditAttrs["class"] = "form-control";
		$this->recordingfile->EditCustomAttributes = "";
		$this->recordingfile->EditValue = $this->recordingfile->CurrentValue;
		$this->recordingfile->PlaceHolder = ew_RemoveHtml($this->recordingfile->FldCaption());

		// recording_name
		$this->recording_name->EditAttrs["class"] = "form-control";
		$this->recording_name->EditCustomAttributes = "";
		$this->recording_name->EditValue = $this->recording_name->CurrentValue;
		$this->recording_name->PlaceHolder = ew_RemoveHtml($this->recording_name->FldCaption());

		// clid
		$this->clid->EditAttrs["class"] = "form-control";
		$this->clid->EditCustomAttributes = "";
		$this->clid->EditValue = $this->clid->CurrentValue;
		$this->clid->PlaceHolder = ew_RemoveHtml($this->clid->FldCaption());

		// src
		$this->src->EditAttrs["class"] = "form-control";
		$this->src->EditCustomAttributes = "";
		$this->src->EditValue = $this->src->CurrentValue;
		$this->src->PlaceHolder = ew_RemoveHtml($this->src->FldCaption());

		// dcontext
		$this->dcontext->EditAttrs["class"] = "form-control";
		$this->dcontext->EditCustomAttributes = "";
		$this->dcontext->EditValue = $this->dcontext->CurrentValue;
		$this->dcontext->PlaceHolder = ew_RemoveHtml($this->dcontext->FldCaption());

		// channel
		$this->channel->EditAttrs["class"] = "form-control";
		$this->channel->EditCustomAttributes = "";
		$this->channel->EditValue = $this->channel->CurrentValue;
		$this->channel->PlaceHolder = ew_RemoveHtml($this->channel->FldCaption());

		// dstchannel
		$this->dstchannel->EditAttrs["class"] = "form-control";
		$this->dstchannel->EditCustomAttributes = "";
		$this->dstchannel->EditValue = $this->dstchannel->CurrentValue;
		$this->dstchannel->PlaceHolder = ew_RemoveHtml($this->dstchannel->FldCaption());

		// lastapp
		$this->lastapp->EditAttrs["class"] = "form-control";
		$this->lastapp->EditCustomAttributes = "";
		$this->lastapp->EditValue = $this->lastapp->CurrentValue;
		$this->lastapp->PlaceHolder = ew_RemoveHtml($this->lastapp->FldCaption());

		// lastdata
		$this->lastdata->EditAttrs["class"] = "form-control";
		$this->lastdata->EditCustomAttributes = "";
		$this->lastdata->EditValue = $this->lastdata->CurrentValue;
		$this->lastdata->PlaceHolder = ew_RemoveHtml($this->lastdata->FldCaption());

		// amaflags
		$this->amaflags->EditAttrs["class"] = "form-control";
		$this->amaflags->EditCustomAttributes = "";
		$this->amaflags->EditValue = $this->amaflags->CurrentValue;
		$this->amaflags->PlaceHolder = ew_RemoveHtml($this->amaflags->FldCaption());

		// accountcode
		$this->accountcode->EditAttrs["class"] = "form-control";
		$this->accountcode->EditCustomAttributes = "";
		$this->accountcode->EditValue = $this->accountcode->CurrentValue;
		$this->accountcode->PlaceHolder = ew_RemoveHtml($this->accountcode->FldCaption());

		// userfield
		$this->userfield->EditAttrs["class"] = "form-control";
		$this->userfield->EditCustomAttributes = "";
		$this->userfield->EditValue = $this->userfield->CurrentValue;
		$this->userfield->PlaceHolder = ew_RemoveHtml($this->userfield->FldCaption());

		// did
		$this->did->EditAttrs["class"] = "form-control";
		$this->did->EditCustomAttributes = "";
		$this->did->EditValue = $this->did->CurrentValue;
		$this->did->PlaceHolder = ew_RemoveHtml($this->did->FldCaption());

		// outbound_cnam
		$this->outbound_cnam->EditAttrs["class"] = "form-control";
		$this->outbound_cnam->EditCustomAttributes = "";
		$this->outbound_cnam->EditValue = $this->outbound_cnam->CurrentValue;
		$this->outbound_cnam->PlaceHolder = ew_RemoveHtml($this->outbound_cnam->FldCaption());

		// dst_cnam
		$this->dst_cnam->EditAttrs["class"] = "form-control";
		$this->dst_cnam->EditCustomAttributes = "";
		$this->dst_cnam->EditValue = $this->dst_cnam->CurrentValue;
		$this->dst_cnam->PlaceHolder = ew_RemoveHtml($this->dst_cnam->FldCaption());

		// linkedid
		$this->linkedid->EditAttrs["class"] = "form-control";
		$this->linkedid->EditCustomAttributes = "";
		$this->linkedid->EditValue = $this->linkedid->CurrentValue;
		$this->linkedid->PlaceHolder = ew_RemoveHtml($this->linkedid->FldCaption());

		// peeraccount
		$this->peeraccount->EditAttrs["class"] = "form-control";
		$this->peeraccount->EditCustomAttributes = "";
		$this->peeraccount->EditValue = $this->peeraccount->CurrentValue;
		$this->peeraccount->PlaceHolder = ew_RemoveHtml($this->peeraccount->FldCaption());

		// sequence
		$this->sequence->EditAttrs["class"] = "form-control";
		$this->sequence->EditCustomAttributes = "";
		$this->sequence->EditValue = $this->sequence->CurrentValue;
		$this->sequence->PlaceHolder = ew_RemoveHtml($this->sequence->FldCaption());

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Aggregate list row values
	function AggregateListRowValues() {
	}

	// Aggregate list row (for rendering)
	function AggregateListRow() {

		// Call Row Rendered event
		$this->Row_Rendered();
	}
	var $ExportDoc;

	// Export data in HTML/CSV/Word/Excel/Email/PDF format
	function ExportDocument(&$Doc, &$Recordset, $StartRec, $StopRec, $ExportPageType = "") {
		if (!$Recordset || !$Doc)
			return;
		if (!$Doc->ExportCustom) {

			// Write header
			$Doc->ExportTableHeader();
			if ($Doc->Horizontal) { // Horizontal format, write header
				$Doc->BeginExportRow();
				if ($ExportPageType == "view") {
					if ($this->calldate->Exportable) $Doc->ExportCaption($this->calldate);
					if ($this->uniqueid->Exportable) $Doc->ExportCaption($this->uniqueid);
					if ($this->cnam->Exportable) $Doc->ExportCaption($this->cnam);
					if ($this->cnum->Exportable) $Doc->ExportCaption($this->cnum);
					if ($this->dst->Exportable) $Doc->ExportCaption($this->dst);
					if ($this->duration->Exportable) $Doc->ExportCaption($this->duration);
					if ($this->billsec->Exportable) $Doc->ExportCaption($this->billsec);
					if ($this->disposition->Exportable) $Doc->ExportCaption($this->disposition);
					if ($this->outbound_cnum->Exportable) $Doc->ExportCaption($this->outbound_cnum);
					if ($this->play->Exportable) $Doc->ExportCaption($this->play);
					if ($this->recordingfile->Exportable) $Doc->ExportCaption($this->recordingfile);
					if ($this->recording_name->Exportable) $Doc->ExportCaption($this->recording_name);
					if ($this->clid->Exportable) $Doc->ExportCaption($this->clid);
					if ($this->src->Exportable) $Doc->ExportCaption($this->src);
					if ($this->dcontext->Exportable) $Doc->ExportCaption($this->dcontext);
					if ($this->channel->Exportable) $Doc->ExportCaption($this->channel);
					if ($this->dstchannel->Exportable) $Doc->ExportCaption($this->dstchannel);
					if ($this->lastapp->Exportable) $Doc->ExportCaption($this->lastapp);
					if ($this->lastdata->Exportable) $Doc->ExportCaption($this->lastdata);
					if ($this->amaflags->Exportable) $Doc->ExportCaption($this->amaflags);
					if ($this->accountcode->Exportable) $Doc->ExportCaption($this->accountcode);
					if ($this->userfield->Exportable) $Doc->ExportCaption($this->userfield);
					if ($this->did->Exportable) $Doc->ExportCaption($this->did);
					if ($this->outbound_cnam->Exportable) $Doc->ExportCaption($this->outbound_cnam);
					if ($this->dst_cnam->Exportable) $Doc->ExportCaption($this->dst_cnam);
					if ($this->linkedid->Exportable) $Doc->ExportCaption($this->linkedid);
					if ($this->peeraccount->Exportable) $Doc->ExportCaption($this->peeraccount);
					if ($this->sequence->Exportable) $Doc->ExportCaption($this->sequence);
				} else {
					if ($this->calldate->Exportable) $Doc->ExportCaption($this->calldate);
					if ($this->uniqueid->Exportable) $Doc->ExportCaption($this->uniqueid);
					if ($this->cnam->Exportable) $Doc->ExportCaption($this->cnam);
					if ($this->cnum->Exportable) $Doc->ExportCaption($this->cnum);
					if ($this->dst->Exportable) $Doc->ExportCaption($this->dst);
					if ($this->duration->Exportable) $Doc->ExportCaption($this->duration);
					if ($this->billsec->Exportable) $Doc->ExportCaption($this->billsec);
					if ($this->disposition->Exportable) $Doc->ExportCaption($this->disposition);
					if ($this->outbound_cnum->Exportable) $Doc->ExportCaption($this->outbound_cnum);
					if ($this->play->Exportable) $Doc->ExportCaption($this->play);
					if ($this->recordingfile->Exportable) $Doc->ExportCaption($this->recordingfile);
					if ($this->recording_name->Exportable) $Doc->ExportCaption($this->recording_name);
					if ($this->clid->Exportable) $Doc->ExportCaption($this->clid);
					if ($this->src->Exportable) $Doc->ExportCaption($this->src);
					if ($this->dcontext->Exportable) $Doc->ExportCaption($this->dcontext);
					if ($this->channel->Exportable) $Doc->ExportCaption($this->channel);
					if ($this->dstchannel->Exportable) $Doc->ExportCaption($this->dstchannel);
					if ($this->lastapp->Exportable) $Doc->ExportCaption($this->lastapp);
					if ($this->lastdata->Exportable) $Doc->ExportCaption($this->lastdata);
					if ($this->amaflags->Exportable) $Doc->ExportCaption($this->amaflags);
					if ($this->accountcode->Exportable) $Doc->ExportCaption($this->accountcode);
					if ($this->userfield->Exportable) $Doc->ExportCaption($this->userfield);
					if ($this->did->Exportable) $Doc->ExportCaption($this->did);
					if ($this->outbound_cnam->Exportable) $Doc->ExportCaption($this->outbound_cnam);
					if ($this->dst_cnam->Exportable) $Doc->ExportCaption($this->dst_cnam);
					if ($this->linkedid->Exportable) $Doc->ExportCaption($this->linkedid);
					if ($this->peeraccount->Exportable) $Doc->ExportCaption($this->peeraccount);
					if ($this->sequence->Exportable) $Doc->ExportCaption($this->sequence);
				}
				$Doc->EndExportRow();
			}
		}

		// Move to first record
		$RecCnt = $StartRec - 1;
		if (!$Recordset->EOF) {
			$Recordset->MoveFirst();
			if ($StartRec > 1)
				$Recordset->Move($StartRec - 1);
		}
		while (!$Recordset->EOF && $RecCnt < $StopRec) {
			$RecCnt++;
			if (intval($RecCnt) >= intval($StartRec)) {
				$RowCnt = intval($RecCnt) - intval($StartRec) + 1;

				// Page break
				if ($this->ExportPageBreakCount > 0) {
					if ($RowCnt > 1 && ($RowCnt - 1) % $this->ExportPageBreakCount == 0)
						$Doc->ExportPageBreak();
				}
				$this->LoadListRowValues($Recordset);

				// Render row
				$this->RowType = EW_ROWTYPE_VIEW; // Render view
				$this->ResetAttrs();
				$this->RenderListRow();
				if (!$Doc->ExportCustom) {
					$Doc->BeginExportRow($RowCnt); // Allow CSS styles if enabled
					if ($ExportPageType == "view") {
						if ($this->calldate->Exportable) $Doc->ExportField($this->calldate);
						if ($this->uniqueid->Exportable) $Doc->ExportField($this->uniqueid);
						if ($this->cnam->Exportable) $Doc->ExportField($this->cnam);
						if ($this->cnum->Exportable) $Doc->ExportField($this->cnum);
						if ($this->dst->Exportable) $Doc->ExportField($this->dst);
						if ($this->duration->Exportable) $Doc->ExportField($this->duration);
						if ($this->billsec->Exportable) $Doc->ExportField($this->billsec);
						if ($this->disposition->Exportable) $Doc->ExportField($this->disposition);
						if ($this->outbound_cnum->Exportable) $Doc->ExportField($this->outbound_cnum);
						if ($this->play->Exportable) $Doc->ExportField($this->play);
						if ($this->recordingfile->Exportable) $Doc->ExportField($this->recordingfile);
						if ($this->recording_name->Exportable) $Doc->ExportField($this->recording_name);
						if ($this->clid->Exportable) $Doc->ExportField($this->clid);
						if ($this->src->Exportable) $Doc->ExportField($this->src);
						if ($this->dcontext->Exportable) $Doc->ExportField($this->dcontext);
						if ($this->channel->Exportable) $Doc->ExportField($this->channel);
						if ($this->dstchannel->Exportable) $Doc->ExportField($this->dstchannel);
						if ($this->lastapp->Exportable) $Doc->ExportField($this->lastapp);
						if ($this->lastdata->Exportable) $Doc->ExportField($this->lastdata);
						if ($this->amaflags->Exportable) $Doc->ExportField($this->amaflags);
						if ($this->accountcode->Exportable) $Doc->ExportField($this->accountcode);
						if ($this->userfield->Exportable) $Doc->ExportField($this->userfield);
						if ($this->did->Exportable) $Doc->ExportField($this->did);
						if ($this->outbound_cnam->Exportable) $Doc->ExportField($this->outbound_cnam);
						if ($this->dst_cnam->Exportable) $Doc->ExportField($this->dst_cnam);
						if ($this->linkedid->Exportable) $Doc->ExportField($this->linkedid);
						if ($this->peeraccount->Exportable) $Doc->ExportField($this->peeraccount);
						if ($this->sequence->Exportable) $Doc->ExportField($this->sequence);
					} else {
						if ($this->calldate->Exportable) $Doc->ExportField($this->calldate);
						if ($this->uniqueid->Exportable) $Doc->ExportField($this->uniqueid);
						if ($this->cnam->Exportable) $Doc->ExportField($this->cnam);
						if ($this->cnum->Exportable) $Doc->ExportField($this->cnum);
						if ($this->dst->Exportable) $Doc->ExportField($this->dst);
						if ($this->duration->Exportable) $Doc->ExportField($this->duration);
						if ($this->billsec->Exportable) $Doc->ExportField($this->billsec);
						if ($this->disposition->Exportable) $Doc->ExportField($this->disposition);
						if ($this->outbound_cnum->Exportable) $Doc->ExportField($this->outbound_cnum);
						if ($this->play->Exportable) $Doc->ExportField($this->play);
						if ($this->recordingfile->Exportable) $Doc->ExportField($this->recordingfile);
						if ($this->recording_name->Exportable) $Doc->ExportField($this->recording_name);
						if ($this->clid->Exportable) $Doc->ExportField($this->clid);
						if ($this->src->Exportable) $Doc->ExportField($this->src);
						if ($this->dcontext->Exportable) $Doc->ExportField($this->dcontext);
						if ($this->channel->Exportable) $Doc->ExportField($this->channel);
						if ($this->dstchannel->Exportable) $Doc->ExportField($this->dstchannel);
						if ($this->lastapp->Exportable) $Doc->ExportField($this->lastapp);
						if ($this->lastdata->Exportable) $Doc->ExportField($this->lastdata);
						if ($this->amaflags->Exportable) $Doc->ExportField($this->amaflags);
						if ($this->accountcode->Exportable) $Doc->ExportField($this->accountcode);
						if ($this->userfield->Exportable) $Doc->ExportField($this->userfield);
						if ($this->did->Exportable) $Doc->ExportField($this->did);
						if ($this->outbound_cnam->Exportable) $Doc->ExportField($this->outbound_cnam);
						if ($this->dst_cnam->Exportable) $Doc->ExportField($this->dst_cnam);
						if ($this->linkedid->Exportable) $Doc->ExportField($this->linkedid);
						if ($this->peeraccount->Exportable) $Doc->ExportField($this->peeraccount);
						if ($this->sequence->Exportable) $Doc->ExportField($this->sequence);
					}
					$Doc->EndExportRow();
				}
			}

			// Call Row Export server event
			if ($Doc->ExportCustom)
				$this->Row_Export($Recordset->fields);
			$Recordset->MoveNext();
		}
		if (!$Doc->ExportCustom) {
			$Doc->ExportTableFooter();
		}
	}

	// Get auto fill value
	function GetAutoFill($id, $val) {
		$rsarr = array();
		$rowcnt = 0;

		// Output
		if (is_array($rsarr) && $rowcnt > 0) {
			$fldcnt = count($rsarr[0]);
			for ($i = 0; $i < $rowcnt; $i++) {
				for ($j = 0; $j < $fldcnt; $j++) {
					$str = strval($rsarr[$i][$j]);
					$str = ew_ConvertToUtf8($str);
					if (isset($post["keepCRLF"])) {
						$str = str_replace(array("\r", "\n"), array("\\r", "\\n"), $str);
					} else {
						$str = str_replace(array("\r", "\n"), array(" ", " "), $str);
					}
					$rsarr[$i][$j] = $str;
				}
			}
			return ew_ArrayToJson($rsarr);
		} else {
			return FALSE;
		}
	}

	// Table level events
	// Recordset Selecting event
	function Recordset_Selecting(&$filter) {

		// Enter your code here	
	}

	// Recordset Selected event
	function Recordset_Selected(&$rs) {

		//echo "Recordset Selected";
	}

	// Recordset Search Validated event
	function Recordset_SearchValidated() {

		// Example:
		//$this->MyField1->AdvancedSearch->SearchValue = "your search criteria"; // Search value

	}

	// Recordset Searching event
	function Recordset_Searching(&$filter) {

		// Enter your code here	
	}

	// Row_Selecting event
	function Row_Selecting(&$filter) {

		// Enter your code here	
	}

	// Row Selected event
	function Row_Selected(&$rs) {

		//echo "Row Selected";
	}

	// Row Inserting event
	function Row_Inserting($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Inserted event
	function Row_Inserted($rsold, &$rsnew) {

		//echo "Row Inserted"
	}

	// Row Updating event
	function Row_Updating($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Updated event
	function Row_Updated($rsold, &$rsnew) {

		//echo "Row Updated";
	}

	// Row Update Conflict event
	function Row_UpdateConflict($rsold, &$rsnew) {

		// Enter your code here
		// To ignore conflict, set return value to FALSE

		return TRUE;
	}

	// Grid Inserting event
	function Grid_Inserting() {

		// Enter your code here
		// To reject grid insert, set return value to FALSE

		return TRUE;
	}

	// Grid Inserted event
	function Grid_Inserted($rsnew) {

		//echo "Grid Inserted";
	}

	// Grid Updating event
	function Grid_Updating($rsold) {

		// Enter your code here
		// To reject grid update, set return value to FALSE

		return TRUE;
	}

	// Grid Updated event
	function Grid_Updated($rsold, $rsnew) {

		//echo "Grid Updated";
	}

	// Row Deleting event
	function Row_Deleting(&$rs) {

		// Enter your code here
		// To cancel, set return value to False

		return TRUE;
	}

	// Row Deleted event
	function Row_Deleted(&$rs) {

		//echo "Row Deleted";
	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Lookup Selecting event
	function Lookup_Selecting($fld, &$filter) {

		//var_dump($fld->FldName, $fld->LookupFilters, $filter); // Uncomment to view the filter
		// Enter your code here

	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here	
	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>); 

		global $conn, $Security, $Language, $Page;

		//
		$recordingfile = 'recordingfile.php?id='.$this->uniqueid->CurrentValue;
		$this->play->ViewValue = '<audio id="'.$this->uniqueid->CurrentValue.'" style="width: 490px;" controls playsinline webkit-playsinline><source src="'.$recordingfile.'" type="audio/mpeg">Your browser does not support the audio tag.</audio>';
		$this->play->ViewAttrs["style"] .= "width: 490px;";
		$this->play->ViewAttrs["style"] .= "direction: ltr;";

	//	$this->play->HrefValue = 'javascript:';
		$this->recordingfile->ViewValue = 'Download';
		$this->recordingfile->HrefValue = $recordingfile;

	//	$this->recordingfile->LinkAttrs["target"] = "_blank";
		//

		$this->calldate->ViewAttrs["style"] .= "width: 125px;";
		$this->calldate->ViewAttrs["style"] .= "direction: ltr;";
		if (date('Y-m-d', strtotime($this->calldate->CurrentValue)) == date('Y-m-d')) {
			$this->calldate->CellAttrs["style"] .= "color: #CC0000;";
		}
		$this->uniqueid->ViewAttrs["style"] .= "direction: ltr;";
		$this->dst->ViewAttrs["style"] .= "direction: ltr;";
		$this->clid->ViewAttrs["style"] .= "direction: ltr;";
		$this->lastdata->ViewAttrs["style"] .= "direction: ltr;";
	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}
}
?>
