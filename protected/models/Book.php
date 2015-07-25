<?php
namespace app\models;


//use app\behaviors\AuthorBehavior;
//use app\models\interfaces\SafeDeleteInterface;
use yii\behaviors\TimestampBehavior;

/**
 * @property $id int
 * @property $name string
 * @property $preview string
 * @property $author_id int
 * @property $date int
 * @property $date_create int
 * @property $date_update int
 */
//class Book extends \yii\db\ActiveRecord implements SafeDeleteInterface
class Book extends \yii\db\ActiveRecord
{
    /* @todo Review Do we need it?
	const STATUS_ACTIVE = 1;
	const STATUS_DRAFT = 2;
	const STATUS_ARCHIVED = 3;

	static public $statuses = [
		self::STATUS_ACTIVE => 'Активна',
		self::STATUS_DRAFT => 'Черновик',
		self::STATUS_ARCHIVED => 'В архиве',
	];
	*/

	public function rules()
	{
		return [
			[['name'],'required'],
			[['name'], 'string', 'min'=>1, 'max' => 255],
			[['preview'], 'string', 'min'=>1, 'max' => 2048],
			[[
			    //'status',
			    //'user_id',
			    'author_id',
			    'date',
			    'date_create',
			    'date_update',
			], 'number', 'integerOnly'=>true],
			//[['status'],'in','range'=>array_keys(self::$statuses)],
		];
	}

	public function behaviors()
	{
		return [
		    /*
			[
				'class'=>AuthorBehavior::className(),
				'authorModel'=>\Yii::$app->user->getModel(),
			],
			*/
			[
				'class'=>TimestampBehavior::className(),
				'createdAtAttribute' => 'date_create',
				'updatedAtAttribute' => 'date_update',
			],
		];
	}

	/*
	// @todo DRY
	public function safeDelete()
	{
		$this->status = self::STATUS_ARCHIVED;
		$this->save(true,['status']);
		return true;
	}
	*/

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAuthor()
	{
		return $this->hasOne(Author::className(),['id'=>'author_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 *\/
	public function getUser()
	{
		return $this->hasOne(User::className(),['id'=>'user_id']);
	}
	*/

	public function getCanonicalUrl()
	{
		return \Yii::$app->urlManager->createAbsoluteUrl("books/{$this->id}");
	}
}