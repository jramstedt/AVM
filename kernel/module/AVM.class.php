<?php
class AVM implements IModule {
	private $kernel;
	
	public static $videoFormat = array('720p', '1080p', '1080i', '576i/p', '480i/p', 'LDTV');
	public static $audioFormat = array('mp3', 'flac');
	public static $torrentFilter;
	public static $torrentTimelimit;
	
	public function __construct(Kernel $kernel) {
		$this->kernel = $kernel;
		
		Util::valueToXml('videoformatjson', json_encode(self::$videoFormat), $kernel->rootNode);
		Util::valueToXml('audioformatjson', json_encode(self::$audioFormat), $kernel->rootNode);

		$id = Sessionmanager::getUserId();
		$settingsQuery = Kernel::mysqli()->query("SELECT * FROM usersetting WHERE id=$id");
		if($settingsQuery->num_rows == 1) {
			$settingsObj = $settingsQuery->fetch_object();
			$settingsQuery->close();
			
			$settingsObj->timelimit = date('m/d/Y', strtotime($settingsObj->timelimit));
			
			self::$torrentFilter = $settingsObj->filter;
			self::$torrentTimelimit = $settingsObj->timelimit;
			
			Util::objToXml($settingsObj, $kernel->rootNode, 'settings');
		}
	}
	
	public static function setFilter($filter) {
		self::$torrentFilter = Kernel::mysqli()->escape_string($filter);
	
		$id = Sessionmanager::getUserId();
		$filter = self::$torrentFilter;
		Kernel::mysqli()->query("UPDATE usersetting SET filter='$filter' WHERE id=$id");
	}
	
	public static function setTimelimit($timelimit) {
		self::$torrentTimelimit = $timelimit;
		
		$id = Sessionmanager::getUserId();
		$unixTime = strtotime($timelimit);
		Kernel::mysqli()->query("UPDATE usersetting SET timelimit=FROM_UNIXTIME($unixTime) WHERE id=$id");
	}
	
	public static function getDetailsObj($file) {
		$retObj = new stdClass();
		
		$fileInfo = pathinfo($file);
		
		// Ignore files
		$parts = preg_grep('/^(AC3|DTSMA|x264|dxva|DTS|DVBC|mp3|Bluray|HDDVD|-\w|.$)/i', preg_split('/[\s.]+/', $fileInfo['filename']), PREG_GREP_INVERT);
		
		$retObj->name = "";
		$retObj->year = "";
		$retObj->season = "";
		$retObj->episode = "";
		$retObj->format = "";
		
		$matched = false;
		foreach($parts as $part) {
			$match = NULL;
			
			if($match = self::getYear($part)) {
				$retObj->year = $match;
				$matched = true;
			}
			
			if($match = self::getSeason($part)) {
				$retObj->season = $match;
				$matched = true;
			}
			
			if($match = self::getEpisode($part)) {
				$retObj->episode = $match;
				$matched = true;
			}
			
			if($match = self::getFormat($part)) {
				$retObj->format = $match;
				$matched = true;
			}
			
			if(!$matched)
				$retObj->name .= $part . ' ';
		}
		
		return $retObj;
	}
	
	public static function getYear($part) {
		$matches = NULL;
		preg_match("/^(18|19|20)\d\d/", $part, $matches);
		
		return $matches[0];
	}
	
	public static function getSeason($part) {
		$matches = NULL;
		preg_match("/(^(S|Season.|part)(\d+))|^(\d+)x(\d+)/i", $part, $matches);//(E(?P<episode>\d+))?
		
		if(empty($matches[3]))
			return $matches[5];
		else
			return $matches[3];
	}
	
	public static function getEpisode($part) {
		$matches = NULL;
		//preg_match("/(^S(?P<season>\d+))?(E|of|part|x)(?P<episode>\d+)/i", $part, $matches);
		preg_match("/(E|of|part|x)(\d+)/i", $part, $matches);

		return $matches[2];
	}
	
	public static function getFormat($part) {
		$matches = NULL;
		preg_match("/(1080p|720p|HDTV|PDTV)/", $part, $matches);
		
		return $matches[0];
	}
}
