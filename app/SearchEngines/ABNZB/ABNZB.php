<?php


namespace ServerMenu\SearchEngines\ABNZB;


use ServerMenu\SearchEngine;
use ServerMenu\Utility;
use ServerMenu\Receiver;

class ABNZB extends SearchEngine
{

	use Receiver;

	const NEWZNAB_API_URL = 'https://abnzb.com/api';

	public function receiveContent($receiverType, $content)
	{
		return $this->getTemplateData($content);
	}

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

		$url = self::NEWZNAB_API_URL
			. '?apikey=' . $this->config['api_key']
			. '&t=search&q=' . urlencode($searchQuery)
			. '&o=json';

		$data = json_decode(Utility::cacheGet($url));

		$items = $data->channel->item;

		$results = array();

		foreach ($items as $item) {
			$attrs = [];
			foreach ($item->attr as $attr) {
				if (in_array($attr->{"@attributes"}->name,
						['size', 'oz_spam_confirmed', 'oz_passworded_confirmed', 'oz_up_votes', 'oz_down_votes'])) {
					$attrs[$attr->{"@attributes"}->name] = $attr->{"@attributes"}->value;
				}
			}

			$results[] = array(
				"title" => $item->title,
				"subtitle" => $item->category,
				"size" => Utility::bytes2human($attrs['size'], 1, false),
				"link" => $item->link,
				"date" => Utility::time2relative($item->pubDate),
				"actions" => array(
					array(
						"pluginType" => "Services",
						"receiverType" => 'nzb',
						"content" => $item->link,
						"glyphicon" => "download",
						"title" => "Download"
					),
				)
			);
		}

		return array('results' => $results);

	}

}
