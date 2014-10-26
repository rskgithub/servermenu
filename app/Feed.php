<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 26/10/14
 * Time: 08:11
 */

namespace ServerMenu;


abstract class Feed extends Plugin {

        const TYPE_MAGNET = 1;
        const TYPE_TORRENT = 2;
        const TYPE_NZB = 3;

        const DEFAULT_AMOUNT = 30;

        /**
         * @return array
         */
        public abstract function supportedClients();

        /**
         * @param int $amount
         *
         * @return array
         */
        protected abstract function get($amount = self::DEFAULT_AMOUNT);

        /**
         * @return array
         */
        public function getTemplateData()
        {
                return array(
                        'data' => 'val'
                );
        }

} 