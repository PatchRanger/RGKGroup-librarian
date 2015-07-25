<?php
namespace app\controllers;


use app\controllers\behaviors\access\ConditionAccessRule;
use app\models\Author;
use app\models\Book;
use app\models\User;
use Yii;
use yii\base\Action;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

class BookController extends ActiveController
{
	public $modelClass = 'app\models\Book';
	public $serializer = [
		'class' => 'yii\rest\Serializer',
		'collectionEnvelope' => 'data',
	];

	public function actions()
	{
		$actions = parent::actions();
		$request = \Yii::$app->request;
		$actions['create']['class'] = 'app\controllers\book\CreateAction';
		$actions['delete']['class'] = 'app\controllers\book\DeleteAction';
		$actions['index']['class'] = 'app\controllers\book\IndexAction';
		$actions['index']['prepareDataProvider'] = function($action) { return $action->getDataProvider(); };
		$actions['index']['limit'] = $request->getQueryParam('limit');
		$actions['index']['perPage'] = $request->getQueryParam('perPage');
		$actions['index']['search'] = $request->getQueryParam('search');
		$actions['index']['sort'] = $request->getQueryParam('sort');
		$actions['index']['type'] = $request->getQueryParam('type');
		$actions['index']['author_id'] = $request->getQueryParam('author_id');
		$actions['update']['class'] = 'app\controllers\book\UpdateAction';
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
						'class'=>ConditionAccessRule::className(),
						'actions' => ['view'],
						'roles' => ['?','@'],
						'model'=>Book::findOne(\Yii::$app->request->getQueryParam('id')),
						'message'=>'Такой книги не существует.',
						// @todo Add RBAC for Book status.
						'condition'=>function($model) { return (empty($model) || $model->status != Book::STATUS_ACTIVE); },
					],
					[
						'allow' => true,
						'actions' => ['index','view'],
						'roles' => ['?','@'],
					],
					[
						'allow' => true,
						'actions' => ['create','new'],
						'roles' => [RbacController::BOOK_createBook],
					],
					[
						'allow' => true,
						'actions' => ['update'],
						'roles' => [RbacController::BOOK_updateOwnBooks],
						'matchCallback' => function (AccessRule $rule, Action $action) {
							/** @var User $currentUser */
							$currentUser = \Yii::$app->user->getModel();
							return !empty($currentUser) && in_array(\Yii::$app->request->getQueryParam('id'),array_keys($currentUser->getBooks()->indexBy('id')->all()));
						},
					],
					[
						'allow' => true,
						'actions' => ['update'],
						'roles' => [RbacController::BOOK_updateOtherBooks],
						'matchCallback' => function (AccessRule $rule, Action $action) {
							/** @var User $currentUser */
							$currentUser = \Yii::$app->user->getModel();
							return !empty($currentUser) && !in_array(\Yii::$app->request->getQueryParam('id'),array_keys($currentUser->getBooks()->indexBy('id')->all()));
						},
					],
					[
						'allow' => true,
						'actions' => ['delete'],
						'roles' => [RbacController::BOOK_deleteOwnBooks],
						'matchCallback' => function (AccessRule $rule, Action $action) {
							/** @var User $currentUser */
							$currentUser = \Yii::$app->user->getModel();
							return !empty($currentUser) && in_array(\Yii::$app->request->getQueryParam('id'),array_keys($currentUser->getBooks()->indexBy('id')->all()));
						},
					],
					[
						'allow' => true,
						'actions' => ['delete'],
						'roles' => [RbacController::BOOK_deleteOtherBooks],
						'matchCallback' => function (AccessRule $rule, Action $action) {
							/** @var User $currentUser */
							$currentUser = \Yii::$app->user->getModel();
							return !empty($currentUser) && !in_array(\Yii::$app->request->getQueryParam('id'),array_keys($currentUser->getBooks()->indexBy('id')->all()));
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
		$newModel = new Book();
		// @todo DRY: copied from loadDefaultValues.
		//$newModel->loadDefaultValues(false);
		foreach ($newModel->getTableSchema()->columns as $column) {
			$newModel->{$column->name} = ($column->defaultValue !== null) ? $column->defaultValue : null;
		}
		return $newModel;
	}
}
