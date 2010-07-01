<?php
class Series extends Kernel implements IPage {
	public $title = 'Series';
	protected $template = __CLASS__;

	function __construct() {
       parent::__construct();
       
       	$editUrl = new Url();
		$editUrl->clearPageParams();
		$editUrl->setParam('edit', 1);
		Util::valueToXml('editurl', $editUrl->buildUrlString(), $this->rootNode);
		
		$editUrl->clearPageParams();
		$editUrl->setParam('seed', 1);
		Util::valueToXml('seedurl', $editUrl->buildUrlString(), $this->rootNode);
		
		$editUrl->clearPageParams();
		$editUrl->setParam('watched', 1);
		Util::valueToXml('watchedurl', $editUrl->buildUrlString(), $this->rootNode);
	}
		
	public function generate() {
		$addUrl = new Url();
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
		$addUrl->setParam('getseasonlist', 1);
		Util::valueToXml('seasonlisturl', $addUrl->buildUrlString(), $this->rootNode);
		
		$addUrl->clearPageParams();
		$addUrl->setParam('getserielist', 1);
		Util::valueToXml('serielisturl', $addUrl->buildUrlString(), $this->rootNode);
		
		$this->serieList(0, 15);
	}
	
	public function generateAjax() {
		if(isset($this->url->getPageParams()->seed)) {
			if(isset($this->url->getPageParams()->serie)) {
				$id = $this->url->getPageParams()->serie;

				Kernel::addInfo(Kernel::INFOBOX_OK, "Seeding started!");
			} else if(isset($this->url->getPageParams()->season)) {
				$id = $this->url->getPageParams()->season;

				Kernel::addInfo(Kernel::INFOBOX_OK, "Seeding started!");
			} else if(isset($this->url->getPageParams()->episode)) {
				$id = $this->url->getPageParams()->episode;

				Kernel::addInfo(Kernel::INFOBOX_OK, "Seeding started!");
			}
		} else if(isset($this->url->getPageParams()->watched)) {
			if(isset($this->url->getPageParams()->season)) {
				$id = $this->url->getPageParams()->season;
				Kernel::mysqli()->query("INSERT INTO season (id, serieid, season, state, watched, file, torrent, ver)
										 SELECT id, serieid, season, state, NOT watched, file, torrent, (ver + 1) FROM season WHERE id=$id ORDER BY ver DESC LIMIT 1");
				
				Kernel::addInfo(Kernel::INFOBOX_OK, "Watched status changed!");
				
				$this->getSeason($id);
			} else if(isset($this->url->getPageParams()->episode)) {
				$id = $this->url->getPageParams()->episode;
				Kernel::mysqli()->query("INSERT INTO episode (id, serieid, seasonid, episode, title, format, watched, file, torrent, ver)
										 SELECT id, serieid, seasonid, episode, title, format, NOT watched, file, torrent, (ver + 1) FROM episode WHERE id=$id ORDER BY ver DESC LIMIT 1");
					
				Kernel::addInfo(Kernel::INFOBOX_OK, "Watched status changed!");
				
				$this->getEpisode($id);
			}
		} else if(isset($this->url->getPageParams()->getserielist)) {
			$this->template = NULL; // No xsl transform!
			$this->contentType = Kernel::CONTENT_TYPE_JSON;
		
			$serieArray = array(array('id' => '0', 'name' => '-- Select serie'));

			$seriesResult = Kernel::mysqli()->query("SELECT a.* FROM serie a WHERE a.ver IN (SELECT MAX(b.ver) FROM serie b WHERE b.id = a.id) ORDER BY name");
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
			
			if(isset($this->url->getPageParams()->id)) {
				$id = Kernel::mysqli()->escape_string($this->url->getPageParams()->id);
				
				$seasonArray = array();
				
				$seasonsResult = Kernel::mysqli()->query("SELECT a.id, a.season FROM season a WHERE a.ver IN (SELECT MAX(b.ver) FROM season b WHERE b.id = a.id AND serieid = $id) ORDER BY season");
				if(is_object($seasonsResult)) {
					while($seasonObj = $seasonsResult->fetch_object()) {
						$seasonArray[] = $seasonObj;
					}
				}
				$seasonsResult->close();
				
				echo json_encode($seasonArray);
			}
		} else if(isset($this->url->getPageParams()->edit)) {
			$this->template = NULL; // No xsl transform!
			$this->contentType = Kernel::CONTENT_TYPE_PLAIN;
			
			if(!empty($_POST)) {
				$typeInfo = explode(':', $this->url->getPageParams()->id);
				$type = Kernel::mysqli()->escape_string($typeInfo[0]);
				
				if($type == 'serie') {
					if(!isset($_POST['id']) || !isset($_POST['value']))
						return ;
					
					$elementInfo = explode(':', $_POST['id']);
					$id = Kernel::mysqli()->escape_string($elementInfo[0]);
					$value = Kernel::mysqli()->escape_string($_POST['value']);
					
					$serieResult = Kernel::mysqli()->query("SELECT * FROM serie WHERE id = $id ORDER BY ver DESC LIMIT 1");
					$serieObj = $serieResult->fetch_object();
					$serieResult->close();
					
					$element = Kernel::mysqli()->escape_string($elementInfo[1]);
					if($element == 'name') {
						$serieObj->name = $value;
					} else if($element == 'year') {
						$serieObj->year = $value;
					} else if($element == 'url') {
						$serieObj->url = $value;
					} else if($element == 'file') {
						$serieObj->file = $value;
					} else if($element == 'torrent') {
						$serieObj->torrent = $value;
					}
					
					Kernel::mysqli()->query("INSERT INTO serie (id, name, year, url, file, torrent, ver)
											 SELECT {$serieObj->id}, '{$serieObj->name}', {$serieObj->year}, '{$serieObj->url}', '{$serieObj->file}', '{$serieObj->torrent}', (coalesce(max(ver), 0) + 1) FROM serie WHERE id=$id");
					
					echo $value;
				} else if($type == 'season') {
					if(!isset($_POST['id']) || !isset($_POST['value']))
						return ;
					
					$elementInfo = explode(':', $_POST['id']);
					$id = Kernel::mysqli()->escape_string($elementInfo[0]);
					$value = Kernel::mysqli()->escape_string($_POST['value']);
					
					$seasonResult = Kernel::mysqli()->query("SELECT * FROM season WHERE id = $id ORDER BY ver DESC LIMIT 1");
					$seasonObj = $seasonResult->fetch_object();
					$seasonResult->close();
					
					$element = Kernel::mysqli()->escape_string($elementInfo[1]);
					if($element == 'season') {
						$seasonObj->season = $value;
					} else if($element == 'state') {
						$seasonObj->state = $value;
					} else if($element == 'watched') {
						$seasonObj->watched = $value;
					} else if($element == 'file') {
						$seasonObj->file = $value;
					} else if($element == 'torrent') {
						$seasonObj->torrent = $value;
					}
					
					Kernel::mysqli()->query("INSERT INTO season (id, serieid, season, state, watched, file, torrent, ver)
											 SELECT {$seasonObj->id}, {$seasonObj->serieid}, {$seasonObj->season}, '{$seasonObj->state}', {$seasonObj->watched}, '{$seasonObj->file}', '{$seasonObj->torrent}', (coalesce(max(ver), 0) + 1) FROM season WHERE id=$id");
					
					echo $value;
				} else if($type == 'episode') {
					if(!isset($_POST['id']) || !isset($_POST['value']))
						return ;
					
					$elementInfo = explode(':', $_POST['id']);
					$id = Kernel::mysqli()->escape_string($elementInfo[0]);
					$value = Kernel::mysqli()->escape_string($_POST['value']);
					
					$episodeResult = Kernel::mysqli()->query("SELECT * FROM episode WHERE id = $id ORDER BY ver DESC LIMIT 1");
					$episodeObj = $episodeResult->fetch_object();
					$episodeResult->close();
					
					$element = Kernel::mysqli()->escape_string($elementInfo[1]);
					if($element == 'episode') {
						$episodeObj->episode = $value;
						$value = str_pad($value, 2, "0", STR_PAD_LEFT); 
					} else if($element == 'title') {
						$episodeObj->title = $value;
					} else if($element == 'format') {
						$value = AVM::$videoFormat[$value];
						$episodeObj->format = $value;
					} else if($element == 'watched') {
						$episodeObj->watched = $value;
					} else if($element == 'file') {
						$episodeObj->file = $value;
					} else if($element == 'torrent') {
						$episodeObj->torrent = $value;
					}
					
					Kernel::mysqli()->query("INSERT INTO episode (id, serieid, seasonid, episode, title, format, watched, file, torrent, ver)
											 SELECT {$episodeObj->id}, {$episodeObj->serieid}, {$episodeObj->seasonid}, {$episodeObj->episode}, '{$episodeObj->title}', '{$episodeObj->format}', {$episodeObj->watched}, '{$episodeObj->file}', '{$episodeObj->torrent}', (coalesce(max(ver), 0) + 1) FROM episode WHERE id=$id");
					
					echo $value;
				}
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
					
					$this->serieList(0, 15);
					
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
					$watched = empty($_POST['watched'])?0:1;
					$file = Kernel::mysqli()->escape_string($_POST['file']);
					$torrent = Kernel::mysqli()->escape_string($_POST['torrent']);
						
					Kernel::mysqli()->query("INSERT INTO season (serieid, season, state, watched, file, torrent)
											VALUES('$serieid','$season','$state','$watched','$file','$torrent')");
					
					$this->serieList(0, 15);
					
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
				} else {
					$serieid = Kernel::mysqli()->escape_string($_POST['serieid']);
					$seasonid = Kernel::mysqli()->escape_string($_POST['seasonid']);
					$episode = Kernel::mysqli()->escape_string($_POST['episode']);
					$title = Kernel::mysqli()->escape_string($_POST['title']);
					$format = AVM::$videoFormat[Kernel::mysqli()->escape_string($_POST['format'])];
					$watched = empty($_POST['watched'])?0:1;
					$file = Kernel::mysqli()->escape_string($_POST['file']);
					$torrent = Kernel::mysqli()->escape_string($_POST['torrent']);
						
					Kernel::mysqli()->query("INSERT INTO episode (serieid, seasonid, episode, title, format, watched, file, torrent)
											VALUES('$serieid','$seasonid','$episode', '$title', '$format','$watched','$file','$torrent')");
					
					$this->serieList(0, 15);
					
					Kernel::addInfo(Kernel::INFOBOX_OK, "Episode $title ($episode) [$format] added!");
				}
			}
		}
	}
	
	public function getSerie($id) {
		$seriesResult = Kernel::mysqli()->query("SELECT * FROM serie WHERE id = $id ORDER BY ver DESC LIMIT 1");
		if(is_object($seriesResult)) {
			while($serieObj = $seriesResult->fetch_object()) {
				$serieNode = Util::objToXml($serieObj, $this->rootNode, 'serie');

				$seasonsResult = Kernel::mysqli()->query("SELECT a.* FROM season a WHERE a.ver IN (SELECT MAX(b.ver) FROM season b WHERE b.id = a.id AND serieid = $serieObj->id) ORDER BY season");
				if(is_object($seasonsResult)) {
					while($seasonObj = $seasonsResult->fetch_object()) {
						$seasonNode = Util::objToXml($seasonObj, $serieNode, 'season');

						$episodesResult = Kernel::mysqli()->query("SELECT a.* FROM episode a WHERE a.ver IN (SELECT MAX(b.ver) FROM episode b WHERE b.id = a.id AND serieid = $serieObj->id AND seasonid = $seasonObj->id) ORDER BY episode");
						if(is_object($episodesResult)) {
							while($episodeObj = $episodesResult->fetch_object()) {
								$episodeObj->season = $seasonObj->season;
								Util::objToXml($episodeObj, $seasonNode, 'episode');
							}
							$episodesResult->close();
						}
					}
					$seasonsResult->close();
				}
			}
			$seriesResult->close();
		}	
	}
	
	public function getSeason($id) {
		$seasonsResult = Kernel::mysqli()->query("SELECT * FROM season WHERE id = $id ORDER BY ver DESC LIMIT 1");
		if(is_object($seasonsResult)) {
			while($seasonObj = $seasonsResult->fetch_object()) {
				$seasonNode = Util::objToXml($seasonObj, $this->rootNode, 'season');

				$episodesResult = Kernel::mysqli()->query("SELECT a.* FROM episode a WHERE a.ver IN (SELECT MAX(b.ver) FROM episode b WHERE b.id = a.id AND serieid = $seasonObj->serieid AND seasonid = $seasonObj->id) ORDER BY episode");
				if(is_object($episodesResult)) {
					while($episodeObj = $episodesResult->fetch_object()) {
						$episodeObj->season = $seasonObj->season;
						Util::objToXml($episodeObj, $seasonNode, 'episode');
					}
					$episodesResult->close();
				}
			}
			$seasonsResult->close();
		}
	}
	
	public function getEpisode($id) {
		$episodesResult = Kernel::mysqli()->query("SELECT * FROM episode WHERE id = $id ORDER BY ver DESC LIMIT 1");
		if(is_object($episodesResult)) {
			while($episodeObj = $episodesResult->fetch_object()) {
				$seasonResult = Kernel::mysqli()->query("SELECT season FROM season WHERE id = {$episodeObj->seasonid} ORDER BY ver DESC LIMIT 1");
				$seasonObj = $seasonResult->fetch_object();
				$seasonResult->close();

				$episodeObj->season = $seasonObj->season;
				Util::objToXml($episodeObj, $this->rootNode, 'episode');
			}
			$episodesResult->close();
		}
	}
	
	public function serieList($page, $count) {
		$seriesResult = Kernel::mysqli()->query('SELECT a.* FROM serie a WHERE a.ver IN (SELECT MAX(b.ver) FROM serie b WHERE b.id = a.id) ORDER BY name');
		if(is_object($seriesResult)) {
			$listNode = Util::tagToXml('list', $this->rootNode);

			while($serieObj = $seriesResult->fetch_object()) {
				$serieNode = Util::objToXml($serieObj, $listNode, 'serie');

				$seasonsResult = Kernel::mysqli()->query("SELECT a.* FROM season a WHERE a.ver IN (SELECT MAX(b.ver) FROM season b WHERE b.id = a.id AND serieid = $serieObj->id) ORDER BY season");
				if(is_object($seasonsResult)) {
					while($seasonObj = $seasonsResult->fetch_object()) {
						$seasonNode = Util::objToXml($seasonObj, $serieNode, 'season');

						$episodesResult = Kernel::mysqli()->query("SELECT a.* FROM episode a WHERE a.ver IN (SELECT MAX(b.ver) FROM episode b WHERE b.id = a.id AND serieid = $serieObj->id AND seasonid = $seasonObj->id) ORDER BY episode");
						if(is_object($episodesResult)) {
							while($episodeObj = $episodesResult->fetch_object()) {
								$episodeObj->season = $seasonObj->season;
								Util::objToXml($episodeObj, $seasonNode, 'episode');
							}
							$episodesResult->close();
						}
					}
					$seasonsResult->close();
				}
			}
			$seriesResult->close();
		}	
	}
}
?>