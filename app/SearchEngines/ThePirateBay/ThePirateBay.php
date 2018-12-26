<?php
namespace ServerMenu\SearchEngines\ThePirateBay;

use ServerMenu\Receiver;
use ServerMenu\SearchEngine;
use ServerMenu\Utility;

class ThePirateBay extends SearchEngine {

	use Receiver;

	/**
	 * @return array
	 */
	public function getReceiverTypes()
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
		$raw = Utility::cacheGet("https://thepiratebay.org/search/$urlenc_term/0/7/0");

		$html = new \simple_html_dom();
		$html->load($raw);

		$results = array();

		foreach($html->find('table#searchResult tbody tr') as $row) {
			$details = $row->find("td",1);
			if (!isset($details))
				continue;

			if (!empty($details->children[0]->children[0]->attr['title']))
				$text = trim($details->plaintext);
			preg_match('/(?\'title\'.*)\sUploaded(?\'date\'.*),\sSize(?\'size\'.*),\sULed\sby/', $text, $matches);

			$title = str_replace('.', ' ', $matches['title']);

			$results[] = array(
				'title' => $title,
				'link' => $details->children[1]->attr['href'],
				'subtitle' => "S: {$row->find("td",2)->plaintext} L: {$row->find("td",3)->plaintext}",
				'size' => html_entity_decode($matches['size']),
				'date' => html_entity_decode($matches['date']),
				'actions' => array(
					array(
						'pluginType' => 'Services',
						'receiverType' => 'magnet',
						'content' => $details->children[1]->attr['href'],
						'glyphicon' => 'download',
						'title' => 'Download'
					)
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