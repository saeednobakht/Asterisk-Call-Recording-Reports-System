<?php

// Compatibility with PHP Report Maker
if (!isset($Language)) {
	include_once "ewcfg12.php";
	include_once "ewshared12.php";
	$Language = new cLanguage();
}

// Responsive layout
if (ew_IsResponsiveLayout()) {
	$gsHeaderRowClass = "hidden-xs ewHeaderRow";
	$gsMenuColumnClass = "hidden-xs ewMenuColumn";
	$gsSiteTitleClass = "hidden-xs ewSiteTitle";
} else {
	$gsHeaderRowClass = "ewHeaderRow";
	$gsMenuColumnClass = "ewMenuColumn";
	$gsSiteTitleClass = "ewSiteTitle";
}
?>
<!DOCTYPE html>
<html lang="<?php echo $gsLanguage ?>">
<head>
	<title><?php echo $Language->ProjectPhrase("BodyTitle") ?></title>
<meta charset="utf-8">
<?php if (@$gsExport == "" || @$gsExport == "print") { ?>
<link rel="stylesheet" type="text/css" href="<?php echo $EW_RELATIVE_PATH ?>bootstrap3/css/bootstrap<?= $RTLFile ?>.css">
<!-- Optional theme -->
<link rel="stylesheet" type="text/css" href="<?php echo $EW_RELATIVE_PATH ?>bootstrap3/css/bootstrap-theme<?= $RTLFile ?>.css">
<link rel="stylesheet" type="text/css" href="<?php echo $EW_RELATIVE_PATH ?>bootstrap3/css/font-awesome.min.css">
<?php } ?>
<?php if (@$gsExport == "" || @$gsExport == "print") { ?>
<link rel="stylesheet" type="text/css" href="<?php echo $EW_RELATIVE_PATH ?>plugins/tagsinput/bootstrap-tagsinput.css">
<link rel="stylesheet" type="text/css" href="<?php echo $EW_RELATIVE_PATH ?>phpcss/jquery.fileupload.css">
<link rel="stylesheet" type="text/css" href="<?php echo $EW_RELATIVE_PATH ?>phpcss/jquery.fileupload-ui.css">
<link rel="stylesheet" type="text/css" href="<?php echo $EW_RELATIVE_PATH ?>colorbox/colorbox.css">
<link rel="stylesheet" type="text/css" href="<?php echo $EW_RELATIVE_PATH ?>phpcss/extra.css">
<link rel="stylesheet" type="text/css" href="<?php echo $EW_RELATIVE_PATH ?>phpcss/webfonts.css">
<?php } ?>
<?php if (@$gsExport == "" || @$gsExport == "print") { ?>
<?php if (ew_IsResponsiveLayout()) { ?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php } ?>
<link rel="stylesheet" type="text/css" href="<?php echo $EW_RELATIVE_PATH ?>phpcss/freepbx<?= $RTLFile ?>.css">
<?php if (@$gsCustomExport == "pdf" && EW_PDF_STYLESHEET_FILENAME <> "") { ?>
<link rel="stylesheet" type="text/css" href="<?php echo $EW_RELATIVE_PATH ?><?php echo EW_PDF_STYLESHEET_FILENAME ?>">
<?php } ?>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>jquery/jquery-1.12.0.min.js"></script>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>jquery/jquery.storageapi.min.js"></script>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>jquery/pStrength.jquery.js"></script>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>jquery/pGenerator.jquery.js"></script>
<?php } ?>
<?php if (@$gsExport == "" || @$gsExport == "print") { ?>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>bootstrap3/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>plugins/tagsinput/bootstrap-tagsinput.min.js"></script>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>plugins/debounce/debounce.min.js"></script>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>phpjs/typeahead.bundle.min.js"></script>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>colorbox/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>phpjs/mobile-detect.min.js"></script>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>phpjs/moment.min.js"></script>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>phpjs/extrafn.js"></script>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>html5lightbox/html5lightbox.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $EW_RELATIVE_PATH ?>calendar/calendar.css">
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>calendar/calendar.min.js"></script>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>calendar/calendar-setup.js"></script>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>phpjs/ewcalendar.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $EW_RELATIVE_PATH ?>mediaelement/build/mediaelementplayer-all.min.css">
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>mediaelement/build/mediaelement-and-player-all.min.js"></script>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>phpjs/ewmediaelement.js"></script>
<script type="text/javascript">
var EW_LANG_DIR = "<?php echo $Language->Dir ?>";
var EW_LANGUAGE_ID = "<?php echo $gsLanguage ?>";
var EW_DATE_SEPARATOR = "-"; // Default date separator
var EW_DEFAULT_DATE_FORMAT = "<?php echo EW_DEFAULT_DATE_FORMAT ?>"; // Default date format
var EW_DECIMAL_POINT = "<?php echo $DEFAULT_DECIMAL_POINT ?>";
var EW_THOUSANDS_SEP = "<?php echo $DEFAULT_THOUSANDS_SEP ?>";
var EW_MIN_PASSWORD_STRENGTH = 30;
var EW_GENERATE_PASSWORD_LENGTH = 7;
var EW_GENERATE_PASSWORD_UPPERCASE = true;
var EW_GENERATE_PASSWORD_LOWERCASE = true;
var EW_GENERATE_PASSWORD_NUMBER = true;
var EW_GENERATE_PASSWORD_SPECIALCHARS = false;
var EW_SESSION_TIMEOUT = <?php echo (EW_SESSION_TIMEOUT > 0) ? ew_SessionTimeoutTime() : 0 ?>; // Session timeout time (seconds)
var EW_SESSION_TIMEOUT_COUNTDOWN = <?php echo EW_SESSION_TIMEOUT_COUNTDOWN ?>; // Count down time to session timeout (seconds)
var EW_SESSION_KEEP_ALIVE_INTERVAL = <?php echo EW_SESSION_KEEP_ALIVE_INTERVAL ?>; // Keep alive interval (seconds)
var EW_RELATIVE_PATH = "<?php echo $EW_RELATIVE_PATH ?>"; // Relative path
var EW_SESSION_URL = EW_RELATIVE_PATH + "ewsession12.php"; // Session URL
var EW_IS_LOGGEDIN = <?php echo IsLoggedIn() ? "true" : "false" ?>; // Is logged in
var EW_IS_AUTOLOGIN = <?php echo IsAutoLogin() ? "true" : "false" ?>; // Is logged in with option "Auto login until I logout explicitly"
var EW_LOGOUT_URL = EW_RELATIVE_PATH + "logout.php"; // Logout URL
var EW_LOOKUP_FILE_NAME = "ewlookup12.php"; // Lookup file name
var EW_AUTO_SUGGEST_MAX_ENTRIES = <?php echo EW_AUTO_SUGGEST_MAX_ENTRIES ?>; // Auto-Suggest max entries
var EW_MAX_EMAIL_RECIPIENT = <?php echo EW_MAX_EMAIL_RECIPIENT ?>;
var EW_DISABLE_BUTTON_ON_SUBMIT = true;
var EW_IMAGE_FOLDER = "phpimages/"; // Image folder
var EW_UPLOAD_URL = "<?php echo EW_UPLOAD_URL ?>"; // Upload URL
var EW_UPLOAD_THUMBNAIL_WIDTH = <?php echo EW_UPLOAD_THUMBNAIL_WIDTH ?>; // Upload thumbnail width
var EW_UPLOAD_THUMBNAIL_HEIGHT = <?php echo EW_UPLOAD_THUMBNAIL_HEIGHT ?>; // Upload thumbnail height
var EW_THUMBNAIL_DEFAULT_WIDTH = <?php echo EW_THUMBNAIL_DEFAULT_WIDTH ?>; // Upload minimum width
var EW_THUMBNAIL_DEFAULT_HEIGHT = <?php echo EW_THUMBNAIL_DEFAULT_HEIGHT ?>; // Upload minimum height
var EW_ALLIMAGE_MINIMUM_WIDTH = <?php echo EW_ALLIMAGE_MINIMUM_WIDTH ?>; // Image minimum width
var EW_ALLIMAGE_MINIMUM_HEIGHT = <?php echo EW_ALLIMAGE_MINIMUM_HEIGHT ?>; // Image minimum height
var EW_MULTIPLE_UPLOAD_SEPARATOR = "<?php echo EW_MULTIPLE_UPLOAD_SEPARATOR ?>"; // Upload multiple separator
var EW_USE_COLORBOX = <?php echo (EW_USE_COLORBOX) ? "true" : "false" ?>;
var EW_USE_JAVASCRIPT_MESSAGE = false;
var EW_MOBILE_DETECT = new MobileDetect(window.navigator.userAgent);
var EW_IS_MOBILE = EW_MOBILE_DETECT.mobile() ? true : false;
var EW_PROJECT_STYLESHEET_FILENAME = "<?php echo EW_PROJECT_STYLESHEET_FILENAME ?>"; // Project style sheet
var EW_PDF_STYLESHEET_FILENAME = "<?php echo EW_PDF_STYLESHEET_FILENAME ?>"; // Pdf style sheet
var EW_TOKEN = "<?php echo @$gsToken ?>";
var EW_CSS_FLIP = <?php echo ($IsRTL) ? "true" : "false" ?>;
var EW_CONFIRM_CANCEL = true;
</script>
<?php } ?>
<?php if (@$gsExport == "" || @$gsExport == "print") { ?>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>phpjs/jsrender.min.js"></script>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>phpjs/ewp12.js"></script>
<?php } ?>
<?php if (@$gsExport == "" || @$gsExport == "print") { ?>
<script type="text/javascript">
var ewVar = <?php echo json_encode($EW_CLIENT_VAR); ?>;
<?php echo $Language->ToJSON() ?>
</script>
<script type="text/javascript" src="<?php echo $EW_RELATIVE_PATH ?>phpjs/userfn12.js"></script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="<?php echo ew_ConvertFullUrl("favicon.ico") ?>"><link rel="icon" type="image/vnd.microsoft.icon" href="<?php echo ew_ConvertFullUrl("favicon.ico") ?>">
</head>
<?php if (!IsLoggedIn() || @$gsExport) { ?>
<body<?= ($IsRTL) ? ' id="rtl"' : '' ?>>
<?php } else { ?>
<body<?= ($IsRTL) ? ' id="rtl"' : '' ?> onLoad="_body_onload()" onUnload="_body_onunload()">
<?php } ?>
<?php if (IsLoggedIn() && !@$gsExport) { ?>
<table width="100%" cellspacing="0" id="loaderContainer" border="0" cellpadding="0" onClick="return false;">
    <tr>
        <td id="loaderContainerWH">
            <div id="loader" style="z-index: 10;">
            <table width="100%" cellspacing="0" border="0" cellpadding="0">
                <tr>
                    <td>
                        <p>
                            <img id="loaderAnimation" style="background-image: url(images/loader/loading.gif);" src="images/loader/1x1.gif" width="20" height="20" alt="1x1.gif">
                            <strong>Please wait. Loading...</strong>
                        </p>
                    </td>
                </tr>
            </table>
            </div>
            <script type="text/javascript">_lanim_start(24, 20);</script>
        </td>
    </tr>
</table>
<?php } ?>
<?php if (@!$gbSkipHeaderFooter) { ?>
<?php if (@$gsExport == "") { ?>
<div class="ewLayout">
	<!-- header (begin) -->
	<div id="ewHeaderRow" class="<?php echo $gsHeaderRowClass ?>">
    	<div class="ewLogoRow">
        	<a href="./" target="_self">
    			<img src="<?php echo $EW_RELATIVE_PATH ?>phpimages/xpanel.png" alt="">
            </a>
        </div>
    	<div class="ewTodayRow">
    		<div style="padding: 8px 0 0;direction:ltr;"><?php echo (($gsLanguage=='fa') ? jdate("Y-m-d l") : date("Y-m-d l")); ?></div>
            <div style="padding: 2px 0 0;"><?php if ($Security->IsLoggedIn()=="login") { echo $Language->Phrase("UserName") . ": <strong>" . $Security->CurrentUserName() . "</strong>"; } ?></div>
        </div>
    	<div class="ewTitleRow">
    		<span><?php echo $Language->ProjectPhrase("BodyTitle") ?></span>
        </div>
    </div>
<?php if (ew_IsResponsiveLayout()) { ?>
<nav id="ewMobileMenu" role="navigation" class="navbar navbar-inverse visible-xs hidden-print">
	<div class="container-fluid"><!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button data-target="#ewMenu" data-toggle="collapse" class="navbar-toggle" type="button">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo (EW_MENUBAR_BRAND_HYPERLINK <> "") ? EW_MENUBAR_BRAND_HYPERLINK : "#" ?>"><?php echo (EW_MENUBAR_BRAND <> "") ? EW_MENUBAR_BRAND : $Language->ProjectPhrase("BodyTitle") ?></a>
		</div>
		<div id="ewMenu" class="collapse navbar-collapse" style="height: auto;"><!-- Begin Main Menu -->
<?php
	$RootMenu = new cMenu("MobileMenu");
	$RootMenu->MenuBarClassName = "";
	$RootMenu->MenuClassName = "nav navbar-nav";
	$RootMenu->SubMenuClassName = "dropdown-menu";
	$RootMenu->SubMenuDropdownImage = "";
	$RootMenu->SubMenuDropdownIconClassName = "icon-arrow-down";
	$RootMenu->MenuDividerClassName = "divider";
	$RootMenu->MenuItemClassName = "dropdown";
	$RootMenu->SubMenuItemClassName = "dropdown";
	$RootMenu->MenuActiveItemClassName = "active";
	$RootMenu->SubMenuActiveItemClassName = "active";
	$RootMenu->MenuRootGroupTitleAsSubMenu = TRUE;
	$RootMenu->MenuLinkDropdownClass = "ewDropdown";
	$RootMenu->MenuLinkClassName = "icon-arrow-right";
?>
<?php include_once "ewmobilemenu.php" ?>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>
<?php } ?>
	<!-- header (end) -->
	<div id="ewMenuRow"<?php if (ew_IsResponsiveLayout()) { ?> class="hidden-xs"<?php } ?>>
		<div class="ewMenu">
<?php include_once "ewmenu.php" ?>
		</div>
	</div>
	<!-- content (begin) -->
	<div id="ewContentTable" class="ewContentTable">
		<div id="ewContentRow">
			<div id="ewContentColumn" class="ewContentColumn">
				<!-- right column (begin) -->
				<h4 class="<?php echo $gsSiteTitleClass ?>"><?php echo $Language->ProjectPhrase("BodyTitle") ?></h4>
<?php } ?>
<?php } ?>
