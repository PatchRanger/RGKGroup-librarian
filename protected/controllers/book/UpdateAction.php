<?php
namespace app\controllers\book;


use app\models\Book;
use app\models\User;

class UpdateAction extends \yii\rest\UpdateAction
{
	public function run($id)
	{
		$request = \Yii::$app->request;
		/** @var User $user */
		$user = \Yii::$app->user->getModel();
		$bodyParams = $request->getBodyParams();
		// Here is the extension point.
		return parent::run($id);
	}
}