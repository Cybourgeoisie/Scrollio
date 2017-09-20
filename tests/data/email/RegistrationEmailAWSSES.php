<?php

declare(strict_types=1);

class RegistrationEmailAWSSES extends \Scrollio\Email\AbstractAWSSESManager
{
	protected $default_email = SCROLLIO_PHPUNIT_DEFAULT_EMAIL_SENDER;

	public function sendRegistrationEmail()
	{
		// Construct the subject and body
		$this->setTemplateLocation(dirname(realpath(__FILE__)) . '/template.html');
		$this->setTemplateKey('EMAIL_TITLE', 'New Account');
		$this->setSubject($this->getRegistrationSubject());
		$this->setBody($this->getRegistrationBody());

		// Send!
		return $this->send();
	}

	private function getRegistrationSubject()
	{
		return 'Welcome to Scrollio!';
	}

	private function getRegistrationBody()
	{
		return '<p>Hello!</p><p>Welcome to Scrollio.</p><p>Thank you for joining, and we hope you enjoy using our framework.</p>';
	}
}
