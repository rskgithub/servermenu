<?php

namespace ServerMenu\Services\NZBGet;

use ServerMenu\Receiver;
use ServerMenu\Service;

class NZBGet extends Service
{
	use Receiver;

	/**
	 * @var int
	 */
	private $status;
	/**
	 * @var int
	 */
	private $remaining;
	/**
	 * @var string
	 */
	private $eta;
	/**
	 * @var string
	 */
	private $speed;
	/**
	 * @var int
	 */
	private $percentage;

	/**
	 * @param $receiverType
	 * @param $content
	 */
	public function receiveContent($receiverType, $content)
	{
		$url = $this->config['url'] . '/jsonrpc';
		$content = json_encode([
			'method' => 'append',
			'params' => ['', $content, '', 0, false, false, '', 0, 'SCORE', []]
		]);
		$context = stream_context_create([
			'http' => [
				'method' => 'POST',
				'header' => [
					'Content-Type: application/json-rpc',
					'Content-Length: '. strlen($content),
				],
				'ignore_errors' => true,
				'content' => $content,
			]
		]);
		file_get_contents($url, false, $context);
	}

	/**
	 * Called when application wants fresh data. Use this method
	 * to query Services for new data.
	 *
	 * @return mixed
	 */
	public function fetchData()
	{
		$resultStatus = file_get_contents($this->config['url'] . '/jsonrpc/status');
		$resultGroups = file_get_contents($this->config['url'] . '/jsonrpc/listgroups');
		$status = json_decode($resultStatus, true)['result'];
		$groups = json_decode($resultGroups, true)['result'];

		//dump($status, $groups);

		if (json_last_error() != JSON_ERROR_NONE) {
			$this->status = Service::STATUS_OFFLINE;
			return;
		}

		$this->status = Service::STATUS_UNKNOWN;
		$this->remaining = count($groups);
		$this->speed = $status['DownloadRate'];

		if ($this->remaining > 0) {
			$this->status = Service::STATUS_DOWNLOADING;
		} else {
			$this->status = Service::STATUS_IDLE;
		}
		if ($status['ServerPaused'] === false) {
			$this->status = Service::STATUS_PAUSED;
		}

		// Get percentage
		$total = 0;
		$completed = 0;
		foreach ($groups as $group) {
			$total += $group['FileSizeMB'];
			$completed += $group['RemainingSizeMB'];
		}
		$this->percentage = round(($total-$completed)/$total * 100);


		// Get ETA
		if ($completed > 0) {
			// @link https://github.com/nzbget/nzbget/blob/0916c2a908df8827313fbaba00edb54d2ade59be/webui/status.js#L157
			$this->eta = (int)(time() + ($completed*1024 / ($this->speed/1024)));
		} else {
			$this->eta = time();
		}
	}

	/**
	 * @return int
	 */
	public function getPercentage()
	{
		return $this->percentage;
	}

	/**
	 * Number of items left in Service queue
	 *
	 * @return int
	 */
	public function getRemaining()
	{
		return $this->remaining;
	}

	/**
	 * UNIX timestamp of completion date
	 *
	 * @return int
	 */
	public function getEta()
	{
		return $this->eta;
	}

	/**
	 * Current combined transfer speed of items in Service queue
	 * in bytes per second
	 *
	 * @return int
	 */
	public function getSpeed()
	{
		return $this->speed;
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
	 * Get list of any items in the queue
	 *
	 * @return array
	 */
	public function getQueueList()
	{
		return [];
	}

	/**
	 * @return array
	 */
	protected function getReceiverTypes()
	{
		return ['nzb'];
	}

	/**
	 * Should return an array of required config items. Should at minimum
	 * contain an array with string 'plugin' equal to the class name
	 * of the Service.
	 *
	 * @return array
	 */
	protected function getRequiredConfig()
	{
		return ['plugin', 'url'];
	}

	/**
	 * @return array
	 */
	public function getConfig() : array
	{
		return ['title' => 'NZBGet'] + $this->config;
	}
}