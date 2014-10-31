servermenu
==========

A modular status page for your HTPC server that lets you control and 
see the status of your services: SABnzbd, Deluge, Transmission, etc.

* Clients can download from relevant sources like feeds, searchengines
  via the web interface.
* Provides a common API to HTPC services like Transmission or SABnzbd (work in progress)
* Easy to extend; feeds, search engines and services are PHP plugins


Screenshot
----------

![screenshot](http://mu.ms/f/pPJedb.png)


Requirements
------------

* Apache
* PHP 5.4+


Installation
------------

1) Install Composer from http://getcomposer.org

1) Unzip or clone into folder /path/to/servermenu

1) Configure app/config.php

1) Run /path/to/composer update in root

1) Setup virtual host pointing to /path/to/servermenu/public
