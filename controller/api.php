<?php
class API
{
	function __construct() {
		spl_autoload_register(array($this, "model"));
	}

	private function model($classname) {
		Framework::loader(__FUNCTION__, $classname);
	}

	/**
	 * create JSON format string
	 * JSON_NUMERIC_CHECK is for number format converting
	 */
	public function toJSON($data) {
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	/**
	 * Class List for the developers
	 */
	public function index() {
		header("Content-Type: text/html");
		
		preg_match("/^\/(.+)\//U", $_SERVER['REQUEST_URI'], $matches);
		$path = $matches[0];
		
		echo "<h1>Class List</h1><ul>";
		foreach (glob(dirname(__FILE__)."/*".strtolower(Framework::CONTROLLER_SUFFIX).Framework::EXTENSION) as $class) {
			$class = preg_replace("/_.+/", '', basename($class));
			echo '<li><a href="'.$path.$class.'/intro">'.$class."</a></li>";
		}
		echo "</ul>";
	}
	
	/**
	 * Method List/Detail for the developers
	 */
	public function get_intro($funcname=null) {
		header("Content-Type: text/html");

		$classname = get_called_class();

		if (class_exists($classname)) {
			$class = new ReflectionClass($classname);
			
			$obj = $class->newInstanceArgs(array());
			$path = preg_replace("/([^\/])$/", "$1/", $_SERVER['REQUEST_URI']);
			if ($funcname && method_exists($obj, $funcname)) {
				$method = new ReflectionMethod($classname, $funcname);
				echo "<h1>Method Detail</h1><pre>\t";
				echo $method->getDocComment();
				echo "</pre>";
				echo '<a href="'.dirname($path).'">Back</a>';
			}
			else {
				$regx = "/^((get_)|(post_)|(put_)|(delete_))/";
				
				echo "<h1>Method List</h1><ul>";
				foreach ($class->getMethods() as $func) {
					if ($func->isPublic() && $func->name != __FUNCTION__ && preg_match($regx, $func->name)) {
						echo '<li><a href="'.$path.$func->name.'">'.preg_replace($regx, '', $func->name)."</a></li>";
					}
				}
				echo "</ul>";
				echo '<a href="'.dirname(dirname($path)).'">Back</a>';
			}
		}
	}
}
?>
