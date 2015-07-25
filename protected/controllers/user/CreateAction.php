<?php
namespace app\controllers\user;


use app\models\User;

class CreateAction extends \yii\rest\CreateAction
{
	public $scenario = User::SCENARIO_REGISTER;

	public function run()
	{
		/** @var User $model */
		$model = parent::run();
		$request = \Yii::$app->request;
		// Log in.
		$duration= 3600*24*30; // 30 days
		\Yii::$app->user->login($model,$duration);
		return $model;
	}
}