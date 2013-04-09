<?php
class Test_API extends API
{
	function __construct() {
		parent::__construct();
	}

	public function get_index() {
		View::render(array("test"), array("title"=>"test"));
	}
}
?>
