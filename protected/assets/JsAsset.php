<?php
namespace app\assets;


use yii\web\AssetBundle as AssetBundle;
use yii\web\View;

class JsAsset extends AssetBundle
{
	public $basePath = '@webroot/js/generated';
	public $baseUrl = '@web/js/generated';
	public $sourcePath = '@webroot/js/source';
	public $js = [
		// @todo Переключить с common-dev на common.
		//'common',
		'common-dev.js',
	];
	public $jsOptions = [
		'position'=>View::POS_HEAD,
	];

	public function publish($am)
	{
		parent::publish($am);
		foreach ($this->js as $index => $js) {
			$this->js[$index] .= '?v='.$this->getVersion();
		}
	}

	protected function getVersion()
	{
		$path = \Yii::getAlias($this->sourcePath);
		// Точка - чтобы папка воспринималась как файл.
		// @link http://php.net/manual/en/function.filemtime.php#32728
		return filemtime("$path/.");
	}
}