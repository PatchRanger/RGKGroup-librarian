<?php
namespace app\controllers\book;


use app\models\Book;
use app\models\User;

class CreateAction extends \yii\rest\CreateAction
{
	/** @var Book $model */
	public $model;

	public function run()
	{
		$this->model = parent::run();
		return $this->model;
	}

	public function afterRun()
	{
		parent::afterRun();
		// @todo Here is the point of extension.
	}
}