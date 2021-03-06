<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 28/10/14
 * Time: 20.21
 */

namespace ServerMenu;


abstract class SearchEngine extends PluginBase {

	/**
	 *
	 */
	const DEFAULT_AMOUNT = 30;

	/**
	 * The twig template to use for the plugin
	 *
	 * @var string
	 */
	public $template = 'feed.html.twig';

	/**
	 * @return array
	 */
	public function getReceiverTypes()
	{
		return array('search');
	}

	/**
	 * Return array with search results and Service senders.
	 *
	 * @param string $searchQuery
	 * @param int $amount
	 * @param int $beginAt
	 * @return mixed
	 */
	abstract public function getTemplateData($searchQuery, $amount = self::DEFAULT_AMOUNT, $beginAt = 0);

}