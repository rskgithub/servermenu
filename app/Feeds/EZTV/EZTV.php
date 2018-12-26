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
                $pie = Utility::get_simplepie("https://eztv.ag/ezrss.xml");

                $items = $pie->get_items(0, $amount);
                $results = array();
                foreach ($items as $item) {
                        $title = trim($item->get_title());

                        preg_match("/(.*)(\[.*\])/", $title, $preg_title);
                        if (isset($preg_title[1])) {
                                $title = $preg_title[1];
                                $subtitle = $preg_title[2];

                        }
                                                
                        $content = $item->get_enclosure()->link;

                        $results[] = array(
                                "title" => $title,
                                "subtitle" => (isset($subtitle) ? $subtitle : ''),
                                "size" => false,
                                "link" => $item->get_link(),
                                "date" => Utility::time2relative($item->get_date()),
                                "actions" => array(
                                        array(
                                                "pluginType" => "Services",
                                                "receiverType" => 'magnet',
                                                "content" => $content,
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