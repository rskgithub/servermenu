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

class Sabnzbd extends \ServerMenu\Service
{
	use Receiver;

	private $status, // Integer containing current status code
		$remaining, // Number of items left in queue
		$eta, // Time left until completion
		$speed; // Current transfer speed

	protected function getRequiredConfig()
	{
		return array('plugin', 'title', 'url', 'api_key', 'public_address');
	}

	protected function getReceiverTypes()
	{
		return array('nzb');
	}

	public function fetchData()
	{
		$apiUrl = "{$this->config['url']}/api?mode=qstatus&output=json&apikey={$this->config['api_key']}";

		$document = file_get_contents($apiUrl);
		if (!$document) {
			$this->status = Service::STATUS_OFFLINE;
			return;
		}

		$data = json_decode($document);
		if (json_last_error() != JSON_ERROR_NONE) {
			$this->status = Service::STATUS_OFFLINE;
			return;
		}

		$this->status = Service::STATUS_IDLE;
		$this->speed = '0.0';
		$this->eta = '0:00:00';
		$this->remaining = '0';

		if (isset($data->jobs) && (count($data->jobs) > 0)) {
			$this->remaining = count($data->jobs);
			$this->eta = $data->timeleft;
			$this->speed = $data->kbpersec;
			$this->status = Service::STATUS_DOWNLOADING;
		}

		if ($data->paused == true) {
			$this->status = Service::STATUS_PAUSED;
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
	
	public function getRemaining()
	{
		return (int)$this->remaining;
	}

	public function getSpeed()
	{
		return (int)$this->speed * 1024; //kbytes to bytes
	}

	public function getEta()
	{
		sscanf($this->eta, "%d:%d:%d", $hours, $minutes, $seconds);
		$time_seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
		return time() + $time_seconds;
	}

	public function getStatusCode()
	{
		return $this->status;
	}

	public function getWanLink()
	{
		return $this->config['public_address'];
	}

	public function getLanLink()
	{
		return $this->config['url'];
	}

} 