<?php

// check if called from an allowed page
if (!defined('ESRC'))
{
	echo "Do not call the script direct!";
	exit ( 1 );
}

class Output {
	
	static function htmlEncodeString($string)
	{
		$strNotes = htmlspecialchars(str_replace("<br />", "\n", $string));
		$strNotes = str_replace("\n", "<br />", $strNotes);
		return $strNotes;
	}


	static function getEveDate($origdate)
	{
		$eveyear = intval(date("Y", strtotime($origdate)))-1898;
		$result = 'YC'. $eveyear .'-'. date("M-d", strtotime($origdate));
		
		return $result;
	}
	
	
	static function getEveDatetime($origdate)
	{
		$eveyear = intval(date("Y", strtotime($origdate)))-1898;
		$result = 'YC'. $eveyear .'-'. date("M-d H:i:s", strtotime($origdate));
		
		return $result;
	}
	
}

?>