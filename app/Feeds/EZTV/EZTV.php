<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 26/10/14
 * Time: 08:29
 */

namespace ServerMenu\Feeds\EZTV;


use ServerMenu\Feed;

class EZTV extends Feed {
        /**
         * @return array
         */
        public function supportedClients()
        {
                return array(Feed::TYPE_MAGNET, Feed::TYPE_TORRENT);
        }

        /**
         * @param int $amount
         *
         * @return array
         */
        public function get($amount = self::DEFAULT_AMOUNT)
        {
                $pie = new \SimplePie();

                $pie->set_feed_url("http://ezrss.it/feed");
        }


}