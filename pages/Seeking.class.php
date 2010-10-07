<?php
class Seeking extends Kernel implements IPage {
	public $title = 'Seeking';
	protected $template = __CLASS__;

	function __construct() {
		parent::__construct();

		$editUrl = new Url();
		$editUrl->clearPageParams();
		$editUrl->setParam('edit', 1);
		Util::valueToXml('editurl', $editUrl->buildUrlString(), $this->rootNode);
	}
	
	public function generate() {
		$addUrl = new Url();
		$addUrl->clearPageParams();
		$addUrl->setParam('add', 1);
		Util::valueToXml('addurl', $addUrl->buildUrlString(), $this->rootNode);
		
		$countResult = Kernel::mysqli()->query('SELECT COUNT(DISTINCT id) FROM seek');
		$countRow = $countResult->fetch_row();
		$count = $countRow[0];
		
		$pages = ceil($count / 15);
		$page = isset($this->url->getPageParams()->edit)?$this->url->getPageParams()->page:0;
		
		$this->seekList($page, 15);
	}
	
	public function generateAjax() {
		$this->contentType = Kernel::CONTENT_TYPE_XML;
		
		if(isset($this->url->getPageParams()->edit)) {
			$this->template = NULL; // No xsl transform!
			$this->contentType = Kernel::CONTENT_TYPE_PLAIN;
			
			if(!empty($_POST)) {
				if(!isset($_POST['id']) || !isset($_POST['value']))
					return ;
				
				$elementInfo = explode(':', $_POST['id']);
				$id = Kernel::mysqli()->escape_string($elementInfo[1]);
				$value = Kernel::mysqli()->escape_string($_POST['value']);
				
				$seekResult = Kernel::mysqli()->query("SELECT * FROM seek WHERE id = $id LIMIT 1");
				$seekObj = $seekResult->fetch_object();
				$seekResult->close();
				
				$element = Kernel::mysqli()->escape_string($elementInfo[0]);
				if($element == 'name') {
					$seekObj->name = $value;
				} else if($element == 'year') {
					$seekObj->year = $value;
				} else if($element == 'url') {
					$seekObj->url = $value;
				} 
				
				Kernel::mysqli()->query("UPDATE seek SET name='{$seekObj->name}', year={$seekObj->year}, url='{$seekObj->url}' WHERE id=$id");
				
				echo $value;
			}
		} else if(isset($this->url->getPageParams()->add)) {
			if(!empty($_POST)) {
				if(empty($_POST['name'])) {
					Kernel::addInfo(Kernel::INFOBOX_ERROR, 'You must enter name.');
				} else {
					$name = Kernel::mysqli()->escape_string($_POST['name']);
					$year = Kernel::mysqli()->escape_string($_POST['year']);
					$url = Kernel::mysqli()->escape_string($_POST['url']);
					
					Kernel::mysqli()->query("INSERT INTO seek (name, year, url)
											VALUES('$name','$year','$url')");

					$this->seekList(0, 15);
					
					Kernel::addInfo(Kernel::INFOBOX_OK, "$name ($year) added!");
				}
			}
		}
	}
	
	public function seekList($page, $count) {
		$seeksResult = Kernel::mysqli()->query('SELECT * FROM seek ORDER BY name');
		if(is_object($seeksResult)) {
			$listNode = Util::tagToXml('list', $this->rootNode);
			
			while($seekObj = $seeksResult->fetch_object()) {
				Util::objToXml($seekObj, $listNode, 'seek');
			}
			
			$seeksResult->close();
		}
	}
}
?>