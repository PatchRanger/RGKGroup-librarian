<?php

// autoloading
require_once(__DIR__ . '/../vendor/autoload.php');

// change the following paths if necessary
$yii=require_once(dirname(__FILE__).'/../vendor/yiisoft/yii2/Yii.php');
$config=require_once(dirname(__FILE__).'/config/console.default.php');

$application = new yii\console\Application($config);
$exitCode = $application->run();
exit($exitCode);