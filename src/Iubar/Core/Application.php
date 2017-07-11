<?php

namespace Iubar\Core;

class Application extends \Slim\Slim {

	public function __construct(){
		parent::__construct();
		$this->loadConfig();
	}

	private function loadConfig(){
		$config = __DIR__ . '/../../config/app.php';
		if (file_exists($config)){
			foreach ($config as $key => $value){
				$this->config($key, $value);
			}
		} else {
			throw new \Exception('Config file not found');
		}
	}

}