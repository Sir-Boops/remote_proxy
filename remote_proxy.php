<?php

if ($_GET['proxyImg']) {

	$URL = $_GET['proxyImg'];

	if (filter_var($URL, FILTER_VALIDATE_URL)) {

		$headers = get_all_headers($URL);

		if ($headers) {

			if (strpos($headers["Content-Type"], 'image/') !== FALSE) {

				header("Content-Type: " . $headers["Content-Type"]);
				header("Content-Length: " . $headers["Content-Length"]);
				readfile($URL);
			} else {
				header("HTTP/1.1 403 Forbidden");
			}

		} else {
			header("HTTP/1.1 403 Forbidden");
		}

	} else {
		header("HTTP/1.1 403 Forbidden");
	}
}

function get_all_headers($URL) {
	stream_context_set_default(
		array(
			'http' => array(
				'method' => 'HEAD'
			)
		)
	);

	$headers = get_headers($URL, 1);

	stream_context_set_default(
		array(
			'http' => array(
				'method' => 'GET'
			)
		)
	);

	return $headers;
}

if ( class_exists("rcube_plugin") ) {
	class remote_proxy extends rcube_plugin {
		public $task = 'mail';
		private $map;
		function init () {
			$this->include_script('remote_proxy.js');
		}
	}
}
