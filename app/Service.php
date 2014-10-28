<?php
namespace ServerMenu;

use Slim\Slim;

abstract class Service extends Plugin
{

        const STATUS_OFFLINE = 0;
        const STATUS_IDLE = 1;
        const STATUS_DOWNLOADING = 2;
        const STATUS_PAUSED = 3;
        const STATUS_UNKNOWN = 10;
        const REQUEST_LAN = 1;
        const REQUEST_WAN = 2;
        const RECEIVER_TORRENT = 1;
        const RECEIVER_MAGNET = 2;
        const RECEIVER_NZB = 3;

        protected
                $requiredConfig, // Array containing the required values in config

                $config, // Configuration store
                $serviceId; // Service ID;

        protected abstract function fetchData();

	public $receiverTypes; // Populate with array containing types of content the service can receive

        /**
         * @return int
         */
        public abstract function getRemaining();

        /**
         * @return int
         */
        public abstract function getEta();

        /**
         * @return int
         */
        public abstract function getSpeed();

        /**
         * @return int
         */
        public abstract function getStatusCode();

        /**
         * @return string
         */
        public abstract function getWanLink();

        /**
         * @return string
         */
        public abstract function getLanLink();

	public function receive($receiverType, $content) {
		if (!empty($this->receiverTypes) && in_array($receiverType, $this->receiverTypes))
			return $this->receiveContent($receiverType, $content);
		return false;
	}

	/**
         * @param $config
         * @param $serviceId
         *
         * @throws \Exception
         */
        public function __construct($config, $serviceId)
        {
                foreach ($this->requiredConfig as $configVar) {
                        if (!isset($config[$configVar])) {
                                throw new \Exception("Config variable missing: $configVar");
                        }
                }
                $this->config = $config;
                $this->serviceId = $serviceId;
                $this->fetchData();
        }

        /**
         * @return string
         */
        private function getStatusString()
        {
                switch ($this->getStatusCode()) {
                        case self::STATUS_IDLE:
                                return 'Idle';
                        case self::STATUS_DOWNLOADING:
                                return 'Downloading';
                        case self::STATUS_PAUSED:
                                return 'Paused';
                        case self::STATUS_UNKNOWN:
                                return 'Unknown';
                        case self::STATUS_OFFLINE:
                        default:
                                return 'Offline';
                }
        }

        /**
         * @return int
         */
        protected final function getRequestType()
        {
                $app = Slim::getInstance();
                $config = $app->config('s');
                foreach ($config['app']['private_ranges'] as $range) {
                        if (Utility::cidr_match($app->request->getIp(), $range)) {
                                return self::REQUEST_LAN;
                        }
                }
                return self::REQUEST_LAN;
        }

        /**
         * @return array
         */
        public final function getTemplateData()
        {
                return array(
                        'config'     => $this->config,
                        'remaining'  => $this->getRemaining(),
                        'eta'        => Utility::time2relative($this->getEta()),
                        'speed'      => Utility::bytes2human($this->getSpeed()),
                        'status'     => $this->getStatusString(),
                        'statuscode' => $this->getStatusCode(),
                        'link'       => (($this->getRequestType() == self::REQUEST_WAN) ? $this->getWanLink() : $this->getLanLink()),
                );
        }


}