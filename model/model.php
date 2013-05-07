<?php
class Model
{
	private $prefix = "";

	public $runtime = null;
	
	function __construct($isdb=true) {
		if ($isdb) {
			$this->db();
		}
		date_default_timezone_set("Asia/Shanghai");
	}

	function __get($name) {
		return $this->prefix.$name;
	}
	
	public function db() {
		$this->runtime = new PDO(
			"mysql:host=".DB_HOST.";dbname=".DB_NAME,
			DB_USER, DB_PASSWORD,
			array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '".DB_CHARSET."';")
		);

		$this->prefix = DB_PREFIX;
	}
}
?>
