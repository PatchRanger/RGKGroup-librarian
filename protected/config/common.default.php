<?php
$basePath = dirname(__FILE__).DIRECTORY_SEPARATOR.'..';
$vendorPath = implode(DIRECTORY_SEPARATOR,[$basePath,'..','vendor']);
return [
	'basePath'=>$basePath,
	'vendorPath'=>$vendorPath,
	'name'=>'RGKGroupLibrarian',
	'bootstrap'=>['log','request','view'],
	'timeZone' => 'Asia/Novosibirsk',
	'language'=>'ru',
	'id' => 'rgkgrouplibrarian',
	//'defaultRoute'=>'frontend/post/list',
	'modules'=>[
	    // @todo BackendModule
		'backend' => 'app\modules\backend\BackendModule',
		// @todo FrontendModule
		'frontend' => 'app\modules\frontend\FrontendModule',
	],
	'components'=>[
		'assetManager'=>[
			'bundles' => ['app\assets\AppAsset'],
			'converter'=>[
				'class'=>'yii\web\AssetConverter',
				'commands'=>[
					// Тут решает 2 беды: на Windows не работают символьные ссылки и слэш в другую сторону.
					'less' => ['css','php '.dirname(__FILE__).implode(DIRECTORY_SEPARATOR,[$vendorPath,'oyejorge','less.php','bin','lessc']).' {from} {to} --no-color --source-map'],
				],
			],
			'linkAssets'=>true,
		],
		'authManager'=>[
			'class' => 'yii\rbac\PhpManager',
			'defaultRoles' => [
				/* @todo Review No autoloading here, it's a pity.
				User::ROLENAME_USER,
				User::ROLENAME_ADMIN,
				 */
				'user',
				'admin',
			],
			// Настройки, специфичные для PhpManager.
			'itemFile' => '@app/data/rbac/items.php',
			'assignmentFile' => '@app/data/rbac/assignments.php',
			'ruleFile' => '@app/data/rbac/rules.php',
		],
		// @todo Заменить кэш на более производительный.
		'cache'=>[
			'class'=>'yii\caching\DbCache',
		],
		'db'=>[
			'class' => '\yii\db\Connection',
			// @todo Correct dsn.
			//'dsn' => 'mysql:host=localhost;dbname=rgkgrouplibrarian',
			'dsn' => 'sqlite:'.implode(DIRECTORY_SEPARATOR,[$basePath,'data','app.db']),
			'emulatePrepare' => true,
			'username' => 'rgkgrouplibrarian',
			'password' => 'rgkgrouplibrarian',
			'charset' => 'utf8',
		],
		'log'=>[
			'targets'=>[
				'file'=>[
					'class'=>'yii\log\FileTarget',
					'levels'=>['error', 'warning'],
					'categories'=>['yii\*'],
				],
			],
		],
		'urlManager'=>[
		    // @todo Move to constant.
			'baseUrl' => 'https://rgkgroup-librarian-patchranger.c9.io',
			'enablePrettyUrl' => true,
			'enableStrictParsing' => true,
			'showScriptName' => false,
			'rules' => [
				[
					'pattern'=>'',
					//'route'=>'site/index',
					'route'=>'books/index',
				],
				[
					'pattern'=>'sitemap.xml',
					'route'=>'site/sitemap',
				],
				[
					'class' => 'yii\rest\UrlRule',
					'controller' => 'author',
					'except'=>['options'],
					'pluralize'=>false,
				],
				[
					'class' => 'yii\rest\UrlRule',
					'controller' => 'book',
					'except'=>['options'],
					'pluralize'=>false,
				],
				[
					'pattern'=>'<controller:\w+>/<id:\d+>/<action:\w+>/<navigation:\w+>',
					'route'=>'<controller>/<action>',
				],
				[
					'pattern'=>'<controller:\w+>/<id:\d+>/<action:\w+>',
					'route'=>'<controller>/<action>',
				],
				[
					'pattern'=>'<controller:\w+>/<action:\w+>/<id:\d+>',
					'route'=>'<controller>/<action>',
				],
				[
					'pattern'=>'<module:\w+>/<controller:\w+>/<action:[\w-]+>',
					'route'=>'<module>/<controller>/<action>',
				],
				[
					'pattern'=>'<controller:\w+>/<id:\d+>',
					'route'=>'<controller>/view',
				],
				[
					'pattern'=>'<controller:\w+>/<action:\w+>',
					'route'=>'<controller>/<action>',
				],
				[
					'pattern'=>'<controller:\w+>',
					'route'=>'<controller>',
				],
			],
		],
		'user'=>[
		    // @todo app\components\WebUser
			'class'=>'app\components\WebUser',
			'enableAutoLogin'=>true,
			// @todo app\models\User
			'identityClass'=>'app\models\User',
			// It's a cunning trick to make it throwing an exception instead of redirecting to login page.
			'loginUrl' => null,
		],
	],
	'params'=>[
		'adminEmail'=>'webmaster@example.com',
		'siteEmail'=>'info@example.com',
		'marketingEmail'=>'marketing@example.com',
		'contentEmail'=>'content@example.com',
	],
];