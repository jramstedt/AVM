<?php
class Unhandled extends Kernel implements IPage {
	public $title = 'Unhandled';
	protected $template = __CLASS__;
	
	private $listNode;

	public function generate() {
		$addUrl = new Url();
		$addUrl->clearPageParams();
		$addUrl->setParam('addmovie', 1);
		Util::valueToXml('addmovieurl', $addUrl->buildUrlString(), $this->rootNode);
		
		$addUrl->clearPageParams();
		$addUrl->setParam('addserie', 1);
		Util::valueToXml('addserieurl', $addUrl->buildUrlString(), $this->rootNode);

		$addUrl->clearPageParams();
		$addUrl->setParam('addseason', 1);
		Util::valueToXml('addseasonurl', $addUrl->buildUrlString(), $this->rootNode);

		$addUrl->clearPageParams();
		$addUrl->setParam('addepisode', 1);
		Util::valueToXml('addepisodeurl', $addUrl->buildUrlString(), $this->rootNode);
		
		$addUrl->clearPageParams();
		$addUrl->setParam('getserielist', 1);
		Util::valueToXml('serielisturl', $addUrl->buildUrlString(), $this->rootNode);
		
		$addUrl->clearPageParams();
		$addUrl->setParam('getseasonlist', 1);
		Util::valueToXml('seasonlisturl', $addUrl->buildUrlString(), $this->rootNode);
		
		$this->listNode = Util::tagToXml('list', $this->rootNode);
		
		$unhandledResult = Kernel::mysqli()->query('SELECT * FROM unhandled ORDER BY torrent');
		if(is_object($unhandledResult)) {
			while($unhandledObj = $unhandledResult->fetch_object()) {
				$this->handeTorrent($unhandledObj);
				//Util::objToXml($unhandledObj, $listNode, 'torrent');
				//Util::objToXml($this->getStuff($unhandledObj), $listNode, 'torrent');
			}
			
			$unhandledResult->close();
		}
	}
	
	private function getTorrentData($torrentFile) {
		$stream = @file_get_contents($torrentFile, FILE_BINARY);
		if ($stream == false) {
			$this->addInfo(Kernel::INFOBOX_ERROR, $object->torrent . ' could not be loaded!');
			return NULL;
		}
		
		$data = BDecode::decode($stream);

		if ($data === false){
			$this->addInfo(Kernel::INFOBOX_ERROR, 'Error in file. Not valid BEncoded Data.');
			return NULL;
		}else{
			if(!isset($data['info'])){
				$this->addInfo(Kernel::INFOBOX_ERROR, 'Error in file. Not a valid torrent file.');
				return NULL;
			}
		}
		
		return $data;
	}
	
	private function handeTorrent(stdClass $object) {
		$data = $this->getTorrentData($object->torrent);
		
		if($data == NULL)
			return;
		
		$nodes = array();
		
		$torrentNode = Util::tagToXml('torrent', $this->listNode);
		
		Util::valueToXml('id', $object->id, $torrentNode);
		Util::valueToXml('torrent', $object->torrent, $torrentNode);
		
		Util::valueToXml('file', $data['info']['name'], $torrentNode);
		
		$rootPath = dirname($object->file.'/.').DIRECTORY_SEPARATOR.$data['info']['name'];
		Util::valueToXml('fullpath', $rootPath, $torrentNode);
		
		Util::objToXml(AVM::getDetailsObj($data['info']['name']), $torrentNode, 'details');
		
		if(isset($data['info']['files'])) {
			foreach($data['info']['files'] as $file) {
				$pathNode = $torrentNode;
				$fullPath = $rootPath;
				foreach($file['path'] as $path) {
					if(!isset($nodes[$path])) {
						$fullPath .= DIRECTORY_SEPARATOR . $path;
						
						$pathNode = $nodes[$path] = Util::tagToXml('path', $pathNode);
						
						Util::valueToXml('id', hash('md4', $path), $pathNode);
						Util::valueToXml('file', $path, $pathNode);
						Util::valueToXml('fullpath', $fullPath, $pathNode);
						Util::objToXml(AVM::getDetailsObj($path), $pathNode, 'details');
					} else {
						$pathNode = $nodes[$path];
					}
				}
			}
		}
	}
	
