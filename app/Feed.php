<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 26/10/14
 * Time: 08:11
 */

namespace ServerMenu;


abstract class Feed extends PluginBase {

        const DEFAULT_AMOUNT = 30;

	public $template = 'feed.html.twig';

	/**
	 * Return array with feed contents and Service senders
	 *
	 * @param int $amount
	 * @return array
	 */
	public abstract function getTemplateData($amount = self::DEFAULT_AMOUNT);

}