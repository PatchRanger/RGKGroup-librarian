<?php
/**
 * This is the bootstrap file for test application.
 * This file should be removed when the application is deployed for production.
 */

error_reporting(E_ALL);

// autoloading
require_once(__DIR__ . '/vendor/autoload.php');

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once(dirname(__FILE__).'/vendor/yiisoft/yii2/Yii.php');
$config=require_once(dirname(__FILE__).'/protected/config/test.php');

$application = new yii\web\Application($config);
$exitCode = $application->run();

exit($exitCode);
