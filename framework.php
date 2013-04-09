<?php
class Framework
{
	private static $instance;

	const DEFAULT_CONTROLLER = "api";
	const DEFAULT_METHOD     = "index";
	const CONTROLLER_SUFFIX  = "API";
	const EXTENSION          = ".php";

	function __construct() {
		include dirname(__FILE__)."/config.php";
		spl_autoload_register(array($this, "controller"));
	}

	private function controller($classname) {
		self::loader(__FUNCTION__, $classname);
	}

	public static function loader($dir, $classname) {
		set_include_path(dirname(__FILE__).DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR);
		spl_autoload_extensions(self::EXTENSION);
		spl_autoload($classname);
	}

	public static function init() {
		if (self::$instance == NULL) {
			self::$instance = new self();
		}
		
		self::$instance->checkHTTPMethod();

		if (!empty($_GET)) {
			$_GET = self::$instance->tideInputs($_GET);
		}
		if (!empty($_POST)) {
			$_POST = self::$instance->tideInputs($_POST);
		}
		
		$_SERVER['PATH_INFO'] = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
		$path = preg_replace("/^\/|\/$/", '', $_SERVER['PATH_INFO']);
		$arr  = explode('/', $path);
		if (isset($arr[0]) && $arr[0]) {
			$classname = ucfirst($arr[0]).'_'.self::CONTROLLER_SUFFIX;
			unset($arr[0]);
		}
		else {
			$classname = self::DEFAULT_CONTROLLER.(strtolower(self::DEFAULT_CONTROLLER) != strtolower(self::CONTROLLER_SUFFIX) ? '_'.self::CONTROLLER_SUFFIX : '');
		}

		if (isset($arr[1]) && $arr[1]) {
			$req = strtolower($_SERVER['REQUEST_METHOD']);
			$req = ($req == "head") ? "get" : $req;
			$funcname  = $req.'_'.$arr[1];
			unset($arr[1]);
		}
		else {
			$funcname  =  self::DEFAULT_METHOD;
		}

		$args = self::$instance->tideInputs(array_values($arr));
		self::$instance->invoke($classname, $funcname, $args);
	}

	private function invoke($classname, $funcname, $args=array()) {
		if (class_exists($classname)) {
			$class = new ReflectionClass($classname);
			
			$methodArg      = $args;
			/* If Constructor wants Parameters:
			$constructorArg = array();
			$c = $class->getConstructor();
			$n = $c->getNumberOfRequiredParameters();
			if ($n>0) {
				$arr = array_chunk($args, $n, false);
				$constructorArg = $arr[0];
				$methodArg = isset($arr[1]) ? $arr[1] : array();
			}
			else {
				$constructorArg = array();
				$methodArg = $args;
			}
			*/
			
			$obj = $class->newInstanceArgs($constructorArg);
			if (method_exists($obj, $funcname)) {
				self::$instance->setHeaders(200);
				if (strtolower($_SERVER['REQUEST_METHOD']) == "head") {
					exit;
				}
				
				$method = new ReflectionMethod($classname, $funcname);
				if (count($methodArg) >= $method->getNumberOfRequiredParameters()) {
					$method->invokeArgs($obj, $methodArg);
				}
				else {
					self::$instance->setHeaders(400);
				}
			}
			else {
				self::$instance->setHeaders(404);
			}
		}
		else {
			self::$instance->setHeaders(404);
		}
	}

	private function checkHTTPMethod() {
		if (!in_array($_SERVER['REQUEST_METHOD'], array("GET", "POST", "PUT", "DELETE"))) {
			self::$instance->setHeaders(406);
		}
	}

	private function clean($data) {
		$clean_input = array();
		if (is_array($data)) {
			foreach ($data as $k => $v) {
				$clean_input[$k] = self::$instance->clean($v);
			}
		}
		else {
			if (get_magic_quotes_gpc()) {
				$data = trim(stripslashes($data));
			}
			$data = strip_tags($data);
			$clean_input = trim($data);
		}
		return $clean_input;
	}

	private function tideInputs($arr) {
		switch($_SERVER['REQUEST_METHOD']){
			case "POST":
			case "GET":
			case "DELETE":
				$arr = self::$instance->clean($arr);
				break;
			case "PUT":
				parse_str(file_get_contents("php://input"), $arr);
				$arr = self::$instance->clean($arr);
				break;
		}
		return $arr;
	}

	private function setHeaders($code=500) {
		$code = intval($code);
		header("HTTP/1.1 {$code}", false, $code);
		if ($code >= 400) {
			exit;
		}
	}
}
?>
