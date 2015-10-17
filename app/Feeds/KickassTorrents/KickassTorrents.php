<?php

namespace ServerMenu\Feeds\KickassTorrents;

use ServerMenu\Feed;
use ServerMenu\Utility;

class KickassTorrents extends Feed {

	/**
	 * Return array with feed contents and Service senders
	 *
	 * @param int $amount
	 * @return array
	 */
	public function getTemplateData($amount = self::DEFAULT_AMOUNT)
	{
		if (isset($this->config['category']) && in_array())
			$category = $this->config['category'];
		else {
			// Default category
			$category = "movies";
		}
		
		$pie = Utility::get_simplepie("https://kat.cr/$category/1?field=seeders&sorder=desc&rss=1");

		$items = $pie->get_items(0, $amount);
		$results = array();

		$i = 0;

		/** @var \SimplePie_Item $item */
		foreach ($items as $item) {
			$i++;
			$magnetURI = $item->get_item_tags('http://xmlns.ezrss.it/0.1/', 'magnetURI')[0]['data'];
			$seeds = $item->get_item_tags('http://xmlns.ezrss.it/0.1/', 'seeds')[0]['data'];
			$peers = $item->get_item_tags('http://xmlns.ezrss.it/0.1/', 'peers')[0]['data'];
			
			$results[$seeds.'-'.$i] = [
				"title"	=> $item->get_title(),
				"subtitle" => $item->get_date() . " – Seeds: $seeds, peers: $peers",
				"size" => \ServerMenu\Utility::bytes2human($items[0]->get_enclosure()->length),
				"link" => $item->get_link(),
				"date" => $item->get_date(),
				"actions" => [
					[
						'pluginType' => 'Services',
						'receiverType' => 'magnet',
						'content' => $magnetURI,
						'glyphicon' => 'download',
						'title' => 'Download'
					]
				]
			];
		}
		
		// Sort by seeders
		krsort($results);
		
		return array(
			"results" => $results
		);

	}


} 