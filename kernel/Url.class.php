<?php
class Url {

	private $sBaseUri;
	private $sPage;
	private $oPageParams;

	public function __construct($https = false) {
		$root = mb_strrchr($_SERVER['PHP_SELF'], '/', true);
		
		if(isset($_SERVER['HTTPS']) || $https)
			$prefix = 'https://';
		else
			$prefix = 'http://';

		$this->setBaseUri($prefix.$_SERVER['HTTP_HOST'].$root);

		$aPageParams = explode('/', $_SERVER['QUERY_STRING'], 2048);
		$this->setPage(array_shift($aPageParams));

		$tmpParamsObj = new stdClass();
		while(count($aPageParams)) {
			$sKey = strip_tags(rawurldecode(array_shift($aPageParams)));
			$sValue = strip_tags(rawurldecode(array_shift($aPageParams)));
				
			if(empty($sKey))
			continue;
				
			if(strstr($sValue, ','))
			$sValue = explode(',', $sValue, 2048);
				
			$tmpParamsObj->{$sKey} = $sValue;
		}

		$this->setPageParams($tmpParamsObj);
	}

	public function setParam($key, $value) {
		$this->oPageParams->{$key} = $value;
	}

	public function getParam($key) {
		return $this->oPageParams->{$key};
	}

	public function clearParam($key) {
		unset($this->oPageParams->{$key});
	}

	public function getParamString($key) {
		$oValue = $this->oPageParams->{$key};

		if(is_array($oValue))
		$oValue = implode(',', $oValue);
			
		return $oValue;
	}

	public function buildUrlString() {
		$sParamString = '/'.strip_tags(rawurlencode($this->sPage));

		if(!empty($this->oPageParams)) {
			foreach($this->oPageParams as $sKey => $oValue) {
				if($oValue == null)
				continue;
					
				if(is_array($oValue))
				$oValue = rawurlencode(implode(',', $oValue));
				else
				$oValue = rawurlencode($oValue);
					
				$sParamString .= '/'.strip_tags(rawurlencode($sKey)).'/'.strip_tags($oValue);
			}
		}

		return $this->sBaseUri.$sParamString;
	}

	public function buildPageUrlString() {
		return $this->sBaseUri.'/'.rawurlencode($this->sPage);
	}

	public function setBaseUri($sUri) {
		$this->sBaseUri = $sUri;
	}

	public function getBaseUri() {
		return $this->sBaseUri;
	}

	public function setPage($sName) {
		if(empty($sName))
		$this->clearPageParams();
			
		$this->sPage = $sName;
	}

	public function getPage() {
		return $this->sPage;
	}

	public function setPageParams(stdClass $oParams) {
		$this->oPageParams = $oParams;
	}

	public function getPageParams() {
		return $this->oPageParams;
	}

	public function clearPageParams() {
		$this->setPageParams(new stdClass());
	}

	public function clear() {
		$this->setPage();
	}
}
?>