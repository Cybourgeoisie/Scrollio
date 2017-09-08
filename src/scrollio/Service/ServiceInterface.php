<?php

namespace Scrollio\Service;

interface ServiceInterface
{
	public function call($method, $request, $args);
}
