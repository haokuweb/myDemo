<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
global $config_db_username;
global $config_db_password;
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'数理统计演示',
	'defaultController'=>'main',
	'language'=>'zh_cn',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'admin888',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		)
		
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
			'loginUrl'=>array('main/login'),
		),
		// uncomment the following to enable URLs in path-format

		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				/*'post/<id:\d+>/<title:.*?>'=>'post/view',*/
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
			'showScriptName'=>false,
			'urlSuffix'=>'.do',
		),
		/*
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),
		*/
		// uncomment the following to use a MySQL database
		
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=lhc',
			'emulatePrepare' => true,
			'username' => $config_db_username,
			'password' => $config_db_password,
			'charset' => 'utf8',
		),
		
		/*'authManager'=>array(
            'class'=>'CDbAuthManager',
            'connectionID'=>'db',
        ),*/
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'main/error',
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
		
		'clientScript'=>array(
		        'scriptMap'=>array(
		                'jquery.js'=>false,
		                'jquery.yii.js'=>false,
		        ),
		),
		
		'cache'=>array(
            'class'=>'system.caching.CMemCache',
			/*CMemCache: 使用 PHP memcache 扩展.
			CApcCache: 使用 PHP APC 扩展.
			CDbCache: 使用一个数据表存储缓存数据。默认情况下，它将创建并使用在 runtime 目录下的一个 SQLite3 数据库。 你也可以通过设置其 connectionID 属性指定一个给它使用的数据库。
			CFileCache: 使用文件存储缓存数据。这个特别适合用于存储大块数据（例如页面）。
			CDummyCache: 目前 dummy 缓存并不实现缓存功能。此组件的目的是用于简化那些需要检查缓存可用性的代码。 例如，在开发阶段或者服务器尚未支持实际的缓存功能，我们可以使用此缓存组件。
			*/
            'servers'=>array(
				array('host'=>'127.0.0.1', 'port'=>11211, 'weight'=>100),
                //array('host'=>'server1', 'port'=>11211, 'weight'=>60),
                //array('host'=>'server2', 'port'=>11211, 'weight'=>40),
            ),
        ),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'zhizhesky@163.com',
	),
);