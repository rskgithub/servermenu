<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 25/10/14
 * Time: 16:15
 */

namespace ServerMenu\Services\Transmission;


use ServerMenu\Receiver;
use ServerMenu\Service;

class Transmission extends Service {

	use Receiver;

        protected $requiredConfig = array('plugin', 'title', 'hostname', 'port', 'username', 'password', 'public_address');

        protected $receiverTypes = array('magnet', 'torrent');

        private $status, // Integer containing current status code
                $remaining, // Number of items left in queue
                $eta, // Time left until completion
                $speed, // Current transfer speed
                $upSpeed; // Upload speed

	private function getConnection()
	{
		$connection = new TransmissionRPC(
			$this->getLanLink() . "/rpc",
			$this->config['username'],
			$this->config['password'],
			false
		);

		return $connection;
	}

        protected function fetchData()
        {
                $rpc = $this->getConnection();

                $this->status = Service::STATUS_OFFLINE;
                $this->remaining = 0;
                $this->eta = 0;
                $this->speed = 0;

                $downloads = $rpc->get(array(), array(
                        'id', 'name', 'status', 'doneDate', 'haveValid',
                        'totalSize', 'percentDone', 'eta', 'rateDownload',
                        'rateUpload'
                ));

                if (@count($downloads->arguments->torrents) > 0) {
                        foreach ($downloads->arguments->torrents as $download) {

                                if ($download->status == TransmissionRPC::TR_STATUS_DOWNLOAD) {
                                        $this->status = Service::STATUS_DOWNLOADING;
                                        $this->remaining++;
                                        if (isset($download->rateDownload)) {
                                                $this->speed += $download->rateDownload;
                                        }
                                        if (isset($download->rateUpload)) {
                                                $this->upSpeed += $download->rateUpload;
                                        }
                                        if (isset($download->eta) && ($download->eta > $this->eta)) {
                                                $this->eta = $download->eta;
                                        }
                                }

                                if ($this->status != Service::STATUS_DOWNLOADING) {
                                        $this->status = Service::STATUS_IDLE;
                                }
                        }
                } else {
                        $this->status = Service::STATUS_IDLE;
                }
        }

	public function receiveContent($receiverType, $content) {
		$rpc = $this->getConnection();
		$result = $rpc->add($content);

		if ($result['result'] == 'success')
			return true;
		return false;
	}

        public function getRemaining()
        {
                return $this->remaining;
        }

        public function getEta()
        {
                return time() + $this->eta;
        }

        public function getSpeed()
        {
                return $this->speed;
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
                return "http://{$this->config['hostname']}:{$this->config['port']}/transmission";
        }


} 