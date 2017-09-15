<?php

namespace Email;

class Manager
{
	protected $mail;
	protected $template_variables = array();

	function __construct(string $to = null)
	{
		// Prepare our mail
		$this->mail = new \PHPMailer\PHPMailer\PHPMailer(true);

		// Grab the default sending email address
		if (defined('DEFAULT_EMAIL') && DEFAULT_EMAIL)
		{
			$this->setSender(DEFAULT_EMAIL);
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

		$this->mail->setFrom(DEFAULT_EMAIL, $from_name);
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

	private function constructHtmlEmail(string $body)
	{
		// Prepare the template variables
		$this->template_variables = array_merge(array(
			'SITE_NAME'          => SITE_NAME,
			'SITE_ADDRESS'       => SITE_ADDRESS,
			'SITE_ADDRESS_CLEAN' => SITE_ADDRESS_CLEAN,
			'EMAIL_CONTENT'      => $body
		), $this->template_variables);

		// Get the template shell
		$template = file_get_contents(CORE_PATH . 'mail/email-template.html');

		// Replace all of the variables
		foreach ($this->template_variables as $tpl_key => $value)
		{
			$template = str_replace('{{' . $tpl_key . '}}', $value, $template);
		}

		return $template;
	}

	public function setBody(string $body)
	{
		// Wrap the email in our template
		$html_body = $this->constructHtmlEmail($body);

		// HTML email
		$mail->Body = $html_body;

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
	}
}
