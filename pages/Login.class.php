<?php
class Login extends Kernel implements IPage {
	public $title = 'Login';
	protected $template = __CLASS__;

	public function generate() {
		if(isset($this->url->getPageParams()->logout)) {
			Sessionmanager::logout();
				
			$this->url->setPage('Login');
			$this->url->clearPageParams();
			header('Location: '.$this->url->buildUrlString());
		} else if(!empty($_POST)) {
			if(Sessionmanager::login($_POST['username'], $_POST['password'])) {
				header('Location: '.$this->url->buildUrlString());
			} else {
				Kernel::addInfo(Kernel::INFOBOX_ERROR, 'Login failed.');
			}
		}
	}
	
	public function generateAjax() {
		$this->template = NULL; // No xsl transform!
		$this->contentType = Kernel::CONTENT_TYPE_PLAIN;
	}
}
?>
