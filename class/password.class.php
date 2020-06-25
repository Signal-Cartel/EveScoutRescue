<?php

// check if called from an allowed page
if (!defined('ESRC'))
{
	echo "Do not call the script direct!";
	exit ( 1 );
}

class Password {
	
	static function generatePassword($length = 10)
	{
		// lowercase letters only for accessibility reasons (web readers for blind users)
		// no numeral "1" or lowercase "L" as they are easy to confuse
		$characters = '023456789abcdefghijkmnopqrstuvwxyz';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}

?>