<?php
namespace app\controllers;


use app\models\User;
use app\components\rbac\UserRoleRule;
use Yii;
use yii\console\Controller;

/**
 * Allows to manage access by role (RBAC).
 * @link http://habrahabr.ru/post/235485/
 */
class RbacController extends Controller
{
	const USER_signup              = 'signup';
	const USER_login               = 'login';
	const USER_logout              = 'logout';

	const BOOK_createBook          = 'createBook';
	const BOOK_updateOwnBooks      = 'updateOwnBooks';
	const BOOK_updateOtherBooks    = 'updateOtherBooks';
	const BOOK_deleteOwnBooks      = 'deleteOwnBooks';
	const BOOK_deleteOtherBooks    = 'deleteOtherBooks';

	public $defaultAction = 'init';

	/**
	 * Command to re-generate roles and permissions basing on access rules listed here.
	 */
	public function actionInit()
	{
		$authManager = \Yii::$app->authManager;
		$authManager->removeAll();

		// Roles.
		$guest = $authManager->createRole(User::ROLENAME_GUEST);
		$user = $authManager->createRole(User::ROLENAME_USER);
		$admin = $authManager->createRole(User::ROLENAME_ADMIN);

		// Permission.
		// Global.
		//$error = $authManager->createPermission('error');
		// Users.
		$signUp		= $authManager->createPermission(self::USER_signup);
		$login		= $authManager->createPermission(self::USER_login);
		$logout		= $authManager->createPermission(self::USER_logout);
		// Books.
		$createBook       = $authManager->createPermission(self::BOOK_createBook);
		$updateOwnBooks   = $authManager->createPermission(self::BOOK_updateOwnBooks);
		$updateOtherBooks = $authManager->createPermission(self::BOOK_updateOtherBooks);
		$deleteOwnBooks   = $authManager->createPermission(self::BOOK_deleteOwnBooks);
		$deleteOtherBooks = $authManager->createPermission(self::BOOK_deleteOtherBooks);

		// Adding permissions to Yii::$app->authManager.
		// Users.
		$authManager->add($signUp);
		$authManager->add($login);
		$authManager->add($logout);
		// Books.
		$authManager->add($createBook);
		$authManager->add($updateOwnBooks);
		$authManager->add($updateOtherBooks);
		$authManager->add($deleteOwnBooks);
		$authManager->add($deleteOtherBooks);

		// Adding access rule UserExt->role === $user->role.
		$userRoleRule = new UserRoleRule();
		$authManager->add($userRoleRule);

		// Adding access rule "UserRoleRule" to the roles.
		$guest->ruleName = $userRoleRule->name;
		$user->ruleName = $userRoleRule->name;
		$admin->ruleName = $userRoleRule->name;

		// Adding roles to Yii::$app->authManager.
		$authManager->add($guest);
		$authManager->add($user);
		$authManager->add($admin);

		// Adding permissions to Yii::$app->authManager.
		// Guest.
		$authManager->addChild($guest, $signUp);
		$authManager->addChild($guest, $login);

		// User.
		$authManager->addChild($user, $logout);
		$authManager->addChild($user, $createBook);
		$authManager->addChild($user, $updateOwnBooks);
		$authManager->addChild($user, $deleteOwnBooks);

		// Admin.
		$authManager->addChild($admin, $updateOtherBooks);
		$authManager->addChild($admin, $deleteOtherBooks);
		$authManager->addChild($admin, $user);
	}
}