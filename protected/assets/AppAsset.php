<?php
namespace app\assets;


use yii\web\AssetBundle as AssetBundle;

class AppAsset extends AssetBundle
{
	public $baseUrl = '@web';
	public $sourcePath = '@webroot';
	public $depends = [
		'app\assets\CssAsset',
		'app\assets\JsAsset',
		'yii\web\YiiAsset',
		'himiklab\colorbox\ColorboxAsset',
	];
}