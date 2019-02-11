<?php

	/*
		trait for rendering HTML pages
	*/

	trait Templater
	{

		/*
			Proority of definition variable data (from low to hight):

			1. Data from config files

			2. Data from common controller array

			2. Data sending to render function
		*/

		public function render(
			string $template = 'main',
			array $dataParams = [],
			int $ttl = 0
		) : void
		{
			if (!isset($this->_templateDir)) {
				throw new Exception('Variable _templateDir is not set');
			}

			$GLOBALS['templateDir'] = $this->_templateDir;

			if (!isset($GLOBALS['templateParams'])) {
				$GLOBALS['templateParams'] = [];
			}

			foreach ($this->configData['main'] as $idx => $configItem) {
				$GLOBALS['templateParams'][$idx] = $configItem;
			}

			foreach ($this->commonData as $idx => $commonItem) {
				$GLOBALS['templateParams'][$idx] = $commonItem;
			}
	
			foreach ($dataParams as $idx => $dataParamItem) {
				$GLOBALS['templateParams'][$idx] = $dataParamItem;
			}

			$GLOBALS['templateScope'] = $this->templateScope;
			$GLOBALS['templateTTL'] = $ttl;
			$template = strlen($template)>0?$template:'main';

			if ($ttl>0) {

				$currSlug = $_SERVER['REQUEST_URI'];
				$currSlug = str_replace('/','_', $currSlug);
				$currSlug = preg_replace('/(^_)|(_$)/su','',$currSlug);
				$tplCacheDir = $this->_templateDir.$currSlug;

				if(!is_dir($tplCacheDir)){
					mkdir($tplCacheDir);
					chmod($tplCacheDir,0775);
				}

				$tplCacheDir = $tplCacheDir.'/'.$this->templateScope;
				if(!is_dir($tplCacheDir)){
					mkdir($tplCacheDir);
					chmod($tplCacheDir,0775);
				}

				$tplCacheDir = "{$tplCacheDir}/{$template}";
				if(!is_dir($tplCacheDir)){
					mkdir($tplCacheDir);
					chmod($tplCacheDir,0775);
				}

				$GLOBALS['templateCacheDir'] = $tplCacheDir;
			}

			foreach($GLOBALS['templateParams'] as $param => $value) {
				$$param = $value;
			}

			include_once(
				$this->_templateDir.$this->templateScope.'/index.tpl'
			);

			if(!$this->pageCache){
				die();
			}
		}
	}
?>