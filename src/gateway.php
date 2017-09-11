<?php

/**
 * Program Gateway - kickstart the program
 */

// Certain definitions are required to work with Scrollio
if (!defined('SCROLLIO_WEBSITE_ORIGINS') && !defined('SCROLLIO_WEBSITE_ORIGINS_ACCEPT_ALL'))
{
	throw new \Exception('Scrollio requires website origins (SCROLLIO_WEBSITE_ORIGINS) to be set, or for all origins to be accepted (SCROLLIO_WEBSITE_ORIGINS_ACCEPT_ALL).');
}
else
{
	if (SCROLLIO_WEBSITE_ORIGINS_ACCEPT_ALL === true) {
		header("Access-Control-Allow-Orgin: *");
		header("Access-Control-Allow-Methods: *");
	} else {
		// Add each website to the acceptable origins
		$websites = explode(',', SCROLLIO_WEBSITE_ORIGINS);

		foreach ($website in $websites) {
			if (filter_var($website, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED) === true) {
				header("Access-Control-Allow-Orgin: " . $website, false);
				header("Access-Control-Allow-Methods: " . $website, false);
			} else {
				throw new \Exception('Scrollio: Invalid website origin, ' . $website);
			}
		}
	}
}

// Autoloader
require_once("../vendor/autoload.php");
