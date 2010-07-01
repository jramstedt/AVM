<?php
class Mainpage extends Kernel implements IPage {
	public $title = 'Mainpage';
	protected $template = __CLASS__;
	
	public function generate() {
		$listNode = Util::tagToXml('list', $this->rootNode);
		
		$filterUrl = new Url();
		$filterUrl->clearPageParams();
		$filterUrl->setParam('filter', 1);
		Util::valueToXml('filterurl', $filterUrl->buildUrlString(), $this->rootNode);
		
		$watchedUrl = new Url();
		$watchedUrl->clearPageParams();
		$watchedUrl->setParam('watched', 'movie');
		Util::valueToXml('watchedmovieurl', $watchedUrl->buildUrlString(), $this->rootNode);
		
		$watchedUrl->clearPageParams();
		$watchedUrl->setParam('watched', 'episode');
		Util::valueToXml('watchedepisodeurl', $watchedUrl->buildUrlString(), $this->rootNode);
		
		$moviesResult = Kernel::mysqli()->query('SELECT a.* FROM movie a WHERE a.ver IN (SELECT MAX(b.ver) FROM movie b WHERE b.id = a.id) AND watched = 0');
		if(is_object($moviesResult)) {
			while($movieObj = $moviesResult->fetch_object()) {
				Util::objToXml($movieObj, $listNode, 'movie');
			}
			
			$moviesResult->close();
		}
		
		$episodesResult = Kernel::mysqli()->query('SELECT a.* FROM episode a WHERE a.ver IN (SELECT MAX(b.ver) FROM episode b WHERE b.id = a.id) AND watched = 0');
		if(is_object($episodesResult)) {
			while($episodeObj = $episodesResult->fetch_object()) {
				Util::objToXml($episodeObj, $listNode, 'episode');
			}
			
			$episodesResult->close();
		}

		$feedsResult = Kernel::mysqli()->query('SELECT * FROM feed');
		if(is_object($feedsResult)) {
			while($feedObj = $feedsResult->fetch_object()) {
				$feed = new Feed($feedObj->data);
				
				foreach($feed->entries as $entryObj) {
					if($entryObj->publishedDate < strtotime(AVM::$torrentTimelimit))
						continue;
					
					$match = @preg_match(AVM::$torrentFilter, $entryObj->title);
					if(empty($match))
						continue;
						
					Util::objToXml($entryObj, $listNode, 'feeditem');
				}
			}
			$feedsResult->close();
		}
		
		$uncategorized = 0;
		
		$statisticsNode = Util::tagToXml('statistics', $this->rootNode);
		
		$torrentsResult = Kernel::mysqli()->query('SELECT (SELECT COUNT(DISTINCT id) FROM episode WHERE torrent <> "") +
															(SELECT COUNT(DISTINCT id) FROM movie WHERE torrent <> "") +
															(SELECT COUNT(DISTINCT id) FROM season WHERE torrent <> "") +
															(SELECT COUNT(DISTINCT id) FROM serie WHERE torrent <> "")');
		$torrentsRow = $torrentsResult->fetch_row();
		Util::valueToXml('torrents', $torrentsRow[0], $statisticsNode);
		
		$moviesResult = Kernel::mysqli()->query('SELECT COUNT(DISTINCT id) FROM movie');
		$moviesRow = $moviesResult->fetch_row();
		Util::valueToXml('movies', $moviesRow[0], $statisticsNode);
		
		$seriesResult = Kernel::mysqli()->query('SELECT COUNT(DISTINCT id) FROM serie');
		$seriesRow = $seriesResult->fetch_row();
		Util::valueToXml('series', $seriesRow[0], $statisticsNode);
		
		$unhandledResult = Kernel::mysqli()->query("SELECT COUNT(*) FROM unhandled");
		$unhandledRow = $unhandledResult->fetch_row();
		Util::valueToXml('uncategorized', $unhandledRow[0], $statisticsNode);
	}
	
	public function generateAjax() {
		$this->contentType = Kernel::CONTENT_TYPE_XML;
		
		$removeNode = Util::tagToXml('remove', $this->rootNode);
		
		if(isset($this->url->getPageParams()->filter)) {
			if(!empty($_POST)) {
				Kernel::addInfo(Kernel::INFOBOX_INFO, "Filter test! ".$_POST['filter']." ".$_POST['timelimit']);
				
				AVM::setFilter($_POST['filter']);
				AVM::setTimelimit($_POST['timelimit']);
			}
		} else if(isset($this->url->getPageParams()->watched)) {
			if(isset($this->url->getPageParams()->id)) {
				$id = $this->url->getPageParams()->id;
				$type = $this->url->getPageParams()->watched;
				
				if($type == 'movie') {
					Kernel::mysqli()->query("INSERT INTO movie (id, name, year, url, format, watched, file, torrent, ver)
											 SELECT id, name, year, url, format, NOT watched, file, torrent, (ver + 1) FROM movie WHERE id=$id ORDER BY ver DESC LIMIT 1");
						
					Kernel::addInfo(Kernel::INFOBOX_OK, "Watched status changed!");
					
					Util::objToXml((object)array('id' => $id), $removeNode, 'movie');
				} else if($type == 'episode') {
					Kernel::mysqli()->query("INSERT INTO episode (id, serieid, seasonid, episode, title, format, watched, file, torrent, ver)
											 SELECT id, serieid, seasonid, episode, title, format, NOT watched, file, torrent, (ver + 1) FROM episode WHERE id=$id ORDER BY ver DESC LIMIT 1");
						
					Kernel::addInfo(Kernel::INFOBOX_OK, "Watched status changed!");
					
					Util::objToXml((object)array('id' => $id), $removeNode, 'episode');
				}
			}
		}
	}
}
?>