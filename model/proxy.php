<?php
class Proxy extends Model
{
  private $curl;
	
	function __construct() {
		parent::__construct();
		
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_TIMEOUT, 5);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_HEADER, false);
	}
	
	public function restReq($url, $data=array(), $method="GET") {
		curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($this->curl, CURLOPT_URL, $url);
		if ($data) {
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($data));
		}
		return curl_exec($this->curl);
	}
	
	function __destruct() {
		curl_close($this->curl);
	}
}
?>
