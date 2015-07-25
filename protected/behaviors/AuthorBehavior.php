<?php
namespace app\behaviors;


use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;

class AuthorBehavior extends AttributeBehavior
{
	public $authorProperty = 'user_id';

	/**
	 * @var \app\models\User
	 */
	public $authorModel;

	public function init()
	{
		parent::init();

		if (empty($this->attributes)) {
			$this->attributes = [
				BaseActiveRecord::EVENT_BEFORE_INSERT => $this->authorProperty,
				// Это ведёт к тому, что автором задаётся последний посмотревший или лайкнувший.
				// Пусть уж автором будет только тот, кто создал.
				//BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->authorProperty,
			];
		}
	}

	protected function getValue($event)
	{
		if ($this->value instanceof Expression) {
			return $this->value;
		} else {
			return $this->value !== null ? call_user_func($this->value, $event) : (!empty($this->authorModel->id) ? $this->authorModel->id : null);
		}
	}
}