<?php
namespace app\controllers;

use app\models\Book;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\Config;

class SiteController extends Controller
{
	public $layout = '@app/views/layouts/front.php';

	public function behaviors()
	{
		return ArrayHelper::merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::className(),
				//'only' => ['login', 'logout', 'signup'],
				'rules' => [
					[
						'allow' => true,
						'actions' => [
						    'index',
						    //'error',
						    //'sitemap',
						    //'rss',
						],
						'roles' => ['?','@'],
					],
					[
						'allow' => true,
						'actions' => ['logout'],
						'roles' => [RbacController::USER_logout],
					],
				],
			],
		]);
	}

	public function actionIndex()
	{
		$this->getView()->title = 'RGKGroupLibrarian - Home';
		return $this->render('dummy');
	}

	/**
	 * Logout пользователя и редирект на главную
	 */
	public function actionLogout()
	{
		\Yii::$app->user->logout();
		return $this->redirect(\Yii::$app->homeUrl);
	}
}