<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 26/10/14
 * Time: 08:29
 */

namespace ServerMenu\Feeds\EZTV;


use ServerMenu\Feed;
use ServerMenu\Utility;

class EZTV extends Feed {

        /**
         * @param int $amount
         *
         * @return array
         */
        public function getTemplateData($amount = self::DEFAULT_AMOUNT)
        {
                $pie = Utility::get_simplepie("http://ezrss.it/feed");

                $items = $pie->get_items(0, $amount);
                $results = array();
                foreach ($items as $item) {
                        $title = trim($item->get_title());
                        preg_match("/(.*)(\[.*\])/", $title, $preg_title);
                        if (isset($preg_title[1])) {
                                $title = $preg_title[1];
                                $subtitle = $preg_title[2];

                        }
                        $magnet = $item->get_item_tags("http://xmlns.ezrss.it/0.1/","torrent")[0]["child"]["http://xmlns.ezrss.it/0.1/"]["magnetURI"][0]["data"];

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