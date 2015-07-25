<?php
// automatically send every new message to available log routes
Yii::getLogger()->flushInterval = 1;
// Enable debug mode.
defined('YII_DEBUG') or define('YII_DEBUG',true);
return [
	//'bootstrap' => ['debug'],
	'modules' => [
		//'debug' => 'yii\debug\Module',
		/*
		'debug' => [
			'class' => 'yii\debug\Module',
			'panels' => [
				'views' => ['class' => 'yii\debug\panels\ConfigPanel'],
			],
		],
		 */
	],
	'components'=>[
		'mailer' => [
			'useFileTransport'=>true,
		],
	],
];