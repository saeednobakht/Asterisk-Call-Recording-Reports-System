<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering

$EW_DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
$EW_RELATIVE_PATH = str_replace('extra', '', __DIR__);

?>
<?php include_once "../ewcfg12.php" ?>
<?php include_once "../phpfn12.php" ?>
<?php include_once "../userfn12.php" ?>
<?php include_once "../ckfinder/config.php" ?>
<?php 
	$sExtension = "";

if (!$_GET['imgExternal'] && $_FILES["upload"]) {

    $FileName = $_FILES["upload"]["name"];
    $FileType = $_FILES["upload"]["type"];
    $TempName = $_FILES["upload"]["tmp_name"];
    $FileSize = $_FILES["upload"]["size"];
    $Error    = $_FILES["upload"]["error"];

    if (!$TempName || $Error > 0)
    {
        echo "ERROR: Please browse for file before uploading\n";
		echo $Error;
        exit();
    } else {
		$sExtension = getimagesize($TempName);
		$sExtension = substr(strrchr($sExtension['mime'], "/"), 1);
		$_GET['imgExternal'] = $TempName;
	}
	
}

$IsAuthorized = false;
if (IsLoggedIn()) {
	$IsAuthorized = true;
}

if (!$sExtension) {
	$sExtension = "";
}

$Quality = 90;
	
if ($IsAuthorized && $_GET['imgExternal']) {
	
#	$imgExternal = html_entity_decode(urldecode($_GET['imgExternal']));
	if (strpos($sExtension,'?') || strpos($sExtension,'&')) {
		$imgExternal = $_GET['imgExternal'];
	} else {
	#	$imgExternal = html_entity_decode(urldecode($_GET['imgExternal']));
		$imgExternal = html_entity_decode($_GET['imgExternal']);
	}

	if (!$sExtension) {
		preg_match('/[^\/]\/[^\/]+\.[a-z]+/i', $imgExternal, $sExtension);
		$sExtension = $sExtension[0];
		$xExtension = pathinfo($sExtension, PATHINFO_EXTENSION);
	#	$xExtension = substr($sExtension, 0, strrpos($sExtension,'?'));
	#	if (!$xExtension) $xExtension = substr($sExtension, 0, strrpos($sExtension,'&'));
	#	if (!$xExtension) $xExtension = substr($sExtension, 0, strrpos($sExtension,'/'));
	#	if (!$xExtension) $xExtension = $sExtension;
	} else {
		$xExtension = $sExtension;
	}
#	echo $imgExternal; exit; //debug

	if ($xExtension);
		switch ($xExtension) {
			case "gif":
				$ImageX = @imagecreatefromgif($imgExternal);
				break;
			case "jpg":
				$ImageX = @imagecreatefromjpeg($imgExternal);
				break;
			case "jpeg":
				$ImageX = @imagecreatefromjpeg($imgExternal);
				break;
			case "png":
				$ImageX = @imagecreatefrompng($imgExternal);
				break;
			case "bmp":
				$ImageX = @imagecreatefromjpeg($imgExternal);
				break;
			case "webp":
				$ImageX = @imagecreatefromwebp($imgExternal);
				break;
			default:
				$ImageX = @imagecreatefromjpeg($imgExternal);
	}

#	if ($ImageX) {
	
		$MethodX = 'imagecreate';

		$FileName = ew_DefaultFileName($xExtension, TRUE);
			
		$uploadFolder = $_GET['uploadFolder'];

	#	$ImageFolder = '/images'.$uploadFolder;
	#	$FileFolder = '/userfiles'.$ImageFolder;

		$ImageFolder = 'images'.$uploadFolder;
		$FileFolder = $config['backends'][0]['baseUrl'].$ImageFolder;

	#	$NewFileName = ew_UniqueFilename($FileFolder, $FileName, FALSE, "sequence");

		$FilePathSave = $EW_DOCUMENT_ROOT . $FileFolder . $FileName;

		if (ew_CreateFolder($EW_DOCUMENT_ROOT.$FileFolder)) {
			
			if (!$ImageX)
			{
				$MethodX = 'imagestring';
				// Create a stream
				$Options = array(
				  'http'=>array(
					'method'=>"GET",
					'header'=>"Accept-language: en\r\n" .
							  "Cookie: foo=bar\r\n" .  // check function.stream-context-create on php.net
							  "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad 
				  )
				);
				$Context = stream_context_create($Options);
				$ImageX = @imagecreatefromstring(file_get_contents($imgExternal));
			}
			
			if (!$ImageX)
			{
				$MethodX = 'curl_exec';
				// Create a new cURL resource
				$ch = curl_init($imgExternal);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
				$raw = curl_exec($ch);
				curl_close($ch);
			#	if(file_exists($FilePathSave)){
			#		unlink($FilePathSave);
			#	}
				$fp = fopen($FilePathSave,'x');
				fwrite($fp, $raw);
				fclose($fp);
			}
			
			if ($ImageX)
			{
				imagejpeg($ImageX, $FilePathSave, $Quality);
			}
		}

		echo '{"fileName":"'.$FileName.'","uploaded":1,"url":"'.$FileFolder.$FileName.'","MethodX":"'.$MethodX.'"}';
#	}
}
exit;

