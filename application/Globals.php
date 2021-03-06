<?php

	// Set this to true to show debugging
	define("DEBUG", true);


	// You can change this to let your files
	// stays protected.
	define("KEY_SALT", 'aghtUJ6y21klnQ83Bj23B');

	date_default_timezone_set("America/Antigua");

	/**
	 * [convert_to_string return value to string]
	 * @param  [type] &$value [description]
	 * @return [type]         [description]
	 * @author Pravin S  <solanki7492@gmail.com>
	 */
	function convert_to_string(&$value) {
		$value = (string)$value;
	} 

?>