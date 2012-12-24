<?php
class API
{
	function __construct() {
		spl_autoload_register(array($this, "model"));
	}

	private function model($classname) {
		Framework::loader(__FUNCTION__, $classname);
	}

	public function index() {
		$this->toJSON(array("API" => "Base Class"));
	}

	public function toJSON($data) {
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}
}
?>