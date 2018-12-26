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
	
	private function getHtml($searchQuery, $amount, $beginAt)
	{
		$url = 'http://www.nzbclub.com/search.aspx?q='.$searchQuery.'+-german+-french+-dutch&st=5&sp=1';

		$raw = Utility::cacheGet($url);
				
		$html = new \simple_html_dom();
		$html->load($raw);

		$results = array();

		foreach($html->find('div.media') as $row) {
			$id = $row->find('.project-action',0)->collectionid;
			
			if (!$row->find('a.text-primary', 0)) continue;
			
			$results[] = [
				'title' => $row->find('a.text-primary', 0)->plaintext,
				'size' => explode(PHP_EOL, $row->find('div.col-xs-2', 0)->plaintext)[0],
				'link' => 'http://www.nzbclub.com'.$row->find('a', 0)->href,
				'date' => explode(PHP_EOL, $row->find('div.col-xs-2', 1)->plaintext)[0],
				'actions' => [
					[
						"pluginType" => "Services",
						"receiverType" => 'nzb',
						"content" => 'http://www.nzbclub.com/nzb_get/'.$id,
						"glyphicon" => "download",
						"title" => "Download"
					]
				]
			];
		}
		
		return $results;

	}
	
	private function getRss($searchQuery, $amount, $beginAt)
	{
		//$pie = Utility::get_simplepie("http://www.nzbclub.com/nzbfeeds.aspx?q=$urlenc_term&ig=2&de=27&szs=14&st=1&ns=1");
		$pie = Utility::get_simplepie("http://www.nzbclub.com/nzbrss.aspx?q=$searchQuery&ig=2&st=5&ns=1");

		$items = $pie->get_items($beginAt, ($beginAt+$amount));
		$results = array();
		
		echo "<pre>";
		print_r(count($items));
		echo "</pre>";

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

		return $results;
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
		return ['results' => $this->getHtml(urlencode($searchQuery), $amount, $beginAt)];
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