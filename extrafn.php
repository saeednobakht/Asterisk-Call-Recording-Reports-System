<?php

// Fix alias uri text for seo url
function xAlias($xTitle) {
	if ( $xTitle ) {
		$iAlias = preg_replace("/([A-Z]+)/e", "strtolower('\\1')", $xTitle);
		$iSymbol = array("!", "?", "ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â¹Ãƒâ€¦Ã¢â‚¬Å“ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¦ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¸", "'", "`", ".", "ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â¹Ãƒâ€¦Ã¢â‚¬Å“ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¦ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢", ",", "ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â«", "ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â»", "&", ":", ";", "-", "+", "=", "ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¾Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬", "#", "%", "~", "$", "^", "*", "@", "|", "(", ")", "[", "]", "{", "}", "/", "\"");
		$iAlias = str_replace($iSymbol, " ", $iAlias);
	  $iAlias = trim($iAlias);
	  while (strpos($iAlias, "  ") !== FALSE)
		  $iAlias = str_replace("  ", " ", $iAlias);
	  $iAlias = str_replace(" ", "-", $iAlias);
	}
	return $iAlias;
}

// Remove numbers from a string
function RemoveNumberOfText($iString) {
	$iVowels = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
	$iString = str_replace($iVowels, '', $iString);
	return $iString;
}

// Function to show embed image file with any format : swf, jpg, gif, bmp... 
// Call iViewEmbedObject in example: iViewEmbedObject(EW_UPLOAD_DEST_PATH, x_field_name, 468, 60, False)
function iViewEmbedObject($iFilePath, $iFileName, $iWidth, $iHeight, $isResize = TRUE) {
    $iViewEmbed = "\n\t";
	$iPathParts = pathinfo($iFileName);
	$iFileExt = $iPathParts['extension'];
    if ( !$iFilePath ) $iFilePath = EW_UPLOAD_DEST_PATH;
    if ( $iFileExt ) {
      if ( $iFileExt != "swf" ) {
        if ( $isResize ) {
          $iViewEmbed = $iViewEmbed . "<img border=\"0\" src=\"ewbv11.php?fn=" . urlencode($iFilePath . $iFileName) . "&width=" . $iWidth . "&height=" . $iHeight . "\"" . (($iWidth) ? " width=\"" . $iWidth . "\"" : "") . (($iHeight) ? " height=\"" . $iHeight . "\"" : ""). ">" . "\n";
	    } else {
          $iViewEmbed = $iViewEmbed . "<img border=\"0\" src=\"" . $iFilePath . $iFileName . "\"" . (($iWidth) ? " width=\"" . $iWidth . "\"" : "") . (($iHeight) ? " height=\"" . $iHeight . "\"" : "") . ">" . "\n";
		}
	  } else {
          $iViewEmbed = $iViewEmbed . "<object id=\"obj1\" classid=\"clsid:D27CDB6E-AE6D-11CF-96B8-444553540000\"" . (($iWidth) ? " width=\"" . $iWidth . "\"" : "") . (($iHeight) ? " height=\"" . $iHeight . "\"" : "") . ">" . "\n" . "\t";
          $iViewEmbed = $iViewEmbed . "<param name=\"movie\" value=\"" . $iFilePath . $iFileName . "\">" . "\n" . "\t";
          $iViewEmbed = $iViewEmbed . "<param name=\"quality\" value=\"high\">" . "\n" . "\t";
          $iViewEmbed = $iViewEmbed . "<embed name=\"obj1\" src=\"" . $iFilePath . $iFileName . "\" type=\"application/x-shockwave-flash\"" . (($iWidth) ? " width=\"" . $iWidth : "") . "\"" . (($iHeight) ? " height=\"" . $iHeight . "\"" : "") . " quality=\"high\"></object>" . "\n";
	  }
	}
    return $iViewEmbed;
}

// Convert seconds time to long time format : 00:00:00
function iSec2FullTime($iTime) {
  if ($iTime && is_numeric($iTime)) {
    $iSeconds = $iTime % 60;
    $iTime = ($iTime - $iSeconds) / 60;
    $iMinutes = $iTime % 60;
    $iHours = ($iTime - $iMinutes) / 60;
    $iSeconds = str_pad($iSeconds, 2, "0", STR_PAD_LEFT);
    $iMinutes = str_pad($iMinutes, 2, "0", STR_PAD_LEFT);
    $iHours = str_pad($iHours, 2, "0", STR_PAD_LEFT);
    $iFullTime = "{$iHours}:{$iMinutes}:{$iSeconds}";
    return $iFullTime;
  }
}

