<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 26/01/15
 * Time: 14:32
 */

namespace ServerMenu\Controllers;

use ServerMenu\Controller;
use ServerMenu\Utility;

class Config extends Controller {

	public function getIndex()
	{
		$this->app->render('config_index.html.twig', [
			'themes' => $this->getThemeList()
		]);
	}
	
	private function getThemeList()
	{
		$url = 'https://bootswatch.com/api/3.json';
		
		$themeData = Utility::cacheGet($url, 60 * 24 * 30);
		$themeData = json_decode($themeData);
		
		$themeList = [
			'default' => '',
			'Cyborg' => 'cyborg'
		];
		
		foreach ($themeData->themes as $theme) {
			$themeList[$theme->name] = strtolower($theme->name);
		}
		
		return $themeList;
	}
	
}