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

class Ajax extends Controller {

	public function getService($serviceType, $serviceId) {
		$service = \ServerMenu\PluginLoader::getPlugin($serviceType, $serviceId);

		if ($service instanceof \ServerMenu\Service)
			$service->fetchData();

		// Render Service HTML
		$this->app->render($service->template, $service->getTemplateData(), 200);
	}

	public function getSearch($pluginId, $query) {
		if (!$plugin = \ServerMenu\PluginLoader::getPlugin('SearchEngines', $pluginId))
			$this->app->notFound();

		// Render Service HTML
		$this->app->render($plugin->template, $plugin->getTemplateData($query), 200);
	}

	public function get($id)
	{
		if (method_exists($this, 'get'.ucfirst($id))) {
			return $this->{'get'.ucfirst($id)}();
		} else {
			$this->app->notFound();
		}
	}

	public function getFileList()
	{
		$data = \ServerMenu\FileList::get();

		$this->app->render('filelist.html.twig', ['FileList' => $data], 200);
	}

	public function getDiskSpace()
	{
		echo \ServerMenu\Utility::getFreeDiskSpace() . ' free';
	}

}