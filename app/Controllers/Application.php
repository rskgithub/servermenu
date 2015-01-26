<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 26/01/15
 * Time: 14:32
 */

namespace ServerMenu\Controllers;


use ServerMenu\Controller;

class Application extends Controller {

	public function getIndex() {
		$template_variables = array(
			'Services' => \ServerMenu\PluginLoader::getPlugins('Services'),
			'SearchEngines' => \ServerMenu\PluginLoader::getPlugins('SearchEngines'),
			'Feeds' => \ServerMenu\PluginLoader::getPlugins('Feeds'),
		);

		$this->app->render('index.html.twig', $template_variables);
	}

	public function getLogin() {
		if (md5($this->app->getCookie('login')) == $this->config['app']['password']) {
			$_SESSION['login'] = true;
			$this->app->redirect('/');
		}

		$this->app->render('login.html.twig');
	}

	public function getLogout() {
		session_unset();
		$this->app->deleteCookie('login');
		$this->app->redirect('/login');
	}

	public function postLogin() {
		if (isset($_POST['password'])) {
			if (md5($_POST['password']) === $this->config['app']['password']) {
				$_SESSION['login'] = true;
				$this->app->setCookie('login', $_POST['password'], '30 days');
			}
		}
		$this->app->redirect('/');
	}
}