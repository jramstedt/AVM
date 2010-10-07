<?php
class Menu implements IModule {
	private $kernel;
	
	public function __construct(Kernel $kernel) {
		$this->kernel = $kernel;

		if(Util::isAjaxRequest())
			return;
		
		if(Sessionmanager::isLogged())
		{
			$menuUrl = new Url();

			$menuUrl->clearPageParams();
			$menuUrl->setPage('Mainpage');
			$this->kernel->addMenuItem($menuUrl, 'Mainpage');
				
			$menuUrl->clearPageParams();
			$menuUrl->setPage('Movies');
			$this->kernel->addMenuItem($menuUrl, 'Movies');
				
			$menuUrl->clearPageParams();
			$menuUrl->setPage('Series');
			$this->kernel->addMenuItem($menuUrl, 'Series');
			
			$menuUrl->clearPageParams();
			$menuUrl->setPage('Unhandled');
			$this->kernel->addMenuItem($menuUrl, 'Unhandled');
			
			$menuUrl->clearPageParams();
			$menuUrl->setPage('Seeking');
			$this->kernel->addMenuItem($menuUrl, 'Seeking');
			
			$menuUrl->clearPageParams();
			$menuUrl->setPage('Login');
			$menuUrl->setParam('logout', 1);
			$this->kernel->addMenuItem($menuUrl, 'Logout');
		}
	}
}