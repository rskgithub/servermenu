<?php

namespace ServerMenu\Feeds\ThePirateBay;


use ServerMenu\Feed;
use ServerMenu\Utility as Utility;

class ThePirateBay extends Feed
{
	// Mapping of TPB internal category IDs to more user friendly strings
	private $categories = [
		'all' => 'all',
		'audio' => 100,
		'video' => 200,
		'video_hd_movies' => 207,
		'video_hd_tv' => 208,
		'apps' => 300,
		'games' => 400,
		'xxx' => 500,
		'other' => 600,
		'books' => 601,
	];

	private $domain = 'https://thepiratebay.org';

	/**
	 * @param int $amount
	 *
	 * @return array
	 */
	public function getTemplateData($amount = self::DEFAULT_AMOUNT)
	{
		$category = $this->categories['all'];

		if (isset($this->config['category'])) {
			$category = $this->categories[$this->config['category']];
		}

		return $this->getTpbRss($category, $amount);
	}

	public function getTpbRss($category, $amount = self::DEFAULT_AMOUNT)
	{
		$url = $this->domain . '/top/' . $category;

		$content = Utility::cacheGet($url);
		if (preg_match('/<table id="searchResult">.*?<\/table>/is', $content, $table)) {
			// Stolen from https://pastebin.com/1JmZUcTt
			preg_match_all(
				'/<div class="detName">.*?<a href="(.*?)".*?>(.*?)<\/a>.*?<a href="magnet:([^"]*).*?Uploaded (.{1,20}), Size (.{1,30}),.*?class="detDesc".*?>(.*?)<\/a>.*?<td .*?>([0-9]{1,8}).*?<td .*?>([0-9]{1,8})/is',
				$table[0],
				$matches,
				PREG_SET_ORDER
			);
		}
		unset($content);

		$results = [];

		foreach ($matches as $result) {
			$results[] = array(
				'title' => $result[2],
				'link' => $this->domain . $result[1],
				'subtitle' => '',
				'size' => html_entity_decode($result[5]),
				'date' => html_entity_decode($result[4]),
				'actions' => array(
					array(
						'pluginType' => 'Services',
						'receiverType' => 'magnet',
						'content' => $result[3],
						'glyphicon' => 'download',
						'title' => 'Download'
					),
					array(
						"pluginType" => "SearchEngines",
						"receiverType" => 'search',
						"content" => $result[2],
						"glyphicon" => "search",
						"title" => "Search"
					)
				)
			);
		}

		return ['results' => $results];
	}
}
