<?php
namespace app\controllers;


use app\controllers\behaviors\access\ConditionAccessRule;
use app\models\User;
use Yii;
use yii\base\Action;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

class UserController extends ActiveController
{
	public $modelClass = 'app\models\User';
	public $serializer = [
		'class' => 'yii\rest\Serializer',
		'collectionEnvelope' => 'data',
	];

	public function actions()
	{
		$actions = parent::actions();
		$request = \Yii::$app->request;
		$actions['create']['class'] = 'app\controllers\user\CreateAction';
		$actions['delete']['class'] = 'app\controllers\user\DeleteAction';
		/* @todo Review For future needs.
		$actions['index']['class'] = 'app\controllers\user\IndexAction';
		$actions['index']['prepareDataProvider'] = function($action) { return $action->getDataProvider(); };
		$actions['index']['limit'] = $request->getQueryParam('limit');
		$actions['index']['perPage'] = $request->getQueryParam('perPage');
		$actions['index']['search'] = $request->getQueryParam('search');
		$actions['index']['sort'] = $request->getQueryParam('sort');
		$actions['index']['type'] = $request->getQueryParam('type');
		$actions['update']['class'] = 'app\controllers\user\UpdateAction';
		$actions['view']['class'] = 'app\controllers\user\ViewAction';
		*/
		return $actions;
	}

	public function behaviors()
	{
		return ArrayHelper::merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::className(),
				//'only' => ['login', 'logout', 'signup'],
				'rules' => [
					[
						'allow' => true,
						'actions' => ['new','create'],
						'roles' => ['?'],
					],
					[
						'class'=>ConditionAccessRule::className(),
						'actions' => ['view'],
						'roles' => ['?','@'],
						'model'=>User::findOne(\Yii::$app->request->getQueryParam('id')),
						'message'=>'Такого пользователя не существует.',
						// @todo Add Реализовать RBAC для статусов Юзеров.
						'condition'=>function($model) { return (empty($model) || $model->status != User::STATUS_ACTIVE); },
					],
					[
						'allow' => true,
						'actions' => ['index','view'],
						'roles' => ['?','@'],
					],
					[
						'allow' => true,
						'actions' => ['update'],
						'roles' => [RbacController::USER_updateOwnProfile],
						'matchCallback' => function (AccessRule $rule, Action $action) {
							return ($id = \Yii::$app->user->getId()) && (\Yii::$app->request->getQueryParam('id') == $id);
						},
					],
					[
						'allow' => true,
						'actions' => ['update'],
						'roles' => [RbacController::USER_updateOtherProfiles],
						'matchCallback' => function (AccessRule $rule, Action $action) {
							return ($id = \Yii::$app->user->getId()) && (\Yii::$app->request->getQueryParam('id') != $id);
						},
					],
					[
						'allow' => true,
						'actions' => ['delete'],
						'roles' => [RbacController::USER_deleteOwnProfile],
						'matchCallback' => function (AccessRule $rule, Action $action) {
							return ($id = \Yii::$app->user->getId()) && (\Yii::$app->request->getQueryParam('id') == $id);
						},
					],
					[
						'allow' => true,
						'actions' => ['delete'],
						'roles' => [RbacController::USER_deleteOtherProfiles],
						'matchCallback' => function (AccessRule $rule, Action $action) {
							return ($id = \Yii::$app->user->getId()) && (\Yii::$app->request->getQueryParam('id') != $id);
						},
					],
				],
			],
		]);
	}

	/**
	 * Returns empty model.
	 * For consistency of create/update forms.
	 */
	public function actionNew()
	{
		$newModel = new User();
		// @todo DRY: copied from loadDefaultValues.
		//$newModel->loadDefaultValues(false);
		foreach ($newModel->getTableSchema()->columns as $column) {
			$newModel->{$column->name} = ($column->defaultValue !== null) ? $column->defaultValue : null;
		}
		return $newModel;
	}
}
