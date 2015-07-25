<?php
namespace app\controllers;


use app\controllers\behaviors\access\ConditionAccessRule;
use app\models\Author;
use app\models\User;
use Yii;
use yii\base\Action;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

class BookController extends ActiveController
{
	public $modelClass = 'app\models\Author';
	public $serializer = [
		'class' => 'yii\rest\Serializer',
		'collectionEnvelope' => 'data',
	];

	public function actions()
	{
		$actions = parent::actions();
		$request = \Yii::$app->request;
		/* @todo Review For future needs.
		$actions['create']['class'] = 'app\controllers\author\CreateAction';
		$actions['delete']['class'] = 'app\controllers\author\DeleteAction';
		$actions['index']['class'] = 'app\controllers\author\IndexAction';
		$actions['index']['prepareDataProvider'] = function($action) { return $action->getDataProvider(); };
		$actions['index']['limit'] = $request->getQueryParam('limit');
		$actions['index']['perPage'] = $request->getQueryParam('perPage');
		$actions['index']['search'] = $request->getQueryParam('search');
		$actions['index']['sort'] = $request->getQueryParam('sort');
		$actions['index']['type'] = $request->getQueryParam('type');
		$actions['update']['class'] = 'app\controllers\author\UpdateAction';
		*/
		return $actions;
	}
}