function duration2sec($strTime)
{
	$strTime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $strTime);
	sscanf($strTime, "%d:%d:%d", $hours, $minutes, $seconds);
	$timeSeconds = $hours * 3600 + $minutes * 60 + $seconds;
	echo $timeSeconds;
}

// Format bytes with PHP ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œ B, KB, MB, GB, TB, PB, EB, ZB, YB converter
// Function takes three parameter: (bytes mandatory, unit optional, decimals optional)
function iFormatByte($bytes, $unit = "", $decimals = 2) {
	$units = array('B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4, 
			'PB' => 5, 'EB' => 6, 'ZB' => 7, 'YB' => 8);
	$value = 0;
	if ($bytes > 0) {

		// Generate automatic prefix by bytes 
		// If wrong prefix given

		if (!array_key_exists($unit, $units)) {
			$pow = floor(log($bytes)/log(1024));
			$unit = array_search($pow, $units);
		}

		// Calculate byte value by prefix
		$value = ($bytes/pow(1024,floor($units[$unit])));
	}

	// If decimals is not numeric or decimals is less than 0 
	// then set default value

	if (!is_numeric($decimals) || $decimals < 0) {
		$decimals = 2;
	}

	// Format output
	return sprintf('%.' . $decimals . 'f '.$unit, $value);
}

function zeroFill($number, $width = 5) {
	return str_pad($number, $width, '0', STR_PAD_LEFT);
}

function Shamsi2Miladi($HijriShamsiDate) {
	$DateTime = explode(' ', $HijriShamsiDate);
	$HijriShamsi = explode('-', $DateTime[0]);
	if (!$HijriShamsi[0])
		$HijriShamsi = explode('/', $DateTime[0]);
	if (!$HijriShamsi[0])
		$HijriShamsi = explode('.', $DateTime[0]);
	$Gregorian = jalali_to_gregorian($HijriShamsi[0], $HijriShamsi[1], $HijriShamsi[2]);
	$Gregorian = $Gregorian[0].'-'.$Gregorian[1].'-'.$Gregorian[2];
	if (explode(':', $DateTime[1])) {
		$MiladiDateTime = $Gregorian.' '.$DateTime[1];
	}
	return $MiladiDateTime;
}

function Miladi2Shamsi($MiladiDateTime) {
	$ShamsiDateTime = jdate('Y-m-d H:i:s', strtotime($MiladiDate), '', '', 'en');
	return $ShamsiDateTime;
}
# php delete function that deals with directories recursively

function rrmdir($dir) {
	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (filetype($dir."/".$object) == "dir") 
					rrmdir($dir."/".$object); 
				else 
					unlink($dir."/".$object);
			}
		}
		reset($objects);
		rmdir($dir);
	}
}

	function fixTextUrl($linkText) {
		if ( $linkText ) {
			$iLinkText = $linkText;
			$iChars = array("#","!", "?", "Ã˜Å¸", "'", ".", "Ã˜Å’", ",", "Ã‚Â«", "Ã‚Â»", "(", ")", "&", ":", ";", "Ã˜â€º", "-", "_", "Ã™â‚¬", "/", "\"", "\\", "%", "+");
			$iLinkText = str_replace($iChars, '', $iLinkText);
			$iLinkText = trim($iLinkText);
			while (strpos($iLinkText, "  ") !== FALSE)
				$iLinkText = str_replace("  ", " ", $iLinkText);
			$iLinkText = str_replace(" ", "-", $iLinkText);
			$iLinkText = urlencode($iLinkText);
			$iLinkText = mb_strtoupper($iLinkText, 'UTF-8');
		}
	#	return $iLinkText;
		return null;
	}

	function fixUrlText($linkText) {
		if ( $linkText ) {
			$iLinkText = $linkText;
			$iChars = array("#","!", "?", "Ã˜Å¸", "'", ".", "Ã˜Å’", ",", "Ã‚Â«", "Ã‚Â»", "(", ")", "&", ":", ";", "Ã˜â€º", "-", "_", "Ã™â‚¬", "/", "\"", "\\", "%", "+");
			$iLinkText = str_replace($iChars, '', $iLinkText);
			$iLinkText = trim($iLinkText);
			while (strpos($iLinkText, "  ") !== FALSE)
				$iLinkText = str_replace("  ", " ", $iLinkText);

		//	$iLinkText = str_replace(" ", "-", $iLinkText);
			$iLinkText = urlencode($iLinkText);

		//	$iLinkText = str_replace(" ", "+", $iLinkText);
			$iLinkText = mb_strtoupper($iLinkText, 'UTF-8');
		}
		return $iLinkText;
	}

	function fixUrlFull($linkText) {
		if ( $linkText ) {
			$linkText = urldecode($linkText);
			$linkText = str_replace(" ", "+", $linkText);

		//	$linkText = mb_strtoupper($linkText, 'UTF-8');
		}
		return $linkText;
	}

	function fixTagText($tagText) {
		if ( $tagText ) {
			$iTagText = $tagText;
			$iChars = array("#","!", "?", "Ã˜Å¸", "'", ".", "Ã˜Å’", ",", "Ã‚Â«", "Ã‚Â»", "(", ")", "&", ":", ";", "Ã˜â€º", "-", "_", "Ã™â‚¬", "/", "\"", "\\", "%", "+");
			$iTagText = str_replace($iChars, '', $iTagText);
			$iTagText = trim($iTagText);
		}
		return $iTagText;
	}

