<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 26/10/14
 * Time: 06:14
 */

namespace ServerMenu;


use Slim\Slim;

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
        public static function fetch($type, $id) {
                if (!isset(self::$plugins)) {
                        self::$plugins = array();
                } elseif (isset(self::$plugins[$type][$id])) {
                        return self::$plugins[$type][$id];
                }

                $config = Slim::getInstance()->config('s')[$type.'s'][$id];
                $name = $config['plugin'];
                $class = ucfirst($name);

                if (file_exists(__DIR__.'/'.$type.'s/'.$class.'/'.$class.'.php')) {
                        $className = "\\ServerMenu\\{$type}s\\$class\\$class";

                        self::$plugins[$type][$name] = new $className($config, $id);
                        return self::$plugins[$type][$name];
                } else {
                        throw new \Exception('Plugin not found');
                }
        }

} 