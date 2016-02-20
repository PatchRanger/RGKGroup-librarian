<?php
/**
 * @var $this yii\web\View
 */
use app\assets\AppAsset;
$assetBundle = AppAsset::register($this);
// @todo Is it really required to do manually?
foreach ($assetBundle->depends as $bundleName) {
	\Yii::$app->assetManager->getBundle($bundleName)->registerAssetFiles($this);
}
?>
<!DOCTYPE html>
<html lang="ru-RU">
<head>
	<meta charset="utf-8" />
	<?= $this->renderHeadHtml(); ?>
	<?= \yii\helpers\Html::csrfMetaTags(); ?>
</head>
<body>
    <?= $content ?>
</body>
</html>
