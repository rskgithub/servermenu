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

        private static $plugins, $receivers;

        /**
         * @param $pluginType
         * @param $pluginId
         *
         * @return Object
         * @throws \Exception
         */
        public static function fetch($pluginType, $pluginId) {
                if (!isset(self::$plugins)) {
                        self::$plugins = array();
                } elseif (isset(self::$plugins[$pluginType][$pluginId])) {
                        return self::$plugins[$pluginType][$pluginId];
                }

                $config = Slim::getInstance()->config('s')[$pluginType.'s'][$pluginId];
                $name = $config['plugin'];
                $class = ucfirst($name);

                if (file_exists(__DIR__.'/'.$pluginType.'s/'.$class.'/'.$class.'.php')) {
                        $className = "\\ServerMenu\\{$pluginType}s\\$class\\$class";

                        self::$plugins[$pluginType][$pluginId] = new $className($config, $pluginId);
                        return self::$plugins[$pluginType][$pluginId];
                } else {
                        throw new \Exception('Plugin not found');
                }

        }

        public static function getReceivers($pluginType, $receiverType) {
                if (!isset(self::$receivers)) {
                        self::$receivers[$pluginType][$receiverType] = array();
                } else {
                        if (isset(self::$receivers[$pluginType][$receiverType]))
                                return self::$receivers[$pluginType][$receiverType];
                }

                $pluginType = $pluginType.'s';
                $pluginClass = ucfirst($pluginType);

                $config = Slim::getInstance()->config('s');
                $config = $config[$pluginType];

                foreach ($config as $pluginId => $pluginConfig) {
                        $name = $pluginConfig['plugin'];

                        if (file_exists(__DIR__.'/'.$pluginType.'/'.$name.'/'.$name.'.php')) {
                                $className = "\\ServerMenu\\{$pluginType}\\$name\\$name";
                                $classInstance = new $className($pluginConfig, $pluginId);

                                if (isset($classInstance->receiverTypes) && (in_array($receiverType, $classInstance->receiverTypes))) {
                                        self::$receivers[$pluginType][$receiverType][] = array(
                                                'pluginId' => $pluginId,
                                                'plugin'   => $name
                                        );
                                }

                        } else {
                                throw new \Exception('Plugin not found');
                        }
                }

                return array("plugins"=>self::$receivers[$pluginType][$receiverType]);
        }

} 