<?php
class Kernel {
	public $title = 'Kernel';
	protected $template = __CLASS__;
	protected $contentType = self::CONTENT_TYPE_XHTML;
	
	private $module = array();
	
	private static $mysqli;

	private $basePath;
	
	private $kernelNode;
	private $menuNode;
	private $infoNode;
	
	public $doc;
	public $url;
	public $post;
	public $rootNode;

	const INFOBOX_OK = 'ok';
	const INFOBOX_ERROR = 'error';
	const INFOBOX_INFO = 'info';

	const CONTENT_TYPE_XHTML = 'application/xhtml+xml';
	const CONTENT_TYPE_XML = 'application/xml';
	const CONTENT_TYPE_JSON = 'application/json';
	const CONTENT_TYPE_PLAIN = 'text/plain';
	
	public function __construct() {
		ob_start();

		$this->basePath = realpath(dirname($_SERVER['SCRIPT_FILENAME'])).'/';
		
		$this->doc = new DOMDocument('1.0', 'utf-8');
		$this->doc->xmlStandalone = true;
		$this->doc->formatOutput = true;

		$this->url = new Url();	// current url
		$this->post = new Post();

		$this->rootNode = Util::rootTagToXml('page', $this->doc);
		
		$this->kernelNode = Util::tagToXml('kernel', $this->rootNode);

		$this->menuNode = Util::tagToXml('menu', $this->kernelNode);

		$this->infoNode = Util::tagToXml('infobox', $this->kernelNode);

		$this->sessionNode = Util::tagToXml('session', $this->kernelNode);

		Util::valueToXml('title', TITLE_PREFIX.$this->title, $this->rootNode);

		if(Sessionmanager::isLogged()) {
			Util::valueToXml('id', Sessionmanager::getUserId(), $this->sessionNode);
			Util::valueToXml('username', Sessionmanager::getUsername(), $this->sessionNode);
			Util::valueToXml('userlevel', Sessionmanager::getUserlevel(), $this->sessionNode);
			Util::valueToXml('url', $this->url->buildUrlString(), $this->sessionNode);
		} else {
			Util::valueToXml('username', "Please login.", $this->sessionNode);
			Util::valueToXml('userlevel', USERLEVEL_NONE, $this->sessionNode);
		}
		
		$this->initializeModules();
	}

	public function __destruct() {
		$this->processPage($this->template);
		//while(@ob_end_flush());
	}

	public function addMenuItem(Url $url, $text) {
		$itemNode = $this->doc->createElement('item');
		$this->menuNode->appendChild($itemNode);
		Util::valueToXml('href', $url->buildUrlString(), $itemNode);
		Util::valueToXml('text', $text, $itemNode);

		if($this->template == $url->getPage())
			Util::valueToXml('current', true, $itemNode);
	}
	
	public function addInfo($infoboxType, $infoboxText) {
		$infoboxItemNode = $this->doc->createElement('item');
		$this->infoNode->appendChild($infoboxItemNode);
		Util::valueToXml('type', $infoboxType, $infoboxItemNode);
		Util::valueToXml('text', $infoboxText, $infoboxItemNode);
	}
	
	public static function mysqli() {
		if(!isset(Kernel::$mysqli)) {
			Kernel::$mysqli = new mysqli(MYSQL_HOSTNAME, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);
			Kernel::$mysqli->set_charset('utf8');
				
			if (Kernel::$mysqli->connect_error)
				Kernel::addInfo(INFOBOX_ERROR, 'MySQLi connection failed.');
		}

		return Kernel::$mysqli;
	}

	private function processPage($template) {
		/*if($this->contentType == self::CONTENT_TYPE_XHTML) {
			if (stristr($_SERVER["HTTP_ACCEPT"], self::CONTENT_TYPE_XHTML))
				header('Content-type: '.self::CONTENT_TYPE_XHTML);
			else
				header('Content-type: text/html');
		} else {*/
			header("Content-type: {$this->contentType}; charset=utf-8");
		//}
		
		if($template == NULL)
			return;
			
		if(DEBUG) {
			if(isset($this->url->getPageParams()->xmlonly)) {
				echo $this->doc->saveXML();
				return;
			}
			
			if(!Util::isAjaxRequest()) {
				$kernelDebugNode = $this->doc->createElement('kernelDebug', htmlspecialchars($this->doc->saveXML()));
				$this->kernelNode->appendChild($kernelDebugNode);
			}
		}
		
		if(Util::isAjaxRequest())
			$template .= 'Ajax';

		$xsl = new DOMDocument();
		$xsl->load($this->basePath.PATH_XSLT.$template.'.xsl');

		$proc = new XSLTProcessor();
		$proc->importStyleSheet($xsl);
		echo $proc->transformToXML($this->doc);
	}
	
	private function initializeModules() {
		if(function_exists('glob')) {
			foreach (glob($this->basePath.PATH_CLASS.'module/*.class.php') as $filename) {
				require_once $filename;
				$className = basename($filename, '.class.php');
				$module[] = new $className($this);
			}
		} else {
			if ($handle = opendir($this->basePath.PATH_CLASS.'module/')) {
				while (false !== ($file = readdir($handle))) {
					if ($file != '.' && $file != '..') {
						require_once $file;
						$className = basename($file, '.class.php');
						$module[] = new $className($this);
					}
				}
				closedir($handle);
			}
		}
		
	}
}
?>
