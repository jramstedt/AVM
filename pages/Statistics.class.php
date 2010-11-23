<?php
class Statistics extends Kernel implements IPage {
	public $title = 'Statistics';
	protected $template = __CLASS__;
	
	private $units = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
	private $unitIndex;
	private $unitDivider;
	
	function __construct() {
		parent::__construct();
	}
	
	public function generate() {
		$addUrl = new Url();
		$addUrl->clearPageParams();
		$addUrl->setParam('getfilesystemusage', 1);
		Util::valueToXml('getfilesystemusageurl', $addUrl->buildUrlString(), $this->rootNode);
	}
	
	public function generateAjax() {
		$this->template = NULL;
		$this->contentType = Kernel::CONTENT_TYPE_JSON;
		
		if(isset($this->url->getPageParams()->getfilesystemusage)) {
			$usageResult = Kernel::mysqli()->query('SELECT MAX(free), MAX(total), MAX(used), MIN(free), MIN(total), MIN(used) FROM fsdata');
			$usageRow = $usageResult->fetch_row();
			
			$maxFree = $usageRow[0];
			$maxTotal = $usageRow[1];
			$maxUsed = $usageRow[2];
			
			$minFree = $usageRow[3];
			$minTotal = $usageRow[4];
			$minUsed = $usageRow[5];
			
			$this->unitIndex = 0;
			$this->unitDivider = 1;
			while(($minUsed / $this->unitDivider) >= 1024) {
				$this->unitDivider *= 1024;
				$this->unitIndex++;
			}
	
			$series = array();
			$usage = array();
			
			$fss = explode(',', MONITORED_FILESYSTEMS);
		
			foreach ($fss as $fs) {
				$fsdataResult = Kernel::mysqli()->query("SELECT * FROM fsdata WHERE filesystem='$fs'");
				if(is_object($fsdataResult)) {
					$fsData = array();
					
					while($fsdataObj = $fsdataResult->fetch_object())
						$fsData[] = array($fsdataObj->date, $fsdataObj->used / $this->unitDivider);
					
					$series[] = array('label'=>$fs);
					$usage[] = $fsData;
					
					$fsdataResult->close();
				}
			}
			
			$jsonData = array(
				'usage'=>$usage,
				'series'=>$series,
				'ylabel'=>$this->units[$this->unitIndex],
				'ymax'=>$maxTotal / $this->unitDivider,
				'ymin'=>0.0
				);
				
			echo json_encode($jsonData);
		}
	}
}
?>