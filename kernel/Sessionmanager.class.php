<?php
class Sessionmanager {
	const TYPE_LOGGED = 'logged';
	const TYPE_ID = 'id';
	const TYPE_USERNAME = 'username';
	const TYPE_USERLEVEL = 'level';

	public static function start() {
		session_set_cookie_params(COOKIE_LIFETIME);
		session_name(COOKIE_NAME);
		session_start();

		if (isset($_COOKIE[COOKIE_NAME]))
			setcookie(COOKIE_NAME, $_COOKIE[COOKIE_NAME], time() + COOKIE_LIFETIME, "/");
	}

	public static function logout() {
		Logger::finest('Logged out.');
		session_unset();
		session_destroy();
	}

	public static function confirmUser($username, $password) {
		if(!empty($username) && !empty($password)) {
			$username = Kernel::mysqli()->real_escape_string($username);
			$password = Kernel::mysqli()->real_escape_string($password);

			$userQuery = Kernel::mysqli()->query("SELECT id FROM user WHERE username='$username' AND authkey=SHA('$password') ORDER BY ver DESC LIMIT 1");
			$userNum = $userQuery->num_rows;
			$userQuery->close();

			return $userNum;
		}
			
		return 0;
	}

	public static function login($username, $password) {
		$username = Kernel::mysqli()->escape_string($username);
		$password = Kernel::mysqli()->escape_string($password);

		$userQuery = Kernel::mysqli()->query("SELECT id, level FROM user WHERE username='$username' AND authkey=SHA('$password') ORDER BY ver DESC LIMIT 1");

		if($userQuery->num_rows == 1) {
			$userQueryObj = $userQuery->fetch_object();
			$userQuery->close();

			$_SESSION[Sessionmanager::TYPE_LOGGED] = true;
			$_SESSION[Sessionmanager::TYPE_ID] = $userQueryObj->id;
			$_SESSION[Sessionmanager::TYPE_USERNAME] = $username;
			$_SESSION[Sessionmanager::TYPE_USERLEVEL] = $userQueryObj->level;
				
			Logger::finest('Login.');
			return true;
		}

		$userQuery->close();
			
		Logger::info('Login error.');
		return false;
	}

	public static function isLogged() {
		return !empty($_SESSION[Sessionmanager::TYPE_LOGGED]);
	}

	public static function getUserId() {
		return isset($_SESSION[Sessionmanager::TYPE_ID])?$_SESSION[Sessionmanager::TYPE_ID]:NULL;
	}
	
	public static function getUsername() {
		return isset($_SESSION[Sessionmanager::TYPE_USERNAME])?$_SESSION[Sessionmanager::TYPE_USERNAME]:NULL;
	}

	public static function getUserlevel() {
		return isset($_SESSION[Sessionmanager::TYPE_USERLEVEL])?$_SESSION[Sessionmanager::TYPE_USERLEVEL]:USERLEVEL_NONE;
	}
}
?>
