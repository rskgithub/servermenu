servermenu
==========

A modular status page for your HTPC server that lets you control and see the status of your services: SABnzbd, Deluge, Transmission, etc.

* Clients can download from relevant sources like feeds, search engines
  via the web interface.
* Provides a common API to HTPC services like Transmission or SABnzbd (work in progress)
* Easy to extend; feeds, search engines and services are PHP plugins


Screenshot
----------

![screenshot](http://mu.ms/f/pPJedb.png?cached)


Requirements
------------

* Apache
* PHP 5.4+
* Composer (see http://getcomposer.org)


Installation
------------

1) Unzip or clone into a folder

2) Configure `/app/config.php` to enable services/feeds/search engines of your choice

3) Run `composer update` in root to download necessary components

4) Setup virtual host pointing to `/public`


Support & Contributing
----------------------

The project consists of a simple Slim Framework-based PHP application and uses a plugin-structure to standardize and API-enable a host of different content sources and destinations. Anyone can build a new plugin for ServerMenu, and a few plugins are included. 

ServerMenu utilizes the configuration file `app/config.php` to keep track of plugins and their configuration values, like hostnames and API keys. It's here you add new plugins and change the password of the UI.

ServerMenu also has it's own JSON API which is still under development.

If you've developed a custom Service, Feed or SearchEngine, feel free to submit a pull request. 

**Services**

Services extend the `Service` class and are used for the applications that reside on your server, like SABnzbd, Torrent apps, and similar. Services must implement a number of different methods (see the Service class) so the app can retrieve information and send content: Use the `Receiver` trait to enable the Service to receive content like magnet links.

**SearchEngines**

SearchEngines extend the `SearchEngine`class and deliver an array with search results which the application will format. 

Each search result has an 'action' that allows the application to send a link or search result to a Service. In the action array, the type of plugin (usually `service`) is specified, along with the receiverType, which are defined by services themselves (there has to be a plugin with a matching receiverType available). Then there's the content parameter which is the link or other string to be sent to the service. Finally there's the title and glyphicon parameters which are shown to the user in the UI in the form of the `Actions` button beside each search result.

**Feeds**

Feeds are basically SearchEngines but without the ability to provide them a search string. They extend the `Feed` class. By default, they show the most recent items in that feed, and as SearchEngines can implement the `actions` array in order to send content to both Services and SearchEngines.

