<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */

/**
 * LESS Routing
 *
 */
Router::parseExtensions('less');
Router::connect('/less/:filename', 
				array(
					'controller' => 'less_handlers', 
					'action' => 'preprocess',
					'filename'      => ':filename'
				),
				array(
					'pass'          => array('filename'),
					'filename'=>'[a-zA-Z0-9_!\-\.\/]+'
					)
			);

/**
 *	the default routing config
 *	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
 */
Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));

/**
 * ViewJS only works when url ends with .pdf
 *
 */
Router::connect( '/documents/:id/presentation.pdf', 
			array('controller' => 'documents', 'action' => 'download'), 
			array(
			'pass' => array('id'),
			'id' => '[0-9]+'
			)
);

		 	
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */

/**	NOT SUPPORTED
 *	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
 */

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */

require CAKE . 'Config' . DS . 'routes.php';


