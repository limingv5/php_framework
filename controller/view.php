<?php
class View
{
	private static $data = array();
	private static $agent;

	private static function init($data=array()) {
		header("Content-Type: text/html; charset=utf-8", false);

		self::$data  = array_merge(self::$data, $data);
		self::$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	}

	private static function isIE() {
		return  preg_match("/msie/", self::$agent);
	}

	private static function isIOS() {
		return preg_match("/(iphone|ipod|ipad).+(like mac os x)/", self::$agent);
	}

	private static function isIPhone() {
		return preg_match("/(iphone|ipod).+(like mac os x)/", self::$agent);
	}
	private static function isIPad() {
		return preg_match("/ipad.+(like mac os x)/", self::$agent);
	}

	private static function isIOS6plus() {
		return preg_match("/os (\d{2,}|[6-9]).+(like mac os x)/", self::$agent);
	}

	private static function isAndroid() {
		return preg_match("/android/", self::$agent);
	}

	private static function isWindowsPhone() {
		return preg_match("/windows phone os/", self::$agent);
	}

	private static function header($title, $post_title, $icon, $scalable=true) {
		if (!self::isIE()) {
			echo "<!DOCTYPE HTML>";
		}
		else {
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		}
		
		$dir = str_replace("index.php", '', $_SERVER['SCRIPT_NAME']);
		$base = "http://{$_SERVER['HTTP_HOST']}{$dir}view/";
		echo "<html><head><base href='".$base."' />";

		if (!self::isIE()) {
			echo '<meta charset="utf-8" />';
		}
		else {
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		}

		if (self::isIOS6plus()) {
			echo <<<TITLE
			<title>{$title}-{$post_title}</title>
			<meta name="apple-mobile-web-app-title" content="{$title}" />
TITLE;
		}
		else {
			if (self::isIOS()) {
				echo "<title>{$title}</title>";
			}
			else {
				echo "<title>{$title}-{$post_title}</title>";
			}
		}

		if (self::isIOS()) {
			echo <<<APPLE
			<meta name="apple-mobile-web-app-capable" content="yes" />
			<meta name="apple-mobile-web-app-status-bar-style" content="black" />
APPLE;
			if ($icon) {
				echo '<link rel="apple-touch-icon" href="'.$icon.'" />';
			}
		}

		if (self::isIOS() || self::isAndroid() || self::isWindowsPhone()) {
			$width = "device-width";
			$initscale = "1.0";
			$scalable = $scalable ? "yes" : "no";

			if (self::isIPhone()) {
				if (self::isIOS6plus()) {
					$width = "320.1";
					$initscale = "0.56";
				}
				else {
					$initscale = "0.47";
				}
			}

			echo <<<MOBILE
			<meta name="viewport" content="width={$width}, maximum-scale=3.0, minimum-scale={$initscale}, initial-scale={$initscale}, user-scalable={$scalable}" />
			<meta name="format-detection" content="telephone=no" />
			<meta name="format-detection" content="email=no" />
MOBILE;
		}
		echo <<<COMMON
			<link rel="stylesheet" href="css/common.css" />
		</head>
		<body>
COMMON;
	}

	private static function footer() {
		echo "</body></html>";
	}

	private static function output($html=array()) {
		extract(self::$data);

		foreach ($html as $part) {
			include dirname(dirname(__FILE__))."/view/{$part}.php";
		}
	}

	public static function render($html=array(), $obj=array(), $scalable=true) {
		self::init($obj);

		$title      = isset($obj['title']) ? $obj['title'] : "PHP";
		$post_title = isset($obj['post_title']) ? $obj['post_title'] : "Framework";
		$icon       = isset($obj['icon']) ? $obj['icon'] : null;
		self::header($title, $post_title, $icon, $scalable);
		
		self::output($html);
		self::footer();
	}

	public static function render_slice($html) {
		self::init();
		echo $html;
	}
}
?>
