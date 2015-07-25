<?php
namespace app\models;


use app\models\interfaces\SafeDeleteInterface;
use yii\base\DynamicModel;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yii\web\IdentityInterface;

/**
 * @property integer $id
 * @property string $email
 * @property integer $status
 * @property integer $role
 * @property string $first_name
 * @property string $middle_name
 * @property string $second_name
 * @property integer $bday
 * @property integer $bmonth
 * @property integer $byear
 * @property integer $sex
 * @property string $password
 * @property integer $date_create
 * @property integer $date_update
 */
class User extends ActiveRecord implements IdentityInterface, SafeDeleteInterface
{
	/**
	 * Сценарии.
	 */
	const SCENARIO_CREATE = 'create';
	const SCENARIO_REGISTER = 'register';

	static public $scenarios = [
		self::SCENARIO_CREATE,
		self::SCENARIO_REGISTER,
	];

	/*
	 * Статусы пользователей
	 */
	const STATUS_ACTIVE = 1;
	const STATUS_MODERATE = 2;
	const STATUS_DISABLED = 3;
	const STATUS_ARCHIVED = 4;
	const STATUS_BANNED = 5;

	static public $statuses = [
		self::STATUS_ACTIVE => 'Активен',
		self::STATUS_MODERATE => 'На модерации',
		self::STATUS_DISABLED => 'Отключен',
		self::STATUS_BANNED => 'Забанен',
		self::STATUS_ARCHIVED => 'В архиве',
	];

	const SEX_NO = 0;
	const SEX_MALE = 1;
	const SEX_FEMALE = 2;

	static public $sexes = [
		self::SEX_NO => 'Не указан',
		self::SEX_MALE => 'Мужчина',
		self::SEX_FEMALE => 'Женщина',
	];
	/*
	 * Роли пользователей
	 */
	const ROLE_ADMIN = 1;
	//const ROLE_AUTHOR = 2;
	const ROLE_USER = 3;

	static public $roles = [
		self::ROLE_ADMIN => 'Администратор',
		//self::ROLE_AUTHOR => 'Автор',
		self::ROLE_USER => 'Пользователь',
	];

	// Необходимо для AuthManager.
	const ROLENAME_ADMIN = 'admin';
	//const ROLENAME_AUTHOR = 'author';
	const ROLENAME_USER = 'user';
	// Виртуальная роль для работы с анонимусами.
	const ROLENAME_GUEST = 'guest';

	static public $roleNames = [
		self::ROLE_ADMIN => self::ROLENAME_ADMIN,
		//self::ROLE_AUTHOR => self::ROLENAME_AUTHOR,
		self::ROLE_USER => self::ROLENAME_USER,
	];

	const TYPE_FOLLOW = 1;
	const DEFAULT_PREVIEW = '/img/ava.svg';

	private $_changedPassword = false;

	private $passwordRaw='';
/*
	public function scenarios()
	{
		parent::scenarios();
		return static::$scenarios;
	}
*/
	public function behaviors()
	{
		return [
			[
				'class'=>TimestampBehavior::className(),
				'createdAtAttribute' => 'date_create',
				'updatedAtAttribute' => 'date_update',
			],
		];
	}

	public function rules()
	{
		// @todo Задать все необходимые правила.
		return [
			[['bday'], 'number', 'integerOnly'=>true, 'min'=>1, 'max'=>31],
			[['bmonth'], 'number', 'integerOnly'=>true, 'min'=>1, 'max'=>12],
			[['byear'], 'number', 'integerOnly'=>true, 'min'=>1900],
			[['email'], 'email'],
			[['first_name','middle_name','second_name'], 'string'],
			[['role','status'], 'number', 'integerOnly'=>true],
			// Запрещаем обновлять роль от греха.
			[['role'],'default','value'=>function($model,$attribute) {
					$oldModel = User::findOne($model->id);
					return (!empty($oldModel) && $oldModel->$attribute == $model->$attribute) ? $model->$attribute : User::ROLE_USER;
				}],
			[['role'],'in','range'=>array_keys(self::$roles), 'except'=>[self::SCENARIO_REGISTER]],
			// Регистрируем всегда только обычных пользователей - чтобы не послали POST с ролью админа.
			[['role'],'in','range'=>[User::ROLE_USER],'on'=>[self::SCENARIO_REGISTER]],
			[['sex'],'default','value'=>User::SEX_NO],
			[['sex'],'in','range'=>array_keys(self::$sexes)],
			[['status'],'default','value'=>User::STATUS_ACTIVE],
			[['status'],'in','range'=>array_keys(self::$statuses)],
		];
	}

