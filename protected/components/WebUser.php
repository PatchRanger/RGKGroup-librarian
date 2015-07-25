<?php
namespace app\components;


use app\models\User;
use yii\web\Cookie;

/**
 * Class WebUser
 *
 * @property User $requestedUser
 */
class WebUser extends \yii\web\User
{
	const USER_ID_VAR = 'reqUser';
	/**
	 * @var User
	 */
	private $_model = null;

	private $_access = array();

	private $_reqUID = null;
	private $_reqModel = null;
	private $_isAdmin = null;

	private $_accessList = [
		User::ROLE_ADMIN,
		//User::ROLE_AUTHOR,
	];

	public function init()
	{
		parent::init();
		if (isset($_REQUEST[self::USER_ID_VAR])) {
			$this->_reqUID = intval($_REQUEST[self::USER_ID_VAR]);
		}
	}

	/**
	 * @return integer
	 */
	public function getRole()
	{
		$model = $this->getModel();
		if ( $model )
			return $model->role;
		return null;
	}

	public function getRequestedUser()
	{
		// Check request and access control
		if ( empty($this->_reqUID)
		    || !in_array($this->getRole(), $this->_accessList)
		) {
			return $this->getModel();
		}

		if ($this->_reqModel === null) {
			$this->_reqModel = User::findOne($this->_reqUID);
		}

		return $this->_reqModel;
	}

	public function setRequestedUser($value)
	{
		return false;
	}

	/**
	 * Переопределяем, чтобы защититься от зацикливания.
	 * @return int|null
	 */
	public function getId()
	{
		if (\Yii::$app instanceof \yii\console\Application)
			return null;
		$_SESSION['user.no_follow'] = true;
		$result = parent::getId();
		unset($_SESSION['user.no_follow']);
		return $result;
	}

	/**
	 * @return User|null
	 */
	public function getModel()
	{
		if (\Yii::$app instanceof \yii\console\Application)
			return null;
		if (!$this->isGuest && $this->_model === null) {
			$this->_model = User::findOne((int) $this->id);
		}
		return $this->_model;
	}


	/**
	 * Метод модифицирован таким образом, что проверку доступа осуществляет по роли,
	 * которая хранится в пользовательском свойстве user->role
	 * Проверку можно осуществлять по нескольким ролям сразу. Для этого нужно первым
	 * параметром передать массив ролей. Доступ считается разрешенным, если текущий
	 * пользователь имеет хотябы одну роль, указанную в $operations
	 *
	 * @param string $operations Роль или массив ролей, которыми дложен обладать пользователь,
	 * 	чтобы получить доступ.
	 * @param array $params
	 * @param bool $allowCaching
	 * @return bool
	 */
	public function checkAccess($operations,$params=array(),$allowCaching=true)
	{
		if ( ! is_array($operations))
			$operations = array($operations);

		$valid = false;
		foreach ($operations as $operation)
		{
			if ($allowCaching && $params===array() && isset($this->_access[$operation]))
				$valid = $valid || $this->_access[$operation];
			else
				$valid = $valid || ($this->_access[$operation] = ($this->role == $operation));
		}

		return $valid;
	}

	/**
	 * Сохраняет значение в cookie
	 * @param string  $varName - ключ
	 * @param any  $value - значение
	 * @param integer|null $expire
	 */
	public function setCookieVariable($varName, $value, $expire=null)
	{
		//$cookie = new Cookie($varName, $value);
		$cookie = new Cookie(['name'=>$varName,'value'=>$value]);
		$cookie->expire = (int) $expire;
		$cookie->domain = \app\models\Config::getCookieDomain();

		//\Yii::$app->request->cookies[$varName] = $cookie;
		\Yii::$app->response->cookies->add($cookie);
	}

	/**
	 * Получает значение из cookie
	 * @param $varName - ключ
	 *
	 * @return any
	 */
	public function getCookieVariable($varName)
	{
		return \Yii::$app->request->cookies->has($varName) ?
			\Yii::$app->request->cookies[$varName]->value : null;
	}


	/**
	 * Проверяет текущего юзера на пренадлежность к админам
	 * @return bool
	 */
	public function getIsAdmin()
	{
		if ( is_null($this->_isAdmin) ) {
			$this->_isAdmin = $this->checkAccess([
				//User::ROLE_AUTHOR,
				User::ROLE_ADMIN,
			]);
		}
		return $this->_isAdmin;
	}

	/**
	 * Определяет принадлежность пользователя к ботам
	 * @return bool
	 */
	public function isBot($jsEnabledOnly=false)
	{
		$bots = ($jsEnabledOnly)
			? ['googlebot','google-sitemaps','appEngine-google','feedfetcher-google','AdsBot-Google',]
			: [
				'googlebot','google-sitemaps','appEngine-google','feedfetcher-google','AdsBot-Google',
				'rambler','aport','yahoo','msnbot','turtle','mail<a href="http://webrelease.ru" style="text-decoration:none;border:none">.</a>ru','omsktele',
				'yetibot','picsearch','sape.bot','sape_context','gigabot','snapbot','alexa.com','megadownload.net','askpeter.info',
				'igde<a href="http://webrelease.ru" style="text-decoration:none;border:none">.</a>ru','ask.com','qwartabot',
				'yanga.co.uk','scoutjet','similarpages','oozbot','shrinktheweb.com','aboutusbot','followsite.com',
				'dataparksearch','liveinternet<a href="http://webrelease.ru" style="text-decoration:none;border:none">.</a>ru',
				'xml-sitemaps.com','agama','metadatalabs.com','h1.hrn.ru','googlealert.com','seo-rus.com','yaDirectBot',
				'yandeG','yandex','yandexSomething','Copyscape.com','domaintools.com','bing.com','dotnetdotcom',
				'Nigma<a href="http://webrelease.ru" style="text-decoration:none;border:none">.</a>ru',
			];

		$botIdentifier = $this->getCookieVariable('is_bot');

		if ( $botIdentifier == null ) {

			foreach($bots as $bot) {

				if(stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false)
				{
					$this->setCookieVariable('is_bot', '1', 0);
					return true;
				}
			}

			$this->setCookieVariable('is_bot', '0', 0);
			return false;
		}

		if ( $botIdentifier == 1)
			return true;
		else
			return false;
	}
}