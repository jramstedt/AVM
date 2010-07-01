<?php
class Post {
	public function __construct() {
		foreach($_POST as $sKey => $sValue) {
			$this->{$sKey} = Kernel::mysqli()->real_escape_string($sValue);
		}
	}
}
?>
