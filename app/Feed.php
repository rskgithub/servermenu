<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 26/10/14
 * Time: 08:11
 */

namespace ServerMenu;


abstract class Feed {

        const TYPE_MAGNET = 1;
        const TYPE_TORRENT = 2;
        const TYPE_NZB = 3;

        const DEFAULT_AMOUNT = 30;

        abstract function getTemplateData();

}