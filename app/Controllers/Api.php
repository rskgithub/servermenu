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

class Api extends Controller {

	public function getListPlugins($type) {
		if (empty($config[$type]) || !isset($config[$type])) {
			$this->app->notFound();
			return;
		}

		$this->app->render(200, array(
			$type => array_keys($config[$type]),
		));
	}

	public function getListReceivers($pluginType, $receiverType) {
		$plugins = \ServerMenu\PluginLoader::getReceivers($pluginType, $receiverType);
		$this->app->render(200, $plugins);
	}

	public function postSend($pluginType, $pluginId) {
		if (!$plugin = \ServerMenu\PluginLoader::getPlugin($pluginType, $pluginId))
			return $this->app->notFound();

		$result = $plugin->receive($_POST['receivertype'], $_POST['content']);

		$this->app->render(200, array('result' => $result));
	}

	public function getSearch($pluginId, $amount, $beginAt, $searchQuery) {
		/* @var $plugin \ServerMenu\SearchEngine */
		if (!$plugin = \ServerMenu\PluginLoader::getPlugin('SearchEngines', $pluginId))
			return $this->app->notFound();

		$result = $plugin->getTemplateData($searchQuery, $amount, $beginAt);

		$this->app->render(200, array('result' => $result));
	}

}