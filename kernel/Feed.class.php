<?php
class Feed extends DOMDocument {
	
	const RSS1 = 1;
	const RSS2 = 2;
	const ATOM1 = 3;
	
	private $xPath;
	
	private $version;
	//private $feeds;
	public $entries;
	
	public function __construct($fileContent) {
		$this->loadXML($fileContent);
		
		$this->xPath = new DOMXPath($this);
		
		$this->entries = array();
		
		switch($this->documentElement->lookupnamespaceURI(NULL)) {
			case 'http://purl.org/rss/1.0/': $this->version = self::RSS1;
											 break;
			case 'http://www.w3.org/2005/Atom': $this->version = self::ATOM1;
												break;
			default: $this->version = self::RSS2;
					 break;
		}
		
		if($this->version == self::ATOM1) {
			//$this->feeds = $this->xPath->query("/feed");
			
			$domEntries = $this->xPath->query('/feed/entry');
			
			$length = $domEntries->length;
			for($entryIndex = 0; $entryIndex < $length; $entryIndex++) {
				$domEntry = $domEntries->item($entryIndex);

				$id = html_entity_decode($this->xPath->query('id', $domEntry)->item(0)->textContent, ENT_QUOTES);
				$title = html_entity_decode($this->xPath->query('title', $domEntry)->item(0)->textContent, ENT_QUOTES);
				$url = html_entity_decode($this->xPath->query('link', $domEntry)->item(0)->attributes->getNamedItem('href')->textContent, ENT_QUOTES);
				$publishedDate = strtotime($this->xPath->evaluate("published", $domEntry)->item(0)->textContent);
				
				$this->entries[] = new FeedItem($id, $title, $url, $publishedDate);
			}
		} else if($this->version == self::RSS1 || $this->version == self::RSS2) {
			//$this->feeds = $this->xPath->query("/rss/channel");
			
			$domEntries = $this->xPath->query('/rss/channel/item');

			$length = $domEntries->length;
			for($entryIndex = 0; $entryIndex < $length; $entryIndex++) {
				$domEntry = $domEntries->item($entryIndex);
				
				$id = html_entity_decode($this->xPath->query('guid', $domEntry)->item(0)->textContent, ENT_QUOTES);
				$title = html_entity_decode($this->xPath->query('title', $domEntry)->item(0)->textContent, ENT_QUOTES);
				$url = html_entity_decode($this->xPath->query('enclosure', $domEntry)->item(0)->attributes->getNamedItem('url')->textContent, ENT_QUOTES);
				$publishedDate = strtotime($this->xPath->query('pubDate', $domEntry)->item(0)->textContent);
				
				$this->entries[] = new FeedItem($id, $title, $url, $publishedDate);
			}
		}
	}
}

class FeedItem extends stdClass {
	public $id;
	public $title;
	public $url;
	public $publishedDate;
	
	public function __construct($id, $title, $url, $publishedDate) {
		$this->id = empty($id)?uniqid('feed_'):$id;
		$this->title = $title;
		$this->url = $url;
		$this->publishedDate = $publishedDate;
	}
}
?>