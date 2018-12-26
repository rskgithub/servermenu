<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 21/10/14
 * Time: 20:49
 */

namespace ServerMenu\Services\Sabnzbd;

use ServerMenu\Receiver;
use ServerMenu\Service;

class Sabnzbd extends Service
{
	use Receiver;

	private $status, // Integer containing current status code
		$remaining, // Number of items left in queue
		$eta, // Time left until completion
		$speed, // Current transfer speed
		$percentage = 0; // Percentage done

	/**
	 * @return array
	 */
	protected function getRequiredConfig()
	{
		return array('plugin', 'title', 'url', 'api_key', 'public_address');
	}

	/**
	 * @return array
	 */
	protected function getReceiverTypes()
	{
		return array('nzb');
	}

	/**
	 * @param string $mode
	 * @return string
	 */
	private function getApiUrl($mode = 'queue')
	{
		$apiUrl = "{$this->config['url']}/api?mode={$mode}&output=json&apikey={$this->config['api_key']}";

		return $apiUrl;
	}

	/**
	 * @return mixed|void
	 */
	public function fetchData()
	{
		$docQueue = file_get_contents($this->getApiUrl());

		if (!$docQueue) {
			$this->status = Service::STATUS_OFFLINE;
			return;
		}

		$data = json_decode($docQueue);

		if (json_last_error() != JSON_ERROR_NONE) {
			$this->status = Service::STATUS_OFFLINE;
			return;
		}

		$data = $data->queue;
		//echo '<pre>'; print_r($data);

		$this->status = Service::STATUS_IDLE;
		$this->speed = '0.0';
		$this->eta = '0:00:00';
		$this->remaining = '0';

		if (isset($data->slots) && (count($data->slots) > 0)) {
			$this->remaining = count($data->slots);
			$this->eta = $data->timeleft;
			$this->speed = $data->kbpersec;
			$this->status = Service::STATUS_DOWNLOADING;
			foreach ($data->slots as $slot) {
				$percentages[] = $slot->percentage;
			}

			if (!empty($percentages)) {
				$this->percentage = round(array_sum($percentages) / count($percentages), 1);
			}
		}

		if ($data->paused == true) {
			$this->status = Service::STATUS_PAUSED;
		}

		if ($this->status == Service::STATUS_IDLE) {
			$docHistory = file_get_contents($this->getApiUrl('history'));
			$dataHistory = json_decode($docHistory)->history;
			$percentages = [];

			foreach ($dataHistory->slots as $slot) {
				if (!empty($slot->status) && !in_array($slot->status, ['Completed', 'Failed'])) {
					$this->status = Service::STATUS_PROCESSING;
					$this->actionLine = $slot->action_line;
					break;
				}
			}

		}
	}

	/**
	 * The plugin must implement this method to safely be able to
	 * receive content.
	 *
	 * @param $receiverType
	 * @param $content
	 * @return mixed
	 */
	public function receiveContent($receiverType, $content)
	{
		$content = urlencode($content);
		$apiUrl = "{$this->config['url']}/api?mode=addurl&name={$content}&apikey={$this->config['api_key']}";

		file_get_contents($apiUrl);
	}

	/**
	 * @return int
	 */
	public function getRemaining()
	{
		return (int)$this->remaining;
	}

	/**
	 * @return float|int
	 */
	public function getSpeed()
	{
		return (int)$this->speed * 1024; //kbytes to bytes
	}

	/**
	 * @return float|int
	 */
	public function getEta()
	{
		sscanf($this->eta, "%d:%d:%d", $hours, $minutes, $seconds);
		$time_seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
		return time() + $time_seconds;
	}

	/**
	 * @return int
	 */
	public function getStatusCode()
	{
		return $this->status;
	}

	/**
	 * @return string
	 */
	public function getWanLink()
	{
		return $this->config['public_address'];
	}

	/**
	 * @return string
	 */
	public function getLanLink()
	{
		return $this->config['url'];
	}

	/**
	 * @return string
	 */
	public function getStatusString()
	{
		if ($this->status == Service::STATUS_PROCESSING) {
			return $this->actionLine;
		} else {
			return parent::getStatusString();
		}
	}

	/**
	 * @return string|void
	 */
	public function getQueueList()
	{

	}

	/**
	 * @return int
	 */
	public function getPercentage()
	{
		return $this->percentage;
	}
} 