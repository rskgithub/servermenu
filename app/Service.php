<?php
namespace ServerMenu;

use Slim\Slim;

abstract class Service
{

        const STATUS_OFFLINE = 0;
        const STATUS_IDLE = 1;
        const STATUS_DOWNLOADING = 2;
        const STATUS_PAUSED = 3;
        const STATUS_UNKNOWN = 10;
        const REQUEST_LAN = 1;
        const REQUEST_WAN = 2;

        protected
                $requiredConfig, // Array containing the required values in config
                $status, // Integer containing current status code
                $remaining, // Number of items left in queue
                $eta, // Time left until completion
                $speed, // Current transfer speed

                $config, // Configuration store
                $serviceId; // Service ID;

        protected abstract function fetchData();

        public abstract function getRemaining();

        public abstract function getEta();

        public abstract function getSpeed();

        public abstract function getStatusCode();

        public abstract function getWanLink();

        public abstract function getLanLink();

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

        private function getStatusString()
        {
                switch ($this->status) {
                        case self::STATUS_OFFLINE:
                                return 'Offline';
                        case self::STATUS_IDLE:
                                return 'Idle';
                        case self::STATUS_DOWNLOADING:
                                return 'Downloading';
                }
                return 'Offline';
        }

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