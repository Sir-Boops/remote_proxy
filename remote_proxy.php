<?php

class remote_proxy extends rcube_plugin {

	public $task = 'mail|login|logout';
	private $map;

	function init () {
		// Load the client script for reqriting URLs
		$this->include_script('remote_proxy.js');

		// Register hooks
		$this->add_hook('login_after', array($this, 'login_after'));
		$this->add_hook('logout_after', array($this, 'logout_after'));
	}

	function login_after () {
		$config = include('config.inc.php');

		$username = rcmail::get_instance()->user->data['username'];
		$token = bin2hex(random_bytes(16));

		$dbc = new PDO($config['dbType'].":host=".$config['dbHost'].";dbname=".$config['dbName'],
			$config['dbUser'], $config['dbPass']);

		$stmt = $dbc->prepare("CREATE TABLE IF NOT EXISTS keys (username TEXT NOT NULL, key TEXT NOT NULL)");
		$stmt->execute();

		if (isset($_COOKIE['proxy'])) {
			$key = $_COOKIE['proxy'];
			$stmt = $dbc->prepare("DELETE FROM keys WHERE key LIKE :key");
			$stmt->execute(array(":key" => $key));
		}

		$stmt = $dbc->prepare("INSERT INTO keys (username, key) VALUES (:user, :token)");
		$stmt->execute(array(":user" => $username, ":token" => $token));

		$stmt = null;
		$dbc = null;

		setcookie("proxy", $token, 0, "/", NULL, TRUE, TRUE);
	}

	function logout_after () {
		$config = include('config.inc.php');

		$key = $_COOKIE['proxy'];

		$dbc = new PDO($config['dbType'].":host=".$config['dbHost'].";dbname=".$config['dbName'],
			$config['dbUser'], $config['dbPass']);

		$stmt = $dbc->prepare("DELETE FROM keys WHERE key LIKE :key");
		$stmt->execute(array(":key" => $key));

		$stmt = null;
		$dbc = null;
	}
}

?>
