<?php
namespace app\models;


//use app\models\interfaces\SafeDeleteInterface;

/**
 * @property $id int
 * @property $firstname string
 * @property $lastname string
 */
//class Author extends \yii\db\ActiveRecord implements SafeDeleteInterface
class Author extends \yii\db\ActiveRecord
{
    /* @todo Review Do we need it?
	const STATUS_ACTIVE = 1;
	const STATUS_DRAFT = 2;
	const STATUS_ARCHIVED = 3;

	static public $statuses = [
		self::STATUS_ACTIVE => 'Активен',
		self::STATUS_DRAFT => 'Черновик',
		self::STATUS_ARCHIVED => 'В архиве',
	];
	*/

    const NAME_FORMAT_FIRSTNAME = '%F';
    //const NAME_FORMAT_MIDDLENAME = '%M';
    const NAME_FORMAT_LASTNAME = '%L';

    public static $nameFormats = [
        self::NAME_FORMAT_FIRSTNAME,
        //self::NAME_FORMAT_MIDDLENAME,
        self::NAME_FORMAT_LASTNAME,
    ];

    const NAME_FORMAT_LF = '%L %F';
    const NAME_FORMAT_FL = '%F %L';

	public function rules()
	{
		return [
			[['firstname','lastname'],'required'],
			[['firstname','lastname'], 'string', 'min'=>1, 'max' => 255],
			//[['status'],'in','range'=>array_keys(self::$statuses)],
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
	public function getBooks()
	{
		return $this->hasMany(Book::className(),['author_id'=>'id']);
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
		return \Yii::$app->urlManager->createAbsoluteUrl("authors/{$this->id}");
	}

    public function getName($format) {
        return strtr($format, array_combine(self::$nameFormats,[$this->firstname,$this->lastname]));
    }
}