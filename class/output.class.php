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


	/**
	 * Prepare display of options for a select list
	 * @param string $post_value The value that will be posted at form submit
	 * @param string $displaytext The text to display in the select list to the user
	 * @param string $selected_value The item to select by default; default to none
	 * @return array
	 */
	static function prepSelectListOption($post_value, $displaytext, $selected_value = '')
	{
		$str = '<option value="'.$post_value.'"';
		if ($post_value == $selected_value) { $str .= ' selected="selected"'; }
		$str .= '>'.$displaytext.'</option>'."\n";
	
		return $str;
	}


	static function prepTextarea($note)
	{
		$strclean = $note;
		$strclean = trim($strclean);
		$strclean = stripslashes($strclean);
		$strclean = htmlspecialchars_decode($strclean);
	
		return $strclean;
	}
	
}

?>