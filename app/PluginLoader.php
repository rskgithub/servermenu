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
	 * Fetch a certain plugin (Service, SearchEngine or Feed)
	 *
	 * @param string $pluginType
	 * @param int $pluginId
	 *
	 * @return Object
	 * @throws \Exception
	 */
	public static function getPlugin($pluginType, $pluginId)
	{
		if (!isset(self::$plugins)) {
			self::$plugins = array();
		} elseif (isset(self::$plugins[$pluginType][$pluginId])) {
			return self::$plugins[$pluginType][$pluginId];
		}

		$config = Slim::getInstance()->config('s');
		$pluginConfig = $config[$pluginType][$pluginId];
		$plugin = $pluginConfig['plugin'];

		$class = "\\ServerMenu\\$pluginType\\$plugin\\$plugin";

		if (class_exists($class)) {
			self::$plugins[$pluginType][$pluginId] = new $class($pluginConfig, $pluginId);
			return self::$plugins[$pluginType][$pluginId];
		} else {
			throw new \Exception('Plugin not found: ' . $pluginType . ' ID ' . $pluginId);
		}

	}

	/**
	 * Returns plugins that can receive certain "receiverTypes".
	 *
	 * @param string $pluginType
	 * @param int $receiverType
	 * @return array
	 * @throws \Exception
	 */
	public static function getReceivers($pluginType, $receiverType) {
                if (!isset(self::$receivers)) {
                        self::$receivers[$pluginType][$receiverType] = array();
                } else {
                        if (isset(self::$receivers[$pluginType][$receiverType]))
                                return self::$receivers[$pluginType][$receiverType];
                }

                $config = Slim::getInstance()->config('s')[$pluginType];

                foreach ($config as $pluginId => $pluginConfig) {
                        $plugin = $pluginConfig['plugin'];

	                $class = "\\ServerMenu\\$pluginType\\$plugin\\$plugin";

                        if (class_exists($class)) {
	                        /* @var $classInstance Receiver */
                                $classInstance = new $class($pluginConfig, $pluginId);

	                        if (!in_array('ServerMenu\Receiver', class_uses($classInstance)))
		                        continue;

	                        if (!$classInstance->canReceive($receiverType))
		                        continue;

                                self::$receivers[$pluginType][$receiverType][] = array(
                                        'pluginId' => $pluginId,
                                        'plugin'   => $plugin
                                );
                        }
                }

		return array("plugins" => self::$receivers[$pluginType][$receiverType]);
        }

} 