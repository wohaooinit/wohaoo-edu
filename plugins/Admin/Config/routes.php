<?php

	$plugin = basename(dirname(dirname(__FILE__)));
	$pluginUnderscored = Inflector::underscore($plugin);

	//Route for login and signup actions
	Router::connect("/{$pluginUnderscored}/login", array('plugin' => $plugin, 'controller' => $pluginUnderscored . '_users', 'action' => 'login'));
	Router::connect("/{$pluginUnderscored}/logout", array('plugin' => $plugin, 'controller' => $pluginUnderscored . '_users', 'action' => 'logout'));
	Router::connect("/{$pluginUnderscored}/signup", array('plugin' => $plugin, 'controller' => $pluginUnderscored . '_users', 'action' => 'signup'));

	Router::connect("/{$pluginUnderscored}", array('plugin' => $plugin, 'controller' => $pluginUnderscored . '_pages', 'action' => 'display', 'home'));

	$pluginControllers = App::objects($plugin . '.Controller');
	$ignoredPluginControllers = array(
		$plugin . 'AppController'
	);

	foreach ($pluginControllers as $pluginControllerName) {
		if(!in_array($pluginControllerName, $ignoredPluginControllers)) {
			$regularControllerName = substr($pluginControllerName, 0, -strlen('Controller'));
			if(strpos($regularControllerName, $plugin) === 0) {
				$regularControllerName = Inflector::underscore(substr($regularControllerName, strlen($plugin)));
				Router::connect("/{$pluginUnderscored}/{$regularControllerName}", array('plugin' => $pluginUnderscored, 'controller' => $pluginUnderscored . '_' . $regularControllerName, 'action' => 'index'));
				Router::connect("/{$pluginUnderscored}/{$regularControllerName}/:action/*", array('plugin' => $pluginUnderscored, 'controller' => $pluginUnderscored . '_' . $regularControllerName));
			}
		}
	}