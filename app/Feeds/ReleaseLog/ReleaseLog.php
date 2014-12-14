<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 30/10/14
 * Time: 20:03
 */

namespace ServerMenu\Feeds\ReleaseLog;


use ServerMenu\Feed;
use ServerMenu\Utility;

class ReleaseLog extends Feed {

	/**
	 * Return array with feed contents and Service senders
	 *
	 * @param int $amount
	 * @return array
	 */
	public function getTemplateData($amount = self::DEFAULT_AMOUNT)
	{
		$pie = Utility::get_simplepie("http://www.rlslog.net/category/movies/bdrip/feed/");

		$items = $pie->get_items(0, $amount);
		$results = array();

		foreach ($items as $item) {
			if (stristr($item->get_title(), '300 GB'))
				continue;

			$matches = false;
			preg_match_all('/[i|I]MDB Rating:.*([0-9]+[\.,]+[0-9]+\/10)/', $item->get_content(), $matches);
			$rating = '';
			if (isset($matches[1][0]))
				$rating = $matches[1][0];

			$matches = false;
			preg_match_all('/(www\.imdb\.com\/title\/tt\d+\/)/', $item->get_content(), $matches);
			$imdblink = '';
			if (isset($matches[0][0])) $imdblink = "http://{$matches[0][0]}";

			$results[] = array(
				"title" => $item->get_title(),
				"subtitle" => "IMDB Rating: $rating",
				"size" => false,
				"link" => $imdblink,
				"date" => $item->get_date(),
				"actions" => array(
					array(
						"pluginType" => "SearchEngines",
						"receiverType" => 'search',
						"content" => $item->get_title(),
						"glyphicon" => "search",
						"title" => "Search"
					)
				)
			);

		}

		return array(
			"results" => $results
		);

	}


} 