	private function getFileDetailsObjFromUrl() {
		$pathInfo = explode(':', $this->url->getPageParams()->current);
		
		$torrentInfo = explode(':', $this->url->getPageParams()->root);
		$torrentId = Kernel::mysqli()->escape_string($torrentInfo[1]);
		
		$details = NULL;
		
		$unhandledResult = Kernel::mysqli()->query("SELECT * FROM unhandled WHERE id = $torrentId");
		if(is_object($unhandledResult)) {
			$unhandledObj = $unhandledResult->fetch_object();
			$data = $this->getTorrentData($unhandledObj->torrent);
			
			if($pathInfo[0] == 'torrent') {
				$details = AVM::getDetailsObj($data['info']['name']);
			} else {
				if(isset($data['info']['files'])) {
					foreach($data['info']['files'] as $file) {
						$file = $file['path'][count($file['path'])-1];
						
						if($pathInfo[1] == hash('md4', $file)) {
							$details = AVM::getDetailsObj($file);
							break;
						}
					}
				}
			}

			$unhandledResult->close();
		}
		
		return $details;
	}
	
	public function generateAjax() {
		$this->template = 'AVM';

		if(isset($this->url->getPageParams()->getserielist)) {
			$this->template = NULL; // No xsl transform!
			$this->contentType = Kernel::CONTENT_TYPE_JSON;
			
			$serieArray = array(array('id' => '0', 'name' => '-- Select serie'));

			$seriesResult = Kernel::mysqli()->query("SELECT a.* FROM serie a WHERE a.ver IN (SELECT MAX(b.ver) FROM serie b WHERE b.id = a.id)");
			if(is_object($seriesResult)) {
				while($serieObj = $seriesResult->fetch_object()) {
					$serieArray[] = $serieObj;
				}
				
				$seriesResult->close();
			}
			
			echo json_encode($serieArray);
		} else if(isset($this->url->getPageParams()->getseasonlist)) {
			$this->template = NULL; // No xsl transform!
			$this->contentType = Kernel::CONTENT_TYPE_JSON;
			
			$this->template = null;

			if(isset($this->url->getPageParams()->id)) {
				$id = Kernel::mysqli()->escape_string($this->url->getPageParams()->id);
				
				$details = $this->getFileDetailsObjFromUrl();
				
				$seasonArray = array();
				
				$seasonsResult = Kernel::mysqli()->query("SELECT a.id, a.season FROM season a WHERE a.ver IN (SELECT MAX(b.ver) FROM season b WHERE b.id = a.id AND serieid = $id)");
				if(is_object($seasonsResult)) {
					while($seasonObj = $seasonsResult->fetch_object()) {
						if($details->season == $seasonObj->season)
							$seasonObj->selected = true;
						
						$seasonArray[] = $seasonObj;
					}
					
					$seasonsResult->close();
				}
				
				echo json_encode($seasonArray);
			}
		} else if(isset($this->url->getPageParams()->addserie)) {
			if(!empty($_POST)) {
				if(empty($_POST['name'])) {
					Kernel::addInfo(Kernel::INFOBOX_ERROR, 'You must enter serie name.');
				} else if(empty($_POST['year'])) {
					Kernel::addInfo(Kernel::INFOBOX_ERROR, 'You must enter serie year.');
				} else {
					$name = Kernel::mysqli()->escape_string($_POST['name']);
					$year = Kernel::mysqli()->escape_string($_POST['year']);
					$url = Kernel::mysqli()->escape_string($_POST['url']);
					$file = Kernel::mysqli()->escape_string($_POST['file']);
					$torrent = Kernel::mysqli()->escape_string($_POST['torrent']);
						
					Kernel::mysqli()->query("INSERT INTO serie (name, year, url, file, torrent)
											VALUES('$name','$year','$url','$file','$torrent')");
						
					Kernel::addInfo(Kernel::INFOBOX_OK, "Serie $name ($year) added!");
				}
			}
		} else if(isset($this->url->getPageParams()->addseason)) {
			if(!empty($_POST)) {
				if(empty($_POST['serieid'])) {
					Kernel::addInfo(Kernel::INFOBOX_ERROR, 'You must select serie.');
				} else if(empty($_POST['season'])) {
					Kernel::addInfo(Kernel::INFOBOX_ERROR, 'You must enter season number.');
				} else {
					$serieid = Kernel::mysqli()->escape_string($_POST['serieid']);
					$season = Kernel::mysqli()->escape_string($_POST['season']);
					$state = Kernel::mysqli()->escape_string($_POST['state']);
					$watched = Kernel::mysqli()->escape_string($_POST['watched']);
					$file = Kernel::mysqli()->escape_string($_POST['file']);
					$torrent = Kernel::mysqli()->escape_string($_POST['torrent']);
						
					Kernel::mysqli()->query("INSERT INTO season (serieid, season, state, watched, file, torrent)
											VALUES('$serieid','$season','$state','$watched','$file','$torrent')");
						
					Kernel::addInfo(Kernel::INFOBOX_OK, "Season $season added!");
				}
			}
		} else if(isset($this->url->getPageParams()->addepisode)) {
			if(!empty($_POST)) {
				if(empty($_POST['serieid'])) {
					Kernel::addInfo(Kernel::INFOBOX_ERROR, 'You must select serie.');
				} else if(empty($_POST['seasonid'])) {
					Kernel::addInfo(Kernel::INFOBOX_ERROR, 'You must select season.');
				} else if(empty($_POST['episode'])) {
					Kernel::addInfo(Kernel::INFOBOX_ERROR, 'You must enter episode number.');
				} else if(empty($_POST['title'])) {
					Kernel::addInfo(Kernel::INFOBOX_ERROR, 'You must enter title.');
				} else if(!isset($_POST['format'])) {
					Kernel::addInfo(Kernel::INFOBOX_ERROR, 'You must enter format.');
				} else {
					$serieid = Kernel::mysqli()->escape_string($_POST['serieid']);
					$seasonid = Kernel::mysqli()->escape_string($_POST['seasonid']);
					$episode = Kernel::mysqli()->escape_string($_POST['episode']);
					$title = Kernel::mysqli()->escape_string($_POST['title']);
					$format = AVM::$videoFormat[Kernel::mysqli()->escape_string($_POST['format'])];
					$watched = Kernel::mysqli()->escape_string($_POST['watched']);
					$file = Kernel::mysqli()->escape_string($_POST['file']);
					$torrent = Kernel::mysqli()->escape_string($_POST['torrent']);
						
					Kernel::mysqli()->query("INSERT INTO episode (serieid, seasonid, episode, title, format, watched, file, torrent)
											VALUES('$serieid','$seasonid','$episode', '$title', '$format','$watched','$file','$torrent')");
						
					Kernel::addInfo(Kernel::INFOBOX_OK, "Episode $title ($episode) [$format] added!");
				}
			}
		} else if(isset($this->url->getPageParams()->addmovie)) {
			if(!empty($_POST)) {
				if(empty($_POST['name'])) {
					Kernel::addInfo(Kernel::INFOBOX_ERROR, 'You must enter movie name.');
				} else if(empty($_POST['year'])) {
					Kernel::addInfo(Kernel::INFOBOX_ERROR, 'You must enter year.');
				} else if(!isset($_POST['format'])) {
					Kernel::addInfo(Kernel::INFOBOX_ERROR, 'You must enter format.');
				} else {
					$name = Kernel::mysqli()->escape_string($_POST['name']);
					$year = Kernel::mysqli()->escape_string($_POST['year']);
					$url = Kernel::mysqli()->escape_string($_POST['url']);
					$format = AVM::$videoFormat[Kernel::mysqli()->escape_string($_POST['format'])];
					$watched = Kernel::mysqli()->escape_string($_POST['watched']);
					$file = Kernel::mysqli()->escape_string($_POST['file']);
					$torrent = Kernel::mysqli()->escape_string($_POST['torrent']);
						
					Kernel::mysqli()->query("INSERT INTO movie (name, year, url, format, watched, file, torrent)
											VALUES('$name','$year','$url','$format','$watched','$file','$torrent')");
						
					Kernel::addInfo(Kernel::INFOBOX_OK, "$name ($year) [$format] added!");
				}
			}
		}
	}
}
?>