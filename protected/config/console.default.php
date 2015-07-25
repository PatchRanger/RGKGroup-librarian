<?php
use yii\helpers\ArrayHelper;

Yii::setAlias('@webroot', __DIR__ . '/../..');
Yii::setAlias('@web', '/');
return ArrayHelper::merge(
	include_once(dirname(__FILE__) . '/common.default.php'),
	[],
	is_file($file = dirname(__FILE__) . '/console.php') ? include_once($file) : []
);