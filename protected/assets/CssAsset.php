<?php
namespace app\assets;


use yii\web\AssetBundle as AssetBundle;

class CssAsset extends AssetBundle
{
	public $baseUrl = '@web/css/source';
	public $sourcePath = '@webroot/css/source';
	public $css = [
		'plugins/jquery-ui.less',
		'plugins/lightbox.less',
		'core/style.less',
	];

	public function publish($am)
	{
		parent::publish($am);
		foreach ($this->css as $index => $css) {
			$this->css[$index] .= '?v='.$this->getVersion();
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