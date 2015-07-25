<?php
namespace app\controllers\book;


use app\models\Book;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Expression;

class IndexAction extends \yii\rest\IndexAction
{
	const SORT_NEW = 'new';
	//const SORT_POPULAR = 'popular';
	const SORT_RANDOM = 'random';
	const SORT_ALPHA = 'title';

	public static $sorts = [
		self::SORT_NEW,
		//self::SORT_POPULAR,
		self::SORT_RANDOM,
		self::SORT_ALPHA,
	];

	public $limit;
	public $perPage;
	public $search;
	public $sort;
	//public $type = self::RELATION_TYPE_AUTHOR;
	public $author_id;

	protected function getQuery()
	{
		/* @var $modelClass \yii\db\BaseActiveRecord */
		$modelClass = $this->modelClass;
		return $modelClass::find();
	}

	public function getDataProvider()
	{
		/** @var \yii\db\ActiveQuery $query */
		$query = $this->getQuery();
		//$query->andWhere(['book.status'=>Collection::STATUS_ACTIVE]);
		if (!empty($this->author_id)) {

		}
		if (!empty($this->search)) {
			$query->andWhere(['like','book.name',$this->search]);
		}
		switch ($this->sort) {
			case self::SORT_ALPHA:
				$query->orderBy(['name'=>SORT_ASC]);
				break;
			/*
			case self::SORT_POPULAR:
				/**
				 * @see http://www.yiiframework.com/wiki/679/filter-sort-by-summary-data-in-gridview-yii-2-0/
				 *\/
				$query->leftJoin([
						'postsViewQnt'=>Post::find()->select('collection_id, SUM(views) as postViewQnt')->groupBy('collection_id'),
					],'postsViewQnt.collection_id = id')
					->orderBy(['postsViewQnt.postViewQnt'=>SORT_DESC,'title'=>SORT_ASC]);
				break;
			*/
			case self::SORT_RANDOM:
				$query->orderBy(new Expression('RAND()'));
				break;
			case self::SORT_NEW:
			default:
				$query->orderBy(['date_create'=>SORT_DESC]);
				break;
		}
		$pagination = [
		    // Re-define deafult parameter to make pagination working.
			'pageParam'=>'page',
			'pageSizeParam'=>'perPage',
		];
		if (empty($this->perPage) && !empty($this->limit)) {
			$query->limit($this->limit);
			// Pagination re-defines limit, we have to erase it.
			$pagination = false;
			// @todo Review Erasing pagination leads to empty meta-data (_links and _meta) - be aware. Below is recipe to fight it.
			//return new ArrayDataProvider(['allModels'=>$query->all()]);
		}
		$dataProvider = new ActiveDataProvider([
			'query'=>$query,
			'pagination'=>$pagination,
		]);
		return $dataProvider;
	}
}