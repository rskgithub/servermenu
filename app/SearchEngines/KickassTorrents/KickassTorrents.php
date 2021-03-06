<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 30/10/14
 * Time: 21:45
 */

namespace ServerMenu\SearchEngines\KickassTorrents;


use ServerMenu\Receiver;
use ServerMenu\SearchEngine;
use ServerMenu\Utility;


class KickassTorrents extends SearchEngine {

	use Receiver;

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
		$url = "https://kat.cr";
		$urlenc_term = urlencode($searchQuery);
		$raw = null;
		$context = ['http' => ['timeout' => 5]];
		$context = stream_context_create($context);
		$raw = file_get_contents("$url/usearch/$urlenc_term/?field=seeders&sorder=desc", false, $context);

		$html = new \simple_html_dom();
		$html->load(gzdecode($raw));

		$results = array();

		foreach($html->find('table.data tbody tr') as $row) {
			$title = $row->find("td",0);
			$size = $row->find("td",1);
			if (!isset($size))
				continue;
			
			$date = html_entity_decode($row->find("td",3)->plaintext);

			$results[] = array(
				'title' => $title->find("a[class=cellMainLink]", -1)->plaintext,
				'link' => $url . $title->find("a", 0)->attr['href'],
				'subtitle' => " – S: {$row->find("td",4)->plaintext} L: {$row->find("td",5)->plaintext}",
				'size' => $size->innertext,
				'date' => $date,
				'actions' => array(
					array(
						'pluginType' => 'Services',
						'receiverType' => 'magnet',
						'content' => $title->find("a", 3)->attr['href'],
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
