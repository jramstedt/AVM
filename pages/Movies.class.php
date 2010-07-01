<?php
class Movies extends Kernel implements IPage {
	public $title = 'Movies';
	protected $template = __CLASS__;

	function __construct() {
       parent::__construct();

		$editUrl = new Url();
		$editUrl->clearPageParams();
		$editUrl->setParam('edit', 1);
		Util::valueToXml('editurl', $editUrl->buildUrlString(), $this->rootNode);
		
		$editUrl->clearPageParams();
		$editUrl->setParam('watched', 1);
		Util::valueToXml('watchedurl', $editUrl->buildUrlString(), $this->rootNode);
	}
	
	public function generate() {
		$addUrl = new Url();
		$addUrl->clearPageParams();
		$addUrl->setParam('add', 1);
		Util::valueToXml('addurl', $addUrl->buildUrlString(), $this->rootNode);
		
		$countResult = Kernel::mysqli()->query('SELECT COUNT(DISTINCT id) FROM movie');
		$countRow = $countResult->fetch_row();
		$count = $countRow[0];
		
		$pages = ceil($count / 15);
		$page = isset($this->url->getPageParams()->edit)?$this->url->getPageParams()->page:0;
		
		$this->movieList($page, 15);
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
				
				$movieResult = Kernel::mysqli()->query("SELECT * FROM movie WHERE id = $id ORDER BY ver DESC LIMIT 1");
				$movieObj = $movieResult->fetch_object();
				$movieResult->close();
				
				$element = Kernel::mysqli()->escape_string($elementInfo[0]);
				if($element == 'name') {
					$movieObj->name = $value;
				} else if($element == 'year') {
					$movieObj->year = $value;
				} else if($element == 'url') {
					$movieObj->url = $value;
				} else if($element == 'format') {
					$value = AVM::$videoFormat[$value];
					$movieObj->format = $value;
				} else if($element == 'watched') {
					$movieObj->watched = $value;
				} else if($element == 'file') {
					$movieObj->file = $value;
				} else if($element == 'torrent') {
					$movieObj->torrent = $value;
				}
				
				Kernel::mysqli()->query("INSERT INTO movie (id, name, year, url, format, watched, file, torrent, ver)
										 SELECT {$movieObj->id}, '{$movieObj->name}', {$movieObj->year}, '{$movieObj->url}', '{$movieObj->format}', {$movieObj->watched}, '{$movieObj->file}', '{$movieObj->torrent}', (coalesce(max(ver), 0) + 1) FROM movie WHERE id=$id");
				
				echo $value;
			}
		} else if(isset($this->url->getPageParams()->watched)) {
			if(isset($this->url->getPageParams()->id)) {
				$id = $this->url->getPageParams()->id;
				Kernel::mysqli()->query("INSERT INTO movie (id, name, year, url, format, watched, file, torrent, ver)
										 SELECT id, name, year, url, format, NOT watched, file, torrent, (ver + 1) FROM movie WHERE id=$id ORDER BY ver DESC LIMIT 1");
				
				Kernel::addInfo(Kernel::INFOBOX_OK, "Watched status changed!");
				
				$movieResult = Kernel::mysqli()->query("SELECT * FROM movie WHERE id = $id ORDER BY ver DESC LIMIT 1");
				$movieObj = $movieResult->fetch_object();
				$movieResult->close();
				
				Util::objToXml($movieObj, $this->rootNode, 'movie');
			}
		} else if(isset($this->url->getPageParams()->add)) {
			if(!empty($_POST)) {
				if(empty($_POST['name'])) {
					Kernel::addInfo(Kernel::INFOBOX_ERROR, 'You must enter movie name.');
				} else if(empty($_POST['year'])) {
					Kernel::addInfo(Kernel::INFOBOX_ERROR, 'You must enter year.');
				} else {
					$name = Kernel::mysqli()->escape_string($_POST['name']);
					$year = Kernel::mysqli()->escape_string($_POST['year']);
					$url = Kernel::mysqli()->escape_string($_POST['url']);
					$format = AVM::$videoFormat[Kernel::mysqli()->escape_string($_POST['format'])];
					$watched = empty($_POST['watched'])?0:1;
					$file = Kernel::mysqli()->escape_string($_POST['file']);
					$torrent = Kernel::mysqli()->escape_string($_POST['torrent']);
						
					Kernel::mysqli()->query("INSERT INTO movie (name, year, url, format, watched, file, torrent)
											VALUES('$name','$year','$url','$format','$watched','$file','$torrent')");

					//$id = Kernel::mysqli()->insert_id;
					
					$this->movieList(0, 15);
					
					Kernel::addInfo(Kernel::INFOBOX_OK, "$name ($year) [$format] added!");
					/*
					$movieResult = Kernel::mysqli()->query("SELECT * FROM movie WHERE id = $id ORDER BY ver DESC LIMIT 1");
					$movieObj = $movieResult->fetch_object();
					$movieResult->close();
					
					Util::objToXml($movieObj, $this->rootNode, 'movie');
					*/
				}
			}
		}
	}
	
	public function movieList($page, $count) {
		$moviesResult = Kernel::mysqli()->query('SELECT a.* FROM movie a WHERE a.ver IN (SELECT MAX(b.ver) FROM movie b WHERE b.id = a.id) ORDER BY name');
		if(is_object($moviesResult)) {
			$listNode = Util::tagToXml('list', $this->rootNode);
			
			while($movieObj = $moviesResult->fetch_object()) {
				Util::objToXml($movieObj, $listNode, 'movie');
			}
			
			$moviesResult->close();
		}
	}
}
?>