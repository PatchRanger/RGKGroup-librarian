<?php
namespace app\controllers\book;


use app\models\interfaces\SafeDeleteInterface;
use yii\web\ServerErrorHttpException;

class DeleteAction extends \yii\rest\DeleteAction
{
    /* @todo Review Do we need it?
	public function run($id)
	{
	    // Not using default implementation as we need safe delete (saving to another status).
		//parent::run($id);
		$model = $this->findModel($id);

		if ($this->checkAccess) {
			call_user_func($this->checkAccess, $this->id, $model);
		}

		$result = ($model instanceof SafeDeleteInterface) ? $model->safeDelete() : $model->delete();
		if ($result === false) {
			throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
		}

		\Yii::$app->getResponse()->setStatusCode(204);
	}
	*/
}