<?php

namespace Scrollio\Api;

abstract class AbstractApi
{
	protected $endpoint_class = '';
	protected $endpoint_method = '';
	protected $http_method = '';

	protected $request = array();
	protected $args = array();

	protected $file = null;

	public function __construct($request)
	{
		// Set the default content type for this API
		header("Content-Type: application/json");

		// Get the arguments, class and method
		$this->request = explode('/', rtrim($request, '/'));
		$this->endpoint_class  = $this->request[0] ? array_shift($this->request) : null;
		$this->endpoint_method = $this->request[0] ? array_shift($this->request) : null;

		// Get the request method
		$this->http_method = $_SERVER['REQUEST_METHOD'];

		// Extend POST to be more specific
		if ($this->http_method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER))
		{
			if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE')
			{
				$this->http_method = 'DELETE';
			}
			else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT')
			{
				$this->http_method = 'PUT';
			}
			else
			{
				throw new \Exception("Unexpected Header");
			}
		}

		switch($this->http_method)
		{
			case 'DELETE':
			case 'POST':
				if (array_key_exists('CONTENT_TYPE', $_SERVER) &&
					strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
				{
					$raw_json   = file_get_contents("php://input");
					$this->args = $this->_parseInputs(json_decode($raw_json, true));
					$this->args = $this->_cleanInputs($this->args);
				}
				else
				{
					$this->args = $this->_parseInputs($_POST);
					$this->args = $this->_cleanInputs($this->args);
				}
				break;
			case 'GET':
				$this->args = $this->_parseInputs($_GET);
				$this->args = $this->_cleanInputs($this->args);
			  	break;
			case 'PUT':
				$this->args = $this->_parseInputs($_GET);
				$this->args = $this->_cleanInputs($this->args);
				$this->file = file_get_contents("php://input");
				break;
			default:
				$this->_response('Invalid Method', 405);
				break;
		}
	}

	public function processRequest()
	{
		// Require that the class belongs to the Service namespace
		$service_class = 'Scrollio\\Service\\' . $this->endpoint_class;

		// Call the service class
		if ($this->endpoint_class && $this->endpoint_method && class_exists($service_class))
		{
			$class = new \ReflectionClass($service_class);

			// Require that this method is a Service
			if ($class->implementsInterface('Scrollio\Service\ServiceInterface'))
			{
				// Get the method and prepare parameters
				$method = $class->getMethod('call');
				$params = array($this->endpoint_method, $this->request, $this->args);

				return $this->_response($method->invokeArgs(new $service_class(), $params));
			}
			else
			{
				throw new \Exception('Illegal Service Call');
			}
		}

		return $this->_response("No Endpoint - " . $this->endpoint_class . '::' . $this->endpoint_method, 404);
	}

	private function _response($data, $status = 200)
	{
		header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));

		// Format the response to a standard - include success and all data
		if (is_array($data))
		{
			if (array_key_exists('success', $data))
			{
				$response = $data;
			}
			else
			{
				$response = array_merge(array('success' => true), $data);
			}
		}
		else
		{
			if (is_bool($data))
			{
				$response = array('success' => $data);
			}
			// Require that all responses have some kind of data coming back
			else if (empty($data))
			{
				throw new \Exception('No response from method call');
			}
			else
			{
				$response = array('success' => true, 'data' => $data);
			}
		}

		return $response;
	}

	private function _parseInputs(array $data)
	{
		$parsed_input = array();
		if (is_array($data))
		{
			foreach ($data as $k => $v)
			{
				// If a key begins with "json_", automatically decode it
				if (strpos($k, 'json_') === 0 && strlen($k) > 5)
				{
					$k = substr($k, 5);
					$parsed_input[$k] = json_decode($v, true);
				}
				else
				{
					$parsed_input[$k] = $v;
				}
			}
		}

		return $parsed_input;
	}

	private function _cleanInputs($data)
	{
		$clean_input = array();
		if (is_array($data))
		{
			foreach ($data as $k => $v)
			{
				$clean_input[$k] = $this->_cleanInputs($v);
			}
		}
		else
		{
			// Not a big fan of this method, but I'll leave it for now
			$clean_input = trim(strip_tags($data));
			// Possible - htmlentities($data, ENT_COMPAT | ENT_HTML5 | ENT_QUOTES)
			// Except that the above messes with email addresses and any special character
		}

		return $clean_input;
	}

	private function _requestStatus($code)
	{
		$status = array(  
			200 => 'OK',
			404 => 'Not Found',   
			405 => 'Method Not Allowed',
			500 => 'Internal Server Error',
		); 

		return ($status[$code]) ? $status[$code] : $status[500];
	}
}