function substrfa($str, $length = FALSE, $offset = 0) {
	mb_internal_encoding('UTF-8');
    if ( $length === FALSE ) {
        return mb_substr($str, $offset);
    } else {
        return mb_substr($str, $offset, $length);
    }
}

function substr_more($str, $length = FALSE, $offset = 0) {
	mb_internal_encoding('UTF-8');
    if ( $length === FALSE ) {
        return mb_substr($str, $offset);
    } else {
        return mb_substr($str, $offset, $length).'...';
    }
}

function substrfa_more($strText, $strLength, $strStart=0) {
	mb_internal_encoding('UTF-8');
	if ($strText) {
		if (is_numeric($strLength) && ($strLength > 0)) {
			if ( mb_strlen($strText) > $strLength ) {
				$fixLength = mb_strpos($strText, " ", $strLength);
				if (!$fixLength) { $fixLength = mb_strlen($strText); }
				$strText = mb_substr($strText, $strStart, $fixLength).'...';
			}
		}
	}
	return $strText;
}

/**
* Reads the requested portion of a file and sends its contents to the client with the appropriate headers.
* 
* This HTTP_RANGE compatible read file function is necessary for allowing streaming media to be skipped around in.
* 
* @param string $location
* @param string $filename
* @param string $mimeType
* @return void
* 
* @link https://groups.google.com/d/msg/jplayer/nSM2UmnSKKA/Hu76jDZS4xcJ
* @link http://php.net/manual/en/function.readfile.php#86244
*/

function smartReadFile($location, $filename, $mimeType = 'application/octet-stream')
{
	if (!file_exists($location))
	{
		header ("HTTP/1.1 404 Not Found");
		return;
	}
	$size	= filesize($location);
	$time	= date('r', filemtime($location));
	$fm		= @fopen($location, 'rb');
	if (!$fm)
	{
		header ("HTTP/1.1 505 Internal server error");
		return;
	}
	$begin	= 0;
	$end	= $size - 1;
	if (isset($_SERVER['HTTP_RANGE']))
	{
		if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches))
		{
			$begin	= intval($matches[1]);
			if (!empty($matches[2]))
			{
				$end	= intval($matches[2]);
			}
		}
	}
	if (isset($_SERVER['HTTP_RANGE']))
	{
		header('HTTP/1.1 206 Partial Content');
	}
	else
	{
		header('HTTP/1.1 200 OK');
	}
	header("Content-Type: $mimeType"); 
	header('Cache-Control: public, must-revalidate, max-age=0');
	header('Pragma: no-cache');  
	header('Accept-Ranges: bytes');
	header('Content-Length:' . (($end - $begin) + 1));
	if (isset($_SERVER['HTTP_RANGE']))
	{
		header("Content-Range: bytes $begin-$end/$size");
	}
	header("Content-Disposition: inline; filename=$filename");
	header("Content-Transfer-Encoding: binary");
	header("Last-Modified: $time");
	$cur	= $begin;
	fseek($fm, $begin, 0);
	while(!feof($fm) && $cur <= $end && (connection_status() == 0))
	{
		print fread($fm, min(1024 * 16, ($end - $cur) + 1));
		$cur += 1024 * 16;
	}
}
?>
