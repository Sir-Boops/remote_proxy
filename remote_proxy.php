<?php

if ($_GET['proxyImg']) {

	$URL = $_GET['proxyImg'];

	if (filter_var($URL, FILTER_VALIDATE_URL)) {

		$headers = get_all_headers($URL);

		if (!is_array($headers["Content-Type"])) {

			if (strpos($headers["Content-Type"], 'image/') !== FALSE) {

				header("Content-Type: " . $headers["Content-Type"]);
				header("Content-Length: " . $headers["Content-Length"]);
				if (isset($headers["Content-Encoding"])) {
					header("Content-Encoding: " . $headers["Content-Encoding"]);
				}
				readfile($URL);
			} else {
				header("HTTP/1.1 403 Forbidden");
			}

		} else {

			foreach ($headers["Content-Type"] as $cont) {

				if (strpos($cont, "image/") !== FALSE) {

					header("Content-Type: " . $cont);
					header("Content-Length: " . $headers["Content-Length"]);
					if (isset($headers["Content-Encoding"])) {
						header("Content-Encoding: " . $headers["Content-Encoding"]);
					}
					readfile($URL);
				}
			}
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

	if (!$headers) {
		header("HTTP/1.1 403 Forbidden");
	}

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
