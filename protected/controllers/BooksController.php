<?php
namespace app\controllers;


use app\controllers\behaviors\access\ConditionAccessRule;
use app\models\Author;
use app\models\Book;
use yii\data\ActiveDataProvider;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\Controller;

class BooksController extends Controller
{
	public $layout = '@app/views/layouts/front.php';

	public function actionIndex($author_id=null, $name=null)
	{
		$date = \Yii::$app->getRequest()->getQueryParam('date',[]);
		$authors = Author::find()->all();
		$query = Book::find();
		if (!empty($author_id) && is_numeric($author_id)) {
			$query->andWhere(['author_id'=>$author_id]);
		}
		if (!empty($name)) {
			$query->andWhere(['like','name',$name]);
		}
		if (!empty($date)) {
			if (!empty($date['from'])) {
				$query->andWhere('date >= %date_from',['%date_from'=>$date['from']]);
			}
			if (!empty($date['to'])) {
				$query->andWhere('date <= %date_to',['%date_to'=>$date['to']]);
			}
		}
		$query->orderBy(['name'=>SORT_ASC]);
		$this->getView()->title = 'Books | RGK Group Librarian';
		return $this->render('index',[
			'author_id'=>$author_id,
			'authors'=>$authors,
			'name'=>$name,
			'date'=>$date,
			'booksDp' => new ActiveDataProvider([
				'query'=>$query,
				'pagination'=>[
					'pageParam'=>'page',
					'pageSizeParam'=>'perPage',
				],
			]),
		]);
	}
}