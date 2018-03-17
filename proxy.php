<?php

if ($_GET['proxyImg']) {

	$URL = $_GET['proxyImg'];

	if (!checkKey($_COOKIE['proxy'])) {
		header("HTTP/1.1 403 Forbidden");
		die;
	}

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

function checkKey ($key) {
	$config = include('config.inc.php');

	$dbc = new PDO($config['dbType'].":host=".$config['dbHost'].";dbname=".$config['dbName'],
		$config['dbUser'], $config['dbPass']);

	$stmt = $dbc->prepare("SELECT * FROM keys WHERE key LIKE :key");
	$stmt->execute(array(":key" => $key));

	$results = $stmt->fetchAll();

	$stmt = null;
	$dbc = null;

	$ans = FALSE;

	if ($results) {
		if (count($results) >= 1) {
			$ans = TRUE;
		}
	}

	return $ans;
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
