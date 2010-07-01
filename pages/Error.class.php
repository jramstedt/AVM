<?php
class Error extends Kernel implements IPage {
	public $title = 'Error';
	protected $template = __CLASS__;

	private $errorCodes = array(404 => 'Page not found.',
								403 => 'Access forbidden.',
								501 => 'Not Implemented.');
	
	public function generate() {
		$errorCode = 501;
		
		if(isset($this->url->getPageParams()->code))
			if(array_key_exists($this->url->getPageParams()->code, $this->errorCodes))
				$errorCode = $this->url->getPageParams()->code;

		header("HTTP/1.1 $errorCode {$this->errorCodes[$errorCode]}");
		header("Status: $errorCode {$this->errorCodes[$errorCode]}");
			
		Util::valueToXml('code', $errorCode, $this->rootNode);
		Util::valueToXml('text', $this->errorCodes[$errorCode], $this->rootNode);
	}
	
	public function generateAjax() {
		$this->template = NULL; // No xsl transform!
		$this->contentType = Kernel::CONTENT_TYPE_PLAIN;
		
		$errorCode = 501;
		
		if(isset($this->url->getPageParams()->code))
			if(array_key_exists($this->url->getPageParams()->code, $this->errorCodes))
				$errorCode = $this->url->getPageParams()->code;
		
		header("HTTP/1.1 $errorCode {$this->errorCodes[$errorCode]}");
		header("Status: $errorCode {$this->errorCodes[$errorCode]}");
		
		echo $this->url->getPageParams()->code;
	}
}
?>