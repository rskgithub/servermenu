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

	/**
	 * Should return an array of required config items. Should at minimum
	 * contain an array with string 'plugin' equal to the class name
	 * of the Service.
	 *
	 * @var
	 */
	protected abstract function getRequiredConfig();

	/**
	 * Will be populated with user configuration of the Service.
	 *
	 * @var
	 */
	protected $config;

	/**
	 * Will be populated with the Service ID.
	 *
	 * @var
	 */
	protected $serviceId;


	/**
	 * Called when application wants fresh data. Use this method
	 * to query Services for new data.
	 *
	 * @return mixed
	 */
	public abstract function fetchData();


        /**
         * Number of items left in Service queue
         *
         * @return int
         */
        public abstract function getRemaining();

        /**
         * UNIX timestamp of completion date
         *
         * @return int
         */
        public abstract function getEta();

        /**
         * Current combined transfer speed of items in Service queue
         * in bytes per second
         *
         * @return int
         */
        public abstract function getSpeed();

        /**
         * @return int
         */
        public abstract function getStatusCode();

        /**
         * Link to Service, accessible outside the local network.
         *
         * @return string
         */
        public abstract function getWanLink();

        /**
         * Link to Service, accessible inside local network.
         *
         * @return string
         */
        public abstract function getLanLink();

	/**
	 * Check and assign config and serviceId to Service.
	 *
         * @param $config
         * @param $serviceId
         *
         * @throws \Exception
         */
        public function __construct($config, $serviceId)
        {
                foreach ($this->getRequiredConfig() as $configVar) {
                        if (!isset($config[$configVar])) {
                                throw new \Exception("Config variable missing: $configVar");
                        }
                }
                $this->config = $config;
                $this->serviceId = $serviceId;
        }

        /**
         * Returns human-readable status string
         *
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
         * Establish whether this is a WAN or LAN request, used to
         * send correct links to browsers.
         *
         * @return int Request type
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
         * Get data for use in Twig templates
         *
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