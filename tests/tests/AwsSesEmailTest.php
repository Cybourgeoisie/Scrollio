<?php

declare(strict_types=1);

require "./data/email/RegistrationEmailAWSSES.php";

use PHPUnit\Framework\TestCase;

/**
 * @covers Scrollio\Email\AbstractAWSSESManager
 */
final class AwsSesEmailTest extends TestCase
{
	public function testAwsCredentialsSet()//: void
	{
		$access_key = getenv('AWS_ACCESS_KEY_ID');
		$secret_key = getenv('AWS_SECRET_ACCESS_KEY');

		$this->assertTrue(!empty($access_key));
		$this->assertTrue(!empty($secret_key));
		$this->assertTrue($access_key != $secret_key);
	}

	public function testSendEmail()
	{
		$reg_email = new \RegistrationEmailAWSSES(SCROLLIO_PHPUNIT_DEFAULT_EMAIL_RECIPIENT);

		// Make sure we have an instance
		$this->assertTrue(!empty($reg_email));

		// Try sending an email
		$result = $reg_email->sendRegistrationEmail();

		// Did it work?
		$this->assertTrue($result);
	}
}