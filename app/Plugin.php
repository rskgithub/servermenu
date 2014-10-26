<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 26/10/14
 * Time: 10:09
 */

namespace ServerMenu;


abstract class Plugin {

        /**
         * @return array
         */
        public abstract function getTemplateData();

} 