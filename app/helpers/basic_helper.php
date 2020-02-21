<?php if (!defined('BASEPATH'))
  exit('No direct script access allowed');

  function debug_($array) {
		echo "<pre>";
		print_r($array);
		die();
	}