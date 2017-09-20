<?php

namespace Scrollio\Email;

abstract class AbstractAWSSESManager extends AbstractManager
{
	protected $msg = array();
	protected $client = null;

	protected $default_email = '';
	protected $default_sender_name = '';

	function __construct(string $to = null)
	{
		$this->client = \Aws\Ses\SesClient::factory(array(
			'version' => 'latest',
			'region'  => 'us-east-1'
		));

		if (!$this->client) {
			throw new \Exception('AWS SES Client not started. Are your configurations set?');
		}

		// Now that you have the client ready, you can build the message 
		$this->msg = array();

		// Grab the default sending email address
		if (isset($this->default_email) && !empty($this->default_email))
		{
			if (isset($this->default_sender_name) && !empty($this->default_sender_name))
			{
				$this->setSender($this->default_email, $this->default_sender_name);
			}
			else
			{
				$this->setSender($this->default_email);
			}
		}

		// If there is an address passed in, set it
		if ($to)
		{
			$this->setRecipient($to);
		}
	}

	public function setRecipient(string $to)
	{
		if (!filter_var($to, FILTER_VALIDATE_EMAIL))
		{
			throw new \Exception('Invalid email address as recipient');
		}

		if (array_key_exists('Destination', $this->msg) && array_key_exists('ToAddresses', $this->msg['Destination']))
		{
			$this->msg['Destination']['ToAddresses'][] = $to;
		}
		else
		{
			$this->msg['Destination'] = array(
				'ToAddresses' => array($to)
			);
		}
	}

	public function setSender(string $from, string $from_name = 'Administrator')
	{
		if (!filter_var($from, FILTER_VALIDATE_EMAIL))
		{
			throw new \Exception('Invalid email address as sender');
		}

		$this->msg['Source'] = $from_name . '<' . $from . '>';
	}

	public function setSubject(string $subject)
	{
		$this->msg['Message']['Subject']['Data']    = $subject;
		$this->msg['Message']['Subject']['Charset'] = "UTF-8";
	}

	public function setBody(string $body)
	{
		if (isset($this->template_location) && !empty($this->template_location))
		{
			// Wrap the email in our template
			$html_body = $this->constructHtmlEmail($body);

			// HTML email
			$this->msg['Message']['Body']['Html']['Data']    = $html_body;
			$this->msg['Message']['Body']['Html']['Charset'] = "UTF-8";
		}
		else
		{
			// Just use straight text
			$this->msg['Message']['Body']['Html']['Data']    = $body;
			$this->msg['Message']['Body']['Html']['Charset'] = "UTF-8";
		}

		// Straight text
		$this->msg['Message']['Body']['Text']['Data']    = $body;
		$this->msg['Message']['Body']['Text']['Charset'] = "UTF-8";
	}

	public function send()
	{
		if (!$this->client->sendEmail($this->msg))
		{
			throw new \Exception('Could not send email.');
		}

		return true;
	}
}
