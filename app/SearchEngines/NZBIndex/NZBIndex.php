<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 28/10/14
 * Time: 20.21
 */

namespace ServerMenu\SearchEngines\NZBIndex;


use ServerMenu\Receiver;
use ServerMenu\SearchEngine;
use ServerMenu\Utility;

class NZBIndex extends SearchEngine {

	use Receiver;

	/**
	 * @return array
	 */
	protected function getReceiverTypes()
	{
		return array("search");
	}

	/**
	 * Return array with search results and Service senders.
	 *
	 * @param string $searchQuery
	 * @param int $amount
	 * @param int $beginAt
	 * @throws \Exception
	 * @return mixed
	 */
	public function getTemplateData($searchQuery, $amount = self::DEFAULT_AMOUNT, $beginAt = 0)
	{
		$urlenc_term = urlencode($searchQuery);

		$pie = Utility::get_simplepie("http://www.nzbindex.nl/rss/?q=$urlenc_term&sort=agedesc&minsize=50&complete=1&max=25&more=1'");

		$items = $pie->get_items($beginAt, ($beginAt+$amount));
		$results = array();

		error_log("test");

		foreach ($items as $item) {
			error_log("parsing: ".print_r($item, true));

			$description = preg_match_all(
				'/^<p>(.*)<br><b>([0-9]+\.*[0-9]* (?:MB|GB))<\/b><br>.*\s([0-9]+\.*[0-9]*)+/',
				$item->get_description(),
				$description_matches
			);

			if ($description == 0 || $description == false)
				throw new \Exception("No results found");

			$results[] = array(
				'title' => $item->get_title(),
				'subtitle' =>
					$description_matches[1][0].' &bull; '
					.$description_matches[2][0].' &bull; '
					.$description_matches[3][0]. ' days',
				"size" => false,
				'link' => $item->get_link(),
				"date" => $item->get_date(),
				"actions" => array(
					array(
						"pluginType" => "service",
						"receiverType" => 'nzb',
						"content" => $item->get_link(),
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