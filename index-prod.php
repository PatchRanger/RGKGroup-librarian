<?php
/**
 * This is the bootstrap file for production application.
 * This file should replace the default index.php when the application is deployed for production.
 */

// autoloading
require_once(__DIR__ . '/vendor/autoload.php');

defined('YII_DEBUG') or define('YII_DEBUG',false);

require_once(dirname(__FILE__).'/vendor/yiisoft/yii2/Yii.php');
$config=require_once(dirname(__FILE__).'/protected/config/main.default.php');

$application = new yii\web\Application($config);
$exitCode = $application->run();

exit($exitCode);
