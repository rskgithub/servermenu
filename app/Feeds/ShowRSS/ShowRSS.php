<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 26/10/14
 * Time: 08:29
 */

namespace ServerMenu\Feeds\ShowRSS;


use ServerMenu\Feed;
use ServerMenu\Utility;

class ShowRSS extends Feed {

        /**
         * @param int $amount
         *
         * @return array
         */
        public function getTemplateData($amount = self::DEFAULT_AMOUNT)
        {
                $pie = Utility::get_simplepie("http://showrss.info/feeds/all.rss");

                $items = $pie->get_items(0, $amount);
                $results = array();
                foreach ($items as $item) {
	                if (isset($item->get_item_tags('http://showrss.info/', 'rawtitle')[0]['data'])) {
		                $title = trim($item->get_item_tags('http://showrss.info/', 'rawtitle')[0]['data']);
	                } else {
		                $title = trim($item->get_title());
	                }

                        $magnet = $item->get_link();
	                $subtitle = $item->get_date();

	                $title = preg_replace('/([0-9]+p)/', '<b class=hd>${1}</b>', $title);

                        $results[] = array(
                                "title" => $title,
                                "subtitle" => (isset($subtitle) ? $subtitle : ''),
                                "size" => false,
                                "link" => $item->get_link(),
                                "date" => false,
                                "actions" => array(
                                        array(
                                                "pluginType" => "Services",
                                                "receiverType" => 'magnet',
                                                "content" => $magnet,
                                                "glyphicon" => "download",
                                                "title" => "Download"
                                        ),
                                        array(
                                                "pluginType" => "SearchEngines",
                                                "receiverType" => 'search',
                                                "content" => $title,
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