<?php


namespace ServerMenu\SearchEngines\DrunkenSlug;


use ServerMenu\SearchEngine;
use ServerMenu\Utility;
use ServerMenu\Receiver;

class DrunkenSlug extends SearchEngine
{

	use Receiver;

	const NEWZNAB_API_URL = 'https://api.drunkenslug.com/api';

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

		
		$items = $data->item;

		$results = array();

		foreach ($items as $item) {
			$attrs = [];
			foreach ($item->{"newznab:attr"} as $attr) {
				if (in_array($attr->{'_name'}, ['size'])) {
					$attrs[$attr->{'_name'}] = $attr->{'_value'};
				}
			}
			
      
      preg_match_all('/.*\/details\/([[:xdigit:]]{31,}).*/', $item->guid->text, $matches, PREG_SET_ORDER, 0);

			
					$url = self::NEWZNAB_API_URL
			. '?apikey=' . $this->config['api_key']
			. '&t=details&id=' . '2bbd7fc873436defd8cf6387733f0668054a66ff'
						. '&o=xml';

      $data = (Utility::cacheGet($url));
			
			//echo '<pre>'; var_dump($data); die();

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
