<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 21/11/14
 * Time: 20.09
 */

namespace ServerMenu;


class PluginBase {

	/**
	 * Will be populated with user configuration of the Service.
	 *
	 * @var
	 */
	public $config;

	/**
	 * Will be populated with the Service ID.
	 *
	 * @var
	 */
	protected $serviceId;

	public function __construct($config, $serviceId)
	{
		$this->config = $config;
		$this->serviceId = $serviceId;
	}
}
