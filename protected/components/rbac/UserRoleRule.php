<?php
namespace app\components\rbac;


use Yii;
use yii\rbac\Rule;
use app\models\User;

/**
 * Соотносит хранящееся в бд значение роли юзера с разрешениями, хранящимимся с помощью PhpManager.
 * @see http://habrahabr.ru/post/235485/
 */
class UserRoleRule extends Rule
{
	public $name = 'userRole';

	public function execute($user, $item, $params)
	{
		if (Yii::$app->user->isGuest) {
			return $item->name === User::ROLENAME_GUEST;
		}
		/** @var User $user */
		$user = User::findOne($user);
		if (!empty($user) && ($role = $user->role) && !empty(User::$roleNames[$role]) && ($roleName = User::$roleNames[$role])) {
			if ($item->name === User::ROLENAME_ADMIN) {
				return $roleName == User::ROLENAME_ADMIN;
			} elseif ($item->name === User::ROLENAME_USER) {
				return $roleName == User::ROLENAME_ADMIN || $roleName == User::ROLENAME_USER;
			}
		}
		return false;
	}
}