<?php
class Model
{
	private $prefix = "";

	public $runtime = null;
	
	function __construct($isdb=true) {
		if ($isdb) {
			include_once dirname(__FILE__)."/../config.php";

			$this->runtime = new PDO(
				"mysql:host=".DB_HOST.";dbname=".DB_NAME,
				DB_USER, DB_PASSWORD,
				array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '".DB_CHARSET."';")
			);

			$this->prefix = DB_PREFIX;
		}
	}

	function __get($name) {
		return $this->prefix.$name;
	}
}
?>