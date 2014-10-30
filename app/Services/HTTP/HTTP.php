<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 26/10/14
 * Time: 06:11
 */

namespace ServerMenu\Services\HTTP;


use ServerMenu\Service;

class HTTP extends Service {

        protected function getRequiredConfig()
        {
	        return array('plugin', 'title', 'hostname', 'port', 'public_address');
        }

        private $upCode = 302,
                $status;

        public function fetchData()
        {
                $url = "http://{$this->config['hostname']}:{$this->config['port']}";

                $headers = @get_headers($url);

                if (strstr($headers[0], $this->upCode)) {
                        $this->status = Service::STATUS_IDLE;
                } else {
                        $this->status = Service::STATUS_OFFLINE;
                }

        }

        public function getRemaining()
        {
                // TODO: Implement getRemaining() method.
        }

        public function getEta()
        {
                // TODO: Implement getEta() method.
        }

        public function getSpeed()
        {
                // TODO: Implement getSpeed() method.
        }

        public function getStatusCode()
        {
                return $this->status;
        }

        public function getWanLink()
        {
                return $this->config['public_link'];
        }

        public function getLanLink()
        {
                return "http://{$this->config['hostname']}:{$this->config['port']}";
        }
}