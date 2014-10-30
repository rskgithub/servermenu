<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 28/10/14
 * Time: 22.36
 */

namespace ServerMenu;

/**
 * Trait Receiver
 *
 * Used for plugins that can receive certain things, like
 * links, URLs, search queries, etc.
 *
 * @package ServerMenu
 */

trait Receiver {

	/**
	 * Populate with array of strings containing types of content the service
	 * can receive.
	 *
	 * @var
	 */
	protected abstract function getReceiverTypes();

	/**
	 * Make sure service can handle the receiverType, and send
	 * content along for consumption.
	 *
	 * @param $receiverType
	 * @param string $content
	 * @return bool Whether request is successful
	 */
	public final function receive($receiverType, $content) {
		if ($this->canReceive($receiverType))
			return $this->receiveContent($receiverType, $content);
		return false;
	}

	/**
	 * Checks whether plugin is able to receive a given type of content.
	 *
	 * @param $receiverType
	 * @return bool
	 */
	public final function canReceive($receiverType) {
		if (in_array($receiverType, $this->getReceiverTypes()))
			return true;
		return false;
	}

	/**
	 * The plugin must implement this method to safely be able to
	 * receive content.
	 *
	 * @param $receiverType
	 * @param $content
	 * @return mixed
	 */
	public abstract function receiveContent($receiverType, $content);

} 