	public function beforeSave($insert)
	{
		if ($success = parent::beforeSave($insert)) {
			// Только что зарегистрированным (неважно, каким путём) задаём пароль.
			if ($this->isNewRecord/* && in_array($this->getScenario(),[self::SCENARIO_REGISTER])*/) {
				$this->password = $this->passwordRaw = \Yii::$app->getSecurity()->generateRandomString(12);
				$this->_changedPassword = true;
			}
			// Обновлённый пароль хэшируем перед сохранением.
			if ($this->_changedPassword) {
				// @todo Не использовать пароль в качестве соли.
				parent::__set('password', crypt($this->password,$this->password));
				$this->_changedPassword = false;
			}
			/* @todo Реализовать изменение Роли и Статуса других пользователей.
			// Из-за магии setStatus и setRole приходится задавать умолчательные значения здесь, а не в определениях параметров.
			if (empty($this->role)) $this->setRole(self::ROLE_USER);
			if (empty($this->status)) $this->setStatus(self::STATUS_ACTIVE);
			 */
		}
		return $success;
	}

	public function toArray(array $fields = [], array $expand = [], $recursive = true)
	{
		$this->fillVirtualModel();
		$expand = ArrayHelper::merge($expand,$this->extraFields());
		$array = parent::toArray($fields,$expand,$recursive);
		// Нафиг светить.
		unset($array['password']);
		// @todo Refactor: защита доступа.
		$currentUser = \Yii::$app->user->getModel();
		if ($this->getScenario() != 'oauth' && (empty($currentUser) || ($currentUser->id != $this->id && !in_array($currentUser->role,[
		    //User::ROLE_AUTHOR,
		    User::ROLE_ADMIN,
		])))) {
			unset($array['bday']);
			unset($array['bmonth']);
			unset($array['byear']);
			unset($array['email']);
			unset($array['role']);
			// Там только связь с соцсетями, можно публично.
			//unset($array['social']);
			unset($array['status']);
		}
		return $array;
	}

	// @todo DRY
	public function safeDelete()
	{
		$this->status = self::STATUS_ARCHIVED;
		$this->save(true,['status']);
		return true;
	}

	// @todo Remove.
	/**
	 * Check password for correct
	 *\/
	public function checkPassword($pass)
	{
		// @todo Заменить cryptPassword.
		return ( $this->password === self::cryptPassword($pass) );
	}
	 */

	// <IdentityInterface>
	public function getId()
	{
		return $this->id;
	}

	public function getAuthKey()
	{
		return $this->password;
	}

	public function validateAuthKey($authKey)
	{
		/**
		 * @var $record User
		 *\/
		$record = User::model()->findByAttributes(array('email'=>$this->username));

		if($record===null)
		$this->errorCode=self::ERROR_USERNAME_INVALID;
		else if( !$record->checkPassword($this->password) )
		$this->errorCode=self::ERROR_PASSWORD_INVALID;
		else if($record->status!=User::STATUS_ACTIVE)
		$this->errorCode=self::ERROR_NOT_ACTIVE;
		else
		{
		$this->_id=$record->id;
		$this->setState('email', $record->email);
		$this->errorCode=self::ERROR_NONE;
		}

		return !$this->errorCode;*/
		//return $this->checkPassword($authKey);
		return $this->password === $authKey;
	}

	public static function findIdentity($id)
	{
		return static::findOne($id);
	}

	public static function findIdentityByAccessToken($token, $type = null)
	{
		return static::findOne(['password' => $token]);
	}
	// </IdentityInterface>

	/**
	 * @return string user full name
	 */
	public function getFullName()
	{
		return $this->getFI();
	}

	public function getFI()
	{
		return implode(' ',array_filter([$this->second_name,$this->first_name]));
	}

	public function getFIO()
	{
		return implode(' ',array_filter([$this->second_name,$this->first_name,$this->middle_name]));
	}
}