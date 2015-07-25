<?php
use yii\helpers\ArrayHelper;

return ArrayHelper::merge(
	include_once(dirname(__FILE__) . '/common.default.php'),
	[
	    // @todo Move to constant.
		'homeUrl' => 'https://rgkgroup-librarian-patchranger.c9.io',
		'components' => [
			'ajax'=>[
				'class'=>'app\components\AjaxComponent',
			],
			'request' => [
				'enableCookieValidation' => true,
				'enableCsrfValidation' => true,
				'cookieValidationKey' => 'RGKGroupLibrarianCVK',
				'parsers' => [
					'application/json' => 'yii\web\JsonParser',
				],
			],
		],
	],
	is_file($file = dirname(__FILE__) . '/main.php') ? include_once($file) : []
);