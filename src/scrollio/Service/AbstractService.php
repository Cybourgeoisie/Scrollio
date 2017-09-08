<?php

namespace Scrollio\Service;

abstract class AbstractService implements ServiceInterface
{
	public function call($method, $request, $args)
	{
		$uncallable_methods = array('call');

		// Route the call as necessary
		if (!method_exists($this, $method) || in_array($method, $uncallable_methods))
		{
			throw new \Exception('Method (' . $method . ') does not exist');
		}

		// Analyze this method's parameters
		$reflection_method = new \ReflectionMethod($this, $method);
		$parameters = $reflection_method->getParameters();

		// Organize the method's parameters
		$pass_args = array();
		foreach ($parameters as $param) 
		{
			$param_name = $param->getName();

			// If found, then pass that shit
			if (isset($args[$param_name]))
			{
				$pass_args[] = $args[$param_name];
			}
			// If not optional, then bomb out
			else if (!$param->isOptional())
			{
				throw new \Exception('Required parameter (' . $param_name . ') is not provided');
			}
			// Else, use the default value
			else
			{
				$pass_args[] = $param->getDefaultValue();
			}
		}

		// Call the method
		return $reflection_method->invokeArgs($this, $pass_args);
	}
}
