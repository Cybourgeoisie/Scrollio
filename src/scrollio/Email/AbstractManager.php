<?php

namespace Scrollio\Email;

abstract class AbstractManager
{
	protected $mail;

	protected $template_location = '';
	protected $template_variables = array();

	protected $default_email = '';

	function __construct(string $to = null)
	{
		// Prepare our mail
		$this->mail = new \PHPMailer\PHPMailer\PHPMailer(true);

		// Grab the default sending email address
		if (isset($this->default_email) && !empty($this->default_email))
		{
			$this->setSender($this->default_email);
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

		$this->mail->addAddress($to);
	}

	public function setSender(string $from, string $from_name = 'Administrator')
	{
		if (!filter_var($from, FILTER_VALIDATE_EMAIL))
		{
			throw new \Exception('Invalid email address as sender');
		}

		$this->mail->setFrom($from, $from_name);
	}

	public function setSubject(string $subject)
	{
		$this->mail->Subject = $subject;
	}

	public function setTemplateKey(string $key, string $value)
	{
		$this->template_variables[$key] = $value;
	}

	public function unsetTemplateKey(string $key)
	{
		if (array_key_exists($key, $this->template_variables))
		{
			unset($this->template_variables[$key]);
		}
	}

	public function setTemplateLocation($template_location)
	{
		// Validate that the location exists
		if (!file_exists($template_location))
		{
			throw new \Exception('Template location (' . $template_location . ') not found.');
		}

		$this->template_location = $template_location;
	}

	protected function constructHtmlEmail(string $body)
	{
		// Ensure that we have a template to work with
		if (!isset($this->template_location) || empty($this->template_location))
		{
			throw new \Exception('Template location not set - can\'t construct HTML email.');
		}

		// Prepare the template variables
		$this->template_variables = array_merge(array(
			'EMAIL_CONTENT' => $body
		), $this->template_variables);

		// Get the template shell
		$template = file_get_contents($this->template_location);

		// Replace all of the variables
		foreach ($this->template_variables as $tpl_key => $value)
		{
			$template = str_replace('{{' . $tpl_key . '}}', $value, $template);
		}

		return $template;
	}

	public function setBody(string $body)
	{
		if (isset($this->template_location) && !empty($this->template_location))
		{
			// Wrap the email in our template
			$html_body = $this->constructHtmlEmail($body);

			// HTML email
			$mail->Body = $html_body;
		}
		else
		{
			// Just use straight text
			$mail->Body = $body;
		}

		// Straight text
		$mail->AltBody = $body;
	}

	public function send()
	{
		try
		{
			$this->mail->send();
		}
		catch (\PHPMailer\PHPMailer\Exception $ex)
		{
			throw new \Exception('Could not send email: ' . $this->mail->ErrorInfo);
		}

		return true;
	}
}
