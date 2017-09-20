<?php

// Set defaults
// You must set these to run the tests
define('SCROLLIO_WEBSITE_ORIGINS_ACCEPT_ALL', true);
define('SCROLLIO_PHPUNIT_DEFAULT_EMAIL_SENDER', null);
define('SCROLLIO_PHPUNIT_DEFAULT_EMAIL_RECIPIENT', null);

// Check that the definitions are set
if (!SCROLLIO_PHPUNIT_DEFAULT_EMAIL_SENDER || !SCROLLIO_PHPUNIT_DEFAULT_EMAIL_RECIPIENT)
{
	die("Required definitions are not set for running Scrollio tests. Update /tests/bootstrap.php to set them.\r\n");
}

// Enter through the gateway in order to run our tests
require_once('../src/gateway.php');
