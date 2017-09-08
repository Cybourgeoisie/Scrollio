<?php

namespace Scrollio\Api;

class RestApi extends AbstractApi
{
	public function __construct($request, $origin)
	{
		// Only allow requests from this server
		// Breaks on mobile
		if ($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR'])
		{
			//throw new Exception('External requests not supported');
		}

		parent::__construct($request);
	}
}
