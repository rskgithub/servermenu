<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 26/10/14
 * Time: 06:14
 */

namespace ServerMenu;


class PluginLoader {

        private static $plugins;

        /**
         * @param $type
         * @param $name
         * @param $config
         * @param $id
         *
         * @return Object
         * @throws \Exception
         */
        public static function fetch($type, $name, $config, $id) {
                if (!isset(self::$plugins)) {
                        self::$plugins = array();
                } elseif (isset(self::$plugins[$type][$name])) {
                        return self::$plugins[$type][$name];
                }

                if (file_exists(__DIR__.'/'.$type.'s/'.$name.'/'.$name.'.php')) {
                        $className = "\\ServerMenu\\{$type}s\\$name\\$name";
                        self::$plugins[$type][$name] = new $className($config, $id);
                        return self::$plugins[$type][$name];
                } else {
                        throw new \Exception('Plugin not found');
                }
        }

} 