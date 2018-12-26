<?php
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

                        $results[] = array(
                                "title" => $title,
                                "subtitle" => '',
                                "size" => false,
                                "link" => $item->get_link(),
                                "date" => Utility::time2relative($item->get_date()),
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