<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/core.php'),
	array(
		// preloading 'log' component
		'preload'=>array('log'),
		'defaultController' => 'dashboard',

		'modules'=>array(
			/*
			'gii'=>array(
				'class'=>'system.gii.GiiModule',
				'generatorPaths' => array(
					'application.gii',
				),
				'password'=>'aoeu',
				'ipFilters'=>array('127.0.0.1','::1'),
			),
			 */
		),
		
		// application components
		'components'=>array(
			'user'=>array(
				// enable cookie-based authentication
				'class' => 'WebUser',
				'allowAutoLogin'=>true,
			),
			'urlManager'=>array(
				'urlFormat'=>'path',
				'showScriptName'=>false,
				'rules'=>array(
					'<controller:\w+>/<id:\d+>'=>'<controller>/view',
					'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
				),
			),
			'session' => array(
				'class' => 'CDbHttpSession',
				'autoCreateSessionTable' => false,
				'connectionID' => 'db',
				'sessionTableName' => 'sessions',
			),
			'widgetFactory' => array(
				'widgets' => array(
					'CTabView' => array(
						'cssFile' => false,
					),
					// This hides all of the dialogs until they are open.
					// Of course, this relies on the CSS file loading...
					'CJuiDialog' => array(
						'htmlOptions' => array(
							'class'=>'hidden',
						),
					),
				),
			),
			'errorHandler'=>array(
				// use 'site/error' action to display errors
				'errorAction'=>'site/error',
			),
			'log'=>array(
				'class'=>'CLogRouter',
				'routes'=>array(
					array(
						'class'=>'CFileLogRoute',
						'levels'=>'error, warning',
					),
					// uncomment the following to show log messages on web pages
					/*
					array(
						'class'=>'CWebLogRoute',
					),
					*/
				),
			),
		),
	)
);
