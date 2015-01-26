<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 26/01/15
 * Time: 14:41
 */

namespace ServerMenu;


use Slim\Slim;

class Controller {

	public function __construct($app = null) {
		$this->app = ($app instanceof Slim) ? $app : Slim::getInstance();
		$this->init();
	}

	public function init() {}

}