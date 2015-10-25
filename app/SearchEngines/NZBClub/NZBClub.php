<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 28/10/14
 * Time: 20.21
 */

namespace ServerMenu\SearchEngines\NZBClub;


use ServerMenu\Receiver;
use ServerMenu\SearchEngine;
use ServerMenu\Utility;

class NZBClub extends SearchEngine {

	use Receiver;

	/**
	 * Return array with search results and Service senders.
	 *
	 * @param string $searchQuery
	 * @param int $amount
	 * @param int $beginAt
	 * @return mixed
	 */
	public function getTemplateData($searchQuery, $amount = self::DEFAULT_AMOUNT, $beginAt = 0)
	{
		$urlenc_term = urlencode($searchQuery);

		$pie = Utility::get_simplepie("http://www.nzbclub.com/nzbfeeds.aspx?q=$urlenc_term&ig=2&de=27&szs=14&st=1&ns=1");

		$items = $pie->get_items($beginAt, ($beginAt+$amount));
		$results = array();

		foreach ($items as $item) {
			$results[] = array(
				"title" => $item->get_title(),
				"subtitle" => substr($item->get_description(), 6, strpos($item->get_description(), "files")-1),
				"size" => false,
				"link" => $item->get_link(),
				"date" => $item->get_date(),
				"actions" => array(
					array(
						"pluginType" => "Services",
						"receiverType" => 'nzb',
						"content" => $item->get_enclosures()[0]->link,
						"glyphicon" => "download",
						"title" => "Download"
					),
				)
			);
		}

		return array('results'=>$results);
	}

	/**
	 * The plugin must implement this method to safely be able to
	 * receive content.
	 *
	 * @param $receiverType
	 * @param $content
	 * @return mixed
	 */
	public function receiveContent($receiverType, $content)
	{
		return $this->getTemplateData($content);
	}


} 