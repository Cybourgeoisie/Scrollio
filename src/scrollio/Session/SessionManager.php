<?php

namespace Scrollio\Session;

abstract class AbstractSessionManager
{
	public static function setData() {
		// TODO
	}

	public static function unsetData() {
		// TODO
	}

	public static function start()
	{
		// Initialize the session.
		session_start();

		// See if the user is logged in
		if ($_SESSION && !empty($_SESSION))
		{
			// Refresh the user credentials
			self::refresh();
		}
	}

	public static function login()
	{
		// Set this user
		self::setData();

		// Update the user's session ID - Database stuff
		//$user_obj->session = session_id();
		//$user_obj->save();

		self::setCookie(session_id(), 86400 * 14);

		session_write_close();
	}

	public static function refresh()
	{
		// Validate the user
		self::validateSession();

		// Set the data
		self::setData();

		// TODO Security - regenerate the session id

		self::setCookie(session_id(), 86400 * 14);

		session_write_close();
	}

	public static function logout()
	{
		// Initialize the session
		session_start();
		
		// Wipe data
		self::unsetData();

		// End the session
		self::destroy();
	}

	protected static function destroy()
	{
		// Unset all of the session variables.
		$_SESSION = array();

		// Reset the cookie
		self::setCookie('', -42000);

		// Finally, destroy the session.
		session_destroy();
	}

	protected static function setCookie(string $data, int $timeout)
	{
		$params = session_get_cookie_params();

		setcookie(session_name(),
			$data,
			time() + $timeout,
			$params["path"],
			$params["domain"],
			$params["secure"],
			$params["httponly"]
		);
	}
}
